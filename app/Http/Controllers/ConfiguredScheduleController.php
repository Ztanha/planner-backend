<?php

namespace App\Http\Controllers;

use App\Helpers\XaraHelper;
use App\Models\Schedule;
use Illuminate\Http\Request;

class ConfiguredScheduleController extends Controller
{
    function create(Request $request) {
        $user = XaraHelper::getAuthenticatedUser($request->sessionId);

        if(empty($user)){
            return XaraHelper::makeAPIError('Please login to proceed.');
        }
        $plansIds = json_decode($request->plansIds);

        foreach ($plansIds as $planId) {
            $schedule= Schedule::create([
                'plan_id'=>$planId,
                'user_id'=>$user->id,
                'date'=>$request->date,
            ]);
        }

        if(!empty($schedule)) {
            return XaraHelper::makeAPISuccess('');
        }
    }
}
