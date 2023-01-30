<?php

namespace App\Http\Controllers;

use App\Helpers\XaraHelper;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ScheduleController extends Controller
{
    function create(Request $request) {
        $user = XaraHelper::getAuthenticatedUser($request->sessionId);
        if(empty($user)){
            return XaraHelper::makeUnauthenticatedError();
        }

        if(!empty($request->scheduleId)) { //update schedule
            $schedule = $user->schedules()->where('id',$request->scheduleId);
            $schedule->update([
               'done'   =>$request->done,
            ]);
            return XaraHelper::makeAPISuccess('done');
        } else {
            $messages = [
                'required'    => ':attribute is empty!',
                'string'      => 'The :attribute must be in a string type.',
//                'array'      => 'The :attribute must be in a array form.'
            ];
            $check= Validator::make($request->all(),
                ['plansIds' =>'required','array',
//                    'type' => 'max:255|string',
                    'date' => 'required|integer']
                ,$messages);

            if($check->fails()){
                return XaraHelper::makeAPIError($check->messages());
            }
            $plansIds =json_decode($request->plansIds);
            foreach ($plansIds as $planId) {
                $id =$this->save($user->id,'r',$planId,$request->date);
            }

            if(!empty($id)) {
                return XaraHelper::makeAPISuccess($id);
            }
        }
    }

    function save($userId,$type,$plan_id,$date) {
        $schedule = Schedule::create([
            'user_id'    => $userId,
            'plan_id'    => $plan_id,
            'date'       => strtotime(date("Y-m-d",$date)),
            'type'       => $type
        ]);
        return $schedule->id;
    }

    function delete(Request $request) {
        $user = XaraHelper::getAuthenticatedUser($request->sessionId);

        if(empty($user)){
            return XaraHelper::makeUnauthenticatedError();
        }
        $schedule = $user->schedules()->where('id',$request->scheduleId)->first();
        if(!empty($schedule)) {
            if($schedule->type ==='r') {
                $user->plans()->where('id',$schedule->plan_id)->delete();
            }
            $schedule->delete();
            return XaraHelper::makeAPISuccess();
        }
    }

    function syncAutoSchedules ($user,$date) {
        $autoSchedules = $user->autoSchedules()->get();
        if(!empty($autoSchedules)) {

            $schedule = Schedule::query()->where('date',$date)
                ->where('type','a')
                ->first(); //shows that autoSchedules have already saved
            if(!empty($schedule)) {
                return ;
            }

            $weekday = date('N', $date);
            foreach ($autoSchedules as $schedule) {
                $days = strval($schedule->weekdays);
                $num = substr($days,$weekday-1,1);
                if($num === '1'){
                    $this->save($user->id,'a',$schedule->plan_id,$date);
                }
            }
        }
    }

    function get(Request $request) {
        $user = XaraHelper::getAuthenticatedUser($request->sessionId);
        $temp=[];
        $date = strtotime(date("Y-m-d",$request->date));
        $date2 = strtotime(date("Y-m-d",$request->date2));
        if(empty($user)){
            return XaraHelper::makeUnauthenticatedError();
        }
        $messages = [
            'required'    => ':attribute is empty!',
            'integer'      => 'The :attribute must be in a integer type.',
        ];
        $check= Validator::make($request->all(),
            [   'date' => 'required|integer',
                'date2' => 'nullable|integer']
            ,$messages);

        if($check->fails()){
            return XaraHelper::makeAPIError($check->messages());
        }

        if(!empty($request->date2)) {
            $schedules = $user->schedules()
                ->whereBetween('date',[$date2,$date])
                ->get();
        }else{

            $this->syncAutoSchedules($user,$request->date);

            $schedules = $user->schedules()->where('date',$date)->get();
        }

        foreach($schedules as $schedule) {
            $plan = $user->plans()->where('id',$schedule->plan_id)->first();

            if(!empty($plan)) {
                $task = $user->tasks()->where('id',$plan->task_id)->first();
                $temp[] = [
                    'subject'   =>  $task->subject,
                    'start'     =>  $plan->start,
                    'end'       =>  $plan->end,
                    'id'        =>  $schedule->id,
                    'type'      =>  $schedule->type,
                    'done'      =>  $schedule->done,
                    'plan_id'   =>  $plan->id,
                ];
            }
        }
        return XaraHelper::makeAPISuccess($temp);

    }
}
