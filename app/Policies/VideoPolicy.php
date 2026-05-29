<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Video;

class VideoPolicy
{
    public function create(User $user): bool
    {
        return !is_null($user);
    }

    public function update(User $user, Video $video): bool
    {
        return $user->id === $video->user_id || $user->is_admin;
    }

    public function delete(User $user, Video $video): bool
    {
        return $user->id === $video->user_id || $user->is_admin;
    }
}
