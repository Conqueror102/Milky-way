<?php

namespace App\Support;

/**
 * The social accounts linked in the footer. The networks and their starting links
 * live in config/milkyway.php; what an admin saves at /admin/socials is kept as
 * one site content entry, "socials", holding each network's link and whether it shows.
 */
class Socials
{
    public const KEY = 'socials';

    public function __construct(protected SiteContent $content) {}

    /**
     * Every network with its current link and whether it shows.
     *
     * @return array<string, array{label: string, url: string, show: bool}>
     */
    public function all(): array
    {
        /** @var array<string, array{label: string, url: string, show: bool}> $networks */
        $networks = config('milkyway.socials', []);

        $saved = json_decode((string) ($this->content->entries()[self::KEY]['value'] ?? ''), true);
        $saved = is_array($saved) ? $saved : [];

        return collect($networks)->map(fn (array $network, string $key) => [
            'label' => $network['label'],
            'url' => is_string($saved[$key]['url'] ?? null) ? $saved[$key]['url'] : $network['url'],
            'show' => is_bool($saved[$key]['show'] ?? null) ? $saved[$key]['show'] : $network['show'],
        ])->all();
    }

    /**
     * The accounts to link, in order: ticked to show and with somewhere to go.
     *
     * @return list<array{key: string, label: string, url: string}>
     */
    public function visible(): array
    {
        return array_values(collect($this->all())
            ->filter(fn (array $network) => $network['show'])
            ->map(fn (array $network, string $key) => ['key' => $key, 'label' => $network['label'], 'url' => $this->link($key, $network['url'])])
            ->filter(fn (array $network) => $network['url'] !== '')
            ->all());
    }

    /**
     * WhatsApp falls back to the business number when no link is given.
     */
    protected function link(string $key, string $url): string
    {
        if ($url === '' && $key === 'whatsapp' && filled(config('milkyway.whatsapp.number'))) {
            return 'https://wa.me/'.config('milkyway.whatsapp.number');
        }

        return $url;
    }
}
