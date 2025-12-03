<?php

namespace Codinglabs\Cody\Commands;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;
use Codinglabs\Cody\Concerns\RunsCodyCommands;

use function Laravel\Prompts\spin;

class CodyCommand extends Command
{
    use RunsCodyCommands;

    protected $signature = 'cody {branch} {--prompt=}';

    protected $description = 'Execute a AI workflow on this project';

    public function handle(): void
    {
        // todo: check for availability of `codex` and `gh` cli tools

        $branchName = $this->branchName();
        $worktreeDirectory = $this->worktreeDirectory();
        $prompt = $this->prompt();

        $this->executeCommands([
            "git worktree add $worktreeDirectory -b $branchName" => fn () => ! $this->workingTreeDirectoryExists(),
        ]);

        $this->executeCommands([
            'composer ai',
            "codex exec '$prompt' --full-auto",
        ], $worktreeDirectory);

        $command = "codex exec 'summarise the git changes into a succinct commit message. For the agent_message text, simply return the commit message, do not wrap it in supporting text.' --json";
        $commitMessage = 'wip';

        $this->info(sprintf('=> %s', Str::limit($commitMessage)));

        spin(
            callback: function () use ($worktreeDirectory, $command, &$commitMessage) {
                return Process::path($worktreeDirectory)
                    ->timeout(300)
                    ->run($command, function (string $type, string $output) use (&$commitMessage) {
                        $data = json_decode($output, true);

                        if (Arr::get($data, 'type') === 'item.completed' && Arr::get($data, 'item.type') === 'agent_message') {
                            $commitMessage = Arr::get($data, 'item.text');
                        }
                    })
                    ->throw();
            },
            message: 'Generating commit message...',
        );

        $this->executeCommands([
            'git add .',
            "git commit -am '$commitMessage'",
            "git push -u origin $branchName",
            "gh pr create --title '$branchName' --body 'This PR handles $commitMessage'",
        ], $worktreeDirectory, [
            'GITHUB_TOKEN' => null, // nullify GITHUB_TOKEN in .env
        ]);
    }
}
