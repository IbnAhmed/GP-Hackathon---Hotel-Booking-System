<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class Booking extends Model 
{
   public function Payment(){
       return $this->hasMany(Payment::class , 'booking_id');
   }
}
