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
            $table->string('title');                                   // Judul tugas
            $table->text('description');                               // Deskripsi tugas
            $table->foreignId('group_id')->constrained('groups');      // Untuk kelompok
            $table->foreignId('created_by_id')->constrained('users');  // Dibuat guru
            $table->dateTime('due_date');                             // Deadline
            $table->enum('status', ['pending', 'completed'])->default('pending'); // [TAMBAHAN]
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
