<?php

namespace Modules\Glitter\CommunityIntelligence\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRuleRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array { return ['enabled' => ['sometimes', 'boolean'], 'observation_window_minutes' => ['sometimes', 'integer', 'between:5,525600'], 'board_scope' => ['sometimes', 'array', 'max:1000'], 'board_scope.*' => ['integer', 'min:1', 'distinct'], 'thresholds' => ['sometimes', 'array', 'max:10'], 'thresholds.*' => ['integer', 'between:1,1000000']]; }
}
