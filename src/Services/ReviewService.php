<?php

namespace Modules\Glitter\CommunityIntelligence\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Modules\Glitter\CommunityIntelligence\Enums\ReviewStatus;
use Modules\Glitter\CommunityIntelligence\Models\Incident;
use Modules\Glitter\CommunityIntelligence\Models\IncidentEvent;
use Modules\Glitter\CommunityIntelligence\Models\Review;
use Modules\Glitter\CommunityIntelligence\Models\Signal;

class ReviewService
{
    public function __construct(private AccessAuditService $audit) {}

    public function review(Signal $signal, int $reviewerId, string $toStatus, ?string $reasonCode, ?string $note): Review
    {
        $status = ReviewStatus::tryFrom($toStatus);
        if (! $status) {
            throw ValidationException::withMessages(['status' => __('glitter-community-intelligence::validation.review.status')]);
        }
        if ($status === ReviewStatus::Dismissed && blank($reasonCode)) {
            throw ValidationException::withMessages(['reason_code' => __('glitter-community-intelligence::validation.review.dismissed_reason')]);
        }

        $current = ReviewStatus::tryFrom($signal->status);
        if (! $current || ! $current->canTransitionTo($status)) {
            throw ValidationException::withMessages(['status' => __('glitter-community-intelligence::validation.review.transition')]);
        }

        return DB::transaction(function () use ($signal, $reviewerId, $status, $reasonCode, $note): Review {
            $from = $signal->status;
            $review = Review::query()->create([
                'signal_id' => $signal->id,
                'reviewer_id' => $reviewerId,
                'from_status' => $from,
                'to_status' => $status->value,
                'reason_code' => $reasonCode,
                'note' => $note,
                'created_at' => now(),
            ]);
            $signal->update(['status' => $status->value]);

            $incident = Incident::query()->firstOrCreate([
                'primary_subject_type' => $signal->subject_type,
                'primary_subject_id' => $signal->subject_id,
            ], [
                'type' => $signal->type,
                'board_id' => $signal->board_id,
                'title_key' => 'glitter-community-intelligence::messages.incident.title',
                'title_params' => ['type' => $signal->type, 'id' => $signal->subject_id],
                'status' => 'open',
                'started_at' => $signal->observed_from ?? $signal->detected_at,
                'last_activity_at' => $review->created_at,
            ]);

            if ($incident->wasRecentlyCreated) {
                $this->event($incident, 'incident_created', 'incident', (int) $incident->id, $reviewerId, $review->created_at, 'incident.created', []);
                $this->event($incident, 'signal_linked', 'signal', (int) $signal->id, $reviewerId, $review->created_at, 'incident.signal_linked', ['signal_type' => $signal->type]);
            }

            $this->event($incident, 'review_added', 'review', (int) $review->id, $reviewerId, $review->created_at, 'incident.review_added', ['from_status' => $from, 'to_status' => $status->value]);

            $incidentStatus = $status->isTerminal() ? 'closed' : 'under_review';
            if ($incident->status !== $incidentStatus) {
                $incidentFrom = $incident->status;
                $incident->update(['status' => $incidentStatus, 'last_activity_at' => $review->created_at]);
                $this->event($incident, 'incident_status_changed', 'review', (int) $review->id, $reviewerId, $review->created_at, 'incident.status_changed', ['from_status' => $incidentFrom, 'to_status' => $incidentStatus]);
            } else {
                $incident->update(['last_activity_at' => $review->created_at]);
            }

            if ($status->isTerminal()) {
                $this->event($incident, 'incident_closed', 'review', (int) $review->id, $reviewerId, $review->created_at, 'incident.closed', ['resolution' => $status->value]);
            }

            $this->audit->record($reviewerId, 'signal', (int) $signal->id, 'signal_reviewed');

            return $review;
        });
    }

    private function event(Incident $incident, string $eventType, string $sourceType, int $sourceId, ?int $actorId, $occurredAt, string $summary, array $metadata): void
    {
        IncidentEvent::query()->firstOrCreate(
            ['idempotency_key' => hash('sha256', $eventType.':'.$sourceType.':'.$sourceId)],
            [
                'incident_id' => $incident->id,
                'event_type' => $eventType,
                'source_type' => $sourceType,
                'source_id' => $sourceId,
                'actor_id' => $actorId,
                'occurred_at' => $occurredAt,
                'summary_key' => 'glitter-community-intelligence::messages.'.$summary,
                'metadata' => $metadata,
            ]
        );
    }
}
