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
        $branchName = $this->branchName() ?? select(
            label: 'Select the branch to remove',
            options: $this->worktrees()->pluck('branch')->toArray(),
        );

        $worktree = $this->worktrees()->firstWhere('branch', $branchName);

        if (! $worktree) {
            $this->error("No worktree found for branch '$branchName'.");

            return;
        }

        $this->executeCommands([
            "git worktree remove {$worktree['path']} --force",
            "git branch -d {$worktree['branch']}",
        ]);
    }
}
