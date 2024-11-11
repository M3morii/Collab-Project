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
        $table->foreignId('task_id')->constrained('tasks');        // Untuk tugas
        $table->foreignId('group_id')->constrained('groups');      // Dari kelompok
        $table->text('description')->nullable();                   // Deskripsi jawaban
        $table->enum('status', ['submitted', 'late'])->default('submitted'); // [TAMBAHAN]
        $table->timestamp('submitted_at');                         // Waktu submit
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
