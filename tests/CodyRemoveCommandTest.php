<?php

use Illuminate\Process\PendingProcess;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Artisan;
use Codinglabs\Cody\Concerns\RunsCodyCommands;

uses(RunsCodyCommands::class);

test('process is invoked', function () {
    Process::fake();

    Artisan::call('cody:remove', [
        'branch' => 'test',
    ]);

    Process::assertRan(function (PendingProcess $process) {
        return $process->command === sprintf('git worktree remove %s --force', $this->worktreeDirectory('test')) &&
            $process->timeout === 300 &&
            $process->environment === [];
    });

    Process::assertRan(function (PendingProcess $process) {
        return $process->command === sprintf('git branch -d %s', $this->branchName('test')) &&
            $process->timeout === 300 &&
            $process->environment === [];
    });
});
