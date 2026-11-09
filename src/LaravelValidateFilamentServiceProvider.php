<?php

namespace Milwad\LaravelValidateFilament;

use Illuminate\Support\ServiceProvider;

class LaravelValidateFilamentServiceProvider extends ServiceProvider
{
    /**
     * Boot applications.
     */
    public function boot(): void
    {
        (new FieldMacros)->register();
        (new DisplayMacros)->register();

        if ($this->app->runningInConsole()) {
            $this->commands([
                Commands\IdeHelperCommand::class,
            ]);
        }
    }
}
