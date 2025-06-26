<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User_team extends Model
{
    use HasFactory;

    protected $connection = 'backoffice';
    protected $table = 'hrd_team_list';

}
