<?php

namespace Milwad\LaravelValidateFilament\Tests\Fixtures;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Livewire\Component;
use Milwad\LaravelValidate\Utils\Country;

class ProfileForm extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    /**
     * The form is untyped so it works with Filament v3 (Form) and v4+ (Schema).
     */
    public function form($form)
    {
        return $form
            ->schema([
                TextInput::make('phone')->required()->validPhoneNumber(Country::IRAN),
                TextInput::make('slug')->validSlug(),
                TextInput::make('color')->validHexColor(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }

    public function render(): string
    {
        return '<div></div>';
    }
}
