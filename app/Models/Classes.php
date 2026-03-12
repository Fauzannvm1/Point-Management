<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classes extends Model
{
    protected $table = 'classes'; // 'classes' is a reserved word, explicit table needed

    protected $fillable = ['name', 'major_id'];

    public function major()
    {
        return $this->belongsTo(Major::class, 'major_id');
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'class_id');
    }

    public function teacherSubjects()
    {
        return $this->hasMany(TeacherSubject::class, 'class_id');
    }
}