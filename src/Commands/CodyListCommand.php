<?php

namespace Codinglabs\Cody\Commands;

use Illuminate\Console\Command;
use Codinglabs\Cody\Concerns\RunsCodyCommands;

class CodyListCommand extends Command
{
    use RunsCodyCommands;

    protected $signature = 'cody:list';

    protected $description = 'List AI workflows';

    public function handle(): void
    {
        $this->executeCommands([
            'git worktree list',
        ]);
    }
}
