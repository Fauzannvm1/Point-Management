<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class TaskImage extends Model
{
    protected $fillable = ['task_id', 'path', 'original_name'];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}