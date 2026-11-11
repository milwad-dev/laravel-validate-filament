<?php

namespace Milwad\LaravelValidateFilament\Tests\Fixtures\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidMultipleOf implements Rule
{
    public function __construct(protected int $factor) {}

    public function passes($attribute, $value): bool
    {
        return (int) $value % $this->factor === 0;
    }

    public function message(): string
    {
        return 'The :attribute is not a multiple.';
    }
}
