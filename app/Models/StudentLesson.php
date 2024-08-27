<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentLesson extends Model
{
    use HasFactory;

    protected $table='student_lessons';

    protected $fillable = [
        'lesson_date',
        'lesson_start_time',
        'lesson_end_time',
        'has_attended'
    ];

}
