<?php

namespace App\Http\Controllers;

use App\Helpers\XaraHelper;
use App\Models\Plan;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PlanController extends Controller
{
    function get(Request $request) {
        $user = XaraHelper::getAuthenticatedUser($request->sessionId);
        if(empty($user)) {
            return XaraHelper::makeAPIError('Unauthenticated attempt!');
        }
        if(!empty($request->planId)) {
            $plan = $user->plans()->where('id',$request->planId);
            if(!empty($plan)){
                return XaraHelper::makeAPISuccess($plan);
            }else{
                return XaraHelper::makeAPIError('not found');
            }
        }else{
            return XaraHelper::makeAPIError('planId is empty');
        }
    }
    function delete(Request $request) {
        $user = XaraHelper::getAuthenticatedUser($request->sessionId);
        if(empty($user)) {
            return XaraHelper::makeAPIError('Unauthenticated attempt!');
        }
        if(!empty($request->planId)) {
            $user->plans()->where('id',$request->planId)->delete();
        }else{
            return XaraHelper::makeAPIError('planId is empty');
        }
    }

    function create(Request $request){
        $plansIds=[];

        $user = XaraHelper::getAuthenticatedUser($request->sessionId);
        if(empty($user)) {
            return XaraHelper::makeAPIError('Unauthenticated attempt!');
        }
        $taskId = json_decode($request->taskId);
        $start = json_decode($request->start);
        $end = json_decode($request->end);
        try {
            if($start > $end) {
                throw new Exception('Time sequence is not correct!');
            }

            if(!empty($request->planId)) {

                $plan = $user->plans()->where('id',$request->planId);

                if(!empty($taskId )) {

                    $plan->update([
                        'start'  =>  $start,
                        'end'   =>  $end,
                        'task_id' => $taskId,
                    ]);
                }else{

                    $plan->update([
                        'start'  =>  $request->start,
                        'end'   =>  $request->end,
                    ]);
                }
                return XaraHelper::makeAPISuccess('');

            }else{ //when it's a new Plan

                $messages = [
                    'required'    => 'taskIds is null!',
                ];
                $check= Validator::make($request->all(),
                    ['tasksIds'    => 'required']
                    ,$messages);
                if($check->fails()){
                    return XaraHelper::makeAPIError($check->messages());
                }

                $tasksIds = json_decode($request->tasksIds);
                foreach ($tasksIds as $taskId) {

                    $plan = Plan::create([
                        'task_id'   => $taskId,
                        'start'     => $start,
                        'end'       => $end,
                        'user_id'   => $user->id
                    ]);
                    $plansIds[]=$plan->id;
                }
                return XaraHelper::makeAPISuccess($plansIds);
            }

        }catch (Exception $e){
            return XaraHelper::makeAPIError($e->getMessage());
        }

//        if(!empty($plan)){
//            return XaraHelper::makeAPISuccess($plansIds);
//        }else{
//            return XaraHelper::makeAPIError('Something went wrong!');
//        }

    }
}
