<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\Ticket;

class TicketsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {


   $faker = Faker::create();
      foreach (range(1,5) as $index) {
        Ticket::create([
            'name' => $faker->name,
            'message' => $faker->email,
            'mobile' => $faker->e164PhoneNumber,
            'message' => $faker->paragraph,
            'seen' => rand(0,1),
            'user_id' => rand(1,5),
            'created_at' => $faker->dateTime($max = 'now'),
            'updated_at' => $faker->dateTime($max = 'now'),
        ]);
      }



    }
}
