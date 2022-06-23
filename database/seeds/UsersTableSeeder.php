<?php

use Illuminate\Database\Seeder;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$username = ['ismail','Ali','Heba','Mona','Ahmed','Khaleel'];
    	$first_name = ['ismail','Ali','Heba','Mona','Ahmed','Khaleel'];
    	$last_name = ['shurrab','AL-fara','Alaga','Shubair','AlBayouk','Khaleel'];
    	foreach (range(0, count($username)-1) as $i => $value) {
         $platform = ['ios','android'];
        shuffle($platform);
        User::create([

         'username'              => $username[$i],
		 'full_name'             => $first_name[$i].' '.$last_name[$i],
		 'email'                 => $username[$i].'@hotmail.com',
		 'password'              => bcrypt(123456),
		 'mobile'                => '059224468'.$i,
		 'api_token'             => hash('sha512', time().rand(200,9000)),
		 'fcm_token'             => hash('sha512', time().$i),
		 'from_app'              => true,
		 'lang'                  => 'ar',
		 'platform'              => $platform[0],
		 'confirmation_code'     => str_random(),
		 'confirmed'             => true,
		 'status'                => true,
		 'lat'                   => 32.64475,
		 'lng'                   => 32.64475,

        ]);

    } // end foreach
    }
}
