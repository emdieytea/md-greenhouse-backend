<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\DHT11SensorController;
use App\Http\Controllers\API\NodeController;
use App\Http\Controllers\API\NPKSensorController;
use App\Http\Controllers\API\SGP30SensorController;
use App\Http\Controllers\API\UploadDataController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

# Actions Handled By Resource Controller
/** 
 * Verb	      |     URI	                  |     Action     |    Route Name
 * ----------------------------------------------------------------------------
 * GET	      |     /photos	              |     index      |    photos.index
 * GET	      |     /photos/create	      |     create	   |    photos.create
 * POST	      |     /photos	              |     store      |    photos.store
 * GET	      |     /photos/{photo}	      |     show	   |    photos.show
 * GET	      |     /photos/{photo}/edit  |     edit	   |    photos.edit
 * PUT/PATCH  |     /photos/{photo}	      |     update	   |    photos.update
 * DELETE	  |     /photos/{photo}	      |     destroy	   |    photos.destroy
*/

Route::prefix('v1')->middleware(['app_auth_key'])->group(function () {
    // Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('forgot-password', [AuthController::class, 'forgot_password']);
    Route::post('reset-password', [AuthController::class, 'reset_password']);

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('profile', [AuthController::class, 'get_profile']);
        Route::post('profile', [AuthController::class, 'post_profile']);

        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('check-session', [AuthController::class, 'check_session']);

        Route::resource('dht11sensor', DHT11SensorController::class)->only([ 'index' ]);
        Route::resource('npksensor', NPKSensorController::class)->only([ 'index' ]);
        Route::resource('sgp30sensor', SGP30SensorController::class)->only([ 'index' ]);
        Route::resource('nodes', NodeController::class)->only([ 'index', 'update' ]);
        
        Route::get('refresh-data', [NodeController::class, 'refresh_data']);
    });
    
    Route::resource('nodes', NodeController::class)->only([ 'store' ]);
    Route::resource('upload-data', UploadDataController::class)->only([ 'store' ]);
});
