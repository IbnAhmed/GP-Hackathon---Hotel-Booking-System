<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Models\Booking;
use App\Models\Room;

class BookingController extends Controller
{
   public function __construct(){}

   /**
    * Get All Bookings
    *
    *
    * @return \Illuminate\Http\Response
    */
   public function index(Request $request){
      $response_type = $request->format('json');
      
      $response_status = 200;

      $bookings = Booking::paginate(15);


      // item per page
      if($request->has('per_page')){
          $per_page = $request->per_page;
          
          if($per_page == 'all'){
              $per_page = Booking::count();
          }
      } else {
          $per_page = 10;
      }

      // get all Booking
      $bookings =  Booking::paginate($per_page);

      if ($bookings) {

          // checking if data set have data
          $response = [
              'status'    => 'success',
              'message'   => 'Bookings found',
              'data'      => $bookings
          ];
      } else {

          // if data set does not have data
          $response = [
              'status'    => 'error',
              'message'   => 'No data for Bookings'
          ];

          $response_status = 404;
      }



      return response()->$response_type($response, $response_status);

   }
   
   /**
    * Create Booking
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function store(Request $request)
    {
        $response_type = $request->format('json');
        
        $response_status = 200;

         // validation
         $this->validate($request, [
             'customer_id'    => 'required|exists:customers,id',
             'room_number'    => 'required|exists:rooms,id',
             'total_person'   => 'required|integer',
             'book_type'      => 'required',
             'arrival'        => 'date_format:Y-m-d H:i:s',
             'checkout'       => 'date_format:Y-m-d H:i:s',
         ]);

          $room = Room::find($request->room_number);

          // check if room locked, and does it has enough space
          if($room->is_locked || $room->max_person < $request->total_person){
            
            if($room->max_person < $request->total_person){
              // response if certian room's guest limit exceed
              $response = [
                  'status'    => 'error',
                  'message'   => "Given room\'s person limit is $room->max_person, Please select another room."
              ];
              $response_status = 406;
            }

            if($room->is_locked){
              // response if certian room is locked
              $response = [
                  'status'    => 'error',
                  'message'   => 'Given room is locked. Please select another room.'
              ];
              $response_status = 406;
            }
          } else {
              // creating Booking object
              $booking  =   new Booking();

              // taking data
              $booking->customer_id = $request->customer_id;
              $booking->booked_by = Auth::id();
              $booking->room_number = $request->room_number;
              $booking->total_person = $request->total_person;
              $booking->book_type = $request->book_type;
              if($request->arrival){
                $booking->arrival = $request->arrival;
              }
              if($request->checkout){
                $booking->checkout = $request->checkout;
              }

              // saving Booking
              $booking->save();

              if ($booking) {
                  
                  // response if data saved
                  $response = [
                      'status'    => 'success',
                      'message'   => 'Booking Created',
                      'data'      => $booking
                  ];
                  $response_status = 201;
              } else {

                  // response if data saving failed
                  $response = [
                      'status'    => 'error',
                      'message'   => 'Booking Creation Failed'
                  ];
                  $response_status = 500;
              }
          }

           // return json response
           return response()->$response_type($response, $response_status);
    }



    /**
     * Display the specified Booking.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $response_type = $request->format('json');

        $response_status = 200;

        // find Booking with id 
        $booking = Booking::find($id);
        if ($booking == null) {

            // if id does not have data
            $response = [
                'status'    => 'error',
                'message'   => 'Booking does not exist',
            ];
            $response_status = 404;

        } else {

            // if id exist and have data
            $response = [
                'status'    => 'success',
                'message'   => 'Booking successfully found',
                'data'      =>  $booking
            ];
        }

        // return json response
        return response()->$response_type($response, $response_status);
    }

    /**
     * Update the specified Booking.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $response_type = $request->format('json');

        $response_status = 200;

        $this->validate($request, [
            'customer_id'    => 'exists:customers,id',
            'room_number'    => 'exists:rooms,id',
            'total_person'   => 'integer',
            'arrival'        => 'date_format:Y-m-d H:i:s',
            'checkout'       => 'date_format:Y-m-d H:i:s',
        ]);

        // find Booking with id
        $booking =  Booking::find($id);
        if ($booking == null) {

            // checking if id exists
            $response = [
                'status'    => 'error',
                'message'   => 'Booking does not exist',
            ];
            $response_status = 404;

        } else {

            // check if room locked, and does it has enough space 
            if($request->room_number && $booking->room_number != $request->room_number){
                $room = Room::find($request->room_number);
                if($room->is_locked || $room->max_person < $request->total_person){
                  
                  if($room->max_person < $request->total_person){
                    // response if certian room's guest limit exceed
                    $response = [
                        'status'    => 'error',
                        'message'   => "Given room\'s person limit is $room->max_person, Please select another room."
                    ];
                    $response_status = 406;
                  }

                  if($room->is_locked){
                    // response if certian room is locked
                    $response = [
                        'status'    => 'error',
                        'message'   => 'Given room is locked. Please select another room.'
                    ];
                    $response_status = 406;
                  }

                  goto booking_data_upgration_end;
                } else {
                  goto booking_data_upgration_start;
                }
            }

            // label for goto statement in upper if-else statement
            booking_data_upgration_start:

            // if id exists then take input and prepare for update
            if ($request->customer_id) {
                $booking->customer_id = $request->customer_id;
            }
            if ($request->room_number) {
                $booking->room_number = $request->room_number;
            }
            if ($request->total_person) {
                $booking->total_person = $request->total_person;
            }
            if ($request->arrival) {
                $booking->arrival = $request->arrival;
            }
            if ($request->checkout) {
                $booking->checkout = $request->checkout;
            }
            if ($request->book_type) {
                $booking->book_type = $request->book_type;
            }

            // Booking update
            $booking->update();

            if ($booking) {

                // if Booking update successfull
                $response = [
                    'status'    => 'success',
                    'message'   => 'Booking updated successfully',
                    'data'      => $booking,
                ];
            } else {

                // if Booking update failed
                $response = [
                    'status'    => 'error',
                    'message'   => 'Booking update failed',
                ];
                $response_status = 500;
            }

            // label for goto statement in upper if-else statement
            booking_data_upgration_end:
        }

        return response()->$response_type($response, $response_status);
    }

    /**
     * Remove the specified Booking.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $response_type = $request->format('json');

        $response_status = 200;

        // finding Booking id
        $booking = Booking::find($id);

        if ($booking == null) {

            // response if Bookings does not have data
            $response = [
                'status'    => 'error',
                'message'   => 'Booking does not exist',
            ];
            $response_status = 404;
        } else {
            // deleting the Booking
            $booking->delete();
            if ($booking) {

                // response if delete successful
                $response = [
                    'status'    => 'success',
                    'message'   => 'Booking Deleted Successfully',
                ];
            } else {

                // response if deletion failed
                $response = [
                    'status'    => 'error',
                    'message'   => 'Booking Deletion Failed',
                ];
                $response_status = 500;
            }
           
        }

        // return json response
        return response()->$response_type($response, $response_status);
    }
}