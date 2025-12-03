<?php

namespace Codinglabs\Cody;

use Spatie\LaravelPackageTools\Package;
use Codinglabs\Cody\Commands\CodyCommand;
use Codinglabs\Cody\Commands\CodyListCommand;
use Codinglabs\Cody\Commands\CodyRemoveCommand;
use Codinglabs\Cody\Commands\CodyLinearCommand;
use Codinglabs\Cody\Commands\CodyMakeTaskCommand;
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
                CodyMakeTaskCommand::class,
                CodyRemoveCommand::class,
            ]);
    }
}
