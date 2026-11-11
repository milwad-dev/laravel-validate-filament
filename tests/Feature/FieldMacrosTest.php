<?php

use Filament\Forms\Components\Field;
use Filament\Forms\Components\TextInput;
use Milwad\LaravelValidate\Utils\Country;
use Milwad\LaravelValidateFilament\FieldMacros;
use Milwad\LaravelValidateFilament\Tests\Fixtures\Rules\ValidLuckyNumber;

it('registers a macro for every laravel-validate rule', function () {
    $rules = collect(glob(dirname(__DIR__, 2).'/vendor/milwad/laravel-validate/src/Rules/*.php'))
        ->map(fn (string $path) => lcfirst(basename($path, '.php')));

    expect($rules)->not->toBeEmpty();

    // A new upstream rule that needs constructor arguments fails here until it gets a hand-written macro.
    $rules->each(fn (string $macro) => expect(Field::hasMacro($macro))->toBeTrue("Missing macro [{$macro}]: add it to rulesWithArguments() in src/FieldMacros.php"));
});

it('discovers new rules that need no arguments', function () {
    $rules = (new FieldMacros)->discoverRules(
        dirname(__DIR__).'/Fixtures/Rules',
        'Milwad\\LaravelValidateFilament\\Tests\\Fixtures\\Rules',
    );

    expect($rules)->toBe([ValidLuckyNumber::class]);
});

it('skips rules that already have a macro when discovering', function () {
    $rules = (new FieldMacros)->discoverRules(
        dirname(__DIR__).'/Fixtures/Rules',
        'Milwad\\LaravelValidateFilament\\Tests\\Fixtures\\Rules',
        ['validLuckyNumber'],
    );

    expect($rules)->toBe([]);
});

it('lists every rule of the installed laravel-validate release by hand', function () {
    $macros = new FieldMacros;
    $discovered = $macros->discoverRules($macros->rulesDirectory(), 'Milwad\\LaravelValidate\\Rules', array_keys($macros->explicitMacros()));

    // Users already get these macros through discovery; this keeps SIMPLE_RULES and the README table complete.
    expect($discovered)->toBe([], 'Add these new rules to SIMPLE_RULES in src/FieldMacros.php and to the README table: '.implode(', ', $discovered));
});

