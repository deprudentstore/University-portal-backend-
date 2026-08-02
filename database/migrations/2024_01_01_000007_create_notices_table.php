<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up() {
        Schema::create('notices', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->foreignId('author_id')->constrained('users')->onDelete('cascade');
            $table->enum('audience', ['all', 'students', 'faculty', 'admin'])->default('all');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }
    public function down() { Schema::dropIfExists('notices'); }
};