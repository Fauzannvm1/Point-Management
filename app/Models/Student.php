<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = ['user_id', 'class_id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function submissions()
    {
        return $this->hasMany(TaskSubmission::class);
    }

    public function points()
    {
        return $this->hasMany(Point::class, 'student_id');
    }

    public function parents()
    {
        return $this->belongsToMany(Parents::class, 'parent_student', 'student_id', 'parent_id');
    }

    public function totalPoints(): int
    {
        return $this->points()->sum('point');
    }

    public function monthPoints(): int
    {
        return $this->points()
                    ->whereMonth('point_date', now()->month)
                    ->whereYear('point_date', now()->year)
                    ->sum('point');
    }
}