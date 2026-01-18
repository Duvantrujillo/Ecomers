<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = [
        'name',
        'slug',
    ];

    // RELACIÓN MUCHOS A MUCHOS CON ROLES
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permission');
    }
}
