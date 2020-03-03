<?php

namespace App\Http\Controllers;

use App\CustomerDiscount;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
use \Validator;


class HomeController extends Controller
{
  
/**
 * @param $request
 * @desc used to get discount
 */
  public function index(Request $request)
  {


    try {
      $rules = [
        'email' => 'required|email|unique:users',
        'mobile_number' => 'required|unique:users,mobile_number',
        'name' => 'required|min:5',
        'address' => 'required|min:5'
      ];
      /*
|
| run validation rules
|
*/
      $validator = Validator::make($request->all(), $rules);
      if ($validator->fails()) {
        //pass validator errors as errors object for ajax response
        return response()->json(['errors' => $validator->errors()]);
      }
      $data = $request->all();
      $data['password'] = Hash::make('123456');

    /**
     * used to get random discount
     */
      $discount = $this->getRandomDiscount();
     
      if ($discount) {  //check wether it giving discount or not
        $customerDiscount = CustomerDiscount::where('discount', $discount)->first();
        $customerDiscount->decrement('remaining_times', 1); // decrease 1 count
        $customerDiscount->save();
        $data['coupon_id']=$customerDiscount->id;
        $message="Coupon Applied successfully";
        User::create($data);
        $http_response_header = ['message' => $message, 'code' => Response::HTTP_OK, 'discount' => $discount];

      }else{ //If there is no discount left
        $message="No more coupon left";
        $http_response_header = ['message' => $message];

      }
      
    } catch (Exception $e) {
      $http_response_header = ['message' => $e->getMessage(), 'code' => $e->getCode()];
    }

    return response()->json($http_response_header);
  }

  /**
   * @desc get discout values
   */
  private function getRandomDiscount()
  {
    $discount = CustomerDiscount::where('remaining_times','>',0)->get()->toArray(); //check remaing times
    $dicountCodeArray = [];
   
    if(count($discount)){

      foreach($discount as $dis){
       for($i=1; $i<=$dis['remaining_times'];$i++){
        $dicountCodeArray[]=$dis['discount'];
       } 
      }
      
      shuffle($dicountCodeArray);   //shuffling array 
      return $dicountCodeArray[rand(0,count($dicountCodeArray)-1)];  //used to get random discounts
    }else{
      return 0;
    }
  }
}
