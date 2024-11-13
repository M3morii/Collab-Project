<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::create('task_groups', function (Blueprint $table) {
        $table->id();
        $table->foreignId('task_id')
              ->constrained()
              ->onDelete('cascade');
        $table->string('name');
        $table->text('description')->nullable();
        $table->integer('max_members');
        $table->foreignId('created_by')
              ->constrained('users')
              ->onDelete('cascade');
        $table->timestamps();
    });
}
    public function down()
    {
        Schema::dropIfExists('task_groups');
    }
};