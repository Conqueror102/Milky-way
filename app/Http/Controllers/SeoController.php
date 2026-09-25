<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Response;

class SeoController extends Controller
{
    /**
     * Crawler rules. Served from a route rather than a static file so the Sitemap
     * line can carry the real domain from APP_URL.
     */
    public function robots(): Response
    {
        $lines = [
            'User-agent: *',
            'Allow: /',
            'Disallow: /dashboard',
            'Disallow: /settings',
            'Disallow: /login',
            'Disallow: /register',
            'Disallow: /forgot-password',
            'Disallow: /reset-password',
            'Disallow: /cart',
            'Disallow: /checkout',
            'Disallow: /orders',
            '',
            'Sitemap: '.$this->siteUrl().'/sitemap.xml',
        ];

        return response(implode("\n", $lines)."\n", 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }

    /**
     * The homepage, then one entry per product page.
     */
    public function sitemap(): Response
    {
        $productUrls = Product::query()
            ->active()
            ->ordered()
            ->pluck('slug')
            ->map(fn (string $slug) => $this->siteUrl().'/products/'.$slug);

        return response()
            ->view('sitemap', ['urls' => [$this->siteUrl().'/', ...$productUrls->all()]])
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    private function siteUrl(): string
    {
        return rtrim((string) config('app.url'), '/');
    }
}
