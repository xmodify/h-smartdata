<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Drugcat_aipn extends Model
{
    use HasFactory;

    protected $table = 'drugcat_aipn'; 
    protected $fillable = [
        'id',
        'Hospdcode',
        'Prodcat',  
        'Tmtid', 
        'Specprep', 
        'Genname',
        'Tradename',
        'Dsfcode', 
        'Dosefm',
        'Strength',
        'Content',
        'UnitPrice',
        'Distrb',
        'Manuf',
        'Ised',
        'Ndc24',
        'Packsize',
        'Packprice',
        'Updateflag',
        'DateChange',
        'DateUpdate',
        'DateEffect',
        'DateChk',
        'Rp',
        'stm_filename',
    ];
    public $timestamps = false;   
}
