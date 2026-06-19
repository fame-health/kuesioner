<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('questionnaire_id')->constrained()->cascadeOnDelete();
            $table->text('question_text');
            $table->string('question_type');
            $table->boolean('is_required')->default(false);
            $table->unsignedInteger('order_number')->default(1);
            $table->timestamps();

            $table->index(['questionnaire_id', 'order_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
