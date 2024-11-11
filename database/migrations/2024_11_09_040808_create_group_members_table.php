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
    Schema::create('group_members', function (Blueprint $table) {
        $table->id();
        $table->foreignId('group_id')->constrained('groups');      // Relasi ke kelompok
        $table->foreignId('student_id')->constrained('users');     // Relasi ke siswa
        $table->boolean('is_leader')->default(false);              // Status ketua
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
        Schema::dropIfExists('group_members');
    }
};
