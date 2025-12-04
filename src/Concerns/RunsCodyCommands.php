<?php

namespace Codinglabs\Cody\Concerns;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Process;

use function Laravel\Prompts\spin;

trait RunsCodyCommands
{
    protected function executeAgentPrompt(string $prompt, string $message, ?string $path = null, array $environment = []): null|string|array
    {
        $command = sprintf("codex exec '%s' --json", $prompt);
        $response = null;

        $this->info(sprintf('=> %s', Str::of($command)->trim()->limit()));

        spin(
            callback: function () use ($path, $environment, $command, &$response) {
                return Process::path($path ?? base_path())
                    ->timeout(300)
                    ->env($environment)
                    ->run($command, function (string $type, string $output) use (&$response) {
                        $data = json_decode($output, true);

                        if (Arr::get($data, 'type') === 'item.completed' && Arr::get($data, 'item.type') === 'agent_message') {
                            $response = Arr::get($data, 'item.text');
                        }
                    })
                    ->throw();
            },
            message: $message
        );

        // parse JSON responses
        if (is_string($response) && json_decode($response, true)) {
            return json_decode($response, true);
        }

        return $response;
    }

    protected function executeCommands(array $commands, ?string $path = null, array $environment = []): void
    {
        collect($commands)
            ->each(function (string|Closure $value, mixed $key) use ($path, $environment) {
                if ($value instanceof Closure) {
                    $shouldRun = $value();
                    $command = $key;
                } else {
                    $shouldRun = true;
                    $command = $value;
                }

                if ($shouldRun) {
                    $this->info("=> $command");

                    Process::path($path ?? base_path())
                        ->timeout(300)
                        ->env($environment)
                        ->run($command, fn (string $type, string $output) => $this->output->write($output))
                        ->throw();
                } else {
                    $this->warn("=> $command (skipped)");
                }
            });
    }

    protected function branchName(?string $branchName = null): string
    {
        return $branchName ?? $this->argument('branch');
    }

    protected function appDirectoryName(): string
    {
        return Str::afterLast(base_path(), '/');
    }

    protected function worktreeDirectory(?string $branchName = null): string
    {
        $branchName = Str::replace('/', '-', ($branchName ?? $this->argument('branch')));

        return base_path(sprintf("../%s-$branchName", $this->appDirectoryName()));
    }

    protected function prompt(): ?string
    {
        return $this->option('prompt');
    }

    protected function workingTreeDirectoryExists(): bool
    {
        return is_dir($this->worktreeDirectory());
    }
}
