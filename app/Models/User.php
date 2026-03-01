<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function student()
    {
        return $this->hasOne(Student::class);
    }

    public function teacher()
    {
        return $this->hasOne(Teacher::class);
    }

    public function parent()
    {
        return $this->hasOne(ParentModel::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin'; 
    }

    public function isTeacher(): bool
    { 
        return $this->role === 'teacher'; 
    }

    public function isStudent(): bool
    { 
        return $this->role === 'student'; 
    }
    
    public function isParent(): bool
    { 
        return $this->role === 'parent';
    }
}