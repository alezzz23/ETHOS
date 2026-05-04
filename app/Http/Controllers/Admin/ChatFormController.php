<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Chat\ChatFormFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ChatFormController extends Controller
{
    public function __construct(private ChatFormFactory $forms) {}

    public function schema(Request $request): JsonResponse
    {
        $data = $request->validate([
            'entity' => ['required', 'string', Rule::in($this->forms->entities())],
            'defaults' => ['nullable', 'array'],
        ]);

        abort_unless($this->forms->canOpen($data['entity'], $request->user()), 403);

        $form = $this->forms->makeCreationForm($data['entity'], $data['defaults'] ?? []);
        abort_if($form === null, 404);

        return response()->json(['form' => $form]);
    }
}