<?php

namespace App\Policies;

use App\Models\Questionnaire;
use App\Models\User;

class QuestionnairePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isRegularUser();
    }

    public function view(User $user, Questionnaire $questionnaire): bool
    {
        return $user->isAdmin() || $questionnaire->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isRegularUser();
    }

    public function update(User $user, Questionnaire $questionnaire): bool
    {
        return $user->isAdmin() || $questionnaire->user_id === $user->id;
    }

    public function delete(User $user, Questionnaire $questionnaire): bool
    {
        return $user->isAdmin() || $questionnaire->user_id === $user->id;
    }
}
