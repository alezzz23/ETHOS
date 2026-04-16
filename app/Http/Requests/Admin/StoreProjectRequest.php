<?php

namespace App\Http\Requests\Admin;

use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('projects.create') ?? false;
    }

    public function rules(): array
    {
        return [
            'client_id'        => ['required', 'integer', 'exists:clients,id'],
            'title'            => ['required', 'string', 'max:255'],
            'description'      => ['nullable', 'string', 'max:5000'],
            'type'             => ['nullable', 'string', 'max:100'],
            'subtype'          => ['nullable', 'string', 'max:100'],
            'urgency'          => ['nullable', Rule::in(['baja', 'media', 'alta'])],
            'complexity'       => ['nullable', Rule::in(['baja', 'media', 'alta'])],
            'starts_at'        => ['nullable', 'date', 'after_or_equal:today'],
            'estimated_budget' => ['nullable', 'numeric', 'min:0', 'max:999999999.99'],
            'currency'         => ['nullable', 'string', 'size:3', 'regex:/^[A-Z]{3}$/'],
        ];
    }

    public function validated($key = null, $default = null): array
    {
        $data = parent::validated($key, $default);
        $data['status']      = Project::STATUS_CAPTURADO;
        $data['captured_by'] = $this->user()?->id;

        return $data;
    }
}
