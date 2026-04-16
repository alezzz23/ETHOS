<?php

namespace App\Http\Requests\Admin\Chat;

use Illuminate\Foundation\Http\FormRequest;

class FeedbackRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('admin.access') ?? false;
    }

    public function rules(): array
    {
        return [
            'admin_chat_log_id' => ['nullable', 'integer', 'exists:admin_chat_logs,id'],
            'rating'            => ['required', 'in:helpful,not_helpful'],
            'context'           => ['nullable', 'string', 'max:120'],
            'user_message'      => ['nullable', 'string', 'max:4000'],
            'assistant_message' => ['nullable', 'string', 'max:8000'],
            'improvement_note'  => ['nullable', 'string', 'max:1000'],
        ];
    }
}
