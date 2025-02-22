<?php

namespace App\Console\Commands;

use App\Models\Clublog;
use App\Models\User;
use Illuminate\Console\Command;

class getClubLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logs:fetchClublogs {call?} {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetches latest logs from Clublog.com. Give a callsign to limit to one user, --force will force refresh.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $force = $this->option('force');
        $call = $this->argument('call');

        $clublogUsers = User::getClublogUsers($call);
        if ($clublogUsers->isEmpty() && $call) {
            print "No such user as $call\n";
            return Command::FAILURE;
        }
        foreach ($clublogUsers as $user) {
            if (!$force && !$user->active) {
                print "- Skipping inactive user {$user->call}\n";
                continue;
            }
            if (!$force && $user->clublog_last_data_pull && !$user->clublog_last_data_pull->addMinutes(getEnv('LOGS_MIN_AGE'))->isPast()) {
                print "- Skipping recently refreshed user {$user->call}\n";
                continue;
            }
            if (Clublog::updateClublogs($user)) {
                print "- Refreshed user {$user->call}\n";
            } else {
                print "- ERROR for user {$user->call} - {$user->clublog_last_result}\n";
            }
        }
        Clublog::updateLogs();
        return Command::SUCCESS;
    }
}
