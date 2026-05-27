<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Services\DetectionEngine;

class AnalyzeSecurityEvent implements ShouldQueue
{
    use Dispatchable,Queueable;



    protected array $data;
    protected int $eventId;

    /**
     * Create a new job instance.
     */
    public function __construct(array $data, int $eventId)
    {
        $this->data = $data;
        $this->eventId = $eventId;

    }

    /**
     * Execute the job.
     */
    public function handle(DetectionEngine $engine): void
    {
        $engine->evaluate($this->data, $this->eventId);
    }
}
