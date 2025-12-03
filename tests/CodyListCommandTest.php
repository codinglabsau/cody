<?php

use Illuminate\Process\PendingProcess;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Artisan;

test('process is invoked', function () {
    Process::fake();

    Artisan::call('cody:list');

    Process::assertRan(function (PendingProcess $process) {
        return $process->command === 'git worktree list' &&
            $process->timeout === 300 &&
            $process->environment === [];
    });
});
