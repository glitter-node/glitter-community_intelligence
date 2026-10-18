<?php

namespace Modules\Glitter\CommunityIntelligence\Http\Resources;

use App\Http\Resources\BaseApiResource;
use Illuminate\Http\Request;

class ReviewResource extends BaseApiResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'signal_id' => $this->signal_id,
            'reviewer_id' => $this->reviewer_id,
            'from_status' => $this->from_status,
            'to_status' => $this->to_status,
            'reason_code' => $this->reason_code,
            'note' => $this->note,
            'created_at' => optional($this->created_at)->toIso8601String(),
        ];
    }
}
