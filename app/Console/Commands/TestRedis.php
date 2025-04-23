<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class TestRedis extends Command
{
    protected $signature = 'test:redis';
    protected $description = 'Test Redis connection';

    public function handle()
    {
        Redis::set('test_key', 'It works!');
        $this->info('Stored value in Redis');

        $value = Redis::get('test_key');
        $this->info("Retrieved from Redis: $value");
    }
}
