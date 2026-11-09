<?php

namespace Milwad\LaravelValidateFilament;

use Closure;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Columns\TextColumn;

class DisplayMacros
{
    /**
     * The components that receive the display macros, when their Filament package is installed.
     *
     * @var array<int, class-string>
     */
    public const COMPONENTS = [
        TextColumn::class,
        TextEntry::class,
    ];

    /**
     * Register the masking macros on table text columns and infolist text entries.
     *
     * Both packages are optional, so a component is skipped when its class is not installed.
     */
    public function register(): void
    {
        foreach ($this->components() as $component) {
            foreach ($this->macros() as $name => $macro) {
                $component::macro($name, $macro);
            }
        }
    }

    /**
     * The installed components that receive the display macros.
     *
     * @return array<int, class-string>
     */
    public function components(): array
    {
        return array_values(array_filter(self::COMPONENTS, 'class_exists'));
    }

    /**
     * Every display macro, keyed by method name.
     *
     * Each macro replaces the component's state formatter. Masking applies while $condition is true,
     * so pass a closure to show the full value to some users only.
     *
     * @return array<string, Closure>
     */
    public function macros(): array
    {
        return [
            'masked' => function (int|Closure $start = 0, int|Closure $end = 4, string|Closure $character = '*', bool|Closure $condition = true) {
                return $this->formatStateUsing(fn (mixed $state) => $this->evaluate($condition)
                    ? Mask::middle($state, $this->evaluate($start), $this->evaluate($end), $this->evaluate($character))
                    : $state);
            },

            'maskedCardNumber' => function (bool|Closure $condition = true) {
                return $this->formatStateUsing(fn (mixed $state) => $this->evaluate($condition) ? Mask::cardNumber($state) : $state);
            },

            'maskedIban' => function (bool|Closure $condition = true) {
                return $this->formatStateUsing(fn (mixed $state) => $this->evaluate($condition) ? Mask::iban($state) : $state);
            },
        ];
    }
}
