<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Finance_debtor_1102050101_504 extends Model
{
    use HasFactory;

    protected $table = 'finance_debtor_1102050101_504'; 
    protected $primaryKey = 'an';
    protected $fillable = [
        'an',   
        'vn',
        'hn', 
        'cid',
        'ptname',
        'regdate', 
        'regtime',
        'dchdate',
        'dchtime',      
        'pttype',
        'hospmain',
        'hipdata_code',
        'pdx',
        'adjrw',
        'income',
        'rcpt_money',
        'other',
        'debtor',
        'charge_date',
        'charge_no',
        'charge',
        'receive_date', 
        'receive_no',  
        'receive',
        'repno',
        'status', 
        'debtor_lock',                    
    ];
    public $timestamps = false;   
}
