<?php

namespace Modules\Glitter\CommunityIntelligence\Http\Resources;

use App\Http\Resources\BaseApiResource;
use Illuminate\Http\Request;

class SignalResource extends BaseApiResource
{
    public function toArray(Request $request): array
    {
        return ['id' => $this->id, 'type' => $this->type, 'subject' => ['type' => $this->subject_type, 'id' => $this->subject_id], 'board_id' => $this->board_id, 'status' => $this->status, 'priority' => $this->priority, 'observed_from' => optional($this->observed_from)->toIso8601String(), 'observed_until' => optional($this->observed_until)->toIso8601String(), 'rule' => ['identifier' => $this->rule_identifier, 'version' => $this->rule_version], 'summary' => ['key' => $this->summary_key, 'params' => $this->summary_params], 'explanation' => ['key' => $this->explanation_key, 'params' => $this->explanation_params], 'evidence_count' => $this->evidence_count, 'detected_at' => optional($this->detected_at)->toIso8601String(), 'observations' => $this->whenLoaded('observations', fn () => $this->observations->map(fn ($item) => ['kind' => $item->kind, 'source_type' => $item->source_type, 'source_id' => $item->source_id, 'occurred_at' => $item->occurred_at->toIso8601String(), 'facts' => $item->facts])), 'reviews' => $this->whenLoaded('reviews', fn () => $this->reviews->map(fn ($item) => ['reviewer_id' => $item->reviewer_id, 'from_status' => $item->from_status, 'to_status' => $item->to_status, 'reason_code' => $item->reason_code, 'note' => $item->note, 'created_at' => $item->created_at->toIso8601String()]))];
    }
}
