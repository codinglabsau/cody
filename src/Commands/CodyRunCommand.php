<?php

namespace Codinglabs\Cody\Commands;

use SplFileInfo;
use Cron\CronExpression;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Symfony\Component\Yaml\Yaml;
use Illuminate\Support\Facades\File;
use Codinglabs\Cody\Concerns\RunsCodyCommands;

class CodyRunCommand extends Command
{
    use RunsCodyCommands;

    protected $signature = 'cody:run';

    protected $description = 'Run any scheduled prompts';

    public function handle(): void
    {
        if (! app()->environment('local') || ! config('cody.scheduler.enabled')) {
            $this->error('Cody scheduler is disabled in this environment.');

            return;
        }

        $promptsPath = base_path('.ai/prompts');

        if (! File::isDirectory($promptsPath)) {
            return;
        }

        $scheduledPrompts = collect(File::files($promptsPath))
            ->filter(function (SplFileInfo $file) {
                if (! Str::endsWith($file->getFilename(), '.yml')) {
                    return false;
                }

                $prompt = Yaml::parseFile($file->getPathname());

                if (! array_key_exists('schedule', $prompt) || empty($prompt['schedule'])) {
                    return false;
                }

                return true;
            });

        /** @phpstan-ignore-next-line */
        while (true) {
            $scheduledPrompts->each(function (SplFileInfo $file) {
                $prompt = Yaml::parseFile($file->getPathname());
                $branch = Str::of($file->getFilename())->beforeLast('.yml');

                $cron = new CronExpression($prompt['schedule']);

                if ($cron->isDue(timeZone: config('cody.scheduler.timezone'))) {
                    $this->info("$branch is scheduled to run now.");

                    // todo: should move to a background process
                    $this->call(CodyCommand::class, [
                        'branch' => $branch,
                        '--prompt' => $prompt['prompt'],
                        // '--scope' => $prompt['scope'], // todo: add scope support
                    ]);
                }
            });

            // sleep until the next minute
            sleep(60 - now()->second);
        }
    }
}
