<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('page_data', function (Blueprint $table) {
            $table->string('page_type')->default('other')->after('url');
            // home | contact | about | services | blog | category | product | checkout | faq | legal | other
        });
    }

    public function down(): void {
        Schema::table('page_data', function (Blueprint $table) {
            $table->dropColumn('page_type');
        });
    }
};