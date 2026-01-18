<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stm_ofc_kidney extends Model
{
    use HasFactory;

    protected $table = 'stm_ofc_kidney'; 
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'stm_filename',
        'round_no',
        'stm_type',
        'hcode', 
        'hname',
        'acc_period',
        'sys',
        'station', 
        'hreg',
        'hn',
        'invno',
        'dttran',
        'vstdate',
        'vsttime',
        'amount',
        'paid',
        'rid',
        'hdflag',
        'receive_no',
        'receipt_date',
        'receipt_by',
    ];
    public $timestamps = false;   
}
