<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class StoreUserInRedis extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'store:users-redis {count=1000000:Number of users to extract} {chunk=1000:chunk size for processing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Store user data in Redis';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $totalUsers = $this->argument('count');
        $chunkSize  = $this->argument('chunk');

        $this->info("Extracting up to $totalUsers users in chunks of $chunkSize");
        $bar = $this->output->createProgressBar($totalUsers);

        $processedCount = 0;

        // Clear existing Redis keys if they exist
        Redis::del('user:names');
        Redis::del('user:emails');

        // Use chunking to process large datasets efficiently
        User::select('id', 'name', 'email')
            ->orderBy('id')
            ->chunk($chunkSize, function ($users) use (&$processedCount, $bar, $totalUsers) {
                $pipeline = Redis::pipeline();

                foreach ($users as $user) {
                    if ($processedCount >= $totalUsers) {
                        return false;
                    }

                    if ($user->name) {
                        // Using HSET to store name as key with value 1 (similar to the array with true values)
                        $pipeline->hset('user:names', $user->name, 1);
                    }

                    if ($user->email) {
                        // Using HSET to store email as key with value 1
                        $pipeline->hset('user:emails', $user->email, 1);
                    }

                    $processedCount++;
                    $bar->advance();
                }

                // Execute all Redis commands in the pipeline at once
                $pipeline->execute();
            });

        $bar->finish();
        $this->newLine(); // Use this instead of $this->info('') for a clean newline
        $this->info("Extracted {$processedCount} users successfully!");

        // Get counts from Redis to confirm
        $namesCount = Redis::hlen('user:names');
        $emailsCount = Redis::hlen('user:emails');

        $this->info("Data saved to Redis:");
        $this->line("- Username hash table: 'user:names' with {$namesCount} entries");
        $this->line("- Email hash table: 'user:emails' with {$emailsCount} entries");

        return Command::SUCCESS;
    }
}
