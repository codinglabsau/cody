<?php

namespace Codinglabs\Cody\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Codinglabs\Cody\Concerns\RunsCodyCommands;

use function Laravel\Prompts\text;
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

        File::put(
            base_path(sprintf('.ai/prompts/%s', Str::slug($title) . '.md')),
            str_replace(
                [
                    '{{ TITLE }}',
                    '{{ SUMMARY }}',
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
                        ->map(fn (string $value) => "- $value")
                        ->implode(PHP_EOL) ?: '- All project files',
                ],
                File::get(__DIR__ . '/../../stubs/prompt.stub')
            )
        );
    }
}
