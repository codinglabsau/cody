<?php

namespace Codinglabs\Cody;

use Illuminate\Support\Str;
use Symfony\Component\Yaml\Yaml;
use Illuminate\Support\Facades\File;
use Spatie\LaravelPackageTools\Package;
use Codinglabs\Cody\Commands\CodyCommand;
use Illuminate\Console\Scheduling\Schedule;
use Codinglabs\Cody\Commands\CodyListCommand;
use Codinglabs\Cody\Commands\CodyLinearCommand;
use Codinglabs\Cody\Commands\CodyRemoveCommand;
use Codinglabs\Cody\Commands\CodyMakePromptCommand;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class CodyServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('cody')
            ->hasConfigFile()
            ->hasCommands([
                CodyCommand::class,
                CodyLinearCommand::class,
                CodyListCommand::class,
                CodyMakePromptCommand::class,
                CodyRemoveCommand::class,
            ]);
    }

    public function packageBooted(): void
    {
        $this->schedulePrompts();
    }

    protected function schedulePrompts(): void
    {
        $this->callAfterResolving(Schedule::class, function (Schedule $schedule) {
            if (! app()->environment('local') || ! config('cody.scheduler.enabled')) {
                return;
            }

            $promptsPath = base_path('.ai/prompts');

            if (! File::isDirectory($promptsPath)) {
                return;
            }

            collect(File::files($promptsPath))
                ->filter(fn ($file) => Str::endsWith($file->getFilename(), '.yml'))
                ->each(function ($file) use ($schedule) {
                    $prompt = Yaml::parseFile($file->getPathname());

                    if (! array_key_exists('schedule', $prompt) || empty($prompt['schedule'])) {
                        return;
                    }

                    $branch = Str::of($file->getFilename())->beforeLast('.yml');

                    $schedule->command(CodyCommand::class, [
                        $branch,
                        '--prompt' => $prompt['prompt'],
                    ])
                        ->cron($prompt['schedule'])
                        ->withoutOverlapping();
                });
        });
    }
}
