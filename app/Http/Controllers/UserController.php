<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;
use Auth;
use Illuminate\Support\Str;

class UserController extends Controller
{
   public function __construct(){}

   /**
    * Login  
    *
    * Maximum Parameter List : email, password
    *
    * @return  \Illuminate\Http\Response
    */
   public function authenticate(Request $request) {
      $response_type = $request->format('json');
      
      $this->validate($request, [
       'email'    => 'required',
       'password' => 'required'
      ]);

      $user = User::where('email', $request->email)->first();

      $response_status = 200;
      if($user && Hash::check($request->password, $user->password)){
          $apikey = base64_encode(Str::random(40));

          User::where('email', $request->email)->update(['api_token' => "$apikey"]);

          $return_data = [
          	'status' 	=> 'success',
          	'message' 	=> 'Successfully Logged in!',
          	'api_token' => $apikey
          ];
      } else{
          $return_data = ['status' => 'error','message' => 'Email or Password did not match!'];
          $response_status = 404;
      }

      return response()->$response_type($return_data, $response_status);
   }

   /**
    * Register  
    *
    * Maximum Parameter List : first_name, last_name, phone, email, password
    *
    * @return  \Illuminate\Http\Response
    */
   public function register(Request $request) {
      $response_type = $request->format('json');
	    
      $this->validate($request, [
	       'first_name' => 'required',
	       'last_name'  => 'required',
	       'phone'      => 'required|min:11',
	       'password'   => 'required|min:8',
	       'email'   	=> 'required|unique:users'
	    ]);

	    $response_status = 200;
      
	    User::create([
	      'first_name'  => $request->first_name,
	      'last_name'   => $request->last_name,
	      'phone'       => $request->phone,
	      'email'       => $request->email,
	      'password'    => Hash::make($request->password)
	    ]);

    	$return_data = [
    		'status' 	=> 'success',
	    	'message' 	=> 'Your account created successfully, Now logged in to step forward.'
	    ];
      return response()->$response_type($return_data, $response_status);
      
   }


   /**
    * Get current logged in user 
    *
    * @return  \Illuminate\Http\Response
    */
   public function currentUser(Request $request) {
      $response_type = $request->format('json');  
	    
      $response_status = 200;
      
      $return_data = [
      	'status' => 'success', 
      	'message' => 'Current logged in user', 
      	'user' => Auth::user()->simpleUserData()
      ];
      
      return response()->$response_type($return_data, $response_status);
   }
}    