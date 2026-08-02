<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up() {
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->constrained('enrollments')->onDelete('cascade');
            $table->decimal('score', 5, 2)->nullable();
            $table->string('letter_grade')->nullable();
            $table->decimal('gpa', 3, 2)->nullable();
            $table->foreignId('graded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->unique(['enrollment_id']);
        });
    }
    public function down() { Schema::dropIfExists('grades'); }
};