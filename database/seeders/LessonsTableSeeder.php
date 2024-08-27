<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class LessonsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define the start date (e.g., the first Friday of the schedule)
        $startDate = Carbon::create('2024', '08', '30'); // Adjust this date if needed
        
        // Define the number of weeks
        $weeks = 10;

        // Define lesson times
        $startTime = '09:00:00';
        $endTime = '10:00:00';

        $lessons = [];

        // Loop through each week to generate Friday dates
        for ($i = 0; $i < $weeks; $i++) {
            $lessonDate = $startDate->copy()->addWeeks($i);

            $lessons[] = [
                'lesson_date' => $lessonDate->format('Y-m-d'),
                'lesson_start_time' => $startTime,
                'lesson_end_time' => $endTime,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Insert data into the database
        DB::table('student_lessons')->insert($lessons);
    }
}
