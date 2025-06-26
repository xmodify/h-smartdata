<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Finance_debtor_1102050101_402 extends Model
{
    use HasFactory;

    protected $table = 'finance_debtor_1102050101_402'; 
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
        'kidney',
        'other',
        'debtor',
        'status', 
        'receive',
        'repno',
        'debtor_lock',               
    ];
    public $timestamps = false;   
}
