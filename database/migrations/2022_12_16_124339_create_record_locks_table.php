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
        Schema::create('record_locks', function (Blueprint $table) {
            $table->id();
            $table->integer('lockable_id');
            $table->string('lockable_type');
            $table->foreignId('id_user')->constrained('users');
            $table->date('locked_at');
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
        Schema::dropIfExists('record_locks');
    }
};
