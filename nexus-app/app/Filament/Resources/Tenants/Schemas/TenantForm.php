<?php

namespace App\Filament\Resources\Tenants\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class TenantForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                TextInput::make('domain'),
                TextInput::make('nit'),
                TextInput::make('currency')
                    ->required()
                    ->default('GTQ'),
                TextInput::make('settings'),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
