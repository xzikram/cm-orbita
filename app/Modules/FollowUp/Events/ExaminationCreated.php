<?php

namespace App\Modules\FollowUp\Events;

use App\Models\Examination;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ExaminationCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Examination $examination
    ) {}
}
