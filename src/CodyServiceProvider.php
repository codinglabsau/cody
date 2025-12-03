<?php

namespace Codinglabs\Cody;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class CodyServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('cody')
            ->hasConfigFile()
            ->hasViews()
            ->hasCommands([
                \Codinglabs\Cody\Commands\CodyCommand::class,
                \Codinglabs\Cody\Commands\CodyLinearCommand::class,
                \Codinglabs\Cody\Commands\CodyListCommand::class,
                \Codinglabs\Cody\Commands\CodyMakeTaskCommand::class,
                \Codinglabs\Cody\Commands\CodyRemoveCommand::class,
            ]);
    }
}
