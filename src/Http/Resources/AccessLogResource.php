<?php

namespace Modules\Glitter\CommunityIntelligence\Http\Resources;

use App\Http\Resources\BaseApiResource;
use Illuminate\Http\Request;

class AccessLogResource extends BaseApiResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'target_type' => $this->target_type,
            'target_id' => $this->target_id,
            'feature_identifier' => $this->feature_identifier,
            'viewed_at' => optional($this->viewed_at)->toIso8601String(),
        ];
    }
}
