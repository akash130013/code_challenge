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
   * @OA\Info(
   *      title="BRDSEYE API Documentation",
   *      version="1.0",
   *      @OA\Contact(
   *          email="sudhir.pandey@appinventiv.com",
   *          name="Support Team"
   *      ),
   * )
   */

  /**
   * @OA\Get(
   *      path="/api/user/login",
   *      operationId="/api/user/login",
   *      tags={"Users"},
   *      summary="User can login",
   *    @OA\Parameter(
   *         name="name",
   *         in="query",
   *         description="name",
   *         required=true,
   *         @OA\Schema(type="string")
   *     ),
   *    @OA\Parameter(
   *         name="email",
   *         in="query",
   *         description="email",
   *         required=true,
   *         @OA\Schema(type="string")
   *     ),
   *    @OA\Response(
   *         response=200,
   *          description="Success"
   *     ),
   *    @OA\Response(
   *         response="400",
   *         description="Error: Bad request.",
   *     ),
   *    @OA\Response(
   *         response="422",
   *         description="Parameter Required",
   *     ),
   *   @OA\Response(
   *         response="401",
   *         description="Unauthorised",
   *     ),
   *  @OA\Response(
   *         response="500",
   *         description="Something went wrong",
   *     )
   * )
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


      $discount = $this->getRandomDiscount();
     
      if ($discount) {
        $customerDiscount = CustomerDiscount::where('discount', $discount)->first();
        $customerDiscount->decrement('remaining_times', 1); // decrease 1 count
        $customerDiscount->save();
        $data['coupon_id']=$customerDiscount->id;
        $message="Coupon Applied successfully";
        User::create($data);
        $http_response_header = ['message' => $message, 'code' => Response::HTTP_OK, 'discount' => $discount];

      }else{
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
    $discount = CustomerDiscount::where('remaining_times','>',0)->get()->toArray();
    $dicountCodeArray = [];
   
    if(count($discount)){
      foreach($discount as $dis){
       for($i=1; $i<=$dis['remaining_times'];$i++){
        $dicountCodeArray[]=$dis['discount'];
       }
        
      }
      
      shuffle($dicountCodeArray);
      return $dicountCodeArray[rand(0,count($dicountCodeArray)-1)];
    }else{
      return 0;
    }
  }
}
