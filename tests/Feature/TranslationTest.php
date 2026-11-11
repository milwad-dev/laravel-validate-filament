<?php

use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Milwad\LaravelValidateFilament\Translation\ValidateTranslationLoader;

/**
 * The first error message for a field with the given rules and value.
 */
function firstError(TextInput $field, mixed $value): ?string
{
    return Validator::make(['value' => $value], ['value' => $field->getValidationRules()])->errors()->first('value');
}

afterEach(function () {
    // Only remove what the override test created: older Testbench skeletons ship their own lang/en files.
    File::delete(lang_path('en/validate.php'));

    if (File::isDirectory(lang_path('en')) && File::isEmptyDirectory(lang_path('en'))) {
        File::deleteDirectory(lang_path('en'));
    }
});

it('decorates the translation loader', function () {
    expect(app('translator')->getLoader())->toBeInstanceOf(ValidateTranslationLoader::class);
});

it('shows the bundled message without publishing the language files', function () {
    expect(lang_path('en/validate.php'))->not->toBeFile()
        ->and(firstError(TextInput::make('value')->validHexColor(), 'laravel'))->toBe('The value is not valid.');
});

it('translates the message into the app locale', function () {
    app()->setLocale('fa');

    expect(firstError(TextInput::make('value')->validHexColor(), 'laravel'))->toBe('مقدار value صحیح نمی باشد.');
});

it('matches bundled locale directories regardless of case and separator', function () {
    $loader = app('translator')->getLoader();

    expect($loader->bundledLines('it'))->not->toBeEmpty()
        ->and($loader->bundledLines('pt-BR'))->toBe($loader->bundledLines('pt_BR'))
        ->and($loader->bundledLines('pt_br'))->not->toBeEmpty();
});

it('falls back to English for a locale without bundled messages', function () {
    app()->setLocale('xx');

    expect(firstError(TextInput::make('value')->validHexColor(), 'laravel'))->toBe('The value is not valid.');
});

it('lets published files override the bundled messages key by key', function () {
    File::ensureDirectoryExists(lang_path('en'));
    File::put(lang_path('en/validate.php'), "<?php\n\nreturn ['hex-color' => 'Pick a real colour for :attribute.'];\n");

    expect(firstError(TextInput::make('value')->validHexColor(), 'laravel'))->toBe('Pick a real colour for value.')
        ->and(firstError(TextInput::make('value')->validSlug(), 'not a slug'))->toBe('The value is not valid.');
});

it('leaves other translation groups alone', function () {
    expect(__('validation.required', ['attribute' => 'value']))->toBe('The value field is required.')
        ->and(app('translator')->getLoader()->load('en', 'auth'))->toBe(app('translator')->getLoader()->getLoader()->load('en', 'auth'));
});
