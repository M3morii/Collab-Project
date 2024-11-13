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
    Schema::create('submissions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('task_id')
              ->constrained()
              ->onDelete('cascade');
        $table->foreignId('user_id')
              ->constrained()
              ->onDelete('cascade');
        $table->foreignId('task_group_id')
              ->nullable()
              ->constrained('task_groups')
              ->onDelete('cascade');
        $table->text('content')->nullable();
        $table->decimal('score', 5, 2)->nullable();
        $table->text('feedback')->nullable();
        $table->enum('status', ['draft', 'submitted', 'graded'])
              ->default('draft');
        $table->timestamp('submitted_at')->nullable();
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
        Schema::dropIfExists('submissions');
    }
};
