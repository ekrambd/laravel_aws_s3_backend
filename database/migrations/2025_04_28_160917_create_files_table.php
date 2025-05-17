<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('bucket_id');
            $table->integer('folder_id')->nullable();
            $table->string('title')->unique();
            $table->string('file_key')->nullable();
            $table->string('temp_file_path')->nullable();
            $table->string('extension')->nullable();
            $table->string('file_size')->nullable();
            $table->string('bucket_url')->nullable();
            $table->string('storage_class');
            $table->enum('status', ['Public', 'Private']);
            $table->enum('file_importance', ['Low', 'Medium', 'High']);
            $table->enum('upload_status', ['Pending', 'Uploaded']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
