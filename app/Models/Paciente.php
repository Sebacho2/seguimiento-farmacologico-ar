<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Paciente extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombres',
        'apellidos',
        'fecha_nacimiento',
        'sexo',
        'tipo_documento',
        'documento_identidad',
        'telefono',
        'correo',
        'direccion',
        'diagnostico',
    ];

    public function getNombreCompletoAttribute()
{
    return "{$this->nombres} {$this->apellidos}";
}

    public function getEdadAttribute()
    {
	return $this->fecha_nacimiento ? Carbon::parse($this->fecha_nacimiento)->age : null;
    }
}
