<?php

namespace App\Livewire\Shop;

use App\Actions\Shop\PlaceOrder;
use App\Support\Cart;
use App\Support\CartLine;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * @property-read Collection<int, CartLine> $lines
 * @property-read int $subtotal
 */
#[Layout('layouts::marketing', ['noindex' => true])]
#[Title('Checkout')]
class Checkout extends Component
{
    public const PLACED_ORDERS_SESSION_KEY = 'orders.placed';

    public string $customer_name = '';

    public string $customer_phone = '';

    public string $customer_email = '';

    public string $delivery_area = '';

    public string $delivery_address = '';

    public string $notes = '';

    public function mount(Cart $cart): void
    {
        if ($cart->isEmpty()) {
            $this->redirectRoute('cart', navigate: false);

            return;
        }

        if ($user = Auth::user()) {
            $this->customer_name = $user->name;
            $this->customer_email = $user->email;
        }
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    protected function rules(): array
    {
        return [
            'customer_name' => ['required', 'string', 'max:120'],
            'customer_phone' => ['required', 'string', 'max:30', 'regex:/^\+?[0-9\s()-]{7,}$/'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'delivery_area' => ['required', 'string', Rule::in(self::deliveryAreas())],
            'delivery_address' => ['required', 'string', 'max:1000'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function validationAttributes(): array
    {
        return [
            'customer_name' => 'name',
            'customer_phone' => 'phone number',
            'customer_email' => 'email',
            'delivery_area' => 'delivery area',
            'delivery_address' => 'delivery address',
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'customer_phone.regex' => 'Enter a phone number we can call or WhatsApp, e.g. +234 816 182 3482.',
        ];
    }

    public function placeOrder(PlaceOrder $placeOrder): void
    {
        /** @var array{customer_name: string, customer_phone: string, customer_email: string, delivery_area: string, delivery_address: string, notes: string} $validated */
        $validated = $this->validate();

        $order = $placeOrder->handle([
            ...$validated,
            'customer_email' => filled($validated['customer_email']) ? $validated['customer_email'] : null,
            'notes' => filled($validated['notes']) ? $validated['notes'] : null,
        ], Auth::user()?->id);

        // Lets this browser reopen its own confirmation page without an account.
        session()->push(self::PLACED_ORDERS_SESSION_KEY, $order->reference);

        $this->dispatch('cart-updated');

        $this->redirectRoute('orders.show', ['order' => $order->reference]);
    }

    /**
     * @return Collection<int, CartLine>
     */
    #[Computed]
    public function lines(): Collection
    {
        return app(Cart::class)->lines();
    }

    #[Computed]
    public function subtotal(): int
    {
        return app(Cart::class)->subtotal($this->lines);
    }

    /**
     * @return list<string>
     */
    public static function deliveryAreas(): array
    {
        /** @var list<string> $areas */
        $areas = config('milkyway.delivery_areas', []);

        return $areas;
    }
}
