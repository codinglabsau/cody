<?php

namespace Codinglabs\Cody\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Codinglabs\Cody\Concerns\RunsCodyCommands;

class CodyLinearCommand extends Command
{
    use RunsCodyCommands;

    protected $signature = 'cody:linear {issueId}';

    protected $description = 'Complete a Linear issue';

    public function handle(): void
    {
        $issueId = $this->argument('issueId');

        $response = $this->executeAgentPrompt(
            prompt: <<<PROMPT
                Retrieve issue $issueId on Linear.

                If the issue does not exist, return nothing.

                The response should be in JSON format:

                {
                    "id": "issue ID",
                    "branchName": "the branchName attribute on the issue"
                }
                PROMPT,
            message: 'Retrieving Linear issue details...',
        );

        if (! is_array($response) || empty($response['branchName'])) {
            $this->error('Invalid response from AI agent.');

            return;
        }

        Artisan::call(CodyCommand::class, [
            'branch' => $response['branchName'],
            '--prompt' => "resolve Linear issue $issueId",
        ], $this->output);
    }
}
