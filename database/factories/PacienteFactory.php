<?php

namespace Database\Factories;

use App\Models\Paciente;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Paciente>
 */
class PacienteFactory extends Factory
{
    protected $model = Paciente::class;

    public function definition(): array
    {
        return [
            'nombres' => $this->faker->firstName,
            'apellidos' => $this->faker->lastName,
            'fecha_nacimiento' => $this->faker->date('Y-m-d', '-18 years'),
            'sexo' => $this->faker->randomElement(['masculino', 'femenino', 'otro']),
            'tipo_documento' => $this->faker->randomElement(['CC', 'TI', 'CE', 'Pasaporte']),
            'documento_identidad' => $this->faker->unique()->numerify('##########'),
            'telefono' => $this->faker->phoneNumber,
            'correo' => $this->faker->unique()->safeEmail,
            'direccion' => $this->faker->address,
        ];
    }
}