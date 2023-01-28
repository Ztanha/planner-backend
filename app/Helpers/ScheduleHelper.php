<?php
namespace App\Helpers;

class ScheduleHelper
{
    static function makeScheduleData($schedules, $user)
    {
        $temp = [];
        foreach ($schedules as $schedule) {
            $plan = $user->plans()->where('id', $schedule->plan_id)->first();
            if (!empty($plan)) {
                $task = $user->tasks()->where('id', $plan->task_id)->first();
                if($schedule->type = 'a') {
                    $temp[] = [
                        'subject' => $task->subject,
                        'start' => $plan->start,
                        'end' => $plan->end,
                        'id' => $schedule->id,
                        'type' => $schedule->type,
                        'weekdays'=>$schedule->weekdays,
                        'plan_id' => $plan->id,
                    ];
                }else{
                    $temp[] = [
                        'subject' => $task->subject,
                        'start' => $plan->start,
                        'end' => $plan->end,
                        'id' => $schedule->id,
                        'type' => $schedule->type,
                        'done' => $schedule->done,
                        'plan_id' => $plan->id,
                    ];
                }
            }
        }
        return $temp;
    }
}