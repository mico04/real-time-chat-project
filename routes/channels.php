<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('chat.message', function ($user) {
    return $user->getAuthIdentifier() !== null;
});

// Broadcast::channel('chat.message', function () {
//     return true; // Always true since public channels do not restrict access
// });
