<?php

namespace Milwad\LaravelValidateFilament\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Milwad\LaravelValidateFilament\IdeHelperGenerator;

class IdeHelperCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laravel-validate-filament:ide-helper
                            {filename=_ide_helper_laravel_validate_filament.php : The path of the helper file, relative to the project root}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate an IDE helper file with autocompletion for the laravel-validate Filament field macros';

    /**
     * Execute the console command.
     */
    public function handle(Filesystem $files, IdeHelperGenerator $generator): int
    {
        $filename = $this->argument('filename');

        $files->put($this->laravel->basePath($filename), $generator->generate());

        $this->components->info("IDE helper written to [{$filename}].");

        return self::SUCCESS;
    }
}
