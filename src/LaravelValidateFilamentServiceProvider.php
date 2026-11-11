<?php

namespace Milwad\LaravelValidateFilament;

use Illuminate\Contracts\Translation\Loader;
use Illuminate\Support\ServiceProvider;
use Milwad\LaravelValidate\LaravelValidateServiceProvider;
use Milwad\LaravelValidateFilament\Translation\ValidateTranslationLoader;
use ReflectionClass;

class LaravelValidateFilamentServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Serve the error messages bundled with milwad/laravel-validate without publishing them.
        $this->app->extend('translation.loader', function (Loader $loader) {
            return $loader instanceof ValidateTranslationLoader
                ? $loader
                : new ValidateTranslationLoader($loader, $this->bundledLangPath());
        });
    }

    /**
     * The lang directory shipped with milwad/laravel-validate.
     */
    protected function bundledLangPath(): string
    {
        return dirname((new ReflectionClass(LaravelValidateServiceProvider::class))->getFileName(), 2).'/lang';
    }

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
