<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Models\User;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class TransactionsRelationManager extends RelationManager
{
    protected static string $relationship = 'transactions';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('price')
            ->columns([
                Tables\Columns\TextColumn::make('price'),
                Tables\Columns\TextColumn::make('currency'),
                Tables\Columns\TextColumn::make('created_at')->label('Date'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\Action::make('Credit')
                    ->action(function (RelationManager $livewire, array $data): Model {
                        $user = $livewire->ownerRecord;
                        $user->balance += $data['price'];
                        $user->save();
                        return $livewire->getRelationship()->create($data);
                    })
                    ->form([
                        Forms\Components\TextInput::make('price')
                            ->label('Price')
                            ->rules([
                                'numeric', 'min:100'
                            ])
                            ->required(),
                        Forms\Components\Select::make('currency')
                            ->required()
                            ->options([
                                'usd' => 'USD'
                            ])->default('usd')
                    ]),
                Tables\Actions\Action::make('Debit')
                    ->action(function (RelationManager $livewire, array $data): Model {
                        $user = $livewire->ownerRecord;
                        $user->balance -= $data['price'];
                        $user->save();
                        return $livewire->getRelationship()->create($data);
                    })
                    ->form([
                        Forms\Components\TextInput::make('price')
                            ->label('Price')
                            ->rules([
                                'numeric', 'min:100'
                            ])
                            ->required(),
                        Forms\Components\Select::make('currency')
                            ->required()
                            ->options([
                                'USD'
                            ])->default('usd')
                    ])

            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
            ]);
    }
}
