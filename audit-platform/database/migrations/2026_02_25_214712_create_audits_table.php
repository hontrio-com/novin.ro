<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('audits', function (Blueprint $table) {
            $table->id();
            $table->string('url');
            $table->string('email');
            $table->string('status')->default('pending');
            // pending | processing | completed | failed
            
            // Scoruri
            $table->unsignedTinyInteger('score_total')->nullable();
            $table->unsignedTinyInteger('score_technical')->nullable();
            $table->unsignedTinyInteger('score_seo')->nullable();
            $table->unsignedTinyInteger('score_legal')->nullable();
            $table->unsignedTinyInteger('score_eeeat')->nullable();
            $table->unsignedTinyInteger('score_content')->nullable();
            $table->unsignedTinyInteger('score_ux')->nullable();
            
            // Rezultate
            $table->string('public_token')->unique()->nullable();
            $table->string('pdf_path')->nullable();
            $table->json('raw_data')->nullable(); // date brute crawler
            
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('audits');
    }
};