<?php

namespace Codinglabs\Cody\Commands;

use Illuminate\Console\Command;
use Codinglabs\Cody\Concerns\RunsCodyCommands;

use function Laravel\Prompts\select;

class CodyRemoveCommand extends Command
{
    use RunsCodyCommands;

    protected $signature = 'cody:remove {branch?}';

    protected $description = 'Remove an AI workflow';

    public function handle(): void
    {
        rd($this->worktrees());

        select();

        $branchName = $this->branchName();
        $worktreeDirectory = $this->worktreeDirectory();

        $this->executeCommands([
            "git worktree remove $worktreeDirectory --force",
            "git branch -d $branchName",
        ]);
    }
}
