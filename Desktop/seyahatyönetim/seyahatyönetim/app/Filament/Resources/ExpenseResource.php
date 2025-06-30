<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExpenseResource\Pages;
use App\Filament\Widgets\BlogPostsChart;
use App\Filament\Widgets\EmployeeExpensesChart;
use App\Filament\Widgets\ExpenseChart;
use App\Filament\Widgets\NewExpenseChart;
use App\Models\Arac;
use App\Models\Expense;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;

class ExpenseResource extends Resource
{
    protected static ?string $model = Expense::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('expense_type')
                    ->options([
                        'hotel' => 'Konaklama',
                        'food' => 'Yemek',
                        'transport' => 'Ulaşım',
                    ])
                    ->required()
                    ->label('Gider Türü')
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, callable $set) {
                        $fields = [
                            'hotel' => ['accommodation_place', 'accommodation_cost'],
                            'food' => ['restaurant_name', 'meal_cost'],
                            'transport' => ['transportation_vehicle', 'kilometers'],
                        ];

                        foreach ($fields as $key => $values) {
                            foreach ($values as $field) {
                                $set($field, $state === $key ? null : '');
                            }
                        }

                        $set('amount', 0);
                    }),

                TextInput::make('accommodation_place')
                    ->label('Konaklanan Yer')
                    ->required(fn ($get) => $get('expense_type') === 'hotel')
                    ->hidden(fn ($get) => $get('expense_type') !== 'hotel')
                    ->live(onBlur: true),

                TextInput::make('accommodation_cost')
                    ->label('Konaklama Ücreti')
                    ->numeric()
                    ->live(onBlur: true)
                    ->minValue(0)
                    ->maxValue(99999999.99)
                    ->required(fn ($get) => $get('expense_type') === 'hotel')
                    ->hidden(fn ($get) => $get('expense_type') !== 'hotel')
                    ->afterStateUpdated(function ($state, callable $set, $get) {
                        if ($get('expense_type') === 'hotel') {
                            $set('amount', $state ?? 0);
                        }
                    }),

                TextInput::make('restaurant_name')
                    ->label('Restoran Adı')
                    ->required(fn ($get) => $get('expense_type') === 'food')
                    ->hidden(fn ($get) => $get('expense_type') !== 'food')
                    ->live(onBlur: true),

                TextInput::make('meal_cost')
                    ->label('Yemek Ücreti')
                    ->numeric()
                    ->live(onBlur: true)
                    ->minValue(0)
                    ->maxValue(99999999.99)
                    ->required(fn ($get) => $get('expense_type') === 'food')
                    ->hidden(fn ($get) => $get('expense_type') !== 'food')
                    ->afterStateUpdated(function ($state, callable $set, $get) {
                        if ($get('expense_type') === 'food') {
                            $set('amount', $state ?? 0);
                        }
                    }),

                Select::make('transportation_vehicle')
                    ->options(Arac::pluck('ad', 'ad')->toArray())
                    ->label('Ulaşım Aracı')
                    ->required(fn ($get) => $get('expense_type') === 'transport')
                    ->hidden(fn ($get) => $get('expense_type') !== 'transport')
                    ->live(onBlur: true)
                    ->afterStateUpdated(function ($state, callable $set, $get) {
                        if ($get('expense_type') === 'transport' && $state && $get('kilometers')) {
                            $expense = new Expense();
                            $amount = $expense->calculateTransportCost($get('kilometers'), $state);
                            $set('amount', $amount);
                        } else {
                            $set('amount', 0);
                        }
                    }),

                TextInput::make('kilometers')
                    ->label('Kilometre')
                    ->numeric()
                    ->live(onBlur: true)
                    ->minValue(0)
                    ->maxValue(1000000)
                    ->required(fn ($get) => $get('expense_type') === 'transport')
                    ->hidden(fn ($get) => $get('expense_type') !== 'transport')
                    ->afterStateUpdated(function ($state, callable $set, $get) {
                        if ($get('expense_type') === 'transport' && $state && $get('transportation_vehicle')) {
                            $expense = new Expense();
                            $amount = $expense->calculateTransportCost($state, $get('transportation_vehicle'));
                            $set('amount', $amount);
                        } else {
                            $set('amount', 0);
                        }
                    }),

                TextInput::make('amount')
                    ->label('Tutar (TL)')
                    ->numeric()
                    ->disabled()
                    ->default(0)
                    ->required(),

                DatePicker::make('expense_date')
                    ->label('Gider Tarihi')
                    ->required()
                    ->default(today())
                    ->maxDate(today())
                    ->live(onBlur: true),

                FileUpload::make('invoice_photo')
                    ->label('Fatura')
                    ->acceptedFileTypes(['application/pdf', 'image/*'])
                    ->directory('receipts')
                    ->disk('public')
                    ->maxSize(5120),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable()->label('ID'),
                TextColumn::make('user.name')->sortable()->label('İsim'),
                TextColumn::make('expense_type')->sortable()->label('Gider Türü'),
                TextColumn::make('amount')->sortable()->label('Tutar (TL)')->money('TRY'),
                TextColumn::make('expense_date')->label('Gider Tarihi')->date('d M Y'),
                TextColumn::make('amount_converted_try')->label('TRY Karşılığı')->money('TRY'),
                TextColumn::make('amount_converted_usd')->label('USD Karşılığı')->money('USD'),
                TextColumn::make('amount_converted_eur')->label('EUR Karşılığı')->money('EUR'),
                ImageColumn::make('invoice_photo')->label('Fatura')->disk('public'),
            ])
            ->filters([
                Filter::make('name')
                    ->form([TextInput::make('name')->label('İsim Ara')])
                    ->query(fn ($query, $data) => $query
                        ->when($data['name'] ?? null, fn ($query, $value) =>
                        $query->where('name', 'like', "%{$value}%"))),

                Filter::make('amount')
                    ->form([
                        TextInput::make('min_amount')->label('Min Tutar')->numeric(),
                        TextInput::make('max_amount')->label('Max Tutar')->numeric(),
                    ])
                    ->query(fn ($query, $data) => $query
                        ->when($data['min_amount'] ?? null, fn ($query, $min) => $query->where('amount', '>=', $min))
                        ->when($data['max_amount'] ?? null, fn ($query, $max) => $query->where('amount', '<=', $max))),
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExpenses::route('/expenses'),
            'create' => Pages\CreateExpense::route('/expenses/create'),
            'edit' => Pages\EditExpense::route('/expenses/{record}/edit'),
        ];
    }

    public static function getModelLabel(): string
    {
        return 'Giderler';
    }

    public static function getWidgets(): array
    {
        return [
            BlogPostsChart::class,
            NewExpenseChart::class,
            EmployeeExpensesChart::class,
            ExpenseChart::class,
        ];
    }
}
