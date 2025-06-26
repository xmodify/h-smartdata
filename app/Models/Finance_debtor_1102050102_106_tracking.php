<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Finance_debtor_1102050102_106_tracking extends Model
{
    use HasFactory;

    protected $table = 'finance_debtor_1102050102_106_tracking'; 
    protected $primaryKey = 'tracking_id';
    protected $fillable = [  
        'vn',      
        'tracking_date',
        'tracking_type',
        'tracking_no',
        'tracking_officer', 
        'tracking_note',                
    ];
   
}
