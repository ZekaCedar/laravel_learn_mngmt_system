<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentLesson;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class DataTablesController extends Controller
{

    public function getStudentData()
    {
        // $data = Student::select('*');
        // $student = Student::find(1);
        // Check if a student with ID 1 exists
        if (Student::find(1) === null) {
            // Create a student with ID 1
            Student::create([
                'first_name' => 'Brian',
                'last_name' => 'Lee',
                'dob' => Carbon::create(1997, 8, 27),
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now(),
                'status' => 'active',
                // 'created_at' => Carbon::now(),
                // 'updated_at' => Carbon::now(),
            ]);
        }

        $student = Student::find(1);
        
        // return DataTables::of($student)->make(true);
        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }

        // Return data as JSON
        return response()->json($student);
    }

    public function generateSchedule(Request $request)
    {

        // Clear existing data from the table
        StudentLesson::truncate(); // This will delete all existing records in the table

        // Validate the incoming request
        $validated = $request->validate([
            'dayOfWeek' => 'required|string',
            'formattedTime' => 'required|string', // e.g., '10am'
        ]);

        // Extract parameters from the request
        $dayOfWeek = $validated['dayOfWeek'];
        $timeSlot = $validated['formattedTime'];

        // Get the current date and the start of the current week
        $now = Carbon::now();
        $startDate = $now->copy()->startOfWeek()->next($dayOfWeek);

        // Combine the date with the provided time slot
        $startDateTime = $startDate->format('Y-m-d') . ' ' . $timeSlot;

        // Create a Carbon instance with the specified date and time
        $startDate = Carbon::createFromFormat('Y-m-d H:i:s', $startDateTime);

        // Define the duration of the lesson (1 hour)
        $duration = 1; // in hours

        // Generate and insert 10 lesson schedules
        $schedules = [];
        for ($i = 0; $i < 10; $i++) {
            // Calculate end time by adding duration to start time
            $endDate = $startDate->copy()->addHours($duration);

            $schedules[] = [
                'lesson_date' => $startDate->format('Y-m-d'),
                'lesson_start_time' => $startDate->format('H:i:s'),
                'lesson_end_time' => $endDate->format('H:i:s'),
                'has_attended'=>false
            ];

            // Move to the next week
            $startDate->addWeek();
        }

        // Optionally, insert the schedules into the database
        foreach ($schedules as $schedule) {
            StudentLesson::create($schedule);
        }
        
        // return DataTables::of($schedules)->make(true);
        return response()->json(['message' => 'Lesson schedules generated successfully.']);
    }

    public function getSchedule(){
        $data = StudentLesson::select('*');
        
        return DataTables::of($data)->make(true);
    }


}
