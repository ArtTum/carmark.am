<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserAuthToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:160', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $user = User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
        ]);

        return response()->json($this->tokenPayload($user), 201);
    }

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::query()->where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json(['message' => 'Invalid email or password.'], 422);
        }

        return response()->json($this->tokenPayload($user));
    }

    public function me(Request $request): JsonResponse
    {
        [$user] = $this->userFromRequest($request);

        return response()->json(['user' => $this->userPayload($user)]);
    }

    public function logout(Request $request): JsonResponse
    {
        [, $token] = $this->userFromRequest($request);
        $token->delete();

        return response()->json(['message' => 'Logged out.']);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        [$user] = $this->userFromRequest($request);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:160', Rule::unique('users', 'email')->ignore($user->id)],
        ]);

        $user->update($data);

        return response()->json(['message' => 'Profile updated.', 'user' => $this->userPayload($user->fresh())]);
    }

    public function updatePassword(Request $request): JsonResponse
    {
        [$user] = $this->userFromRequest($request);

        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        if (!Hash::check($data['current_password'], $user->password)) {
            return response()->json(['message' => 'Current password is incorrect.'], 422);
        }

        $user->update(['password' => $data['password']]);
        $user->authTokens()->delete();

        return response()->json($this->tokenPayload($user->fresh()));
    }

    public function forgotPassword(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:160'],
        ]);

        $email = strtolower($data['email']);
        $user = User::query()->where('email', $email)->first();
        $plainToken = null;

        if ($user) {
            $plainToken = Str::random(64);

            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $email],
                [
                    'token' => hash('sha256', $plainToken),
                    'expires_at' => now()->addMinutes(30),
                    'created_at' => now(),
                ],
            );
        }

        return response()->json([
            'message' => 'If this email exists, password reset instructions have been sent.',
            'email' => $email,
            'reset_url' => $plainToken
                ? $this->resetUrl($request, $email, $plainToken)
                : null,
        ]);
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:160'],
            'token' => ['required', 'string'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $email = strtolower($data['email']);
        $reset = DB::table('password_reset_tokens')->where('email', $email)->first();

        if (
            !$reset
            || !hash_equals($reset->token, hash('sha256', $data['token']))
            || ($reset->expires_at && now()->greaterThan($reset->expires_at))
        ) {
            return response()->json(['message' => 'Password reset link is invalid or expired.'], 422);
        }

        $user = User::query()->where('email', $email)->first();

        if (!$user) {
            DB::table('password_reset_tokens')->where('email', $email)->delete();
            return response()->json(['message' => 'Password reset link is invalid or expired.'], 422);
        }

        $user->update(['password' => $data['password']]);
        $user->authTokens()->delete();
        DB::table('password_reset_tokens')->where('email', $email)->delete();

        return response()->json(['message' => 'Password has been reset.']);
    }

    private function tokenPayload(User $user): array
    {
        $plainToken = Str::random(64);

        $user->authTokens()->create([
            'token_hash' => hash('sha256', $plainToken),
            'name' => 'web',
            'expires_at' => now()->addDays(30),
        ]);

        return [
            'token' => $plainToken,
            'user' => $this->userPayload($user),
        ];
    }

    private function userFromRequest(Request $request): array
    {
        $plainToken = (string) $request->bearerToken();

        if ($plainToken === '') {
            throw new HttpResponseException(response()->json(['message' => 'Unauthorized.'], 401));
        }

        $token = UserAuthToken::query()
            ->with('user')
            ->where('token_hash', hash('sha256', $plainToken))
            ->first();

        if (!$token || !$token->user || ($token->expires_at && $token->expires_at->isPast())) {
            $token?->delete();
            throw new HttpResponseException(response()->json(['message' => 'Unauthorized.'], 401));
        }

        $token->forceFill(['last_used_at' => now()])->save();

        return [$token->user, $token];
    }

    private function userPayload(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ];
    }

    private function resetUrl(Request $request, string $email, string $plainToken): string
    {
        $frontend = rtrim((string) ($request->headers->get('origin') ?: config('app.frontend_url')), '/');
        $baseUrl = $frontend !== '' ? $frontend : $request->getSchemeAndHttpHost();

        return $baseUrl . '/hy/reset-password?email=' . urlencode($email) . '&token=' . urlencode($plainToken);
    }
}
