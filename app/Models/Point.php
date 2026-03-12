<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Point extends Model
{
    protected $fillable = ['student_id', 'teacher_id', 'category_id', 'point', 'note'];

    public function student() {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function teacher() {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function category() {
        return $this->belongsTo(PointCategory::class);
    }
}
