<?php

namespace Modules\Glitter\CommunityIntelligence\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Glitter\CommunityIntelligence\Enums\ReviewStatus;
use Modules\Glitter\CommunityIntelligence\Enums\SignalType;

class SignalListRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array { return ['type' => ['nullable', Rule::enum(SignalType::class)], 'status' => ['nullable', Rule::enum(ReviewStatus::class)], 'board_id' => ['nullable', 'integer', 'min:1'], 'from' => ['nullable', 'date'], 'until' => ['nullable', 'date', 'after_or_equal:from'], 'priority' => ['nullable', 'integer', 'between:0,100'], 'rule_version' => ['nullable', 'integer', 'min:1'], 'sort_by' => ['nullable', Rule::in(['detected_at', 'priority', 'evidence_count', 'status'])], 'sort_order' => ['nullable', Rule::in(['asc', 'desc'])], 'per_page' => ['nullable', 'integer', 'between:1,100']]; }
}
