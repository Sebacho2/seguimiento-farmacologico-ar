<?php

namespace App\Filament\Resources\SeguimientoResource\Pages;

use App\Filament\Resources\SeguimientoResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\CreateAction;

class ListSeguimientos extends ListRecords
{
    protected static string $resource = SeguimientoResource::class;

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('paciente.documento_identidad')
                ->label('Documento')
                ->searchable()
                ->sortable(),

            TextColumn::make('paciente.nombre_completo')
                ->label('Nombre completo')
                ->searchable(),

            TextColumn::make('paciente.diagnostico')
                ->label('Diagnóstico')
                ->limit(30)
                ->tooltip(fn ($state) => $state)
                ->searchable(),

            TextColumn::make('paciente.fecha_nacimiento')
                ->label('Edad')
                ->formatStateUsing(fn ($state) => \Carbon\Carbon::parse($state)->age . ' años'),

            TextColumn::make('fecha_seguimiento')
                ->label('Fecha')
                ->date('d/m/Y')
                ->sortable()
                ->url(fn ($record) => SeguimientoResource::getUrl('view', ['record' => $record]))
                ->openUrlInNewTab(),

            TextColumn::make('creatinina')
                ->label('Creatinina')
                ->sortable(),

            TextColumn::make('TFG')
                ->label('TFG (ml/min)')
                ->formatStateUsing(fn ($state) =>
                    is_numeric($state) ? match (true) {
                        $state < 15 => "❗️ {$state}",
                        $state < 30 => "⚠️ {$state}",
                        $state < 60 => "🔸 {$state}",
                        $state < 90 => "{$state}",
                        default => $state,
                    } : '-'
                )
                ->color(fn ($state) =>
                    is_numeric($state) ? match (true) {
                        $state < 15 => 'danger',
                        $state < 30 => 'danger',
                        $state < 60 => 'warning',
                        $state < 90 => 'info',
                        default => 'success',
                    } : 'gray'
                )
                ->sortable(),

            TextColumn::make('HB')
                ->label('HB')
                ->formatStateUsing(fn ($state) =>
                    is_numeric($state) && $state < 10 ? "⚠️ {$state}" : $state
                )
                ->color(fn ($state) =>
                    is_numeric($state) && $state < 10 ? 'danger' : 'success'
                )
                ->sortable(),

            TextColumn::make('RECUENTO_PLAQUETAS')
                ->label('PLAQUETAS')
                ->formatStateUsing(fn ($state) =>
                    is_numeric($state) && $state < 100000 ? "⚠️ {$state}" : $state
                )
                ->color(fn ($state) =>
                    is_numeric($state) && $state < 100000 ? 'danger' : 'success'
                )
                ->sortable(),

            TextColumn::make('NEUTROFILOS')
                ->label('NEUTRÓFILOS')
                ->formatStateUsing(fn ($state) =>
                    is_numeric($state) ? match (true) {
                        $state < 500 => "❗️ {$state}",
                        $state < 1000 => "⚠️ {$state}",
                        $state < 1500 => "🔸 {$state}",
                        default => $state,
                    } : '-'
                )
                ->color(fn ($state) =>
                    is_numeric($state) ? match (true) {
                        $state < 500 => 'danger',
                        $state < 1000 => 'warning',
                        $state < 1500 => 'warning',
                        default => 'success',
                    } : 'gray'
                )
                ->sortable(),

            TextColumn::make('AST')
                ->label('AST')
                ->formatStateUsing(fn ($state) =>
                    is_numeric($state) && $state > 40 ? "⚠️ {$state}" : $state
                )
                ->color(fn ($state) =>
                    is_numeric($state) && $state > 40 ? 'danger' : 'success'
                )
                ->sortable(),

            TextColumn::make('ALT')
                ->label('ALT')
                ->formatStateUsing(fn ($record) => 
                    is_numeric($record->ALT) && (
                        ($record->paciente->sexo === 'Masculino' && $record->ALT > 33) ||
                        ($record->paciente->sexo === 'Femenino' && $record->ALT > 25)
                    ) ? "⚠️ {$record->ALT}" : $record->ALT
                )
                ->color(fn ($record) => 
                    is_numeric($record->ALT) && (
                        ($record->paciente->sexo === 'Masculino' && $record->ALT > 33) ||
                        ($record->paciente->sexo === 'Femenino' && $record->ALT > 25)
                    ) ? 'danger' : 'success'
                )
                ->sortable(),

            TextColumn::make('VSG')
                ->label('VSG')
                ->sortable(),

            TextColumn::make('PCR')
                ->label('PCR')
                ->sortable(),

            TextColumn::make('TUBERCULINA')
                ->label('TUBERCULINA')
                ->formatStateUsing(fn ($state) =>
                    is_numeric($state) && $state >= 5 ? "⚠️ {$state}" : $state
                )
                ->color(fn ($state) =>
                    is_numeric($state) && $state >= 5 ? 'danger' : 'success'
                )
                ->sortable(),

            TextColumn::make('TRIGLICERIDOS')
                ->label('TRIGLICÉRIDOS')
                ->formatStateUsing(fn ($state) =>
                    is_numeric($state) ? match (true) {
                        $state >= 500 => "❗️ {$state}",
                        $state >= 200 => "⚠️ {$state}",
                        $state >= 150 => "🔸 {$state}",
                        default => $state,
                    } : '-'
                )
                ->color(fn ($state) =>
                    is_numeric($state) ? match (true) {
                        $state >= 500 => 'danger',
                        $state >= 200 => 'warning',
                        $state >= 150 => 'warning',
                        default => 'success',
                    } : 'gray'
                )
                ->sortable(),

            TextColumn::make('adherencia')
                ->label('Adherencia')
                ->badge()
                ->color(fn ($state) => match ($state) {
                    'Adherente' => 'success',
                    'Inadherente' => 'danger',
                    default => 'gray',
                }),

            TextColumn::make('tiene_ram')
                ->label('RAM')
                ->boolean(),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}