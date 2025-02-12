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
        // Xóa bảng cũ nếu tồn tại
        Schema::dropIfExists('stories');

        // Tạo lại bảng với cấu trúc mới
        Schema::create('stories', function (Blueprint $table) {
            $table->id();
            $table->string('uuid', 255)->unique();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->json('background')->nullable();
            $table->json('text')->nullable();
            $table->timestamp('expired_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stories');
    }
}; 