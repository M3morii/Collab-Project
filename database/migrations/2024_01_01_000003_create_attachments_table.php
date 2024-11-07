<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('attachments', function (Blueprint $table) {
            $table->id();
            $table->string('filename');
            $table->string('file_path');
            $table->string('file_type');
            $table->bigInteger('file_size')->nullable();
            $table->foreignId('task_id')->constrained()->onDelete('cascade');
            $table->foreignId('uploaded_by_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes(); // Menambahkan soft delete
        });
    }

    public function down()
    {
        Schema::dropIfExists('attachments');
    }
};