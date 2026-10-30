# Laravel Validate Filament

<img src="https://banners.beyondco.de/Laravel%20Validate%20Filament.png?theme=light&packageManager=composer+require&packageName=milwad%2Flaravel-validate-filament&pattern=bubbles&style=style_2&description=This+is+why+it%27s+awesome&md=1&showWatermark=0&fontSize=100px&images=https%3A%2F%2Flaravel.com%2Fimg%2Flogomark.min.svg" alt="laravel-validate-filament-banner">

[![PHP Version Require](https://img.shields.io/packagist/dependency-v/milwad/laravel-validate-filament/php)](https://packagist.org/packages/milwad/laravel-validate-filament)
[![Latest Stable Version](https://img.shields.io/packagist/v/milwad/laravel-validate-filament.svg?style=flat-square)](https://packagist.org/packages/milwad/laravel-validate-filament)
[![Total Downloads](https://img.shields.io/packagist/dt/milwad/laravel-validate-filament.svg?style=flat-square)](https://packagist.org/packages/milwad/laravel-validate-filament)
[![License](https://img.shields.io/packagist/l/milwad/laravel-validate-filament)](https://packagist.org/packages/milwad/laravel-validate-filament)
[![Passed Tests](https://github.com/milwad-dev/laravel-validate-filament/actions/workflows/run-tests.yml/badge.svg)](https://github.com/milwad-dev/laravel-validate-filament/actions/workflows/run-tests.yml)
[![Quality Score](https://img.shields.io/scrutinizer/g/milwad-dev/laravel-validate-filament.svg?style=flat-square)](https://scrutinizer-ci.com/g/milwad-dev/laravel-validate-filament)
[![Check Imports](https://github.com/milwad-dev/laravel-validate-filament/actions/workflows/imports.yml/badge.svg?branch=1.x)](https://github.com/milwad-dev/laravel-validate-filament/actions/workflows/imports.yml)
[![MadeWithLaravel.com shield](https://madewithlaravel.com/storage/repo-shields/4333-shield.svg)](https://madewithlaravel.com/p/laravel-validate-filament/shield-link)

Use every rule from [milwad/laravel-validate](https://github.com/milwad-dev/laravel-validate) directly on your Filament form fields:

```php
use Filament\Forms\Components\TextInput;
use Milwad\LaravelValidate\Utils\Country;

TextInput::make('phone')->validPhoneNumber(Country::IRAN),
TextInput::make('iban')->validIban([Country::IRAN, Country::GERMANY]),
TextInput::make('color')->validHexColor(),
TextInput::make('slug')->validSlug(),
```

## Requirements

| Package   | Versions           |
|-----------|--------------------|
| PHP       | 8.1+               |
| Laravel   | 10, 11, 12, 13     |
| Filament  | 3, 4, 5            |

## Installation

```bash
composer require milwad/laravel-validate-filament
```

The service provider is auto-discovered. It registers a macro for each rule on `Filament\Forms\Components\Field`, so the macros work on every field: `TextInput`, `Textarea`, `Select`, `DatePicker`, ...

### Validation messages

Error messages come from `milwad/laravel-validate`. Publish the language file for your locale, otherwise the raw translation key (for example `validate.hex-color`) is shown:

```bash
php artisan vendor:publish --tag="validate-lang-en"
```

Replace `en` with your language. See the [supported languages](https://github.com/milwad-dev/laravel-validate#support-languages).

## Usage

Every macro is named after its rule class (`ValidPhoneNumber` → `validPhoneNumber()`). The `valid` prefix keeps them from clashing with Filament's own methods such as `url()`, `uuid()` or `hexColor()`.

### Conditional rules

Like Filament's `rule()`, every macro accepts a `$condition` as its last argument:

```php
TextInput::make('national_card')
    ->validNationalCard(condition: fn (Get $get) => $get('country') === 'IR'),
```

### Dynamic arguments

Arguments can be closures. Filament evaluates them at validation time, with its usual dependency injection:

```php
Select::make('country')
    ->options(['IR' => 'Iran', 'DE' => 'Germany'])
    ->live(),

TextInput::make('phone')
    ->validPhoneNumber(fn (Get $get) => $get('country')),
```

## Available macros

| Macro | Arguments | Rule |
|-------|-----------|------|
| `validBase64()` | | `ValidBase64` |
| `validBitcoinAddress()` | | `ValidBitcoinAddress` |
| `validCamelCase()` | | `ValidCamelCase` |
| `validCapitalCharWithNumber()` | | `ValidCapitalCharWithNumber` |
| `validCarNumber()` | | `ValidCarNumber` |
| `validCartNumberIran()` | | `ValidCartNumberIran` |
| `validCountry()` | `bool $validByKey = false` | `ValidCountry` |
| `validCreditCard()` | | `ValidCreditCard` |
| `validDiscordUsername()` | | `ValidDiscordUsername` |
| `validDomain()` | | `ValidDomain` |
| `validDuplicate()` | | `ValidDuplicate` |
| `validDuplicateCharacter()` | | `ValidDuplicateCharacter` |
| `validEvenNumber()` | | `ValidEvenNumber` |
| `validHashtag()` | | `ValidHashtag` |
| `validHexColor()` | | `ValidHexColor` |
| `validHtmlTag()` | | `ValidHtmlTag` |
| `validIban()` | `array\|string $countries = []` | `ValidIban` |
| `validImei()` | | `ValidImei` |
| `validIpAddressIPV4()` | | `ValidIpAddressIPV4` |
| `validIpAddressIPV6()` | | `ValidIpAddressIPV6` |
| `validIranPostalCode()` | | `ValidIranPostalCode` |
| `validJalaliDate()` | `string $character = '/'` | `ValidJalaliDate` |
| `validJwt()` | | `ValidJwt` |
| `validKebabCase()` | | `ValidKebabCase` |
| `validLandlineNumber()` | `?string $code = null` | `ValidLandlineNumber` |
| `validLatitude()` | | `ValidLatitude` |
| `validLongitude()` | | `ValidLongitude` |
| `validNameDaysWeek()` | | `ValidNameDaysWeek` |
| `validNationalCard()` | | `ValidNationalCard` |
| `validNoHtml()` | `bool $strict = false` | `ValidNoHtml` |
| `validOddNumber()` | | `ValidOddNumber` |
| `validPascalCase()` | | `ValidPascalCase` |
| `validPattern()` | `int $length, string $separator = '-'` | `ValidPattern` |
| `validPhoneNumber()` | `?string $code = null` | `ValidPhoneNumber` |
| `validPort()` | | `ValidPort` |
| `validSlashEndOfString()` | | `ValidSlashEndOfString` |
| `validSlug()` | | `ValidSlug` |
| `validSnakeCase()` | | `ValidSnakeCase` |
| `validStrongPassword()` | | `ValidStrongPassword` |
| `validUlid()` | | `ValidUlid` |
| `validUrl()` | | `ValidUrl` |
| `validUsername()` | | `ValidUsername` |
| `validUuid()` | | `ValidUuid` |
| `validVatId()` | | `ValidVatId` |

Every macro also takes `bool|Closure $condition = true` as its last argument. Country codes are available as constants on `Milwad\LaravelValidate\Utils\Country`. For details on each rule, see the [laravel-validate docs](https://github.com/milwad-dev/laravel-validate).

## Roadmap

v1 ships field macros only. Planned for next versions:

- [ ] Ready-made field components for the most common rules, e.g. `PhoneNumberInput::make('phone')`, `IbanInput::make('iban')`, `NationalCardInput::make('national_card')`, `StrongPasswordInput::make('password')`, each with the rule applied plus sensible defaults (mask, placeholder, prefix icon, input mode).
- [ ] Translated error messages out of the box, without having to publish the `milwad/laravel-validate` language files.
- [ ] IDE autocompletion for the macros (stub file with `@method` annotations, or a `php artisan laravel-validate-filament:ide-helper` command).
- [ ] Optional table column / infolist helpers (e.g. masked IBAN / card number display).
- [ ] Keep the macro list in sync when `milwad/laravel-validate` adds new rules.

## Testing

```bash
composer test
```

## License

The MIT License (MIT). See [LICENSE](LICENSE).
