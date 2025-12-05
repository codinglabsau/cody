<?php

namespace Codinglabs\Cody\Commands;

use Illuminate\Support\Arr;
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
            headers: ['Hash', 'Branch'],
            rows: $this->worktrees()
                ->map(fn (array $line) => Arr::only($line, ['hash', 'branch']))
                ->toArray()
        );
    }
}
