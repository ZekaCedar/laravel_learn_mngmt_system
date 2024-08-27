<?php

namespace App\Http\Controllers;

use App\Http\Resources\ScheduleResource;
use App\Models\StudentLesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ScheduleController extends Controller
{
    public function update(Request $request, StudentLesson $schedule)
    {
        // validation
        $validator = Validator::make($request->all(),[
            'lesson_date' => 'required|string|max:255',
            'lesson_start_time' => 'required|string|max:255',
            'lesson_end_time' => 'required|string|max:255',
            'has_attended' => 'required'
        ]);

        if($validator->fails())
        {
            return response()->json([
                'message' => 'All fields are mandatory',
                'error' => $validator->messages(),
            ], 422);
        }

        // updating the record
        $schedule->update([
            'lesson_date' => $request->lesson_date,
            'lesson_start_time' => $request->lesson_start_time,
            'lesson_end_time' => $request->lesson_end_time,
            'has_attended' => $request->has_attended,
        ]);

        return response()->json([
            'message' => 'Schedule Updated Successfully',
            'data' => new ScheduleResource($schedule),
        ], 200);

    }
}
