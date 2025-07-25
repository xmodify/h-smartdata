<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Finance_stm_ofc extends Model
{
    use HasFactory;

    protected $table = 'finance_stm_ofc'; 
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'repno',  
        'no', 
        'hn', 
        'an',
        'cid',
        'pt_name', 
        'datetimeadm',
        'vstdate',
        'vsttime',
        'datetimedch',
        'dchdate',
        'dchtime',
        'projcode',
        'adjrw',
        'charge',
        'act',
        'receive_room',
        'receive_instument',
        'receive_drug',
        'receive_treatment',
        'receive_car',
        'receive_waitdch',
        'receive_other',
        'receive_total',
        'stm_filename',
    ];
    public $timestamps = false;   
}
