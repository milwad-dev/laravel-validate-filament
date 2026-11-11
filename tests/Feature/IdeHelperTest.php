<?php

use Filament\Forms\Components\Field;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Columns\TextColumn;
use Milwad\LaravelValidateFilament\FieldMacros;
use Milwad\LaravelValidateFilament\IdeHelperGenerator;

afterEach(function () {
    @unlink(base_path('_ide_helper_laravel_validate_filament.php'));
    @unlink(base_path('custom_helper.php'));
});

it('lists every registered macro', function () {
    $fieldMacros = new FieldMacros;
    $macros = array_keys($fieldMacros->macros());

    expect($macros)->toHaveCount(count(glob($fieldMacros->rulesDirectory().'/*.php')));

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

it('annotates the display macros on table columns and infolist entries', function () {
    $helper = (new IdeHelperGenerator)->generate();

    expect($helper)
        ->toContain("namespace Filament\\Tables\\Columns {")
        ->toContain('class TextColumn extends \\'.get_parent_class(TextColumn::class).' {}')
        ->toContain("namespace Filament\\Infolists\\Components {")
        ->toContain('class TextEntry extends \\'.get_parent_class(TextEntry::class).' {}')
        ->toContain('@method $this maskedIban(\Closure|bool $condition = true) Mask the displayed IBAN')
        ->toContain('@method $this masked(\Closure|int $start = 0, \Closure|int $end = 4, \Closure|string $character = \'*\', \Closure|bool $condition = true)');
});
