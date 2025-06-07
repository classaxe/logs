<?php

namespace App\Console\Commands;

use App\Models\Park;
use App\Models\User;
use Illuminate\Console\Command;

class getParks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logs:fetchParks {extras?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Downloads Parks from POTA for user countries, augmented with optional csv list of 2-char country codes.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle() {
        $prefixes = User::getAllUserItus();
        if ($this->argument('extras')) {
            $extras = explode(',', $this->argument('extras'));
            foreach ($extras as $extra) {
                $prefixes[] = $extra;
            }
        }
        $prefixes = array_unique($prefixes);
        sort($prefixes);
        foreach($prefixes as $prefix) {
            $count = Park::updateParks($prefix);
            print "$prefix: $count parks\n";
        }
        return Command::SUCCESS;
    }
}
