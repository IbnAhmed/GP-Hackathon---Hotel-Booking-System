<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Models\Customer;

class CustomerController extends Controller
{
   public function __construct(){}

   /**
    * Get All Customers
    *
    *
    * @return \Illuminate\Http\Response
    */
   public function index(Request $request){
      $response_type = $request->format('json');
      
      $response_status = 200;

      $customers = Customer::paginate(15);


      // item per page
      if($request->has('per_page')){
          $per_page = $request->per_page;
          
          if($per_page == 'all'){
              $per_page = Customer::count();
          }
      } else {
          $per_page = 10;
      }

      // get all customer
      $customers =  Customer::paginate($per_page);

      if ($customers) {

          // checking if data set have data
          $response = [
              'status'    => 'success',
              'message'   => 'Customers found',
              'data'      => $customers
          ];
      } else {

          // if data set does not have data
          $response = [
              'status'    => 'error',
              'message'   => 'No data for Customers'
          ];

          $response_status = 404;
      }



      return response()->$response_type($response, $response_status);

   }
   
   /**
    * Create Customer
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
             'first_name'   => 'required',
             'last_name'    => 'required',
             'phone'        => 'required|min:11',
             'email'        => 'required',
         ]);

         $customer_exist = Customer::where('email', $request->email)->orWhere('phone', $request->phone)->first();
         if($customer_exist){
            
            // response if customer exist
            $response = [
                'status'    => 'success',
                'message'   => 'Customer Already Exist!',
                'data'      => $customer_exist
            ];
         } else {
            // creating customer object
            $customer  =   new Customer();

            // taking data
            $customer->first_name = $request->first_name;
            $customer->last_name = $request->last_name;
            $customer->phone = $request->phone;
            $customer->email = $request->email;
            $customer->registered_by = Auth::id();

            // saving customer
            $customer->save();

            if ($customer) {

                // response if data saved
                $response = [
                    'status'    => 'success',
                    'message'   => 'Customer Created',
                    'data'      => $customer
                ];
                $response_status = 201;
            } else {

                // response if data saving failed
                $response = [
                    'status'    => 'error',
                    'message'   => 'Customer Creation Failed'
                ];
                $response_status = 500;
            }
           }

           // return json response
           return response()->$response_type($response, $response_status);
    }



    /**
     * Display the specified Customer.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $response_type = $request->format('json');

        $response_status = 200;

        // find Customer with id 
        $customer = Customer::find($id);
        if ($customer == null) {

            // if id does not have data
            $response = [
                'status'    => 'error',
                'message'   => 'Customer does not exist',
            ];
            $response_status = 404;

        } else {

            // if id exist and have data
            $response = [
                'status'    => 'success',
                'message'   => 'Customer successfully found',
                'data'      =>  $customer
            ];
        }

        // return json response
        return response()->$response_type($response, $response_status);
    }

    /**
     * Update the specified Customer.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $response_type = $request->format('json');

        $response_status = 200;

        // find Customer with id
        $customer =  Customer::find($id);
        if ($customer == null) {

            // checking if id exists
            $response = [
                'status'    => 'error',
                'message'   => 'Customer does not exist',
            ];
            $response_status = 404;

        } else {

            // if id exists then take input and prepare for update
            if ($request->first_name) {
                $customer->first_name = $request->first_name;
            }
            if ($request->last_name) {
                $customer->last_name = $request->last_name;
            }
            if ($request->phone_number) {
                $customer->phone_number = $request->phone_number;
            }
            if ($request->email) {
                $customer->email = $request->email;
            }

            // Customer update
            $customer->update();

            if ($customer) {

                // if Customer update successfull
                $response = [
                    'status'    => 'success',
                    'message'   => 'Customer updated successfully',
                    'data'      => $customer,
                ];
            } else {

                // if Customer update failed
                $response = [
                    'status'    => 'error',
                    'message'   => 'Customer update failed',
                ];
                $response_status = 500;
            }
        }

        return response()->$response_type($response, $response_status);
    }

    /**
     * Remove the specified Customer.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $response_type = $request->format('json');

        $response_status = 200;

        // finding Customer id
        $customer = Customer::find($id);

        if ($customer == null) {

            // response if Customers does not have data
            $response = [
                'status'    => 'error',
                'message'   => 'Customer does not exist',
            ];
            $response_status = 404;
        } else {
            // deleting the Customer
            $customer->delete();
            if ($customer) {

                // response if delete successful
                $response = [
                    'status'    => 'success',
                    'message'   => 'Customer Deleted Successfully',
                ];
            } else {

                // response if deletion failed
                $response = [
                    'status'    => 'error',
                    'message'   => 'Customer Deletion Failed',
                ];
                $response_status = 500;
            }
           
        }

        // return json response
        return response()->$response_type($response, $response_status);
    }
}