<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('library.folders_table', 'library_folders'), function (Blueprint $table) {
            $table->id();
            $table->morphs('owner');
            $table->foreignId('library_collection_id')
                ->constrained(config('library.collections_table', 'library_collections'))
                ->cascadeOnDelete();
            $table->foreignId('parent_folder_id')
                ->nullable()
                ->constrained(config('library.folders_table', 'library_folders'))
                ->nullOnDelete();
            $table->string('name');
            $table->json('metadata')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('library.folders_table', 'library_folders'));
    }
};
