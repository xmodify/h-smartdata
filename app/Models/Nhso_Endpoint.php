<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nhso_Endpoint extends Model
{
    use HasFactory;

    protected $table = 'nhso_endpoint'; 
    protected $primaryKey = 'transId';
    protected $fillable = [  
        'transId',
        'hmain', 
        'hname', 
        'personalId',
        'patientName', 
        'addrNo', 
        'moo',  
        'moonanName',
        'tumbonName',
        'amphurName',
        'changwatName',
        'birthdate',
        'tel',
        'mainInscl',
        'mainInsclName',
        'subInscl',
        'subInsclName',
        'claimStatus',
        'patientType', 
        'claimCode',  
        'claimType',
        'claimTypeName',
        'claimDate', 
        'createDate',     
        'updateBy',
        'updateDate',   
        'mainInsclWithName',           
    ];
    public $timestamps = false;   
}
