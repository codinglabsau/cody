<?php

namespace Codinglabs\Cody\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Codinglabs\Cody\Concerns\RunsCodyCommands;

class CodyCommand extends Command
{
    use RunsCodyCommands;

    protected $signature = 'cody {branch} {--prompt=} {--timeout=300}';

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

        $response = $this->executeAgentPrompt(
            prompt: <<<'PROMPT'
                summarise the git changes into a succinct commit message and description.

                Check for the existence of .github/PULL_REQUEST_TEMPLATE.md, and if it exists, fill in details as appropriate to summarise the PR.

                The response should be in JSON format:

                {
                    "commit": "the generated commit message",
                    "description": "the generated PR description"
                }
                PROMPT,
            message: 'Generating summary of changes...',
            path: $worktreeDirectory,
            timeout: (int) $this->option('timeout'),
        );

        if (! is_array($response)) {
            $this->error('Invalid response from AI agent.');

            return;
        }

        $this->executeCommands([
            'git add .',
            "git commit -am '{$response['commit']}'",
            "git push -u origin $branchName",
            "gh pr create --title '$branchName' --body '{$response['description']}'",
        ], $worktreeDirectory, [
            'GITHUB_TOKEN' => null, // nullify GITHUB_TOKEN in .env
        ]);

        Artisan::call(CodyRemoveCommand::class, [
            'branch' => $branchName,
        ], $this->output);
    }
}
