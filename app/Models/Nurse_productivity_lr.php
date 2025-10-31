<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nurse_productivity_lr extends Model
{
    use HasFactory;

    protected $table = 'nurse_productivity_lrs';

    protected $fillable = [
        'report_date',
        'shift_time',
        'opd_normal',
        'opd_high',
        'patient_all',
        'convalescent',
        'moderate_ill',
        'semi_critical_ill',
        'critical_ill',
        'patient_hr',
        'nurse_oncall',
        'nurse_partime',
        'nurse_fulltime',
        'nurse_hr',
        'productivity',
        'hhpuos',
        'nurse_shift_time',
        'recorder',
        'note',
    ];
}
