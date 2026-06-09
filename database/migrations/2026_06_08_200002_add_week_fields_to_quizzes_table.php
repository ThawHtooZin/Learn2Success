<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->foreignId('week_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->unsignedInteger('time_limit_seconds')->nullable()->after('mark_per_question');
            $table->unsignedInteger('sort_order_in_week')->default(0)->after('time_limit_seconds');
        });
    }

    public function down(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('week_id');
            $table->dropColumn(['time_limit_seconds', 'sort_order_in_week']);
        });
    }
};
