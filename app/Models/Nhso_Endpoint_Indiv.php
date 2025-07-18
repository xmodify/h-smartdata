<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nhso_Endpoint_Indiv extends Model
{
    use HasFactory;

    protected $table = 'nhso_endpoint_indiv'; 
    protected $primaryKey = 'id';
    protected $fillable = [  
        'cid',
        'firstName',          
        'lastName',          
        'mainInscl',          
        'mainInsclName',          
        'subInscl',          
        'subInsclName',
        'serviceDateTime', 
        'vstdate', 
        'sourceChannel', 
        'claimCode',  
        'claimType',                        
    ];
    public $timestamps = false;   
}
