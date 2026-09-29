<?php

namespace App\Policies;

use App\Models\Alert;
use App\Models\User;

class AlertPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdvisor() || $user->isAdmin();
    }

    public function view(User $user, Alert $alert): bool
    {
        return $user->isAdvisor() || $user->isAdmin() || $user->id === $alert->user_id;
    }

    public function acknowledge(User $user, Alert $alert): bool
    {
        return $user->isAdvisor() || $user->isAdmin();
    }

    public function resolve(User $user, Alert $alert): bool
    {
        return $user->isAdvisor() || $user->isAdmin();
    }
}