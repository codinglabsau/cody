<?php

namespace Codinglabs\Cody\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\File;
use Codinglabs\Cody\Concerns\RunsCodyCommands;

use function Laravel\Prompts\text;
use function Laravel\Prompts\select;
use function Laravel\Prompts\confirm;
use function Laravel\Prompts\textarea;
use function Laravel\Prompts\multisearch;

class CodyMakePromptCommand extends Command
{
    use RunsCodyCommands;

    protected $signature = 'cody:prompt';

    protected $description = 'Create a new AI workflow on this project';

    public function handle(): void
    {
        $title = text('What is the name of the prompt?', required: true);

        File::ensureDirectoryExists(base_path('.ai/prompts'));
        $outputFile = base_path(sprintf('.ai/prompts/%s', Str::slug($title) . '.yml'));

        File::put(
            $outputFile,
            str_replace(
                [
                    '{{ TITLE }}',
                    '{{ PROMPT }}',
                    '{{ SCOPE }}',
                ],
                [
                    $title,
                    textarea('Summarise what you would like to do.', required: true),
                    collect(
                        multisearch(
                            'Search for the directories to limit the scope of the prompt',
                            fn (string $value) => strlen($value) > 0
                                ? collect(File::allDirectories(base_path()))
                                    ->map(fn ($dir) => Str::after($dir, base_path() . '/'))
                                    ->reject(fn ($dir) => Str::startsWith($dir, ['vendor/', 'node_modules/', 'storage/', '.git/']))
                                    ->filter(fn ($dir) => Str::contains(Str::lower($dir), Str::lower($value)))
                                    ->mapWithKeys(fn ($dir) => [$dir => $dir])
                                    ->toArray()
                                : []
                        )
                    )
                        ->map(fn (string $value) => " - $value")
                        ->implode(PHP_EOL) ?: '- All project files',
                ],
                File::get(__DIR__ . '/../../stubs/prompt.stub')
            )
        );

        if (confirm('Do you want to run this prompt on a schedule?')) {
            $time = text(
                label: 'What time do you want to run this prompt?',
                placeholder: 'hh:mm (24-hour format)',
                required: true,
                validate: ['time' => Rule::date()->format('H:i')]
            );

            $frequency = select(
                label: 'How often do you want to run this prompt?',
                options: [
                    'Every hour',
                    'Every day',
                    'Every week',
                    'Every month',
                ]
            );

            // Convert provided time (hh:mm) into cron fields (m H ...)
            [$hour, $minute] = array_map(fn ($v) => (int) $v, explode(':', $time));

            $cron = match ($frequency) {
                // Run at the specified minute of every hour
                'Every hour' => "$minute * * * *",
                // Run daily at the specified hour and minute
                'Every day' => "$minute $hour * * *",
                // Run weekly (Sunday) at the specified hour and minute
                'Every week' => "$minute $hour * * 0",
                // Run monthly on the 1st at the specified hour and minute
                default => "$minute $hour 1 * *",
            };

            File::append(
                $outputFile,
                PHP_EOL . "schedule: '$cron'" . PHP_EOL
            );
        }

        $this->info('Prompt created successfully.');
    }
}
