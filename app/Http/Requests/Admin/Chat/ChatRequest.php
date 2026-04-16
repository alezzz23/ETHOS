<?php

namespace App\Http\Requests\Admin\Chat;

use Illuminate\Foundation\Http\FormRequest;

class ChatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('admin.access') ?? false;
    }

    public function rules(): array
    {
        $max = (int) config('chatbot.limits.input_max_chars', 2000);

        return [
            'message'             => ['required', 'string', "max:{$max}"],
            'history'             => ['nullable', 'array', 'max:60'],
            'history.*.role'      => ['required_with:history', 'string', 'in:user,assistant,system'],
            'history.*.content'   => ['required_with:history', 'string', 'max:8000'],
            'conversation_id'     => ['nullable', 'string', 'size:36'],
        ];
    }

    public function prepareForValidation(): void
    {
        if (is_string($this->input('message'))) {
            $this->merge(['message' => trim($this->input('message'))]);
        }
    }
}
