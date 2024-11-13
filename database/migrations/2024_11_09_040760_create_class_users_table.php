<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::create('class_users', function (Blueprint $table) {
        $table->id();
        $table->foreignId('class_id')
              ->constrained()
              ->onDelete('cascade');
        $table->foreignId('user_id')
              ->constrained()
              ->onDelete('cascade');
        $table->enum('role', ['teacher', 'student']);
        $table->enum('status', ['active', 'inactive'])->default('active');
        $table->timestamps();
        
        $table->unique(['class_id', 'user_id']);
    });
}
    public function down()
    {
        Schema::dropIfExists('class_users');
    }
};