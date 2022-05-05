<?php

namespace App\Console\Commands;

use App\Services\CollectService;
use Illuminate\Console\Command;

class Collector extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'collect:execute {--game=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Execute collect program';

    /**
     * Execute the console command.
     *
     * @param CollectService $collectService
     */
    public function handle(CollectService $collectService)
    {
        $collectService->refresh($this->option('game'));
    }
}
