<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Models\Room;

class RoomController extends Controller
{
   public function __construct(){}

   /**
    * Get All Rooms
    *
    *
    * @return \Illuminate\Http\Response
    */
   public function index(Request $request){
      $response_type = $request->format('json');
      
      $response_status = 200;

      $rooms = Room::paginate(15);


      // item per page
      if($request->has('per_page')){
          $per_page = $request->per_page;
          
          if($per_page == 'all'){
              $per_page = Room::count();
          }
      } else {
          $per_page = 10;
      }

      // get all Room
      $rooms =  Room::paginate($per_page);

      if ($rooms) {

          // checking if data set have data
          $response = [
              'status'    => 'success',
              'message'   => 'Rooms found',
              'data'      => $rooms
          ];
      } else {

          // if data set does not have data
          $response = [
              'status'    => 'error',
              'message'   => 'No data for Rooms'
          ];

          $response_status = 404;
      }



      return response()->$response_type($response, $response_status);

   }
   
   /**
    * Create Room
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
           'room_number'    => 'required|unique:rooms,room_number',
           'price'          => 'required|numeric',
           'max_person'     => 'required|integer',
           'is_locked'      => 'boolean',
           'room_type'      => 'required|string',
        ]);

        // creating Room object
        $room  =   new Room();

        // taking data
        $room->room_number  = $request->room_number;
        $room->price        = $request->price;
        $room->max_person   = $request->max_person;
        $room->room_type    = $request->room_type;

        if($request->is_locked){
          $room->is_locked  = $request->is_locked;
        } else {
          $room->is_locked  = 0;
        }

        // saving Room
        $room->save();

        if ($room) {
            
            // response if data saved
            $response = [
                'status'    => 'success',
                'message'   => 'Room Created',
                'data'      => $room
            ];
            $response_status = 201;
        } else {

            // response if data saving failed
            $response = [
                'status'    => 'error',
                'message'   => 'Room Creation Failed'
            ];
            $response_status = 500;
        }

        // return json response
        return response()->$response_type($response, $response_status);
    }



    /**
     * Display the specified Room.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $response_type = $request->format('json');

        $response_status = 200;

        // find Room with id 
        $room = Room::find($id);
        if ($room == null) {

            // if id does not have data
            $response = [
                'status'    => 'error',
                'message'   => 'Room does not exist',
            ];
            $response_status = 404;

        } else {

            // if id exist and have data
            $response = [
                'status'    => 'success',
                'message'   => 'Room successfully found',
                'data'      =>  $room
            ];
        }

        // return json response
        return response()->$response_type($response, $response_status);
    }

    /**
     * Update the specified Room.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $response_type = $request->format('json');

        $response_status = 200;

        // validation
        $this->validate($request, [
           'price'          => 'numeric',
           'max_person'     => 'integer',
           'is_locked'      => 'boolean',
           'room_type'      => 'string',
        ]);

        // find Room with id
        $room =  Room::find($id);
        if ($room == null) {

            // checking if id exists
            $response = [
                'status'    => 'error',
                'message'   => 'Room does not exist',
            ];
            $response_status = 404;

        } else {

            if ($request->price) {
                $room->price = $request->price;
            }
            if ($request->max_person) {
                $room->max_person = $request->max_person;
            }
            if ($request->is_locked) {
                $room->is_locked = $request->is_locked;
            }
            if ($request->room_type) {
                $room->room_type = $request->room_type;
            }

            // Room update
            $room->update();

            if ($room) {

                // if Room update successfull
                $response = [
                    'status'    => 'success',
                    'message'   => 'Room updated successfully',
                    'data'      => $room,
                ];
            } else {

                // if Room update failed
                $response = [
                    'status'    => 'error',
                    'message'   => 'Room update failed',
                ];
                $response_status = 500;
            }

        }

        return response()->$response_type($response, $response_status);
    }

    /**
     * Remove the specified Room.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $response_type = $request->format('json');

        $response_status = 200;

        // finding Room id
        $room = Room::find($id);

        if ($room == null) {

            // response if Rooms does not have data
            $response = [
                'status'    => 'error',
                'message'   => 'Room does not exist',
            ];
            $response_status = 404;
        } else {
            // deleting the Room
            $room->delete();
            if ($room) {

                // response if delete successful
                $response = [
                    'status'    => 'success',
                    'message'   => 'Room Deleted Successfully',
                ];
            } else {

                // response if deletion failed
                $response = [
                    'status'    => 'error',
                    'message'   => 'Room Deletion Failed',
                ];
                $response_status = 500;
            }
           
        }

        // return json response
        return response()->$response_type($response, $response_status);
    }

    /**
     * Get All Available Rooms
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function availableRooms(Request $request){
       $response_type = $request->format('json');
       
       $response_status = 200;

       $rooms = Room::where('is_locked', 0)->paginate(15);


       // item per page
       if($request->has('per_page')){
           $per_page = $request->per_page;
           
           if($per_page == 'all'){
               $per_page = Room::count();
           }
       } else {
           $per_page = 10;
       }

       // get all Room
       $rooms =  Room::paginate($per_page);

       if ($rooms) {

           // checking if data set have data
           $response = [
               'status'    => 'success',
               'message'   => 'Rooms found',
               'data'      => $rooms
           ];
       } else {

           // if data set does not have data
           $response = [
               'status'    => 'error',
               'message'   => 'No data for Rooms'
           ];

           $response_status = 404;
       }



       return response()->$response_type($response, $response_status);

    }
}