<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('participatesin', function (Blueprint $table) {
            $table->boolean('on_break')->default(false)->after('is_ready');
            $table->timestamp('last_break_end_at')->nullable()->after('on_break');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('participatesin', function (Blueprint $table) {
            $table->dropColumn(['on_break', 'last_break_end_at']);
        });
    }
};
