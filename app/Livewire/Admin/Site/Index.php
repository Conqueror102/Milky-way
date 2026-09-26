<?php

namespace App\Livewire\Admin\Site;

use App\Support\SiteContent;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Site content')]
class Index extends Component
{
    public function render(SiteContent $content): View
    {
        $sections = collect($content->sections())->map(function (array $section, string $key) use ($content) {
            $fields = array_keys($content->fields($key));

            return [
                'key' => $key,
                'label' => $section['label'],
                'description' => $section['description'],
                'fields' => count($fields),
                'changed' => count(array_filter($fields, fn (string $field) => $content->isCustom($field))),
                'preview' => "/images/admin/sections/{$key}.jpg",
            ];
        });

        return view('livewire.admin.site.index', ['sections' => $sections]);
    }
}
