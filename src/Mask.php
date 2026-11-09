<?php

namespace Milwad\LaravelValidateFilament;

class Mask
{
    /**
     * Mask an IBAN, keeping the country code, check digits and last four characters, in groups of four.
     *
     * "IR062960000000100324200001" becomes "IR06 **** **** **** **** **00 01".
     */
    public static function iban(mixed $value, string $character = '*'): mixed
    {
        if (! static::isMaskable($value)) {
            return $value;
        }

        return static::group(static::middle(static::compact($value), 4, 4, $character));
    }

    /**
     * Mask a card number, keeping only the last four digits, in groups of four.
     *
     * "4111111111111111" becomes "**** **** **** 1111".
     */
    public static function cardNumber(mixed $value, string $character = '*'): mixed
    {
        if (! static::isMaskable($value)) {
            return $value;
        }

        return static::group(static::middle(static::compact($value), 0, 4, $character));
    }

    /**
     * Replace everything except the first $start and last $end characters with $character.
     *
     * A value too short to hide anything is masked entirely, so masking never reveals the whole value.
     */
    public static function middle(mixed $value, int $start = 0, int $end = 4, string $character = '*'): mixed
    {
        if (! static::isMaskable($value)) {
            return $value;
        }

        $value = (string) $value;
        $length = mb_strlen($value);
        $start = max(0, $start);
        $end = max(0, $end);

        if ($length <= $start + $end) {
            return str_repeat($character, $length);
        }

        return mb_substr($value, 0, $start)
            .str_repeat($character, $length - $start - $end)
            .mb_substr($value, $length - $end);
    }

    /**
     * Whether the value is a non-empty string or number.
     */
    protected static function isMaskable(mixed $value): bool
    {
        return (is_string($value) || is_int($value)) && (string) $value !== '';
    }

    /**
     * Strip spaces and dashes, e.g. from "4111 1111-1111 1111".
     */
    protected static function compact(mixed $value): string
    {
        return preg_replace('/[\s-]+/u', '', (string) $value);
    }

    /**
     * Split a value into space-separated groups of four characters.
     */
    protected static function group(string $value): string
    {
        return implode(' ', mb_str_split($value, 4));
    }
}
