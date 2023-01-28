<?php

namespace App\Http\Controllers;

use App\Helpers\XaraHelper;
use App\Models\AutoSchedule;
use App\Models\Schedule;
use App\Models\Plan;
use App\Models\Task;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    function create(Request $request){
        $user = XaraHelper::getAuthenticatedUser($request->sessionId);
        if(empty($user)){
            return XaraHelper::makeAPIError('Unauthorized attempt.');
        }
        try{
            if(!empty($request->taskId)) {
                $task = $user->tasks()->where('id',$request->taskId);

                if(!empty($task)) {
                    if(!empty($request->desc )){
                        $task->update([
                            'description' => $request->desc,
                            'subject'=>$request->subject,
                        ]);
                        return XaraHelper::makeAPISuccess();
                    }else{
                        $task->update([
                            'subject'=>$request->subject,
                        ]);
                        return XaraHelper::makeAPISuccess();
                    }
                }
            }else{
                $messages = [
                    'unique'      => 'There already is a task with this :attribute',
                    'required'    => 'We need to know the :attribute of the task!',
                    'max'         => 'The :attribute must be less than :max',
                    'string'      => 'The :attribute must be in a string type.'
                ];
                $check= Validator::make($request->all(),
                    ['subject'=>'required|max:255|unique:tasks,user_id|string',
                        'desc' => 'max:255|nullable|string']
                    ,$messages);

                if($check->fails()){
                    return XaraHelper::makeAPIError($check->messages());
                }
                $task= Task::create([
                    'subject'=>$request->subject,
                    'description'=>$request->desc,
                    'user_id'=>$user->id,
                ]);
                if(!empty($task)){
                    return XaraHelper::makeAPISuccess(['id'=>$task->id]);
                }
            }
        }catch(Exception $e){
            return XaraHelper::makeAPIError($e);
        }
    }

    function get(Request $request) {
        $user = XaraHelper::getAuthenticatedUser($request->sessionId);
        if(empty($user)){
            return XaraHelper::makeUnauthenticatedError();
        }

        if(!empty($request->id)){
            $task = $user->tasks()->where('id',$request->id)->first();
            if(!empty($task)) {
                return XaraHelper::makeAPISuccess($task);
            }else{
                return XaraHelper::makeAPIError('not found');
            }
        }else {
            return XaraHelper::makeAPISuccess($user->tasks()->get());
        }
    }
    function delete(Request $request){
        $user = XaraHelper::getAuthenticatedUser($request->sessionId);
        if(empty($user)){
            return XaraHelper::makeAPIError('login to proceed!');
        }
        $task = $user->tasks()->where('id',$request->id);
        if(!empty($task)) {
            $plansQuery = Plan::query()->where('task_id',$request->id);
            $plans =$plansQuery->get();

            foreach ($plans as $plan) {
                $cShs = Schedule::query()->where('plan_id',$plan->id);
                $aShs = AutoSchedule::query()->where('plan_id',$plan->id);
                foreach ($cShs as $sch) {
                    $sch->delete();
                }
                foreach ($aShs as $sch) {
                    $sch->delete();
                }
            }
            $plansQuery->delete();
            $task->delete();

            return XaraHelper::makeAPISuccess('');
        }else{
            return XaraHelper::makeAPIError('not found');
        }
    }
}
