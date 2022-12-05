<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'surname' => 'Admin',
            'first_name' => 'Admin',
            'other_name' => 'Admin',
            'name' => 'Admin',
            'email' => 'ict.makbrc@gmail.com',
            'email_verified_at' => now(),
            'password' => '$2y$10$O/QUIxcvTYaRLrxuJ9FkMuDxUgh/9jmx2kHfjxYXLp61nS8x20qQe', // admin@2022
            // 'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',// password
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return static
     */
    public function unverified()
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
