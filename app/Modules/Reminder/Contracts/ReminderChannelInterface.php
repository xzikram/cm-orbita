<?php

namespace App\Modules\Reminder\Contracts;

use App\Modules\Reminder\DTOs\ReminderDTO;
use App\Modules\Reminder\DTOs\ReminderResult;

interface ReminderChannelInterface
{
    /**
     * Send a reminder through this channel.
     */
    public function send(ReminderDTO $reminder): ReminderResult;

    /**
     * Get the channel identifier name.
     */
    public function getChannelName(): string;

    /**
     * Check if this channel is currently available.
     */
    public function isAvailable(): bool;
}
