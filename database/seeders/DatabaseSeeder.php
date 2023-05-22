<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $faker = Faker::create();
        foreach (range(1, 50) as $index) {
            DB::table('event_registrations')->insert([


                'name' => $faker->firstName,
                'Church_Name' => $faker->company,
                'mobile' => $faker->phoneNumber,
                'Sub_County' => $faker->city,
            ]);
        }
    }
}
