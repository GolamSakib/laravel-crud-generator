<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class SearchingTimeRedis extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:search-time-redis';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check email search time using Redis';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $startTime = microtime(true);

        // Check if the email exists in Redis hash
        $email = 'tyrese78@example.com';
        $exists = Redis::hexists('user:emails', $email);

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        $this->info("Execution time: " . ($executionTime * 1000) . " milliseconds");
        $this->info("Email exists: " . ($exists ? "Yes" : "No"));

        return Command::SUCCESS;
    }
}
