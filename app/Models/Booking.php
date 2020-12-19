<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class Booking extends Model 
{
	// has many Relationship 
   	public function Payment(){
       return $this->hasMany(Payment::class , 'booking_id');
   	}

   	// has one Relationship 
   	public function Room(){
       return $this->hasOne(Room::class , 'id');
   	}
}
