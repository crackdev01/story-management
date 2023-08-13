<?php

use App\Http\Controllers\API\StoryController;
use App\Http\Controllers\API\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group(['middleware' => 'auth:api'], function()
{
    // Story
    Route::get('/stories', [StoryController::class, 'index']);
    Route::post('/story', [StoryController::class, 'store']);
    Route::group([
        'middleware' => ['find_model:App\Models\Story', 'check_user_access_for_story'],
        'prefix' => 'story'
    ], function(){
        Route::get('/{id}', [StoryController::class, 'show']);
        Route::patch('/{id}', [StoryController::class, 'update']);
        Route::delete('/{id}', [StoryController::class, 'destroy']);
    });

    // User
    Route::get('/me', function (Request $request) {
        return ['data' => $request->user()];
    });
    Route::get('/users', [UserController::class, 'index']);
});

Route::post('/login', [UserController::class, 'login']);
