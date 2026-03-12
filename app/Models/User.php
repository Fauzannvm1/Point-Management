<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
// use App\Models\Point;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Get the student profile linked to this user
    public function student()
    {
        return $this->hasOne(Student::class, 'student_id');
    }

    // Get the teacher profile linked to this user
    public function teacher()
    {
        return $this->hasOne(Teacher::class, 'student_id');
    }

    /**
    * @return int|float
    */
    // Total points for this user (goes through student profile)
    public function totalPoints()
    {
        return $this->receivedPoints()->sum('point');
    }
    
    // Points received as a student
    public function receivedPoints()
    {
        return $this->hasMany(Point::class, 'student_id');
    }

    // Parent → children (through parent_student pivot)
    public function children()
    {
        return $this->belongsToMany(User::class, 'parent_student', 'parent_id', 'student_id');
    }
}
