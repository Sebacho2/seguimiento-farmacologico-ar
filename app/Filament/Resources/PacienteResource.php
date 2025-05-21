<?php

namespace App\Filament\Resources;

use App\Models\Paciente;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Filament\Resources\PacienteResource\Pages;

class PacienteResource extends Resource
{
    protected static ?string $model = Paciente::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('nombres')->required(),
            Forms\Components\TextInput::make('apellidos')->required(),
            Forms\Components\DatePicker::make('fecha_nacimiento')->required(),
            Forms\Components\Select::make('sexo')->options([
                'masculino' => 'Masculino',
                'femenino' => 'Femenino',
            ])->required(),
            Forms\Components\Select::make('tipo_documento')->options([
                'CC' => 'Cédula de ciudadanía',
                'TI' => 'Tarjeta de identidad',
                'CE' => 'Cédula de extranjería',
            ])->required(),
            Forms\Components\TextInput::make('documento_identidad')->required(),
            Forms\Components\TextInput::make('telefono'),
            Forms\Components\TextInput::make('correo')->email(),
            Forms\Components\TextInput::make('direccion'),
            Forms\Components\Textarea::make('diagnostico')->label('Diagnóstico'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombres')->label('Nombre(s)'),
                Tables\Columns\TextColumn::make('apellidos')->label('Apellido(s)'),
                Tables\Columns\TextColumn::make('documento_identidad')->label('Documento'),
                Tables\Columns\TextColumn::make('sexo')->label('Sexo'),
                Tables\Columns\TextColumn::make('edad')->label('Edad')->getStateUsing(fn ($record) => $record->edad),
                Tables\Columns\TextColumn::make('diagnostico')->label('Diagnóstico')->limit(50),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPacientes::route('/'),
            'create' => Pages\CreatePaciente::route('/create'),
            'edit' => Pages\EditPaciente::route('/{record}/edit'),
        ];
    }
}


