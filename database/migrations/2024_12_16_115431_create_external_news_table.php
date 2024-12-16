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
        Schema::create('external_news', function (Blueprint $table) {
            $table->id();
            $table->string('source_id')->nullable();
            $table->string('source_name');
            $table->string('author')->nullable();
            $table->string('title');
            $table->text('description');
            $table->string('url');
            $table->string('url_to_image')->nullable();
            $table->timestamp('published_at');
            $table->text('content')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('external_news');
    }
};