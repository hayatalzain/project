<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\Infraction;
use App\Models\Post;
use App\Models\Story;

class InfractionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {


   $faker = Faker::create();
   $classes = [Post::class,Story::class];
   foreach (range(1,5) as $index) {
        Infraction::create([
            'infractionable_id' => rand(1,5),
            'infractionable_type' => array_random($classes),
            'reason' => $faker->paragraph,
            'user_id' => rand(1,5),
        ]);
      }



    }
}
