<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\GroupUser;
use App\Models\User;
use App\Models\FileGroup;
use Illuminate\Support\Arr;

use Validator;

class GroupController extends Controller
{

    public function ____construct()
    {

        $this->middleware('auth:sanctum');
    }

   ##############################  create #############################
    public function create(Request $request)
    {

        $token = PersonalAccessToken::findToken($request->bearerToken());
        $user = $token->tokenable;
        $validator=Validator::make($request->all(),['name'=>'required|unique:groups']);

      if ($validator->fails())
     {
         $response=[
             'success'=>false,
              'message'=> $validator->errors()
         ];
         return response()->json($response,400);
     }


     $input=$request->all();
     $input = Arr::add($input, 'id_admin', $user->id);
     $input = Arr::add($input, 'number_user', 0);
     $group=Group::create($input);
     $user_group=GroupUser::create(
      [
        'id_user'=>$user->id,
        'id_group'=>$group->id,
      ]);
      $response=[
         'success'=>true,
         'message'=>"user create group successfully"
          ];
      return response()->json($response,200);

    }


    ##############################   Found User not Found in Group  #############################
  public function foundUserAddGroup(Request $request)
  {
    $validator=Validator::make($request->all(),['id_group:required']);

      if ($validator->fails())
     {
         $response=[
             'success'=>false,
              'message'=> $validator->errors()
         ];
         return response()->json($response,400);
     }

   //  return Group::foundUser($request->id_group);
       $user_ids= GroupUser::where('id_group','=',$request->id_group)->get('id_user as id');
      $user= User::whereNOTIn('id',$user_ids)->get();
      $response=[
        'success'=>true,
         'date'=> $user
    ];
    return response()->json($response,200);



  }


    ##############################  Add user to Group #############################

    public function addUserToGroup(Request $request)
    {


        $validator=Validator::make($request->all(),['id_user'=>'required','id_group:required']);

      if ($validator->fails())
     {
         $response=[
             'success'=>false,
              'message'=> $validator->errors()
         ];
         return response()->json($response,400);
     }

     $group_user=GroupUser::where('id_user',$request->id_user)->where('id_group',$request->id_group)->get()->first();
     if($group_user)
     {
      $response=[
        'success'=>false,
         'message'=> "the user add before"
    ];
    return response()->json($response,400);
     }
    $group=Group::find($request->id_group);
    $group->number_user+=1;
    $group->save();
     $input=$request->all();
     $user=GroupUser::create($input);

      $response=[
         'success'=>true,
         'message'=>"user add to group successfully"
          ];
      return response()->json($response,200);


    }



    public function myGroup(Request $request)
    {

      $token = PersonalAccessToken::findToken($request->bearerToken());
      $user = $token->tokenable;
       $my_group=Group::myGroup($user->id);
       $response=[
        'success'=>true,
         'date'=> $my_group
    ];
    return response()->json($response,200);

    }

    public function userInGroup(Request $request)
    {
      $token = PersonalAccessToken::findToken($request->bearerToken());
      $user = $token->tokenable;
      return  $userInGroup=Group::userInGroup($user->id,$request->id_group);
    }

    public function deleteUserFromGroup($id_user,$id_group,Request $request)
    {
      $groupuser=GroupUser::where('id_user',$id_user)->where('id_group',$id_group)->get()->first();
      if($groupuser)
      if($groupuser->counter_file==0)
      {
         $file=FileGroup::where('id_group',$groupuser->id)->get();
       foreach ($file as $key => $value)
        {
          $value->id_group=null;
          $value->save();
       }
        $groupuser->delete();
        $response=[
          'success'=>true,
           'message'=> "the user delete from group"
      ];
      return response()->json($response,200);
      }

      else {
        $response=[
          'success'=>false,
           'message'=> "the user check-in file in group"
      ];
      return response()->json($response,200);

      }


      $response=[
        'success'=>false,
         'message'=> "the user not found in group"
    ];
    return response()->json($response,200);
    }


    public function deleteGroup(Request $request,$id_group)
    {
        $token = PersonalAccessToken::findToken($request->bearerToken());
        $user = $token->tokenable;
        $group=Group::find($id_group);
        if($group)
        if($group->number_file_check_in==0 && $group->id_admin==$user->id)
        {
           $group->delete();
            $response=[
                'success'=>true,
                 'message'=> "the group delete "

            ];
            return response()->json($response,200);
        }
        $response=[
            'success'=>false,
             'date'=> "You are not the admin Or the group have check-in file"
        ];
        return response()->json($response,400);
    }

}
