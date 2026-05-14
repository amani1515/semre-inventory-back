<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InventoryResource\Pages;
use App\Models\Product;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class InventoryResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-o-archive-box';
    protected static ?string $navigationLabel = 'Inventory';
    protected static ?string $modelLabel = 'Stock';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('sku')->searchable(),
                TextColumn::make('category')->sortable(),
                TextColumn::make('selling_price')->money('ETB'),
                TextColumn::make('stock_quantity')->label('Stock')->sortable()
                    ->color(fn ($state) => $state <= 5 ? 'danger' : ($state <= 20 ? 'warning' : 'success')),
                TextColumn::make('updated_at')->label('Last Updated')->dateTime()->sortable(),
            ])
            ->actions([
                Action::make('stock_in')
                    ->label('Stock In')
                    ->icon('heroicon-o-plus-circle')
                    ->color('success')
                    ->form([
                        TextInput::make('quantity')
                            ->label('Quantity to Add')
                            ->numeric()->required()->minValue(1),
                    ])
                    ->action(function (Product $record, array $data) {
                        $record->increment('stock_quantity', $data['quantity']);
                        Notification::make()
                            ->title("Added {$data['quantity']} units to {$record->name}.")
                            ->success()->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInventory::route('/'),
        ];
    }
}
