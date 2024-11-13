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
        $table->foreignId('group_id')
              ->constrained()
              ->onDelete('cascade');  // Jika group dihapus, member juga terhapus
        $table->foreignId('student_id')
              ->constrained('users')
              ->onDelete('cascade');  // Jika user dihapus, membership juga terhapus
        $table->boolean('is_leader')->default(false);
        $table->timestamps();

        // Memastikan student hanya bisa jadi member di group yang sama sekali
        $table->unique(['group_id', 'student_id']);
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
