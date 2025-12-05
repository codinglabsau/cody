<?php

use Illuminate\Process\PendingProcess;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Process;
use Codinglabs\Cody\Concerns\RunsCodyCommands;

uses(RunsCodyCommands::class);

test('error if branch cannot be found', function () {
    Process::fake();

    $this->artisan('cody:remove', [
        'branch' => 'test',
    ])->assertFailed();
});

test('process is invoked', function () {
    Process::fake([
        'git worktree list' => '/Users/cody/code/app-foo-bar  900ef4420 [cody/foo-bar]',
        'git worktree remove /Users/cody/code/app-foo-bar --force',
        //        'ls *' => 'Test "ls" output',
    ]);

    Artisan::call('cody:remove', [
        'branch' => 'test',
    ]);

    Process::assertRan(function (PendingProcess $process) {
        return $process->command === 'git worktree list';
    });

    Process::assertRan(function (PendingProcess $process) {
        ray($process->command);

        return $process->command === sprintf('git worktree remove %s --force', $this->worktreeDirectory('test')) &&
            $process->timeout === 300 &&
            $process->environment === [];
    });
    //
    //    Process::assertRan(function (PendingProcess $process) {
    //        return $process->command === sprintf('git branch -d %s', $this->branchName('test')) &&
    //            $process->timeout === 300 &&
    //            $process->environment === [];
    //    });
});
