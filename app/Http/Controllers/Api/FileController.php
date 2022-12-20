<?php

namespace App\Http\Controllers\Api;

use Laravel\Sanctum\PersonalAccessToken;
use Tokenly\RecordLock\Facade\RecordLock;
use App\Http\Controllers\Controller;
use App\Models\_FILE;
use App\Models\FileGroup;
use App\Models\GroupUser;
use App\Models\Group;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Validator;
use DB;

class FileController extends Controller
{

   public function create(Request $request)
   {

 // return request()->ip();
    $validator=Validator::make($request->all(),
      //role
    [
        
        'file'=>'required|mimes:doc,docx,pdf,txt,csv,json|max:2048',
        'name_file'=>"required"

    ] );
    if ($validator->fails())
    {
        $response=[
            'success'=>false,
             'message'=> $validator->errors()
        ];
        return response()->json($response,400);
    }

    $token = PersonalAccessToken::findToken($request->bearerToken());
    $user = $token->tokenable;
    $input=$request->all();


         $file = $request->file('file');
          $path = $file->store('public');
        $name = $file->getClientOriginalName();
        $name= basename($path);
        $f= _FILE::create([
         "id_owner"=>$user->id,
         'state'=>"check-out",
         'public'=>"1",
         'name_file'=>$request->name_file,
         'name'=>$name,
         'url'=>'/storage/'.$name

     ]);

     $response=[
        'success'=>true,
        'message'=>"user add file to public successfully",
        "url"=>$f->url
         ];
     return response()->json($response,200); 


   }


   public function addToGroup(Request $request)
   {

    $validator=Validator::make($request->all(),
      //role
    [
        'file'=>'required|mimes:doc,docx,txt,png,pdf,json',
        'name_file'=>"required",
        'id_group'=>'required'

    ] );
    if ($validator->fails())
    {
        $response=[
            'success'=>false,
             'message'=> $validator->errors()
        ];
        return response()->json($response,400);
    }

    $token = PersonalAccessToken::findToken($request->bearerToken());
    $user = $token->tokenable;
    $input=$request->all();

    $file = $request->file('file');
    $path = $file->store('public/'.$request->id_group);
    $name = $file->getClientOriginalName();
    $name= basename($path);
       /*  $file = $request->file("file");
        $name = time().'.'.$file->getClientOriginalExtension();
        Storage::disk('local')->put('public/'.$request->id_group.'/'.$name, 'Contents'); */

        $file=_FILE::create([
            "id_owner"=>$user->id,
            'state'=>"check-out",
            'public'=>"0",
            'name_file'=>$request->name_file,
            'name'=>$name,
            'url'=>'/storage/'.$request->id_group.'/'.$name
   
        ]);


    $file_group=FileGroup::create(['id_file'=>$file->id,'id_group'=>$request->id_group]);

     $response=[
        'success'=>true,
        'message'=>"user add file to group successfully",
        "url"=>$file->url

         ];
     return response()->json($response,200);


   }


   public function deleteFileGroup(Request $request,$id)
   {

     $file=_File::find($id);
     if($file->state=="check-in")
     {
        $response=[
            'success'=>false,
            'message'=>"File Reserved"
        ];
        return response()->json($response,400);
     }

     $token = PersonalAccessToken::findToken($request->bearerToken());
      $user = $token->tokenable;



     if($file->id_owner!=$user->id)
     {
        $response=[
            'success'=>false,
             'message'=> "You can't delete because you're not the owner"
        ];
        return response()->json($response,200);
     }

     if($file->public=="1")
     {
        if(Storage::exists('public/'.$file->name))
        {
            Storage::delete(['public/'.$file->name]);
            $file->delete();
            $response=[
                'success'=>true,
                'message'=>"The file has been deleted successfully"
            ];
            return response()->json($response,200);

        }

     }
      $file_group=FileGroup::where('id_file',$file->id)->get()->first();
     if(Storage::exists('public/'.$file_group->id_group.'/'.$file->name))
     {
        Storage::delete(['public/'.$file_group->id_group.'/'.$file->name]);
        $file_group->delete();
        $file->delete();
        $response=[
            'success'=>true,
            'message'=>"The file has been deleted successfully"
        ];
        return response()->json($response,200);
     }


   }


   public function fileInGroup(Request $request)
   {
    return  $userInGroup=FileGroup::fileInGroup($request->id_group);
   }


