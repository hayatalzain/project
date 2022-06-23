<?php

use Illuminate\Database\Seeder;
use App\Models\Admin;

class AdminsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$username = ['admin'];
    	$first_name = ['ismail'];
    	$last_name = ['shurrab'];
    	foreach (range(0, count($username)-1) as $i => $value) {

        Admin::create([

         'username'              => $username[$i],
		 'full_name'             => $first_name[$i].' '.$last_name[$i],
		 'email'                 => $username[$i].'@hotmail.com',
		 'password'              => bcrypt(123456),
		 'api_token'             => hash('sha512', time().rand(200,9000)),
		 'fcm_token'             => hash('sha512', time().$i),
		 'lang'                  => 'ar',
		 'status'                => true,

        ]);

    } // end foreach
    }
}
