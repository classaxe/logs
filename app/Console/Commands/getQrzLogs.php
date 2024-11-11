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
    protected $signature = 'logs:fetch {call?} {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetches latest logs from QRZ.com. Give a callsign to limit to one user. Specify --force to force refresh.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $force = $this->option('force');
        $call = $this->argument('call');
        if ($call) {
            $activeUsers = User::where('call', $call)->get();
        } else {
            $activeUsers = User::getVisibleUsers();
        }
        foreach ($activeUsers as $user) {
            if (!$force && !$user->active) {
                print "- Skipping inactive user {$user->call}\n";
                continue;
            }
            if (!$force && $user->qrz_last_data_pull && !$user->qrz_last_data_pull->addMinutes(getEnv('LOGS_MIN_AGE'))->isPast()) {
                print "- Skipping recently refreshed user {$user->call}\n";
                continue;
            }
            if (Log::getQRZDataForUser($user)) {
                print "- Refreshed user {$user->call}\n";
            } else {
                print "- ERROR for user {$user->call} - {$user->qrz_last_result}\n";
            }
        }
        return Command::SUCCESS;
    }
}
