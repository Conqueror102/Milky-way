@props([
    'quantity',
    'decrement',
    'increment',
    'label' => 'Quantity',
])

{{-- Minus / count / plus. The actions are Livewire calls, passed in as strings. --}}
<div {{ $attributes->class('font-sub inline-flex items-center rounded-full bg-white ring-1 ring-cosmic-900/15') }}>
    <button type="button" wire:click="{{ $decrement }}" class="grid size-10 place-items-center rounded-full text-cosmic-900 transition hover:bg-cosmic-900/6">
        <span class="sr-only">Decrease {{ strtolower($label) }}</span>
        <x-icon name="minus-solid" class="size-4" />
    </button>
    <span class="min-w-8 text-center text-sm font-bold text-cosmic-900" aria-label="{{ $label }}">{{ $quantity }}</span>
    <button type="button" wire:click="{{ $increment }}" class="grid size-10 place-items-center rounded-full text-cosmic-900 transition hover:bg-cosmic-900/6">
        <span class="sr-only">Increase {{ strtolower($label) }}</span>
        <x-icon name="plus-solid" class="size-4" />
    </button>
</div>
