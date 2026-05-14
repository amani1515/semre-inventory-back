<?php

namespace App\Filament\Resources;

use App\Exports\ProductsExport;
use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')->required()->maxLength(255),
            TextInput::make('category')->required()->maxLength(255),
            TextInput::make('sku')->required()->unique(ignoreRecord: true)->maxLength(255),
            TextInput::make('cost_price')->required()->numeric()->prefix('ETB')->minValue(0),
            TextInput::make('selling_price')->required()->numeric()->prefix('ETB')->minValue(0),
            TextInput::make('stock_quantity')->numeric()->default(0)->minValue(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('category')->searchable()->sortable(),
                TextColumn::make('sku')->searchable(),
                TextColumn::make('cost_price')->money('ETB')->sortable(),
                TextColumn::make('selling_price')->money('ETB')->sortable(),
                TextColumn::make('stock_quantity')->sortable()
                    ->color(fn ($state) => $state <= 5 ? 'danger' : 'success'),
                TextColumn::make('updated_at')->dateTime()->sortable(),
            ])
            ->filters([
                Filter::make('date_range')
                    ->form([
                        DatePicker::make('date_from')->label('From'),
                        DatePicker::make('date_to')->label('To'),
                    ])
                    ->query(fn (Builder $query, array $data) => $query
                        ->when($data['date_from'], fn ($q) => $q->whereDate('created_at', '>=', $data['date_from']))
                        ->when($data['date_to'],   fn ($q) => $q->whereDate('created_at', '<=', $data['date_to']))
                    ),
            ])
            ->headerActions([
                Action::make('export')
                    ->label('Export to Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->form([
                        DatePicker::make('date_from')->label('From'),
                        DatePicker::make('date_to')->label('To'),
                    ])
                    ->action(fn (array $data) => Excel::download(
                        new ProductsExport($data['date_from'], $data['date_to']),
                        'products-' . now()->format('Y-m-d') . '.xlsx'
                    )),
            ])
            ->actions([EditAction::make(), DeleteAction::make()])
            ->bulkActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit'   => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
