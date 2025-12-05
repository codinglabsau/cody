<?php

use Illuminate\Process\PendingProcess;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Process;

test('process is invoked', function () {
    Process::fake();

    Artisan::call('cody:list');

    Process::assertRan(function (PendingProcess $process) {
        return $process->command === 'git worktree list' &&
            $process->environment === [];
    });
});
