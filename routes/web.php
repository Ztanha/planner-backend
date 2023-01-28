<?php

use App\Http\Controllers\AutoScheduleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TaskController;
use \App\Http\Controllers\PerformanceController;
use \App\Http\Controllers\ConfiguredScheduleController;
use \App\Http\Controllers\ScheduleController;
use \App\Http\Controllers\PlanController;
use App\Helpers\ScheduleHelper;
use Illuminate\Support\Facades\Route;


//Route::middleware(['first','second'])->group(['prefix'=>'api/'], function (){

//});


Route::group(['prefix'=>'api/','middleware'=> \App\Http\Middleware\Cors::class],function (){
    Route::group(['prefix'=>'/','middleware'=> \App\Http\Middleware\EnsureSessionIsValid::class],function (){

    });
    Route::options('users/',[UserController::class, function(){} ]);
    Route::options('tasks/',[TaskController::class, function(){} ]);
    Route::options('autoSchedules/',[TaskController::class, function(){} ]);

    Route::put('users/',[UserController::class,'create']);
    Route::post('users/get',[UserController::class,'get']);
    Route::post('users/',[UserController::class,'login']);

    Route::put('tasks/',[TaskController::class,'create']);
    Route::post('tasks/',[TaskController::class,'get']);
    Route::delete('tasks/',[TaskController::class,'delete']);

    Route::post('schedules/',[ScheduleController::class,'get']);
    Route::put('schedules/',[ScheduleController::class,'create']);
    Route::delete('schedules/',[ScheduleController::class,'delete']);

    Route::put('autoSchedules/',[AutoScheduleController::class,'create']);
    Route::post('autoSchedules/',[AutoScheduleController::class, 'get']);
    Route::delete('autoSchedules/',[AutoScheduleController::class, 'delete']);

    Route::put('plans/',[PlanController::class,'create']);
    Route::delete('plans/',[PlanController::class,'delete']);
//    Route::get('performance/:date',[PerformanceController::class,'getDailyPerformance']);
//    Route::get('performance/:taskId',[PerformanceController::class,'getPerformanceByTask']);
//    Route::get('performance/:date',[PerformanceController::class,'getWeeklyPerformance']);
//    Route::get('performance/:date',[PerformanceController::class,'getMonthlyPerformance']);

});
