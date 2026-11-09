<?php

use Filament\Forms\Components\Field;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Schemas\Schema;
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

/**
 * Make a table column or infolist entry that can format state outside a rendered table or infolist.
 *
 * @template T of object
 *
 * @param  class-string<T>  $component
 * @return T
 */
function displayComponent(string $component, string $name): object
{
    $instance = $component::make($name);

    if ($instance instanceof TextEntry) {
        // Entries resolve evaluation context through their parent schema (Filament v4+) or infolist (v3).
        $instance->container(class_exists(Schema::class) ? Schema::make() : Infolist::make());
    }

    return $instance;
}
