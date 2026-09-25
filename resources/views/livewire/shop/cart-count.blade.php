<a
    href="{{ route('cart') }}"
    class="liquid-glass relative grid size-10 shrink-0 place-items-center rounded-full text-cosmic-900 transition hover:bg-white/85 lg:size-11"
>
    <span class="sr-only">Cart, {{ $count }} {{ Str::plural('item', $count) }}</span>
    <x-icon name="shopping-bag-solid" class="size-5" />
    @if ($count > 0)
        <span aria-hidden="true" class="font-sub absolute -top-1 -right-1 grid h-5 min-w-5 place-items-center rounded-full bg-gold-500 px-1 text-[0.65rem] font-bold text-white">{{ $count > 99 ? '99+' : $count }}</span>
    @endif
</a>
