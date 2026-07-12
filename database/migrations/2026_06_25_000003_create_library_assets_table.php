<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(config('library.assets_table', 'library_assets'), function (Blueprint $table) {
            $table->id();
            $table->morphs('owner');
            $table->foreignId('library_collection_id')
                ->nullable()
                ->constrained(config('library.collections_table', 'library_collections'))
                ->nullOnDelete();
            $table->foreignId('library_folder_id')
                ->nullable()
                ->constrained(config('library.folders_table', 'library_folders'))
                ->nullOnDelete();
            // Open vocabulary: note/image/offering/profile now; snippet/link/video later, no migration.
            $table->string('kind')->default('note');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->longText('body')->nullable();
            $table->json('metadata')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('library_collection_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('library.assets_table', 'library_assets'));
    }
};
