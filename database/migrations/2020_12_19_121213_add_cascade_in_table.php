<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCascadeInTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->unsignedBigInteger('registered_by')->nullable()->change();

            $table->dropForeign('customers_registered_by_foreign');
            $table->foreign('registered_by')->references('id')->on('users')->onDelete('set null');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->unsignedBigInteger('customer_id')->nullable()->change();
            $table->unsignedBigInteger('booked_by')->nullable()->change();
            $table->unsignedBigInteger('room_number')->nullable()->change();

            $table->dropForeign('bookings_customer_id_foreign');
            $table->dropForeign('bookings_booked_by_foreign');
            $table->dropForeign('bookings_room_number_foreign');

            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
            $table->foreign('booked_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('room_number')->references('id')->on('rooms')->onDelete('set null');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->unsignedBigInteger('customer_id')->nullable()->change();
            $table->unsignedBigInteger('booking_id')->nullable()->change();

            $table->dropForeign('payments_customer_id_foreign');
            $table->dropForeign('payments_booking_id_foreign');

            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropForeign('customers_registered_by_foreign');
            
            $table->foreign('registered_by')->references('id')->on('users');
        });


        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign('bookings_customer_id_foreign');
            $table->dropForeign('bookings_booked_by_foreign');
            $table->dropForeign('bookings_room_number_foreign');

            $table->foreign('customer_id')->references('id')->on('customers');
            $table->foreign('booked_by')->references('id')->on('users');
            $table->foreign('room_number')->references('id')->on('rooms');

        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign('payments_customer_id_foreign');
            $table->dropForeign('payments_booking_id_foreign');
            
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->foreign('booking_id')->references('id')->on('bookings');
        });
    }
}
