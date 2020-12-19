<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Models\Payment;

class PaymentController extends Controller
{
   public function __construct(){}

   /**
    * Get All Payments
    *
    *
    * @return \Illuminate\Http\Response
    */
   public function index(Request $request){
      $response_type = $request->format('json');
      
      $response_status = 200;

      $payments = Payment::paginate(15);


      // item per page
      if($request->has('per_page')){
          $per_page = $request->per_page;
          
          if($per_page == 'all'){
              $per_page = Payment::count();
          }
      } else {
          $per_page = 10;
      }

      // get all Payment
      $payments =  Payment::paginate($per_page);

      if ($payments) {

          // checking if data set have data
          $response = [
              'status'    => 'success',
              'message'   => 'Payments found',
              'data'      => $payments
          ];
      } else {

          // if data set does not have data
          $response = [
              'status'    => 'error',
              'message'   => 'No data for Payments'
          ];

          $response_status = 404;
      }



      return response()->$response_type($response, $response_status);

   }
   
   /**
    * Create Payment
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
           'customer_id'  => 'required|exists:customers,id',
           'booking_id'   => 'required|exists:bookings,id',
           'amount'       => 'required|numeric',
        ]);

        // creating Payment object
        $payment  =   new Payment();

        // taking data
        $payment->customer_id = $request->customer_id;
        $payment->booking_id = $request->booking_id;
        $payment->amount = $request->amount;
        

        // saving Payment
        $payment->save();

        if ($payment) {
            
            // response if data saved
            $response = [
                'status'    => 'success',
                'message'   => 'Payment Created',
                'data'      => $payment
            ];
            $response_status = 201;
        } else {

            // response if data saving failed
            $response = [
                'status'    => 'error',
                'message'   => 'Payment Creation Failed'
            ];
            $response_status = 500;
        }

        // return json response
        return response()->$response_type($response, $response_status);
    }



    /**
     * Display the specified Payment.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $response_type = $request->format('json');

        $response_status = 200;

        // find Payment with id 
        $payment = Payment::find($id);
        if ($payment == null) {

            // if id does not have data
            $response = [
                'status'    => 'error',
                'message'   => 'Payment does not exist',
            ];
            $response_status = 404;

        } else {

            // if id exist and have data
            $response = [
                'status'    => 'success',
                'message'   => 'Payment successfully found',
                'data'      =>  $payment
            ];
        }

        // return json response
        return response()->$response_type($response, $response_status);
    }

    /**
     * Update the specified Payment.
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
           'customer_id'  => 'exists:customers,id',
           'booking_id'   => 'exists:bookings,id',
           'amount'       => 'numeric',
        ]);

        // find Payment with id
        $payment =  Payment::find($id);
        if ($payment == null) {

            // checking if id exists
            $response = [
                'status'    => 'error',
                'message'   => 'Payment does not exist',
            ];
            $response_status = 404;

        } else {

            if ($request->customer_id) {
                $payment->customer_id = $request->customer_id;
            }
            if ($request->booking_id) {
                $payment->booking_id = $request->booking_id;
            }
            if ($request->amount) {
                $payment->amount = $request->amount;
            }
            // Payment update
            $payment->update();

            if ($payment) {

                // if Payment update successfull
                $response = [
                    'status'    => 'success',
                    'message'   => 'Payment updated successfully',
                    'data'      => $payment,
                ];
            } else {

                // if Payment update failed
                $response = [
                    'status'    => 'error',
                    'message'   => 'Payment update failed',
                ];
                $response_status = 500;
            }

        }

        return response()->$response_type($response, $response_status);
    }

    /**
     * Remove the specified Payment.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $response_type = $request->format('json');

        $response_status = 200;

        // finding Payment id
        $payment = Payment::find($id);

        if ($payment == null) {

            // response if Payments does not have data
            $response = [
                'status'    => 'error',
                'message'   => 'Payment does not exist',
            ];
            $response_status = 404;
        } else {
            // deleting the Payment
            $payment->delete();
            if ($payment) {

                // response if delete successful
                $response = [
                    'status'    => 'success',
                    'message'   => 'Payment Deleted Successfully',
                ];
            } else {

                // response if deletion failed
                $response = [
                    'status'    => 'error',
                    'message'   => 'Payment Deletion Failed',
                ];
                $response_status = 500;
            }
           
        }

        // return json response
        return response()->$response_type($response, $response_status);
    }
}