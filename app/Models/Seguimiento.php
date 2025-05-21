<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Date; 

class Seguimiento extends Model
{
    use HasFactory;

    protected $fillable = [
        'paciente_id',
        'fecha_seguimiento',
        'pcr',
	    'vsg',
        'creatinina',
	    'hb',
	    'recuento_plaquetas',
	    'neutrofilos',
	    'ast',
	    'alt',
	    'proteinurea',
	    'vih',
	    'tuberculina',
	    'trigliceridos',
        'es_adherente',
        'motivo_no_adherencia',
        'hay_reaccion_adversa',
        'naranjo_resultado',
        'observaciones',
        'medicamento_nombre',
        'marca_comercial',
        'lote',
        'fecha_vencimiento',
        'registro_sanitario',
        'descripcion_reaccion',
        'gravedad_reaccion',
        'acciones_tomadas',
        'TFG',
        'peso',
    ];

    protected static function booted()
{
    static::saving(function ($seguimiento) {
        $paciente = $seguimiento->paciente;

        if (!$paciente || !$seguimiento->creatinina || !$seguimiento->peso) {
            return;
        }

        $edad = now()->diffInYears($paciente->fecha_nacimiento);
        $creatinina = $seguimiento->creatinina;
        $peso = $seguimiento->peso;
        $sexo = $paciente->sexo;

        $tfg = ((140 - $edad) * $peso) / (72 * $creatinina);
        if (strtolower($sexo) === 'femenino') {
            $tfg *= 0.85;
        }

        $seguimiento->TFG = round($tfg, 2);
    });
}

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

}