<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Auth;
use App\Models\User;

class AuthController extends Controller
{
//LogAfterRequest
public function ____construct()
{

    $this->middleware('LogAfterRequest');
}

    public function register(Request $request)
    {
        

   $validator=Validator::make($request->all(),
   //role
    [
        'email'=>'required|email|unique:users',
        'user_name'=>'required|unique:users',
        'last_name'=>'required',
        'first_name'=>'required',
        'password'=>'required',
        'c_password'=>'required|same:password',

    ] );
   
    if ($validator->fails())
    {
        $response=[
            'success'=>false,
             'message'=> $validator->errors()
        ];
        return response()->json($response,400);
    }
        $input=$request->all();
        $input["password"]=bcrypt($input["password"]);
        $user=User::create($input);
        $success["token"]=$user->createToken('MyApp')->plainTextToken;
        $success["id"]=$user->id ;
        $success["name"]=$user->first_name ;

         $response=[
            'success'=>true,
            'date'=>$success,
            'message'=>"user register successfully"
         ];
         return response()->json($response,200);
    
    }


    public function login(Request $request)
    {

    if(Auth::attempt(['user_name' => $request->user_name, 'password' => $request->password]))
    {
      $user=Auth::user();
      $success["token"]=$user->createToken('MyApp')->plainTextToken;
      $success["id"]=$user->id ;
      $success["name"]=$user->first_name ;
      
      $response=[
        'success'=>true,
        'date'=>$success,
        'message'=>"user login successfully",
        
     ];
     return response()->json($response,200);

    }

    else {
        
        $response=[
            'success'=>false,
             'message'=> "Unauthorised"
        ];
        return response()->json($response,400);
    }
        
    }
}
