<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('audit_issues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('audit_id')->constrained()->cascadeOnDelete();
            $table->string('category');
            // technical | seo | legal | eeeat | content | ux
            $table->string('severity');
            // critical | warning | info
            $table->string('title');
            $table->text('description');
            $table->text('suggestion')->nullable();
            $table->string('affected_url')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('audit_issues');
    }
};