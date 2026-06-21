<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->boolean('show_public_analysis')
                ->default(false)
                ->after('is_required')
                ->index();
        });
    }

    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropIndex(['show_public_analysis']);
            $table->dropColumn('show_public_analysis');
        });
    }
};
