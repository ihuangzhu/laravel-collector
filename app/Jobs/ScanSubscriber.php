<?php

namespace App\Jobs;

use App\Models\GameMatch;
use App\Services\SubscribeService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ScanSubscriber implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The game match instance.
     *
     * @var GameMatch
     */
    public $gameMatch;

    /**
     * The number of seconds after which the job's unique lock will be released.
     *
     * @var int
     */
    public $uniqueFor = 3600;

    /**
     * Create a new job instance.
     *
     * @param GameMatch $gameMatch
     * @return void
     */
    public function __construct(GameMatch $gameMatch)
    {
        $this->gameMatch = $gameMatch->withoutRelations();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(SubscribeService $subscribeService)
    {
        $subscribeService->scan($this->gameMatch);
    }

    /**
     * The unique ID of the job.
     *
     * @return string
     */
    public function uniqueId()
    {
        return $this->gameMatch->game_id;
    }
}
