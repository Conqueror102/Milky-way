<?php

use App\Livewire\Admin\Socials;
use App\Models\User;
use App\Support\SiteContent;
use Livewire\Livewire;

function footerHtml(): string
{
    app(SiteContent::class)->flush();

    $html = test()->get(route('home'))->getContent();

    return substr($html, strpos($html, 'id="site-footer"'));
}

test('customers cannot reach the social links page', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('admin.socials'))->assertForbidden();
});

test('the footer starts with the store\'s known accounts', function () {
    expect(footerHtml())
        ->toContain('https://instagram.com/milky_cosmetics_sales')
        ->toContain('https://tiktok.com/@milkywaycosmeticssales')
        ->toContain('https://wa.me/'.config('milkyway.whatsapp.number'))
        ->not->toContain('>YouTube<');
});

test('the footer shows only the accounts an admin ticked', function () {
    $this->actingAs(User::factory()->admin()->create());

    Livewire::test(Socials::class)
        ->set('networks.youtube.url', 'https://youtube.com/@milkyway')
        ->set('networks.youtube.show', true)
        ->set('networks.facebook.show', false)
        ->call('save')
        ->assertHasNoErrors();

    expect(footerHtml())
        ->toContain('https://youtube.com/@milkyway')
        ->not->toContain('facebook.com');
});

test('a ticked account needs a full link', function () {
    $this->actingAs(User::factory()->admin()->create());

    Livewire::test(Socials::class)
        ->set('networks.x.show', true)
        ->set('networks.snapchat.url', 'snapchat.com/add/milkyway')
        ->set('networks.snapchat.show', true)
        ->call('save')
        ->assertHasErrors(['networks.x.url', 'networks.snapchat.url' => 'url'])
        ->assertSee('Add the link to your X (Twitter) account, or untick Show.')
        ->assertSee('Enter the full link, starting with https://');
});

test('the footer drops the social column when no account shows', function () {
    $this->actingAs(User::factory()->admin()->create());

    $component = Livewire::test(Socials::class);
    foreach (array_keys(config('milkyway.socials')) as $key) {
        $component->set("networks.{$key}.show", false);
    }
    $component->call('save')->assertHasNoErrors();

    expect(footerHtml())->not->toContain('>Social<');
});
