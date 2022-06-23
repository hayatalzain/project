<?php

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {


        Setting::create([
            'title_ar' => 'تعارف المشهورين',
            'title_en' => 'FameDate',
            'email' => 'info@famedate.com',
            'mobile' => +966592244683,
        ]);



    }
}
