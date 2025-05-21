<?php

namespace App\Filament\Resources;

use App\Models\Seguimiento;
use App\Models\Paciente;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Placeholder;
use App\Filament\Resources\SeguimientoResource\Pages;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Repeater;
use Filament\Actions;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Get;

class SeguimientoResource extends Resource
{
    protected static ?string $model = Seguimiento::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    public static function calcularTFG(array $data): ?float
    {
        if (!isset($data['edad'], $data['peso'], $data['creatinina'])) {
            return null;
        }

        $edad = $data['edad'];
        $peso = $data['peso'];
        $creatinina = $data['creatinina'];
        $sexo = $data['sexo'] ?? 'masculino';

        if ($creatinina <= 0) {
            return null;
        }

        $factor = ($sexo === 'femenino') ? 0.85 : 1;
        return round(((140 - $edad) * $peso) / (72 * $creatinina) * $factor, 2);
    }

    public static function form(Form $form): Form
    {
    return $form->schema([
        Grid::make(2)->schema([

            Select::make('paciente_id')
            ->label('Paciente')
            ->relationship('paciente', 'nombres') // o el campo que quieras mostrar
            ->searchable()
            ->required()
            ->live(),
    
            DatePicker::make('fecha_seguimiento')->label('Fecha de seguimiento'),

            TextInput::make('peso')->label('Peso (kg)')->numeric()->required(),

            TextInput::make('creatinina')->label('Creatinina (mg/dL)')
                ->numeric()
                ->live(),

            Placeholder::make('TFG')->label('TFG estimada (Cockcroft-Gault)')
            ->content(function (Get $get) {
                $pacienteId = $get('paciente_id');
                $paciente = \App\Models\Paciente::find($pacienteId);
                if (!$paciente) return 'Seleccione un paciente';
            
                $fechaNacimiento = $paciente->fecha_nacimiento;
                $sexo = $paciente->sexo;
            
                $edad = now()->diffInYears($fechaNacimiento);
                $peso = (float) $get('peso');
                $creat = (float) $get('creatinina');
            
                if (!$creat || !$peso) return 'Ingrese peso y creatinina';
            
                $tfg = ((140 - $edad) * $peso) / (72 * $creat);
                if ($sexo === 'femenino') $tfg *= 0.85;
                $tfg = round($tfg, 2);
            
                return match (true) {
                    $tfg >= 90 => "<span style='color:green;font-weight:bold'>{$tfg} ml/min (Normal)</span>",
                    $tfg >= 60 => "<span style='color:blue;font-weight:bold'>{$tfg} ml/min (Ligeramente reducida)</span>",
                    $tfg >= 30 => "<span style='color:orange;font-weight:bold'>{$tfg} ml/min (Moderadamente reducida)</span>",
                    $tfg >= 15 => "<span style='color:darkorange;font-weight:bold'>{$tfg} ml/min (Gravemente reducida)</span>",
                    default => "<span style='color:red;font-weight:bold'>{$tfg} ml/min (Insuficiencia severa)</span>",
                };
            })
               ->html(),

            Select::make('vih')->label('VIH')
                ->options([
                    'Reactivo' => 'Reactivo',
                    'No reactivo' => 'No reactivo',
                ])
                ->live(),

            TextInput::make('carga_viral')->label('Carga viral (copias/ml)')
                ->visible(fn (Get $get) => $get('vih') === 'Reactivo'),

            DatePicker::make('fecha_carga_viral')->label('Fecha carga viral')
                ->visible(fn (Get $get) => $get('vih') === 'Reactivo'),

            TextInput::make('cd4')->label('CD4 (cel/mm³)')
                ->visible(fn (Get $get) => $get('vih') === 'Reactivo'),

            DatePicker::make('fecha_cd4')->label('Fecha CD4')
                ->visible(fn (Get $get) => $get('vih') === 'Reactivo'),

            TextInput::make('HB')->label('HEMOGLOBINA (g/dL)')
                ->numeric()
                ->suffix(function (Get $get) {
                    $hb = (float) $get('hb');
                    $sexo = $get('paciente.sexo') ?? 'M';
                    if ($sexo === 'M' && ($hb < 13.8 || $hb > 17.2)) return '⚠️';
                    if ($sexo === 'F' && ($hb < 12.1 || $hb > 15.1)) return '⚠️';
                    return '✔️';
                }),

            TextInput::make('RECUENTO_PLAQUETAS')->label('PLAQUETAS (/mcL)')
                ->numeric()
                ->suffix(fn (Get $get) => match (true) {
                    $get('RECUENTO_PLAQUETAS') < 150000 => 'Trombocitopenia ⚠️',
                    $get('RECUENTO_PLAQUETAS') > 450000 => 'Trombocitosis ⚠️',
                    default => '✔️',
                }),

            TextInput::make('neutrofilos')->label('NEUTRÓFILOS (/mcL)')
                ->numeric()
                ->suffix(fn (Get $get) => match (true) {
                    $get('neutrofilos') < 500 => 'Grave ⚠️',
                    $get('neutrofilos') < 1000 => 'Moderada ⚠️',
                    $get('neutrofilos') < 1500 => 'Leve ⚠️',
                    default => '✔️',
                }),

            TextInput::make('ast')->label('AST (U/L)')
                ->numeric()
                ->suffix(fn (Get $get) => $get('ast') > 1000 ? 'Grave ⚠️' : ($get('ast') > 40 ? '⚠️' : '✔️')),

            TextInput::make('ALT')->label('ALT (U/L)')
                ->numeric()
                ->suffix(function (Get $get) {
                    $alt = (float) $get('alt');
                    $sexo = $get('paciente.sexo') ?? 'M';
                    return ($sexo === 'M' && $alt > 33) || ($sexo === 'F' && $alt > 25) ? '⚠️' : '✔️';
                }),

            TextInput::make('tuberculina')->label('TUBERCULINA (mm)')
                ->numeric()
                ->suffix(fn (Get $get) => $get('tuberculina') >= 5 ? 'Riesgo ⚠️' : '✔️'),

            TextInput::make('trigliceridos')->label('TRIGLICÉRIDOS (mg/dL)')
                ->numeric()
                ->suffix(fn (Get $get) => match (true) {
                    $get('trigliceridos') >= 500 => 'Muy alto ⚠️',
                    $get('trigliceridos') >= 200 => 'Alto ⚠️',
                    $get('trigliceridos') >= 150 => 'Límite ⚠️',
                    default => '✔️',
                }),

        ]),

        Fieldset::make('En tratamiento con:')
            ->schema([
                Grid::make(2)->schema(
                    collect(range(1, 12))->flatMap(fn ($i) => [
                        Select::make("medicamento_$i")->label("Medicamento $i")
                            ->options([
                                'Betaduo 2 mg/ml (Betametasona)' => 'Betaduo 2 mg/ml (Betametasona)',
                                'Metilprednisolona 500 mg' => 'Metilprednisolona 500 mg',
                                'Adalimumab 40 mg' => 'Adalimumab 40 mg',
                                'Etanercept 50 mg' => 'Etanercept 50 mg',
                                'Infliximab 100 mg' => 'Infliximab 100 mg',
                                'Tocilizumab 162 mg' => 'Tocilizumab 162 mg',
                                'Certolizumab 200 mg' => 'Certolizumab 200 mg',
                                'Golimumab 50 mg' => 'Golimumab 50 mg',
                                'Abatacept 250 mg' => 'Abatacept 250 mg',
                                'Rituximab 500 mg' => 'Rituximab 500 mg',
                                'DICLOFENACO' => 'DICLOFENACO',
        		                'NAPROXENO' => 'NAPROXENO',
                                'IBUPROFENO' => 'IBUPROFENO',
                                'MELOXICAM' => 'MELOXICAM',
                                'CELECOXIB' => 'CELECOXIB',
                            ])->searchable(),

                        TextInput::make("concentracion_$i")->label("Concentración $i"),
                    ])->toArray()
                )
            ]),
        ]);        
}

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('paciente.nombre_completo')->label('Paciente'),
                Tables\Columns\TextColumn::make('fecha_seguimiento')->label('Fecha'),
                Tables\Columns\TextColumn::make('TFG')->label('TFG'),
                // Agrega más columnas si lo necesitas
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
            'index' => Pages\ListSeguimientos::route('/'),
            'create' => Pages\CreateSeguimiento::route('/create'),
            'edit' => Pages\EditSeguimiento::route('/{record}/edit'),
        ];
    }
}
