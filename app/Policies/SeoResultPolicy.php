<?php

namespace App\Policies;

use App\Models\SeoResult;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SeoResultPolicy
{
    use HandlesAuthorization;

    public function view(User $user, SeoResult $seoResult)
    {
        return $user->id === $seoResult->content->user_id;
    }
}
