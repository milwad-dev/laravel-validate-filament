<?php

namespace Milwad\LaravelValidateFilament;

use Closure;
use Filament\Forms\Components\Field;
use Milwad\LaravelValidate\Rules;

class FieldMacros
{
    /**
     * Rules without constructor arguments, each registered as a macro named after the rule class.
     *
     * @var array<int, class-string>
     */
    public const SIMPLE_RULES = [
        Rules\ValidBase64::class,
        Rules\ValidBitcoinAddress::class,
        Rules\ValidCamelCase::class,
        Rules\ValidCapitalCharWithNumber::class,
        Rules\ValidCarNumber::class,
        Rules\ValidCartNumberIran::class,
        Rules\ValidCreditCard::class,
        Rules\ValidDiscordUsername::class,
        Rules\ValidDomain::class,
        Rules\ValidDuplicate::class,
        Rules\ValidDuplicateCharacter::class,
        Rules\ValidEvenNumber::class,
        Rules\ValidHashtag::class,
        Rules\ValidHexColor::class,
        Rules\ValidHtmlTag::class,
        Rules\ValidImei::class,
        Rules\ValidIpAddressIPV4::class,
        Rules\ValidIpAddressIPV6::class,
        Rules\ValidIranPostalCode::class,
        Rules\ValidJwt::class,
        Rules\ValidKebabCase::class,
        Rules\ValidLatitude::class,
        Rules\ValidLongitude::class,
        Rules\ValidNameDaysWeek::class,
        Rules\ValidNationalCard::class,
        Rules\ValidOddNumber::class,
        Rules\ValidPascalCase::class,
        Rules\ValidPort::class,
        Rules\ValidSlashEndOfString::class,
        Rules\ValidSlug::class,
        Rules\ValidSnakeCase::class,
        Rules\ValidStrongPassword::class,
        Rules\ValidUlid::class,
        Rules\ValidUrl::class,
        Rules\ValidUsername::class,
        Rules\ValidUuid::class,
        Rules\ValidVatId::class,
    ];

    /**
     * Register every laravel-validate rule as a macro on Filament fields.
     *
     * Macros are prefixed with "valid" (matching the rule class names) so they
     * never clash with Filament's own methods such as url(), uuid() or hexColor().
     */
    public function register(): void
    {
        foreach ($this->macros() as $name => $macro) {
            Field::macro($name, $macro);
        }
    }

    /**
     * Every macro this package provides, keyed by method name.
     *
     * @return array<string, Closure>
     */
    public function macros(): array
    {
        $macros = [];

        foreach (self::SIMPLE_RULES as $rule) {
            $macros[lcfirst(class_basename($rule))] = function (bool|Closure $condition = true) use ($rule) {
                return $this->rule(new $rule, $condition);
            };
        }

        return array_merge($macros, $this->rulesWithArguments());
    }

    /**
     * Macros for rules that accept constructor arguments.
     *
     * Each argument may also be a closure, evaluated by Filament at validation time.
     *
     * @return array<string, Closure>
     */
    protected function rulesWithArguments(): array
    {
        return [
            'validCountry' => function (bool|Closure $validByKey = false, bool|Closure $condition = true) {
                return $this->rule(fn () => new Rules\ValidCountry($this->evaluate($validByKey)), $condition);
            },

            'validIban' => function (array|string|Closure $countries = [], bool|Closure $condition = true) {
                return $this->rule(fn () => new Rules\ValidIban((array) $this->evaluate($countries)), $condition);
            },

            'validJalaliDate' => function (string|Closure $character = '/', bool|Closure $condition = true) {
                return $this->rule(fn () => new Rules\ValidJalaliDate($this->evaluate($character)), $condition);
            },

            'validLandlineNumber' => function (string|Closure|null $code = null, bool|Closure $condition = true) {
                return $this->rule(fn () => new Rules\ValidLandlineNumber($this->evaluate($code)), $condition);
            },

            'validNoHtml' => function (bool|Closure $strict = false, bool|Closure $condition = true) {
                return $this->rule(fn () => new Rules\ValidNoHtml($this->evaluate($strict)), $condition);
            },

            'validPattern' => function (int|Closure $length, string|Closure $separator = '-', bool|Closure $condition = true) {
                return $this->rule(fn () => new Rules\ValidPattern($this->evaluate($length), $this->evaluate($separator)), $condition);
            },

            'validPhoneNumber' => function (string|Closure|null $code = null, bool|Closure $condition = true) {
                return $this->rule(fn () => new Rules\ValidPhoneNumber($this->evaluate($code)), $condition);
            },
        ];
    }
}
