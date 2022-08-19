<?php

namespace App\Jobs;

use App\Models\GameMatch;
use App\Models\GameSubscribe;
use App\Services\SubscribeService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class NotifySubscriber implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public $backoff = 3;

    /**
     * The game match instance.
     *
     * @var GameMatch
     */
    public $gameMatch;

    /**
     * The game subscribe instance.
     *
     * @var GameSubscribe
     */
    public $gameSubscribe;

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
     * @param GameSubscribe $gameSubscribe
     * @return void
     */
    public function __construct(GameMatch $gameMatch, GameSubscribe $gameSubscribe)
    {
        $this->gameMatch = $gameMatch->withoutRelations();
        $this->gameSubscribe = $gameSubscribe->withoutRelations();
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws Throwable
     */
    public function handle(SubscribeService $subscribeService)
    {
        $subscribeService->notify($this->gameMatch, $this->gameSubscribe);
    }

    /**
     * The unique ID of the job.
     *
     * @return string
     */
    public function uniqueId()
    {
        return $this->gameMatch->game_id . ':' . $this->gameSubscribe->id;
    }

    /**
     * Handle a job failure.
     *
     * @param  Throwable  $exception
     * @return void
     */
    public function failed(Throwable $exception)
    {
        Log::error("执行Notify失败：{$exception->getMessage()}", [
            $this->gameMatch->toArray(),
            $this->gameSubscribe->toArray(),
        ]);
    }
}
