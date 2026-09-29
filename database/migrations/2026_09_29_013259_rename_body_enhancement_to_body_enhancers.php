<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * The category "Body Enhancement" is now "Body Enhancers": on the category, its
 * products, and in any home page text an admin saved with the old wording.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->rename('Body Enhancement', 'Body Enhancers', [
            'Body-enhancement' => 'Body enhancers',
            'body-enhancement' => 'body enhancers',
            'body enhancement' => 'body enhancers',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->rename('Body Enhancers', 'Body Enhancement', [
            'body enhancers' => 'body enhancement',
        ]);
    }

    /**
     * @param  array<string, string>  $wording  other spellings to swap in descriptions and saved text
     */
    private function rename(string $from, string $to, array $wording): void
    {
        DB::table('categories')->where('name', $from)->update(['name' => $to]);
        DB::table('products')->where('category', $from)->update(['category' => $to]);

        $replacements = [$from => $to, ...$wording];

        foreach (['categories' => ['id', 'description'], 'site_content_entries' => ['key', 'value']] as $table => [$key, $column]) {
            foreach (DB::table($table)->whereNotNull($column)->get([$key, $column]) as $row) {
                $text = str_replace(array_keys($replacements), array_values($replacements), $row->{$column});

                if ($text !== $row->{$column}) {
                    DB::table($table)->where($key, $row->{$key})->update([$column => $text]);
                }
            }
        }
    }
};
