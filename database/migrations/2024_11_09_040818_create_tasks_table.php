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
    Schema::create('tasks', function (Blueprint $table) {
        $table->id();
        $table->foreignId('class_id')
              ->constrained()
              ->onDelete('cascade');
        $table->string('title');
        $table->text('description');
        $table->dateTime('start_date');
        $table->dateTime('deadline');
        $table->enum('task_type', ['individual', 'group']);
        $table->integer('max_score');
        $table->decimal('weight_percentage', 5, 2);
        $table->enum('status', ['draft', 'published', 'closed'])
              ->default('draft');
        $table->foreignId('created_by')
              ->constrained('users')
              ->onDelete('cascade');
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
        Schema::dropIfExists('tasks');
    }
};
