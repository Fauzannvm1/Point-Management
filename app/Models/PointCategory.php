<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PointCategory extends Model
{
    protected $fillable = ['name', 'type', 'default_points', 'description', 'color',];

    public function points() {
        return $this->hasMany(Point::class, 'category_id');
    }
    public function tasks() 
    {
        return $this->hasMany(Task::class, 'point_category_id');
    }
}
//unused