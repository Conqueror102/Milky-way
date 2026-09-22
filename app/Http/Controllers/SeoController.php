<?php

namespace App\Http\Controllers;

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
            '',
            'Sitemap: '.$this->siteUrl().'/sitemap.xml',
        ];

        return response(implode("\n", $lines)."\n", 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }

    /**
     * A single-page site, so a single entry.
     */
    public function sitemap(): Response
    {
        return response()
            ->view('sitemap', ['urls' => [$this->siteUrl().'/']])
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    private function siteUrl(): string
    {
        return rtrim((string) config('app.url'), '/');
    }
}
