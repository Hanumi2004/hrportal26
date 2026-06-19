<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\EventCategory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'created_by'         => User::inRandomOrder()->first()->id,
            'event_name'         => fake()->catchPhrase(),
            'description'        => fake()->paragraph(),
            'event_date'         => fake()->dateTimeBetween('now', '+3 months')->format('Y-m-d'),
            'event_time'         => fake()->time('H:i:s'),
            'event_location'     => fake()->city(),
            'event_category_id'  => EventCategory::inRandomOrder()->first()?->id ?? 1,
            'image'              => null, // or fake()->imageUrl() for a random image URL
            'event_status'       => fake()->randomElement(['upcoming', 'ongoing', 'completed', 'cancelled']),
            'tags'               => fake()->words(3, true), // returns a string of 3 words
        ];
    }
}
