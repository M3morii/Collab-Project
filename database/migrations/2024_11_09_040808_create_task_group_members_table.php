<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::create('task_group_members', function (Blueprint $table) {
        $table->id();
        $table->foreignId('task_group_id')
              ->constrained('task_groups')
              ->onDelete('cascade');
        $table->foreignId('user_id')
              ->constrained('users')
              ->onDelete('cascade');
        $table->timestamps();
        
        $table->unique(['task_group_id', 'user_id']);
    });
}
    public function down()
    {
        Schema::dropIfExists('task_group_members');
    }
};