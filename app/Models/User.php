<?php

namespace App\Models;

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

    /**
     * RELACIÓN MUCHOS A MUCHOS CON ROLES
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_role');
    }

    /**
     * VERIFICAR SI EL USUARIO TIENE UN ROL
     */
    /**
 * VERIFICAR SI EL USUARIO TIENE UN ROL
 */
public function hasRole(string|array $roleSlugs): bool
{
    // Asegurarse que siempre sea array
    $roleSlugs = is_array($roleSlugs) ? $roleSlugs : [$roleSlugs];

    // Verificar existencia en la relación
    return $this->roles()->whereIn('slug', $roleSlugs)->exists();
}

    /**
     * VERIFICAR SI EL USUARIO TIENE UN PERMISO
     */
    public function hasPermission(string $permissionSlug): bool
    {
        return $this->roles()->whereHas('permissions', function ($q) use ($permissionSlug) {
            $q->where('slug', $permissionSlug);
        })->exists();
    }

    /**
 * ASIGNAR UN ROL AL USUARIO
 */
public function assignRole(string $roleName, string $roleSlug): void
{
    $role = \App\Models\Role::where('name', $roleName)
                             ->where('slug', $roleSlug)
                             ->first();

    if ($role) {
        // syncWithoutDetaching evita eliminar otros roles que tenga
        $this->roles()->syncWithoutDetaching([$role->id]);
    }
}
}
