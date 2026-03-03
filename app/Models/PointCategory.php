<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PointCategory extends Model
{
    protected $fillable = ['name', 'description'];

    public function points() {
        return $this->belongsTo(Point::class, 'category_id');
    }    
}
