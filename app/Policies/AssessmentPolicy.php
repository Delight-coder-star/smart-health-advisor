<?php

namespace App\Policies;

use App\Models\Assessment;
use App\Models\User;

class AssessmentPolicy
{
    public function view(User $user, Assessment $assessment): bool
    {
        return $user->id === $assessment->user_id
            || $user->isAdvisor()
            || $user->isAdmin();
    }

    public function update(User $user, Assessment $assessment): bool
    {
        return $user->id === $assessment->user_id || $user->isAdmin();
    }

    public function delete(User $user, Assessment $assessment): bool
    {
        return $user->isAdmin();
    }
}