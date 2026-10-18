<?php

namespace Modules\Glitter\CommunityIntelligence\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Glitter\CommunityIntelligence\Enums\ReviewStatus;

class ReviewSignalRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array { return ['status' => ['required', Rule::enum(ReviewStatus::class)], 'reason_code' => ['nullable', 'string', 'max:64', 'required_if:status,dismissed'], 'note' => ['nullable', 'string', 'max:2000']]; }
}
