<?php

use Livewire\Livewire;
use Milwad\LaravelValidateFilament\Tests\Fixtures\ProfileForm;

it('passes a filament form with valid values', function () {
    Livewire::test(ProfileForm::class)
        ->set('data.phone', '09366000000')
        ->set('data.slug', 'milwad-dev')
        ->set('data.color', '#fcba03')
        ->call('save')
        ->assertHasNoErrors();
});

it('fails a filament form with invalid values', function () {
    Livewire::test(ProfileForm::class)
        ->set('data.phone', '123')
        ->set('data.slug', 'milwad_dev')
        ->set('data.color', 'not-a-color')
        ->call('save')
        ->assertHasErrors(['data.phone', 'data.slug', 'data.color']);
});

it('ignores empty optional fields in a filament form', function () {
    Livewire::test(ProfileForm::class)
        ->set('data.phone', '09366000000')
        ->call('save')
        ->assertHasNoErrors();
});
