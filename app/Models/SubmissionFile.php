<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SubmissionFile extends Model
{
    protected $fillable = ['task_submission_id', 'path', 'original_name', 'mime_type'];

    public function submission()
    {
        return $this->belongsTo(TaskSubmission::class);
    }
}