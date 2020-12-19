<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	for ($i=1; $i <= 10; $i++) { 
    		DB::table('rooms')->insert([
	            'room_number' => 'Room-'.$i,
	            'price' => ($i%2==0)?1000:500,
	            'is_locked' => 0,
	            'max_person' => ($i%2==0)?8:4,
	            'room_type' => ($i%2==0)?'A/C':'Non A/C',
	        ]);
    	}
        
    }
}
