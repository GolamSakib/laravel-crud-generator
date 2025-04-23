<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $totalUsers = 1000000;
        $chunkSize = 1000;
        for($i=1;$i<$totalUsers;$i+=$chunkSize){
         User::factory()->count($chunkSize)->create();
        }
    }
}
