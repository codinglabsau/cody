<?php

namespace Codinglabs\Cody\Concerns;

use Closure;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Process;

trait RunsCodyCommands
{
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

    protected function branchName(): string
    {
        return sprintf('cody/%s', $this->argument('branch'));
    }

    protected function appDirectoryName(): string
    {
        return Str::afterLast(base_path(), '/');
    }

    protected function worktreeDirectory(): string
    {
        $branchName = $this->argument('branch');

        return base_path(sprintf("../%s-$branchName", $this->appDirectoryName()));
    }

    protected function prompt(): string
    {
        return $this->option('prompt');
    }

    protected function workingTreeDirectoryExists(): bool
    {
        return is_dir($this->worktreeDirectory());
    }
}
