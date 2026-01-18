<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    // RELACIÓN MUCHOS A MUCHOS CON USERS
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_role');
    }

    // RELACIÓN MUCHOS A MUCHOS CON PERMISSIONS
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permission');
    }
}

