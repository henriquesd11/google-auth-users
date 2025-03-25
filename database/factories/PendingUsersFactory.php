<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PendingUsers>
 */
class PendingUsersFactory extends Factory
{
    public function definition(): array
    {
        return [
            'google_id' => Crypt::encryptString(Str::random(10)),
            'email' => $this->faker->unique()->safeEmail,
            'google_token' => Crypt::encryptString(Str::random(10)),
        ];
    }
}
