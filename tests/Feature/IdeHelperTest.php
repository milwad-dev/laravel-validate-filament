<?php

use Filament\Forms\Components\Field;
use Milwad\LaravelValidateFilament\FieldMacros;
use Milwad\LaravelValidateFilament\IdeHelperGenerator;

afterEach(function () {
    @unlink(base_path('_ide_helper_laravel_validate_filament.php'));
    @unlink(base_path('custom_helper.php'));
});

it('lists every registered macro', function () {
    $macros = array_keys((new FieldMacros)->macros());

    expect($macros)->toHaveCount(44);

    foreach ($macros as $macro) {
        expect(Field::hasMacro($macro))->toBeTrue("Macro [{$macro}] is not registered");
    }
});

it('generates an annotation for every macro', function () {
    $helper = (new IdeHelperGenerator)->generate();

    foreach (array_keys((new FieldMacros)->macros()) as $macro) {
        expect($helper)->toContain("@method \$this {$macro}(");
    }
});

it('renders macro signatures with types and defaults', function () {
    $helper = (new IdeHelperGenerator)->generate();

    expect($helper)
        ->toContain('@method $this validBase64(\Closure|bool $condition = true) Validate with \Milwad\LaravelValidate\Rules\ValidBase64.')
        ->toContain('validIban(\Closure|array|string $countries = [], \Closure|bool $condition = true)')
        ->toContain("validJalaliDate(\\Closure|string \$character = '/', \\Closure|bool \$condition = true)")
        ->toContain('validPattern(\Closure|int $length, ')
        ->toContain('validPhoneNumber(\Closure|string|null $code = null, ');
});

it('produces syntactically valid PHP', function () {
    $path = tempnam(sys_get_temp_dir(), 'ide');
    file_put_contents($path, (new IdeHelperGenerator)->generate());

    exec('php -l '.escapeshellarg($path), $output, $exitCode);
    unlink($path);

    expect($exitCode)->toBe(0, implode("\n", $output));
})->skip(fn () => ! function_exists('exec'), 'exec() is disabled');

it('writes the helper file with the artisan command', function () {
    $this->artisan('laravel-validate-filament:ide-helper')->assertSuccessful();

    expect(file_get_contents(base_path('_ide_helper_laravel_validate_filament.php')))
        ->toBe((new IdeHelperGenerator)->generate());
});

it('accepts a custom filename', function () {
    $this->artisan('laravel-validate-filament:ide-helper', ['filename' => 'custom_helper.php'])->assertSuccessful();

    expect(base_path('custom_helper.php'))->toBeFile();
});
