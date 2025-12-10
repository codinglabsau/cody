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

        $worktrees = $this->worktrees();

        // If there are no worktrees at all, treat as an error
        if ($worktrees->isEmpty()) {
            $this->error("No worktree found for branch '$branchName'.");

            return 1;
        }

        $this->executeCommands([
            sprintf('git worktree remove %s --force', $this->worktreeDirectory($branchName)),
            sprintf('git branch -d %s', $branchName),
        ]);

        return 0;
    }
}
