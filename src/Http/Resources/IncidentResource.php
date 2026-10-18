<?php

namespace Modules\Glitter\CommunityIntelligence\Http\Resources;

use App\Http\Resources\BaseApiResource;
use Illuminate\Http\Request;

class IncidentResource extends BaseApiResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'subject' => ['type' => $this->primary_subject_type, 'id' => $this->primary_subject_id],
            'board_id' => $this->board_id,
            'title' => ['key' => $this->title_key, 'params' => $this->title_params],
            'status' => $this->status,
            'started_at' => optional($this->started_at)->toIso8601String(),
            'last_activity_at' => optional($this->last_activity_at)->toIso8601String(),
            'events' => $this->whenLoaded('events', fn () => $this->events->map(fn ($event) => [
                'id' => $event->id,
                'event_type' => $event->event_type,
                'source_type' => $event->source_type,
                'source_id' => $event->source_id,
                'actor_id' => $event->actor_id,
                'occurred_at' => optional($event->occurred_at)->toIso8601String(),
                'summary_key' => $event->summary_key,
                'metadata' => $event->metadata ?? [],
            ])),
        ];
    }
}
