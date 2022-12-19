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
        Schema::create('__files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_owner')->constrained('users');
            $table->string('name');
            $table->enum('state', ['check-out', 'check-in'])->default('check-out');
            $table->date('upload_date')->default(date("Y-m-d H:i:s"));
            $table->date('update_date')->default(date("Y-m-d H:i:s"));
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
        Schema::dropIfExists('file_groups');
    }
};
