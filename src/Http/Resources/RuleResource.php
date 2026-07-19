<?php

namespace Modules\Glitter\CommunityIntelligence\Http\Resources;

use App\Http\Resources\BaseApiResource;
use Illuminate\Http\Request;

class RuleResource extends BaseApiResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'identifier' => $this->identifier,
            'version' => $this->version,
            'enabled' => $this->enabled,
            'board_scope' => $this->board_scope,
            'observation_window_minutes' => $this->observation_window_minutes,
            'thresholds' => $this->thresholds,
            'configured_at' => optional($this->configured_at)->toIso8601String(),
        ];
    }
}
