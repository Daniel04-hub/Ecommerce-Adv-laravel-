<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $role
 * @property-read \App\Models\Vendor|null $vendor
 * @method bool hasRole(string|array $roles)
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        // Keep backward-compatible role column
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function vendor()
    {
        return $this->hasOne(Vendor::class);
    }

    public function customerProfile()
    {
        return $this->hasOne(CustomerProfile::class);
    }
}
