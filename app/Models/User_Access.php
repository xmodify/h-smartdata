<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User_Access extends Model
{
    use HasFactory;

    protected $table = 'users_access';
    protected $primaryKey = 'username';
    protected $fillable = [
        'username',
        'ptname',
        'role',
        'del_product',
        'h_rims',
        'hn_plus'
    ];
}
