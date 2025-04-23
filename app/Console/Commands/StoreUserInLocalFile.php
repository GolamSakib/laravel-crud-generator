<?php
namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class StoreUserInLocalFile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'store:users {count=1000000:Number of users to extract} {chunk=1000:chunk size for processing}';

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
        $totalUsers = $this->argument('count');
        $chunkSize  = $this->argument('chunk');
        
        $this->info("Extracting up to $totalUsers users in chunks of $chunkSize");
        $bar = $this->output->createProgressBar($totalUsers);
        
        $storageDir = storage_path('app/users');
        if (!file_exists($storageDir)) {
            mkdir($storageDir, 0777, true);
        }
        
        $userNameFile = $storageDir . '/users.php';
        $emailfile = $storageDir . '/emails.php';
        
        $usernames = [];
        $emails = [];
        $processedCount = 0;
        
        // Use chunking to process large datasets efficiently
        User::select('id', 'name', 'email')
            ->orderBy('id')
            ->chunk($chunkSize, function ($users) use (&$usernames, &$emails, &$processedCount, $bar, $totalUsers) {
                foreach ($users as $user) {
                    if ($processedCount >= $totalUsers) {
                        return false;
                    }
                    
                    if ($user->name) {
                        $usernames[$user->name] = true;
                    }
                    
                    if ($user->email) {
                        $emails[$user->email] = true;
                    }
                    
                    $processedCount++;
                    $bar->advance();
                }
            });
        
        file_put_contents($userNameFile, '<?php return ' . var_export($usernames, true) . ';');
        file_put_contents($emailfile, '<?php return ' . var_export($emails, true) . ';');
        
        $bar->finish();
        $this->newLine(); // Use this instead of $this->info('') for a clean newline
        $this->info("Extracted {$processedCount} users successfully!");
        $this->info("Files saved to:");
        $this->line("- Username hash table: {$userNameFile}");
        $this->line("- Email hash table: {$emailfile}");
        
        return Command::SUCCESS;
    }
}