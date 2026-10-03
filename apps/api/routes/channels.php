<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('projects.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
