<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('day', function () {
    return true;
});
