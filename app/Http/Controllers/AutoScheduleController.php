<?php

namespace App\Http\Controllers;

use App\Helpers\ScheduleHelper;
use App\Helpers\XaraHelper;
use App\Models\AutoSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Runner\Exception;

class AutoScheduleController extends Controller
{
    function create(Request $request){

        $user = XaraHelper::getAuthenticatedUser($request->sessionId);

        if(empty($user)){
            return XaraHelper::makeUnauthenticatedError();
        }

        $messages = [
            'required'    => ':attribute is null!',
            'string'      => ':attribute must be in a format of string'
        ];

        if(!empty($request->scheduleId)) {


            $check= Validator::make($request->all(),
                ['weekdays'    => 'required',
                    'planId'  => 'nullable|string',
                ]
                ,$messages);

            if($check->fails()){
                return XaraHelper::makeAPIError($check->messages());
            }

            $schedule = $user->autoSchedules()
                ->where('id',$request->scheduleId)
                ->first();
            try {

                if(!empty($request->planId)) {

                    $schedule->update([
                        'weekdays'=>$request->weekdays,
                    ]);
                }else{
                    $schedule->update([
                        'weekdays'=>$request->weekdays,
                        'plan_id'=>$request->planId,
                    ]);
                }
                return XaraHelper::makeAPISuccess();

            }catch(Exception $e){
                return XaraHelper::makeAPIError($e->getMessage());
            }
        }else{

            $check= Validator::make($request->all(),
                [   'weekdays'    => 'required|string',
                    'planId'  => 'required',
                ]
                ,$messages);

            if($check->fails()){
                return XaraHelper::makeAPIError($check->messages());
            }
            try {
                $schedule = AutoSchedule::create([
                    'plan_id'=>$request->planId,
                    'weekdays'=>$request->weekdays,
                    'user_id'=>$user->id,
                ]);
                return XaraHelper::makeAPISuccess(['id' => $schedule->id]);

            }catch(Exception $e){
                return XaraHelper::makeAPIError($e->getMessage());
            }

        }

    }

    function get(Request $request) {
        $user = XaraHelper::getAuthenticatedUser($request->sessionId);

        if(empty($user)){
            return XaraHelper::makeUnauthenticatedError();
        }

        try {
            $schedules = $user->autoSchedules();
            if(empty($schedules)) throw new Exception('');

            if(!empty($request->sIds)){
                $sIds = json_decode($request->sIds);
                $schedules = $schedules->whereIn('id',$sIds);
            }
            $schedules =$schedules->get();
            $temp = ScheduleHelper::makeScheduleData($schedules, $user);

            return XaraHelper::makeAPISuccess($temp);

        }catch(Exception $e) {

            return XaraHelper::makeAPIError($e->getMessage());
        }
    }

    function delete(Request $request) {
        $user = XaraHelper::getAuthenticatedUser($request->sessionId);

        if(empty($user)){
            return XaraHelper::makeUnauthenticatedError();
        }
        try {
            if(!empty($request->scheduleId)) {


                DB::beginTransaction();
                try {
                    $schedule = $user->autoSchedules()->where('id',$request->scheduleId)->firstOrFail();

                    DB::table('plans')
                        ->where('id',$schedule->plan_id)
                        ->where('user_id',$user->id)
                        ->delete();

                    DB::table('auto_schedules')
                        ->where('id',$request->scheduleId)
                        ->delete();

                    DB::commit();
                    return XaraHelper::makeAPISuccess();

                }catch (Exception $e) {
                    DB::rollback();
                    throw $e;
                }

            }else{

                throw new Exception('ScheduleId is null');
            }
        }catch(Exception $e){
            return XaraHelper::makeAPIError($e);
        }
    }

}
