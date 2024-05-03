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
        Schema::create('contents', function (Blueprint $table) {
            $table->id();
            $table->string('name_bn');
            $table->string('name_en')->nullable();
            $table->bigInteger('category_id')->unsigned();
            $table->bigInteger('subject_id')->unsigned();
            $table->bigInteger('chapter_id')->unsigned();
            $table->string('raw_file')->nullable();
            $table->string('transcoded_file_path')->nullable();
            $table->string('compressed_file_path')->nullable();
            $table->string('thumbnail')->nullable();
            $table->enum('content_type', ['Video', 'Document', 'Ebook'])->default('Video');
            $table->boolean('status')->nullable()->default(0);
            $table->bigInteger('created_by')->default(0);
            $table->softDeletesTz($column = 'deleted_at', $precision = 0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contents');
    }
};
