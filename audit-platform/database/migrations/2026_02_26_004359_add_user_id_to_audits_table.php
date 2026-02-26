<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('audits', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete()->after('id');
        });

        if (!Schema::hasColumn('audits', 'ai_summary')) {
            Schema::table('audits', function (Blueprint $table) {
                $table->longText('ai_summary')->nullable();
                $table->timestamp('ai_summary_generated_at')->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::table('audits', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\User::class);
            $table->dropColumn('user_id');
        });
    }
};