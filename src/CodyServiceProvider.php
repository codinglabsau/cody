<?php

namespace Codinglabs\Cody;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Codinglabs\Cody\Commands\CodyCommand;

class CodyServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('cody')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_cody_table')
            ->hasCommand(CodyCommand::class);
    }
}
