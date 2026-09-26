@props(['status'])

<flux:badge :color="$status->color()" size="sm">{{ __($status->label()) }}</flux:badge>
