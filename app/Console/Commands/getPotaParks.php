<?php

namespace App\Console\Commands;

use App\Models\Park;
use App\Models\User;
use Illuminate\Console\Command;

class getPotaParks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logs:fetchPota {prefix?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Downloads Parks from POTA for user countries, or provide csv list of 2-char country codes.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle() {
        $prefixes = [];
        if ($this->argument('prefix')) {
            $prefixes = explode(',', $this->argument('prefix'));
        } else {
            $prefixes = User::getAllUserItus();
        }
        foreach($prefixes as $prefix) {
            $count = Park::updateParks($prefix);
            print "$prefix: $count parks\n";
        }
        return Command::SUCCESS;
    }
}
