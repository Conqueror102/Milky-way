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
        // What admins have changed on the home page. The defaults live in
        // config/site_content.php; a row only exists once something differs.
        Schema::create('site_content_entries', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->text('value')->nullable();
            // For photos: Cloudinary's public id, so a replaced upload can be deleted,
            // and the description that goes with it.
            $table->string('public_id')->nullable();
            $table->string('alt')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_content_entries');
    }
};
