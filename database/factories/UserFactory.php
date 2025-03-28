<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'google_id' => Crypt::encryptString(Str::random(10)),
            'google_token' => Crypt::encryptString(Str::random(10)),
            'birth_date' => $this->faker->date(),
            'cpf' => $this->faker->unique()->numerify('###########'),
        ];
    }
}
