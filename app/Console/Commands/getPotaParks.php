<?php

namespace App\Console\Commands;

use App\Models\Potapark;
use App\Models\User;
use Illuminate\Console\Command;

class getPotaParks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pota:parks {prefix?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refreshes Pota Parks database with latest data from POTA. Specify prefix e.g. CA or US, otherwise all user prefixes are fetched';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle() {
        $prefixes = [];
        if ($this->argument('prefix')) {
            $prefixes[] = $this->argument('prefix');
        } else {
            $prefixes = User::getAllUserItus();
        }
        foreach($prefixes as $prefix) {
            $count = Potapark::updateParks($prefix);
            print "$prefix: $count parks\n";
        }
        return Command::SUCCESS;
    }
}
