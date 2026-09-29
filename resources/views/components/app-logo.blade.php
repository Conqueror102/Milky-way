@props([
    'sidebar' => false,
])

@if($sidebar)
    <flux:sidebar.brand :name="config('app.name')" {{ $attributes }}>
        <x-slot name="logo" class="flex h-8 w-11 items-center justify-center rounded-md bg-white p-0.5">
            <x-app-logo-icon class="h-7 w-auto" />
        </x-slot>
    </flux:sidebar.brand>
@else
    <flux:brand :name="config('app.name')" {{ $attributes }}>
        <x-slot name="logo" class="flex h-8 w-11 items-center justify-center rounded-md bg-white p-0.5">
            <x-app-logo-icon class="h-7 w-auto" />
        </x-slot>
    </flux:brand>
@endif