it('validates values with the macro', function (string $macro, array $arguments, mixed $valid, mixed $invalid) {
    $field = TextInput::make('value')->{$macro}(...$arguments);

    expect(passesFieldRules($field, $valid))->toBeTrue()
        ->and(passesFieldRules($field, $invalid))->toBeFalse();
})->with([
    'validBase64' => ['validBase64', [], 'bWlsd2Fk', 'milwad'],
    'validBitcoinAddress' => ['validBitcoinAddress', [], '1KFHE7w8BhaENAswwryaoccDb6qcT6DbYY', 'loremipsum'],
    'validCamelCase' => ['validCamelCase', [], 'milwadDev', 'milwad_dev'],
    'validCapitalCharWithNumber' => ['validCapitalCharWithNumber', [], 'MILWAD-84', 'Milwad-84'],
    'validCarNumber' => ['validCarNumber', [], 'KA01AB1234', '854128'],
    'validCartNumberIran' => ['validCartNumberIran', [], '6280231331655562', '1234123412341234'],
    'validCountry' => ['validCountry', [], 'Iran', 'Unknown'],
    'validCountry by key' => ['validCountry', [true], 'IR', 'Iran'],
    'validCreditCard' => ['validCreditCard', [], '4111111111111111', '1234567812345678'],
    'validDiscordUsername' => ['validDiscordUsername', [], 'Milwad#2134', '#2134'],
    'validDomain' => ['validDomain', [], 'github.com', 'github/com'],
    'validDuplicate' => ['validDuplicate', [], '123456', '1123456'],
    'validDuplicateCharacter' => ['validDuplicateCharacter', [], '1,2,3,4,5,6,7,8,9', '1,2,2,3,3,3,4,5,6,7,8,9'],
    'validEvenNumber' => ['validEvenNumber', [], '1024', '1025'],
    'validHashtag' => ['validHashtag', [], '#laravel', 'laravel'],
    'validHexColor' => ['validHexColor', [], '#fcba03', 'laravel-framework'],
    'validHtmlTag' => ['validHtmlTag', [], '<h1></h1>', 'milwad-dev'],
    'validIban' => ['validIban', [], 'IR062960000000100324200001', 'IR062960000000100324200002'],
    'validIban with country' => ['validIban', [Country::ICELAND], 'IS750001121234563108962099', 'IR062960000000100324200001'],
    'validIban with countries' => ['validIban', [[Country::IRAN, Country::ICELAND]], 'IR062960000000100324200001', 'TR320010009999901234567890'],
    'validImei' => ['validImei', [], '354809104295874', '80484080484'],
    'validIpAddressIPV4' => ['validIpAddressIPV4', [], '129.144.50.56', '123456789'],
    'validIpAddressIPV6' => ['validIpAddressIPV6', [], '2001:db8:3333:4444:5555:6666:7777:8888', '12345671213145131212'],
    'validIranPostalCode' => ['validIranPostalCode', [], '3354355599', '1111111111'],
    'validJalaliDate' => ['validJalaliDate', [], '1384/8/25', '2016/15/25'],
    'validJalaliDate with character' => ['validJalaliDate', ['-'], '1384-8-25', '1384/8/25'],
    'validJwt' => ['validJwt', [], 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZCI6IjEyMzQ1Njc4OTAiLCJuYW1lIjoiSm9obiBEb2UiLCJhZG1pbiI6dHJ1ZSwiZXhwIjoxNTgyNjE2MDA1fQ.umEYVDP_kZJGCI3tkU9dmq7CIumEU8Zvftc-klp-334', '1ZSwiZXhwIjoxNTgyNjE2MDA1fQ.umEYVDP_kZJGCI3tkU9dmq7CIumEU8Zvftc-klp-334'],
    'validKebabCase' => ['validKebabCase', [], 'milwad-dev', 'milwadDev'],
    'validLandlineNumber' => ['validLandlineNumber', [], '+98212223343', 'milwad'],
    'validLandlineNumber with country' => ['validLandlineNumber', [Country::GERMANY], '+49301234567', '+98212223343'],
    'validLatitude' => ['validLatitude', [], '50.1109', '95.0000'],
    'validLongitude' => ['validLongitude', [], '-120.28544719333155', '190.5'],
    'validNameDaysWeek' => ['validNameDaysWeek', [], 'monday', 'day'],
    'validNationalCard' => ['validNationalCard', [], '0151016437', '0151016438'],
    'validNoHtml' => ['validNoHtml', [], 'Milwad Khosravi', '<h1>Milwad Khosravi</html>'],
    'validNoHtml strict' => ['validNoHtml', [true], 'Milwad Khosravi', '&lt;script&gt;alert("Milwad Khosravi")&lt;/script&gt;'],
    'validOddNumber' => ['validOddNumber', [], '1025', '1024'],
    'validPascalCase' => ['validPascalCase', [], 'MilwadDev', 'milwadDev'],
    'validPattern' => ['validPattern', [4], '4444-4444-4444', '44444-4444-44444'],
    'validPattern with separator' => ['validPattern', [4, ' '], '4444 4444 4444', '4444-4444-4444'],
    'validPhoneNumber' => ['validPhoneNumber', [], '09366000000', 'milwad'],
    'validPhoneNumber with country' => ['validPhoneNumber', [Country::NIGER], '+22799123456', '123456789'],
    'validPort' => ['validPort', [], '8080', '158754'],
    'validSlashEndOfString' => ['validSlashEndOfString', [], 'milwad/', 'milwad'],
    'validSlug' => ['validSlug', [], 'milwad-dev', 'milwad_dev'],
    'validSnakeCase' => ['validSnakeCase', [], 'milwad_dev', 'milwadDev'],
    'validStrongPassword' => ['validStrongPassword', [], 'Milwad123!', 'Milwad123'],
    'validUlid' => ['validUlid', [], '01ARZ3NDEKTSV4RRFFQ69G5FAV', '01ARZ3NDEKTSV4RRFFQ69G5FA'],
    'validUrl' => ['validUrl', [], 'https://github.com/milwad-dev', 'milwad-dev'],
    'validUsername' => ['validUsername', [], 'milwad', 'milwad!@!'],
    'validUuid' => ['validUuid', [], '123e4567-e89b-12d3-a456-426655440000', '5050'],
    'validVatId' => ['validVatId', [], 'EL123456789', 'EL123456789123678912'],
]);

it('skips the rule when the condition is false', function () {
    $field = TextInput::make('value')->validHexColor(condition: false);

    expect(passesFieldRules($field, 'not-a-color'))->toBeTrue();
});

it('evaluates closure arguments at validation time', function () {
    $country = Country::IRAN;

    $field = TextInput::make('value')->validIban(function () use (&$country) {
        return $country;
    });

    expect(passesFieldRules($field, 'IR062960000000100324200001'))->toBeTrue();

    $country = Country::ICELAND;

    expect(passesFieldRules($field, 'IR062960000000100324200001'))->toBeFalse();
});

it('keeps empty optional fields valid', function () {
    $field = TextInput::make('value')->validPhoneNumber(Country::IRAN);

    expect(passesFieldRules($field, null))->toBeTrue();
});

it('returns the field for chaining', function () {
    $field = TextInput::make('value');

    expect($field->validSlug())->toBe($field);
});
