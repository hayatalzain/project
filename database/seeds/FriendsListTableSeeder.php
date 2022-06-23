<?php

use Illuminate\Database\Seeder;
use App\Models\FriendList;

class FriendsListTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $names_ar = ['العائلة','الأصدقاء','العمل'];
        $names_en = ['Family','Friends','Business'];
    	foreach (range(0, count($names_ar)-1) as $i => $value) {

        FriendList::create([
         'name_ar'             => $names_ar[$i],
		 'name_en'             => $names_en[$i],
		 'photo'               => '',
        ]);

    } // end foreach
    }
}
