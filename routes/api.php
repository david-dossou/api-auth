<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


use App\Http\Controllers\UsersController;

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });


/*
|--------------------------------------------------------------------------
| API Routes begin with /user/
|--------------------------------------------------------------------------
|
*/

Route::prefix('user')->group( function() {

    // User authenticate routes
    Route::middleware('auth:api')->group( function() {      
        // GET methode routes 
        Route::get('liste-users', [UsersController::class, 'listeUsers']);


        // POST methode routes 
        Route::post('create-user', [UsersController::class, 'createUser']);

        Route::post('update-user', [UsersController::class, 'updateUser']);

        Route::post('update-password-user', [UsersController::class, 'updatePasswordUser']);

        Route::post('delete-user', [UsersController::class, 'deleteUser']);



        // ANY methode routes 

    });


    // Visitor routes

    // GET methode routes 



    // POST methode routes 
    Route::post('login', [UsersController::class, 'login']);


    // ANY methode routes 


});
