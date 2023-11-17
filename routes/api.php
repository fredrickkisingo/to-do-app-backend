<?php

use App\Http\Controllers\API\Auth\InitController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Auth\LoginController;
use App\Http\Controllers\API\TasksController;
use App\Http\Controllers\API\UserTasksController;
use App\Http\Controllers\API\StatusController;

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

    Route::group(['prefix' => 'v1'], function() {

Route::post('login',  [LoginController::class, 'login']);
Route::post('log-out',  [LoginController::class, 'logout'])->middleware('auth:api');
Route::post('register',  [LoginController::class, 'register']);
Route::get('init',  [InitController::class, 'init']);

Route::get('task-chart',[TasksController::class, 'taskChart'])->middleware('auth:api');
Route::middleware('auth:api')->group(function () {

    //routes for status
Route::get('status', [StatusController::class, 'index']);
Route::get('status/{id}', [StatusController::class, 'show']);
Route::post('status', [StatusController::class, 'store']);
Route::put('status/{id}', [StatusController::class, 'update']);
Route::delete('status/{id}', [StatusController::class, 'destroy']);


    //routes for tasks
Route::get('/tasks', [TasksController::class, 'index']);
Route::get('/tasks/{id}', [TasksController::class, 'show']);
Route::post('/tasks', [TasksController::class, 'store']);
Route::put('/tasks/{id}', [TasksController::class, 'update']);
Route::delete('/tasks/{id}', [TasksController::class, 'destroy']);

//routes for user tasks
Route::get('/user-tasks', [UserTasksController::class, 'index']);
Route::get('/user-tasks/{id}', [UserTasksController::class, 'show']);
Route::post('/user-tasks', [UserTasksController::class, 'store']);
Route::put('/user-tasks/{id}', [UserTasksController::class, 'update']);
Route::delete('/user-tasks/{id}', [UserTasksController::class, 'destroy']);
});

});