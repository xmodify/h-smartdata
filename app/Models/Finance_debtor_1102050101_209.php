<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Finance_debtor_1102050101_209 extends Model
{
    use HasFactory;

    protected $table = 'finance_debtor_1102050101_209'; 
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
        'pp',
        'ppfs',
        'debtor', 
        'status', 
        'receive',
        'repno',
        'debtor_lock',             
    ];
    public $timestamps = false;   
}
