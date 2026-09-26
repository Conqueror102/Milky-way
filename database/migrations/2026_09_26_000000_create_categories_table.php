<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The categories the shop launched with. Those with an image_path show a
     * photo card (the bundled /images/categories/<key>.jpg and .webp).
     *
     * @var list<array{name: string, description: string, image_path: string|null}>
     */
    private array $launchCategories = [
        ['name' => 'Skincare', 'description' => 'Cleansers, moisturisers, creams, serums, soaps and scrubs for every routine.', 'image_path' => '/images/categories/skincare'],
        ['name' => 'Beauty & Cosmetics', 'description' => 'Makeup, beauty essentials, accessories and the tools to apply them.', 'image_path' => '/images/categories/beauty'],
        ['name' => 'Health & Beauty', 'description' => 'Selected health and personal-care products to sit alongside your beauty shelf.', 'image_path' => null],
        ['name' => 'Sexual Enhancement', 'description' => 'Products to support intimacy and libido, for individuals and couples.', 'image_path' => null],
        ['name' => 'Body Enhancement', 'description' => 'Body-enhancement and personal-care products for a complete regimen.', 'image_path' => '/images/categories/body'],
        ['name' => 'Spa & Massage', 'description' => 'Products and essentials for spas, massage businesses and professionals.', 'image_path' => '/images/categories/spa'],
        ['name' => 'Wholesale', 'description' => 'Bulk purchasing for retailers, resellers, salons, spas and beauty businesses.', 'image_path' => '/images/categories/wholesale'],
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->string('image_url')->nullable();
            $table->string('image_public_id')->nullable();
            $table->string('image_path')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        $now = now();
        $names = collect($this->launchCategories)->pluck('name');

        // Keep any category already used by a product, after the launch ones.
        $extra = DB::table('products')->distinct()->pluck('category')
            ->reject(fn ($name) => $names->contains($name))
            ->map(fn ($name) => ['name' => $name, 'description' => null, 'image_path' => null]);

        $rows = collect($this->launchCategories)->concat($extra)->values()
            ->map(fn (array $row, int $position) => [...$row, 'sort_order' => $position, 'created_at' => $now, 'updated_at' => $now]);

        DB::table('categories')->insert($rows->all());
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
