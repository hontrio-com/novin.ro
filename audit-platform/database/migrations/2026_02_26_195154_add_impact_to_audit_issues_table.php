<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('audit_issues', function (Blueprint $table) {
            $table->string('impact')->nullable()->after('affected_url');
            // ex: "SEO,UX" | "Legal" | "Conversie,UX"
        });
    }

    public function down(): void {
        Schema::table('audit_issues', function (Blueprint $table) {
            $table->dropColumn('impact');
        });
    }
};