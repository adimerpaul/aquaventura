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
        Schema::create('cards', function (Blueprint $table) {
            $table->id();
            $table->string('ci')->nullable();
            $table->string('code')->nullable();
            $table->date('dateIni')->nullable();
            $table->date('date')->nullable();
            $table->date('dateEnd')->nullable();
            $table->string('codeTarget')->nullable();
            $table->string('name')->nullable();
            $table->date('birthday')->nullable();
            $table->string('phone')->nullable();
            $table->string('schedule')->nullable();
            $table->integer('amount')->nullable();
            $table->string('type')->nullable();
            $table->integer('number')->nullable();
            $table->string('observation')->nullable();
            $table->string('days')->nullable();
            $table->string('photo')->nullable()->default('avatar.png');
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
        Schema::dropIfExists('cards');
    }
};
