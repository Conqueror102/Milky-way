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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('category')->index();
            $table->string('type')->nullable();
            $table->text('description')->nullable();

            // Whole naira. Null means "price on request": the product shows, but can't be carted.
            $table->unsignedInteger('price')->nullable();

            // A hosted image (e.g. Cloudinary) wins over the bundled one when both are set.
            $table->string('image_url')->nullable();
            // Cloudinary's public id for image_url, so the admin can delete the upload.
            $table->string('image_public_id')->nullable();
            // Path under public/ without extension; a .webp and a .jpg sit side by side.
            $table->string('image_path')->nullable();

            $table->unsignedInteger('stock')->default(0);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
