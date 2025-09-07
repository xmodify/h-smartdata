<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Debtor_1102050102_801 extends Model
{
    use HasFactory;

    protected $table = 'debtor_1102050102_801'; 
    protected $primaryKey = 'vn';
    protected $fillable = [  
        'vn',
        'hn', 
        'an', 
        'cid',
        'ptname',
        'vstdate', 
        'vsttime',  
        'pttype',
        'hospmain',
        'hipdata_code',
        'pdx',
        'income',
        'rcpt_money',
        'lgo',
        'kidney',
        'pp',
        'other',
        'debtor', 
        'debtor_change',
        'status', 
        'receive',
        'repno',
        'debtor_lock',             
    ];
    public $timestamps = false;   
}
