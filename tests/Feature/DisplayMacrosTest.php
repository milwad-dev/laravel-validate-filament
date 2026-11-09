<?php

use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Columns\TextColumn;
use Milwad\LaravelValidateFilament\DisplayMacros;
use Milwad\LaravelValidateFilament\Mask;

describe('Mask', function () {
    it('masks an IBAN, keeping the country code, check digits and last four characters', function () {
        expect(Mask::iban('IR062960000000100324200001'))->toBe('IR06 **** **** **** **** **00 01')
            ->and(Mask::iban('IR06 2960 0000 0010 0324 2000 01'))->toBe('IR06 **** **** **** **** **00 01');
    });

    it('masks a card number, keeping the last four digits', function () {
        expect(Mask::cardNumber('4111111111111111'))->toBe('**** **** **** 1111')
            ->and(Mask::cardNumber('4111-1111-1111-1111'))->toBe('**** **** **** 1111')
            ->and(Mask::cardNumber(4111111111111111))->toBe('**** **** **** 1111');
    });

    it('masks the middle of a value', function () {
        expect(Mask::middle('0151016437'))->toBe('******6437')
            ->and(Mask::middle('0151016437', 2, 2, '#'))->toBe('01######37')
            ->and(Mask::middle('میلاد خسروی', 2, 2))->toBe('می*******وی');
    });

    it('masks a value too short to hide anything entirely', function () {
        expect(Mask::middle('1234'))->toBe('****')
            ->and(Mask::cardNumber('12'))->toBe('**');
    });

    it('leaves empty and non-string values untouched', function (mixed $value) {
        expect(Mask::iban($value))->toBe($value)
            ->and(Mask::cardNumber($value))->toBe($value)
            ->and(Mask::middle($value))->toBe($value);
    })->with([
        'null' => [null],
        'empty string' => [''],
        'array' => [['1234']],
    ]);
});

describe('display macros', function () {
    it('registers every macro on text columns and text entries', function (string $component) {
        foreach (array_keys((new DisplayMacros)->macros()) as $macro) {
            expect($component::hasMacro($macro))->toBeTrue("Missing macro [{$macro}] on [{$component}]");
        }
    })->with('components');

    it('masks the displayed state', function (string $component) {
        expect(displayComponent($component, 'iban')->maskedIban()->formatState('IR062960000000100324200001'))
            ->toBe('IR06 **** **** **** **** **00 01')
            ->and(displayComponent($component, 'card')->maskedCardNumber()->formatState('4111111111111111'))
            ->toBe('**** **** **** 1111')
            ->and(displayComponent($component, 'code')->masked(2, 2)->formatState('0151016437'))
            ->toBe('01******37');
    })->with('components');

    it('shows the full value when the condition is false', function (string $component) {
        expect(displayComponent($component, 'iban')->maskedIban(false)->formatState('IR062960000000100324200001'))
            ->toBe('IR062960000000100324200001')
            ->and(displayComponent($component, 'card')->maskedCardNumber(fn () => false)->formatState('4111111111111111'))
            ->toBe('4111111111111111');
    })->with('components');

    it('evaluates closure arguments', function () {
        expect(TextColumn::make('code')->masked(fn () => 3, fn () => 0, fn () => '•')->formatState('0151016437'))
            ->toBe('015•••••••');
    });

    it('returns the component for chaining', function () {
        $column = TextColumn::make('iban');

        expect($column->maskedIban())->toBe($column);
    });
});

dataset('components', [
    'table column' => [TextColumn::class],
    'infolist entry' => [TextEntry::class],
]);
