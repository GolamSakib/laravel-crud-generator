<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class SearchingTime extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CheckSearchingTime';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Store user in local file';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $startTime = microtime(true);
        $emails    = Cache::remember('emails', 3600, function () {
            return include storage_path('app/users/emails.php');
        });

        $email         = 'tyrese78@example.com';
        $exists        = isset($emails[$email]);
        $endTime       = microtime(true);
        $executionTime = $endTime - $startTime;
        echo "Execution time: " . ($executionTime * 1000) . " milliseconds";
        echo "Email exists: " . ($exists ? "Yes" : "No");
    }

}
