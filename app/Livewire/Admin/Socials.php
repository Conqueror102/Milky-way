<?php

namespace App\Livewire\Admin;

use App\Models\SiteContentEntry;
use App\Support\SiteContent;
use App\Support\Socials as SocialLinks;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * Link the store's social accounts in the footer. A network shows only when it
 * is ticked and has a link, so accounts the store doesn't have stay hidden.
 */
#[Title('Social links')]
class Socials extends Component
{
    /** @var array<string, array{label: string, url: string, show: bool}> */
    public array $networks = [];

    public function mount(SocialLinks $socials): void
    {
        $this->networks = $socials->all();
    }

    public function save(SiteContent $content): void
    {
        $rules = [];
        $messages = ['networks.*.url.url' => __('Enter the full link, starting with https://')];

        foreach ($this->networks as $key => $network) {
            $this->networks[$key]['url'] = trim($network['url']);

            // WhatsApp can show without a link: it uses the business WhatsApp number.
            $rules["networks.{$key}.url"] = [...($key === 'whatsapp' ? [] : ["required_if_accepted:networks.{$key}.show"]), 'nullable', 'url:http,https', 'max:255'];
            $rules["networks.{$key}.show"] = ['boolean'];
            $messages["networks.{$key}.url.required_if_accepted"] = __('Add the link to your :network account, or untick Show.', ['network' => $network['label']]);
        }

        $this->validate($rules, $messages);

        SiteContentEntry::updateOrCreate(['key' => SocialLinks::KEY], [
            'value' => json_encode(collect($this->networks)->map(fn (array $network) => [
                'url' => $network['url'],
                'show' => (bool) $network['show'],
            ])->all()),
        ]);

        $content->flush();

        session()->flash('status', __('Saved. The footer shows these accounts now.'));
    }

    public function render(): View
    {
        return view('livewire.admin.socials');
    }
}
