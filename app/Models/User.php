<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\User_Access;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $connection = 'backoffice';
    protected $table = 'users';

    public function hasAccessRole($role)
    {
        return User_Access::where('username', $this->username)
                        ->where('role', $role)
                        ->exists();
    }

    public function hasAccessHrims($hrims)
    {
        return User_Access::where('username', $this->username)
                        ->where('h_rims', $hrims)
                        ->exists();
    }
           
     protected $fillable = [
        'name',
        'email',
        'username',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // public function user_team(): HasMany
    // {
    //     return $this->hasMany(User_team::class, 'PERSON_ID', 'PERSON_ID')
    //                 ->where('TEAM_ID', '16');
    // }

}
