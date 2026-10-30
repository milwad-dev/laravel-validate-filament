<?php

use Filament\Forms\Components\Field;
use Illuminate\Support\Facades\Validator;
use Milwad\LaravelValidateFilament\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

/**
 * Run the field's Filament validation rules against the given value.
 */
function passesFieldRules(Field $field, mixed $value): bool
{
    return Validator::make(['value' => $value], ['value' => $field->getValidationRules()])->passes();
}
