@props(['status'])

@php
    $color = match ($status) {
        \App\Enums\PaymentStatus::Paid => 'green',
        \App\Enums\PaymentStatus::Failed => 'red',
        \App\Enums\PaymentStatus::Refunded => 'amber',
        default => 'zinc',
    };
@endphp

<flux:badge :color="$color" size="sm">{{ __($status->label()) }}</flux:badge>
