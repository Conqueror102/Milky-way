<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * The places the shop delivers to, each with its own fee. They used to be a list of
 * lines under Site content → Business details, with fees kept beside them by name, so
 * whatever an admin saved there moves in here and the old entries go.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('delivery_areas', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            // Whole naira. Null means delivery there is arranged with the shopper.
            $table->unsignedInteger('fee')->nullable();
            // Drawn on the home page's delivery orbits.
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        $saved = DB::table('site_content_entries')->pluck('value', 'key');

        $names = collect(preg_split('/\R/', (string) ($saved['business.delivery_areas'] ?? "Lagos\nOgun\nAbuja\nAccra, Ghana")) ?: [])
            ->map(fn (string $name) => trim($name))
            ->filter()
            ->unique()
            ->values();

        DB::table('delivery_areas')->insert($names->map(fn (string $name, int $index) => [
            'name' => $name,
            'fee' => is_numeric($saved["delivery_fee.{$name}"] ?? null) ? (int) $saved["delivery_fee.{$name}"] : null,
            'is_featured' => $index < 4,
            'sort_order' => $index,
            'created_at' => now(),
            'updated_at' => now(),
        ])->all());

        DB::table('site_content_entries')
            ->where('key', 'business.delivery_areas')
            ->orWhere('key', 'like', 'delivery_fee.%')
            ->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_areas');
    }
};
