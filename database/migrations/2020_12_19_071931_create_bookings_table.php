<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            // key
            $table->bigIncrements('id');

            $table->unsignedBigInteger('customer_id');
            $table->foreign('customer_id')->references('id')->on('customers');

            $table->unsignedBigInteger('booked_by'); // who booked the room for customer
            $table->foreign('booked_by')->references('id')->on('users');

            $table->unsignedBigInteger('room_number');
            $table->foreign('room_number')->references('id')->on('rooms');

            // General Information
            $table->string('book_type');
            $table->integer('total_person')->default(1);

            // Time
            $table->dateTime('arrival');
            $table->dateTime('checkout');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bookings');
    }
}
