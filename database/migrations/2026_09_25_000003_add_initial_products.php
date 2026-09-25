<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Moves the catalogue that used to be hard-coded in the shop section into the products
 * table, so every environment (including production, which only runs migrations) starts
 * with the same stock. Prices were never listed, so each product starts as "price on
 * request" until one is set.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $products = [
            ['name' => 'Vaseline Body Oils', 'category' => 'Skincare', 'type' => 'Body Oil', 'description' => 'Vaseline Cocoa Radiant, Blue Seal Aloe Fresh and Healthy Bright Daily Brightening.', 'image_path' => '/images/categories/skincare2/vaseline'],
            ['name' => 'COSRX Alpha-Arbutin Serum', 'category' => 'Skincare', 'type' => 'Serum', 'description' => 'The Alpha-Arbutin 2% Discoloration Care serum, Tranexamic Acid 3% + Niacinamide 5%, 50ml.', 'image_path' => '/images/categories/skincare2/cosrx'],
            ['name' => 'Alpha Skin Care Renewal Lotion', 'category' => 'Skincare', 'type' => 'Body Lotion', 'description' => 'Alpha Skin Care Renewal Body Lotion, 12% Glycolic AHA, 12oz.', 'image_path' => '/images/categories/skincare2/alphaskincare'],
            ['name' => 'Olay Dark Spot Body Lotion', 'category' => 'Skincare', 'type' => 'Body Lotion', 'description' => 'Olay Dark Spot Correcting Body Lotion, AHA & Vitamin C + Niacinamide, 17 fl oz.', 'image_path' => '/images/categories/skincare2/olay'],
            ['name' => 'Aveeno Body Oil Mist', 'category' => 'Skincare', 'type' => 'Body Oil', 'description' => 'Aveeno Daily Moisturizing Body Oil Mist with oat and jojoba oil, 200ml.', 'image_path' => '/images/categories/skincare2/aveeno'],
            ['name' => 'Dr Teal\'s Citrus Body Care', 'category' => 'Skincare', 'type' => 'Body Care', 'description' => 'Dr Teal\'s Body Wash, Body Lotion and Shea Sugar Scrub, citrus and vitamin C.', 'image_path' => '/images/categories/skincare2/drteals'],
            ['name' => 'Anua Niacinamide Serum', 'category' => 'Health & Beauty', 'type' => 'Serum', 'description' => 'Anua Niacinamide 10% + TXA4 Serum, 30ml.', 'image_path' => '/images/categories/health/anua'],
            ['name' => 'Niiracell Glutathione', 'category' => 'Health & Beauty', 'type' => 'Supplement', 'description' => 'Niiracell Glutathione 90,000mg dietary supplement, 60 capsules.', 'image_path' => '/images/categories/health/niiracell'],
            ['name' => 'Beefar Probiotic Gummies', 'category' => 'Health & Beauty', 'type' => 'Supplement', 'description' => 'Beefar Women\'s Probiotic + Slippery Elm gummies, 60 count.', 'image_path' => '/images/categories/health/beefar'],
            ['name' => 'NeoCell Collagen Peptides', 'category' => 'Health & Beauty', 'type' => 'Supplement', 'description' => 'NeoCell Grassfed Collagen Peptides + Vitamin C, 360 caplets.', 'image_path' => '/images/categories/health/neocell'],
            ['name' => 'Ginseng Six Treasures Tea', 'category' => 'Health & Beauty', 'type' => 'Tea', 'description' => 'Kanglai Ginseng Six Treasures Tea, 250g (10g x 25 packs).', 'image_path' => '/images/categories/health/ginseng'],
            ['name' => 'Glutax Glutathione Injection', 'category' => 'Health & Beauty', 'type' => 'Injectable', 'description' => 'Glutax 2000000GX glutathione injection kit. For professional administration.', 'image_path' => '/images/categories/health/glutax'],
            ['name' => 'Menopause Tea', 'category' => 'Health & Beauty', 'type' => 'Tea', 'description' => 'Herbal tea to support mood and relieve menopause symptoms, 10 tea bags.', 'image_path' => '/images/categories/teas/menopause'],
            ['name' => 'Flat Tummy Tea', 'category' => 'Health & Beauty', 'type' => 'Tea', 'description' => 'Wins Town 28 Days Detox Flat Tummy Tea, eases bloating and digestion, 28 teabags.', 'image_path' => '/images/categories/teas/flattummy'],
            ['name' => 'Grazer Herbal Detox Tea', 'category' => 'Health & Beauty', 'type' => 'Tea', 'description' => 'Grazer Herbal Detox Tea, 30 teabags, 100g.', 'image_path' => '/images/categories/teas/grazerdetox'],
            ['name' => 'Double Root Coffee', 'category' => 'Health & Beauty', 'type' => 'Coffee', 'description' => 'Double Root Coffee, 100% Arabica.', 'image_path' => '/images/categories/teas/doubleroot'],
            ['name' => 'Ginseng Six Treasures Tea (Green Nature)', 'category' => 'Health & Beauty', 'type' => 'Tea', 'description' => 'Green Nature Ginseng Six Premium Health Treasures Tea.', 'image_path' => '/images/categories/teas/ginsenggn'],
            ['name' => 'Erection Tea', 'category' => 'Sexual Enhancement', 'type' => 'Tea', 'description' => 'Tebillah Sam Erection Tea, boosts sex drive and libido. 30 tea bags.', 'image_path' => '/images/categories/sexual/tea'],
            ['name' => 'Men Power Gummies', 'category' => 'Sexual Enhancement', 'type' => 'Supplement', 'description' => 'Favret H&B Men Power, horny goat weed, coffee and mushroom, 8000mg, 60 gummies.', 'image_path' => '/images/categories/sexual/menpower'],
            ['name' => 'X Power Coffee for Men', 'category' => 'Sexual Enhancement', 'type' => 'Coffee', 'description' => 'Wins Town X Power Coffee for men, NAFDAC registered. 16 sachets.', 'image_path' => '/images/categories/sexual/coffee'],
            ['name' => 'Juliet Eve Booty Bloom', 'category' => 'Body Enhancement', 'type' => 'Shake', 'description' => 'Booty Bloom curve-enhancer shake, smooth butterscotch, with Pueraria Mirifica.', 'image_path' => '/images/categories/body2/bootybloom'],
            ['name' => 'Beckon Hip & Butt Oils', 'category' => 'Body Enhancement', 'type' => 'Oil', 'description' => 'Beckon Hip Up Oil, Maca Oil and Hip Butt Oil range for women.', 'image_path' => '/images/categories/body2/beckon'],
            ['name' => 'Ultimate Maca Capsules', 'category' => 'Body Enhancement', 'type' => 'Supplement', 'description' => 'Ultimate Maca Capsules, 7500mg, 120 veggy capsules, made for butt/hips.', 'image_path' => '/images/categories/body2/macacapsules'],
            ['name' => 'Duozi Hip & Butt Drink', 'category' => 'Body Enhancement', 'type' => 'Drink', 'description' => 'Duozi Quick Effect Hip/Butt Enlargement Drink with Maca Plus, 10 x 30ml bottles.', 'image_path' => '/images/categories/body2/duozi'],
            ['name' => 'Percussion Massage Gun', 'category' => 'Spa & Massage', 'type' => 'Massage', 'description' => 'Deep-tissue massage gun with 4 head attachments, plus a free keyholder.', 'image_path' => '/images/categories/spa2/massagegun'],
            ['name' => 'Mooyam Massage Oil Set', 'category' => 'Spa & Massage', 'type' => 'Massage Oil', 'description' => 'Lavender, Frankincense and Sore Muscle massage oils, 8 fl oz each.', 'image_path' => '/images/categories/spa2/mooyam'],
            ['name' => 'Wooden Spine Roller', 'category' => 'Spa & Massage', 'type' => 'Massage Tool', 'description' => 'Handheld wooden roller for back and muscle massage.', 'image_path' => '/images/categories/spa2/woodenroller'],
        ];

        $now = now();

        DB::table('products')->insert(array_map(fn (array $product, int $index) => $product + [
            'slug' => Str::slug($product['name']),
            'price' => null,
            'is_active' => true,
            'sort_order' => ($index + 1) * 10,
            'created_at' => $now,
            'updated_at' => $now,
        ], $products, array_keys($products)));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('products')->whereNull('image_url')->where('image_path', 'like', '/images/categories/%')->delete();
    }
};
