<?php

namespace App\Auth;

use App\Models\SimpleUser;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Support\Facades\Cache;

class CacheUserProvider implements UserProvider
{
    protected string $cacheKey = 'registered_users';

    public function retrieveById($identifier)
    {
        if ($this->userExists($identifier)) {
            return new SimpleUser($identifier);
        }

        return null;
    }

    public function retrieveByToken($identifier, $token)
    {
        return null;
    }

    public function updateRememberToken(
        Authenticatable $user,
        $token
    ) {
        // Remember tokens are not used.
    }

    public function retrieveByCredentials(array $credentials)
    {
        $username = $credentials['username'] ?? null;

        if ($username && $this->userExists($username)) {
            return new SimpleUser($username);
        }

        return null;
    }

    public function validateCredentials(
        Authenticatable $user,
        array $credentials
    ) {
        return $this->userExists(
            $credentials['username'] ?? ''
        );
    }

        public function rehashPasswordIfRequired(
        Authenticatable $user,
        array $credentials,
        bool $force = false
    ): void {
        // No password authentication.
    }

    protected function userExists(string $username): bool
    {
        $users = Cache::get($this->cacheKey, []);

        return in_array($username, $users, true);
    }
}
