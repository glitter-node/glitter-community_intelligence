<?php

namespace Modules\Glitter\CommunityIntelligence\Services;

use Illuminate\Support\Facades\DB;
use Modules\Glitter\CommunityIntelligence\Models\Incident;
use Modules\Glitter\CommunityIntelligence\Models\IncidentEvent;
use Modules\Glitter\CommunityIntelligence\Models\Observation;
use Modules\Glitter\CommunityIntelligence\Models\Signal;

class SignalWriter
{
    public function write(array $signalData, array $facts, bool $dryRun): bool
    {
        if (Signal::query()->where('deduplication_key', $signalData['deduplication_key'])->exists()) return false;
        if ($dryRun) return true;

        return DB::transaction(function () use ($signalData, $facts): bool {
            $signal = Signal::query()->create($signalData);
            Observation::query()->create([
                'signal_id' => $signal->id, 'kind' => $signal->type,
                'source_type' => $signal->subject_type, 'source_id' => $signal->subject_id,
                'actor_id' => null, 'occurred_at' => $signal->observed_until ?? $signal->detected_at,
                'facts' => $facts,
            ]);
            $incident = Incident::query()->firstOrCreate([
                'primary_subject_type' => $signal->subject_type,
                'primary_subject_id' => $signal->subject_id,
            ], [
                'type' => $signal->type, 'board_id' => $signal->board_id,
                'title_key' => 'glitter-community-intelligence::messages.incident.title',
                'title_params' => ['type' => $signal->type, 'id' => $signal->subject_id],
                'status' => 'open', 'started_at' => $signal->observed_from ?? $signal->detected_at,
                'last_activity_at' => $signal->detected_at,
            ]);

            if ($incident->wasRecentlyCreated) {
                IncidentEvent::query()->firstOrCreate(['idempotency_key' => hash('sha256', 'incident_created:'.$incident->id)], [
                    'incident_id' => $incident->id, 'event_type' => 'incident_created',
                    'source_type' => 'incident', 'source_id' => $incident->id, 'actor_id' => null,
                    'occurred_at' => $signal->detected_at,
                    'summary_key' => 'glitter-community-intelligence::messages.incident.created',
                    'metadata' => [],
                ]);
            } else {
                $incident->update(['last_activity_at' => $signal->detected_at]);
            }

            IncidentEvent::query()->firstOrCreate(['idempotency_key' => hash('sha256', 'signal_linked:'.$signal->id)], [
                'incident_id' => $incident->id, 'event_type' => 'signal_linked',
                'source_type' => 'signal', 'source_id' => $signal->id, 'actor_id' => null,
                'occurred_at' => $signal->detected_at,
                'summary_key' => 'glitter-community-intelligence::messages.incident.signal_linked',
                'metadata' => ['signal_type' => $signal->type],
            ]);
            return true;
        });
    }
}
