<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('page_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('audit_id')->constrained()->cascadeOnDelete();
            $table->string('url');
            $table->unsignedSmallInteger('status_code')->nullable();
            $table->unsignedInteger('load_time_ms')->nullable();
            $table->string('title')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('h1')->nullable();
            $table->unsignedSmallInteger('images_total')->default(0);
            $table->unsignedSmallInteger('images_missing_alt')->default(0);
            $table->unsignedSmallInteger('broken_links_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('page_data');
    }
};