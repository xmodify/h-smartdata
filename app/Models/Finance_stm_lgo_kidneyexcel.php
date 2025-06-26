<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Finance_stm_lgo_kidneyexcel extends Model
{
    use HasFactory;

    protected $table = 'finance_stm_lgo_kidneyexcel'; 
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'no',
        'repno', 
        'hn', 
        'cid',
        'pt_name',
        'dep',  
        'datetimeadm',
        'compensate_total',
        'note',        
    ];
    public $timestamps = false;   
}
