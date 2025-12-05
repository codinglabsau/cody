<?php

namespace Codinglabs\Cody\Commands;

use Illuminate\Console\Command;
use Codinglabs\Cody\Concerns\RunsCodyCommands;
use function Laravel\Prompts\table;

class CodyListCommand extends Command
{
    use RunsCodyCommands;

    protected $signature = 'cody:list';

    protected $description = 'List AI workflows';

    public function handle(): void
    {
        table(
            headers: ['Branch', 'Hash'],
            rows: $this->worktrees()
                ->map(fn (array $line) => [
                    $line['branch'],
                    $line['hash'],
                ])
                ->toArray()
        );
    }
}
