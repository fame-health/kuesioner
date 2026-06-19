<?php

namespace App\Policies;

use App\Models\Response;
use App\Models\User;

class ResponsePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isRegularUser();
    }

    public function view(User $user, Response $response): bool
    {
        return $user->isAdmin() || $response->questionnaire?->user_id === $user->id;
    }

    public function delete(User $user, Response $response): bool
    {
        return $user->isAdmin() || $response->questionnaire?->user_id === $user->id;
    }
}
