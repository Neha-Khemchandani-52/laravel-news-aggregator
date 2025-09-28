<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source_id')->constrained()->cascadeOnDelete();
            $table->string('fingerprint')->unique(); // dedupe hash
            $table->string('title')->index();
            $table->text('description')->nullable();
            $table->longText('content')->nullable();
            $table->text('url')->nullable();
            $table->text('url_to_image')->nullable();
            $table->foreignId('author_id')->nullable()->constrained('authors')->nullOnDelete();
            $table->timestamp('published_at')->nullable()->index();
            $table->json('raw')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
