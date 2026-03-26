<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information (AJAX).
     */
    public function update(ProfileUpdateRequest $request): JsonResponse|RedirectResponse
    {
        $user = $request->user();
        $data = $request->validated();

        if (isset($data['email']) && $data['email'] !== $user->email) {
            $data['email_verified_at'] = null;
        }

        $user->fill($data);
        $user->save();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Perfil actualizado correctamente.',
                'user'    => [
                    'name'       => $user->name,
                    'email'      => $user->email,
                    'phone'      => $user->phone,
                    'birth_date' => $user->birth_date?->format('Y-m-d'),
                    'position'   => $user->position,
                    'bio'        => $user->bio,
                    'initials'   => $user->initials,
                    'avatar_url' => $user->avatar_url,
                ],
            ]);
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Update the user's password (AJAX).
     */
    public function updatePassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'current_password' => ['required'],
            'password'         => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'current_password.required' => 'La contraseña actual es obligatoria.',
            'password.required'         => 'La nueva contraseña es obligatoria.',
            'password.min'              => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed'        => 'Las contraseñas no coinciden.',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $user = $request->user();

        if (! Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'errors'  => ['current_password' => ['La contraseña actual es incorrecta.']],
            ], 422);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return response()->json([
            'success' => true,
            'message' => 'Contraseña actualizada correctamente.',
        ]);
    }

    /**
     * Update the user's avatar (AJAX).
     */
    public function updateAvatar(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'avatar' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
        ], [
            'avatar.required' => 'Selecciona una imagen.',
            'avatar.image'    => 'El archivo debe ser una imagen.',
            'avatar.mimes'    => 'Formatos permitidos: jpeg, png, jpg, gif, webp.',
            'avatar.max'      => 'La imagen no puede superar los 2MB.',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $user = $request->user();

        // Delete old avatar
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = $request->file('avatar')->store('avatars', 'public');
        $user->update(['avatar' => $path]);

        return response()->json([
            'success'    => true,
            'message'    => 'Foto de perfil actualizada.',
            'avatar_url' => $user->avatar_url,
            'initials'   => $user->initials,
        ]);
    }

    /**
     * Update notification and privacy preferences (AJAX).
     */
    public function updatePreferences(Request $request): JsonResponse
    {
        $user = $request->user();

        $user->update([
            'notif_email'           => (bool) $request->input('notif_email', false),
            'notif_browser'         => (bool) $request->input('notif_browser', false),
            'notif_project_updates' => (bool) $request->input('notif_project_updates', false),
            'notif_client_activity' => (bool) $request->input('notif_client_activity', false),
            'privacy_show_email'    => (bool) $request->input('privacy_show_email', false),
            'privacy_show_phone'    => (bool) $request->input('privacy_show_phone', false),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Preferencias guardadas correctamente.',
        ]);
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
