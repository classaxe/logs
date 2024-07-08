<?php

namespace App\Console\Commands;

use App\Models\Log;
use App\Models\User;
use Illuminate\Console\Command;

class getQrzLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logs:fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetches latest logs from QRZ.com for all active users';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $activeUsers = User::getActiveUsers();
        foreach ($activeUsers as $user) {
            Log::getQRZDataForUser($user);
        }
        return Command::SUCCESS;
    }
}
