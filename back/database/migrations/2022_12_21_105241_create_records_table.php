<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('records', function (Blueprint $table) {
            $table->id();
            $table->string('ci')->nullable();
            $table->string('code')->nullable();
            $table->date('dateIni')->nullable();
            $table->date('dateEnd')->nullable();
            $table->string('codeTarget')->nullable();
            $table->string('name')->nullable();
            $table->date('birthday')->nullable();
            $table->string('phone')->nullable();
            $table->string('schedule')->nullable();
            $table->integer('amount')->nullable();
            $table->string('type')->nullable();
            $table->string('observation')->nullable();
            $table->date('date')->nullable();
            $table->time('time')->nullable();
            $table->string('days')->nullable();
            $table->foreignId('card_id')->nullable()->constrained();
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
        Schema::dropIfExists('records');
    }
};
