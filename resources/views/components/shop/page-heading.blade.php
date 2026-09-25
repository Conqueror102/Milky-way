@props([
    'eyebrow',
])

<div {{ $attributes }}>
    <div class="flex items-center gap-3">
        <span class="h-px w-10 bg-gold-500/60"></span>
        <span class="font-sub text-[0.68rem] font-bold tracking-[0.32em] text-gold-600 uppercase">{{ $eyebrow }}</span>
    </div>
    <h1 class="mt-4 text-[clamp(2rem,3.6vw,3rem)] leading-[1.1] font-bold tracking-[-0.01em] text-cosmic-900">
        {{ $slot }}
    </h1>
</div>
