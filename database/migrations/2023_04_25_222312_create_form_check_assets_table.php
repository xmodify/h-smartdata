<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFormCheckAssetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('form_check_assets', function (Blueprint $table) {
            $table->id();
            $table->string('depart');
            $table->string('hr_check');
            $table->string('asset1');
            $table->string('asset2');
            $table->string('asset3');
            $table->string('asset4');
            $table->string('asset5');
            $table->string('asset6');
            $table->string('asset7');
            $table->string('asset8');
            $table->string('asset9');
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
        Schema::dropIfExists('form_check_assets');
    }
}
