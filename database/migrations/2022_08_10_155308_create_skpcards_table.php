<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSkpcardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('skpcards', function (Blueprint $table) {
            $table->id();
            $table->string('cid');
            $table->string('name');
            $table->date('birthday');
            $table->string('address');
            $table->string('phone');
            $table->date('buy_date');
            $table->date('ex_date');
            $table->string('price');
            $table->string('rcpt');
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
        Schema::dropIfExists('skpcards');
    }
}
