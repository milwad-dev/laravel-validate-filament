<?php

namespace Milwad\LaravelValidateFilament\Tests\Fixtures\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidLuckyNumber implements Rule
{
    public function passes($attribute, $value): bool
    {
        return (string) $value === '7';
    }

    public function message(): string
    {
        return 'The :attribute is not lucky.';
    }
}
