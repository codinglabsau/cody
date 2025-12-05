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

    public function handle(): int
    {
        $branchName = $this->branchName() ?? select(
            label: 'Select the branch to remove',
            options: $this->worktrees()->pluck('branch')->toArray(),
        );

        ray($branchName);

        $worktree = $this->worktrees()->firstWhere('branch', $branchName);

        ray($worktree);

        if (! $worktree) {
            $this->error("No worktree found for branch '$branchName'.");

            return 1;
        }

        $this->executeCommands([
            "git worktree remove {$worktree['path']} --force",
            "git branch -d {$worktree['branch']}",
        ]);

        return 0;
    }
}
