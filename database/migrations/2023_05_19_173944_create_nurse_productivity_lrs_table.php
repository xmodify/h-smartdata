<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNurseProductivityLrsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nurse_productivity_lrs', function (Blueprint $table) {
            $table->id();
            $table->date('report_date');
            $table->string('shift_time');
            $table->integer('opd_normal');
            $table->integer('opd_high');
            $table->integer('patient_all');
            $table->integer('convalescent');
            $table->integer('moderate_ill');      
            $table->integer('semi_critical_ill');     
            $table->integer('critical_ill');
            $table->float('patient_hr');
            $table->integer('nurse_oncall');
            $table->integer('nurse_partime');
            $table->integer('nurse_fulltime');
            $table->float('nurse_hr');
            $table->float('productivity'); 
            $table->float('hhpuos');
            $table->float('nurse_shift_time');    
            $table->string('recorder'); 
            $table->string('note');     
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nurse_productivity_lrs');
    }
}