   public function filePublic()
   {
     $file= _File::where('public',1)->get()  ;
     $response=[
        'success'=>true,
        'date'=>$file
         ];
     return response()->json($response,200);
   }
   public function myFile(Request $request)
   {

    $token = PersonalAccessToken::findToken($request->bearerToken());
    $user = $token->tokenable;
    $file=_File::where('id_owner','=',$user->id)->get('__files.*');
    $response=[
        'success'=>true,
        'date'=>$file
         ];
     return response()->json($response,200);
   }


   public function stateFile(Request $request)
   {

    $validator=Validator::make($request->all(),['id_file'=>'required']);
     if ($validator->fails())
     {
      $response=[
          'success'=>false,
           'message'=> $validator->errors()
      ];
      return response()->json($response,400);
      }



    $file=_File::find($request->id_file);
     if(!$file)
     {
    $response=[
        'success'=>false,
         'message'=> "the id file not found"
    ];
    return response()->json($response,400);
     }

    $response=[
        'success'=>true,
        'date'=>$file->state
         ];
     return response()->json($response,200);

   }

   public function test()
   {
   return \DB::table('users')
        ->lockForUpdate()
        ->get();
   }


   public function readFile(Request $request)
   {

    $file = _File::find($request->id_file); // 'balance' =>
   if($file->state=="check-in")
   {
    $response=[
        'success'=>false,
        'date'=>"the file check-in before from another user"
         ];
   }

   else
  {  $response=[
       'success'=>true,
       'date'=>$file
        ];}
    return response()->json($response,200);

 /*    $file = \File::get(public_path('storage\lina.pdf'));
   $response = \Response::make($file, 200);
  // $response->header('Content-Type', 'application/pdf');

   return $response; */

   }


   public function check_in(Request $request)
   {

    $token = PersonalAccessToken::findToken($request->bearerToken());
    $user = $token->tokenable;
    $file=_File::find($request->id_file);
    if($file)
        if(_File::find($request->id_file)->state=="check-in")
        {
            $response=[
                'success'=>false,
                'date'=>"the file check-in before from  user"
                 ];
             return response()->json($response,200);
        }
         $file = _File::lockForUpdate()->find($request->id_file); // 'balance' =>
         $file->state="check-in";
         $file->save();

         $response=[
            'success'=>true,
            'date'=>$file
             ];
   

  if($file->public=="0")
  {
     $group_file=FileGroup::where('id_file',$file->id)->get()->first();  
     $group_user=GroupUser::where('id_user',$user->id)->where('id_group',$group_file->id_group)->get()->first();
     $group_user->counter_file+=1;
     $group_user->save();

     $group=Group::find($group_file->id_group);
     $group->number_file_check_in+=1;
     $group->save();

  }
         return response()->json($response,200);
   }


   public function check_out(Request $request)
   {

    $token = PersonalAccessToken::findToken($request->bearerToken());
    $user = $token->tokenable;
    $file=_File::find($request->id_file);
         $file->state="check-out";
         $file->save();

         $response=[
            'success'=>true,
            'message'=>"The file has been check-out successfully"
             ];
             if($file->public=="0")
             {
                $group_file=FileGroup::where('id_file',$file->id)->get()->first();  
                $group_user=GroupUser::where('id_user',$user->id)->where('id_group',$group_file->id_group)->get()->first();
                $group_user->counter_file-=1;
                $group_user->save();
           
                $group=Group::find($group_file->id_group);
                $group->number_file_check_in-=1;
                $group->save();
           
             }

         return response()->json($response,200);
   }



public function bulk_check_in(Request $request)
{

    $ids = $request->ids;
    $ids=json_decode($ids);
    DB::beginTransaction();

    try {

        foreach ($ids as $key => $id) {
        if(_File::find($id)->state=="check-out")
          {
           $file = _File::lockForUpdate()->find($id);
           $file->state="check-in";
           $file->save();
          }
      else{
           DB::rollback();
           $response=[
          'success'=>false,
          'message'=>"something went wrong"
            ];
          return response()->json($response,400);
          }
    }
    DB::commit();

        $response=[
            'success'=>true,
            'message'=>"all good"
             ];
         return response()->json($response,200);

    // all good
      } catch (\Exception $e) {

      DB::rollback();
       $response=[
        'success'=>false,
        'message'=>"something went wrong"
         ];
        return response()->json($response,400);
    // something went wrong
}
}



}
