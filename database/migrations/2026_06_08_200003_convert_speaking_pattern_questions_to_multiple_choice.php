<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('questions')
            ->where('question_type', 'speaking_pattern')
            ->update(['question_type' => 'multiple_choice']);
    }

    public function down(): void
    {
        // Cannot reliably restore speaking_pattern rows.
    }
};
