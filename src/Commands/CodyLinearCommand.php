<?php

namespace Codinglabs\Cody\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class CodyLinearCommand extends Command
{
    //    use RunsCodyCommands;

    protected $signature = 'cody:linear {issueId}';

    protected $description = 'Complete a Linear issue';

    public function handle(): void
    {
        $issueId = $this->argument('issueID');

        Artisan::call(CodyCommand::class, [
            'branch' => $issueId, // todo: first fetch and validate the issue, then retrieve the Linear-generated branch name
            '--prompt' => "resolve Linear issue $issueId",
        ]);
    }
}
