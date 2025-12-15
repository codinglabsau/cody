<?php

namespace Codinglabs\Cody;

use Spatie\LaravelPackageTools\Package;
use Codinglabs\Cody\Commands\CodyCommand;
use Codinglabs\Cody\Commands\CodyRunCommand;
use Codinglabs\Cody\Commands\CodyListCommand;
use Codinglabs\Cody\Commands\CodyLinearCommand;
use Codinglabs\Cody\Commands\CodyRemoveCommand;
use Codinglabs\Cody\Commands\CodyMakePromptCommand;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Spatie\LaravelPackageTools\Commands\InstallCommand;

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
                CodyMakePromptCommand::class,
                CodyRemoveCommand::class,
                CodyRunCommand::class,
            ])
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->askToStarRepoOnGitHub('codinglabsau/cody');
            });
    }
}
