<?php
namespace Rivulet\Auth;

use Rivulet\Model;

class TokenGuard
{
    private string $model = \App\Models\User::class;

    public function authenticate(string $token): ?Model
    {
        $userClass = $this->model;
        return $userClass::where('token', $token)->first();
    }

    public function generateToken(Model $user): string
    {
        $token = strRandom(60);

        $user->update([
            'token' => $token,
        ]);

        return $token;
    }

    public function revokeToken(Model $user): bool
    {
        return $user->update([
            'token' => null,
        ]);
    }

    public function checkToken(string $token): bool
    {
        $userClass = $this->model;
        return $userClass::where('token', $token)->exists();
    }
}
