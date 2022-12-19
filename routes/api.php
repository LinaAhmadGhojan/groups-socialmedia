<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FileController;
use App\Http\Controllers\Api\GroupController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::controller(AuthController::class)->group(function()
{
    Route::post('/login','login');
    Route::post('/register','register');

});

Route::controller(GroupController::class)->prefix('/group') ->group(function()
{

    Route::post('/create','create');
    Route::post('/myGroup','myGroup');
    Route::post('/users','userInGroup');
    Route::post('/found/user','foundUserAddGroup');
    Route::post('/add/user','addUserToGroup');
    Route::delete('/delete/{id_user}/{id_group}','deleteUserFromGroup');
    Route::delete('/delete/{id_group}','deleteGroup');

});

Route::controller(FileController::class)->prefix('/file')->group(function()
{

    Route::post('/public/create','create');
    Route::post('/myFile','myFile');
    Route::post('/test','test');
    Route::post('/state','stateFile');
    Route::post('/group/create','addToGroup');
    Route::post('/group','fileInGroup');
    Route::post('/public','filePublic');
    Route::delete('/delete/{id}','deleteFileGroup');
    Route::post('/check-in','check_in');
    Route::post('/bulk-check-in','bulk_check_in');
    Route::post('/check-out','check_out');
    Route::put('/update/{id_user}','updateFile');
    Route::delete('/delete/{id_user}','deleteFile');
    Route::post('/read','readFile');

});



