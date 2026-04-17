<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\TextInput::make('name')
                    ->label('Nombre del Producto')
                    ->required()
                    ->columnSpanFull(),

                \Filament\Forms\Components\Select::make('unit_id')
                    ->label('Unidad de Medida')
                    ->relationship('unit', 'name')
                    ->required(),

                \Filament\Forms\Components\Select::make('category_id')
                    ->label('Categoría')
                    ->relationship('category', 'name')
                    ->searchable(),

                \Filament\Forms\Components\TextInput::make('price')
                    ->label('Precio de Venta')
                    ->numeric()
                    ->minValue(0)
                    ->prefix('Q')
                    ->required(),

                \Filament\Forms\Components\TextInput::make('costo')
                    ->label('Precio de Compra')
                    ->numeric()
                    ->minValue(0)
                    ->prefix('Q')
                    ->required(),

                \Filament\Forms\Components\Toggle::make('manage_stock')
                    ->label('Manejar Inventario')
                    ->default(true),
            ]);
    }
}
