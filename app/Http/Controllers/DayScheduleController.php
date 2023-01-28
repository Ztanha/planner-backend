<?php
//
//namespace App\Http\Controllers;
//
//use App\Helpers\ScheduleHelper;
//use App\Helpers\XaraHelper;
//use App\Models\DaySchedule;
//
//use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Validator;
//
//class DayScheduleController extends Controller
//{
//
//    function create(Request $request) {
//
//        $user = XaraHelper::getAuthenticatedUser($request->sessionId);
//        if(empty($user)){
//            return XaraHelper::makeAPIError('Please login to proceed');
//        }
//
//        if(!empty($request->scheduleId)) {
//
//        }
//        $messages = [
//            'required'    => 'We need to know the :attribute of the task!',
//            'max'         => 'The :attribute must be less than :max',
//        ];
//        $check= Validator::make($request->all(),
//            ['scheduleId' =>'required',
//                'type' => 'string|required',
//                'done' => 'boolean|nullable',
//            ]
//            ,$messages);
//
//        if($check->fails()){
//            return XaraHelper::makeAPIError($check->messages());
//        }
//
//        $daySch = DaySchedule::create([
//            'schedule_id' => $request->scheduleId,
//            'type' => $request->type,
//            'done' =>$request->done
//        ]);
//        if(!empty($daySch)) {
//            return XaraHelper::makeAPISuccess($daySch->id);
//        }
//    }
//
//    function get(Request $request) {
//        $user = XaraHelper::getAuthenticatedUser($request->sessionId);
//        if(!empty($user)){
//            $schedules = $user->daySchedules()->where('date',$request->day)->get();
//            if($schedules->isNotEmpty()){
//                return XaraHelper::makeAPISuccess($schedules);
//            }else{
//                ScheduleHelper::makeDailySchedule($request->day,$user);
//            }
//        }else{
//            return XaraHelper::makeAPIError('Unauthorized attempt');
//        }
//    }
//    function delete(Request $request) {
//        $user = XaraHelper::getAuthenticatedUser($request->sessionId);
//        if(empty($user)){
//            return XaraHelper::makeAPIError('Please login to proceed');
//        }
//        $user->daySchedules()->where('id',$request->id)->delete();
//
//        return XaraHelper::makeAPISuccess();
//    }
//}
