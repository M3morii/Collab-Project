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
    Schema::create('classes', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->text('description')->nullable();
        $table->foreignId('teacher_id')
              ->nullable()
              ->constrained('users')
              ->onDelete('cascade');
        $table->integer('kkm_score');
        $table->string('academic_year');
        $table->enum('semester', ['1', '2']);
        $table->enum('status', ['active', 'inactive'])->default('active');
        $table->softDeletes();
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
        Schema::dropIfExists('classes');
    }
};
