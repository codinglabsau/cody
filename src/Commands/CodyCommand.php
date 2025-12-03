<?php

namespace Codinglabs\Cody\Commands;

use Illuminate\Console\Command;

class CodyCommand extends Command
{
    public $signature = 'cody';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
