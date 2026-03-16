<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = ['name',];

    public function teacherSubjects()
    {
        return $this->hasMany(TeacherSubject::class, 'subject_id');
    }
}