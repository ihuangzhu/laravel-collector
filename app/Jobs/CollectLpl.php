<?php

namespace App\Jobs;

use App\Models\Game;
use App\Services\CollectLplService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CollectLpl implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Game
     */
    protected $game;

    /**
     * The number of seconds after which the job's unique lock will be released.
     *
     * @var int
     */
    public $uniqueFor = 3600;

    /**
     * Create a new job instance.
     *
     * @param Game $game
     */
    public function __construct(Game $game)
    {
        $this->game = $game->withoutRelations();
    }

    /**
     * The unique ID of the job.
     *
     * @return string
     */
    public function uniqueId()
    {
        return $this->game->id;
    }

    /**
     * Execute the job.
     *
     * @param CollectLplService $collectLolService
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle(CollectLplService $collectLolService)
    {
        $collectLolService->refresh($this->game);
    }
}
