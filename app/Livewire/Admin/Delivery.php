<?php

namespace App\Livewire\Admin;

use App\Models\DeliveryArea;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * The places the shop delivers to and what delivery costs in each. Changes stay
 * on this page until Save. Orders already placed keep the place and fee they
 * were placed with.
 */
#[Title('Delivery')]
class Delivery extends Component
{
    /** @var array<int, array{id: int|null, name: string, fee: string, featured: bool}> */
    public array $places = [];

    public function mount(): void
    {
        $this->loadPlaces();
    }

    public function addPlace(): void
    {
        $this->places[] = ['id' => null, 'name' => '', 'fee' => '', 'featured' => false];
    }

    public function removePlace(int $index): void
    {
        unset($this->places[$index]);
        $this->places = array_values($this->places);
        $this->resetValidation();
    }

    /**
     * Swap a place with the one above (-1) or below (1) it.
     */
    public function movePlace(int $index, int $direction): void
    {
        $target = $index + $direction;

        if (! isset($this->places[$index], $this->places[$target])) {
            return;
        }

        [$this->places[$index], $this->places[$target]] = [$this->places[$target], $this->places[$index]];
        $this->resetValidation();
    }

    public function save(): void
    {
        $this->places = array_map(fn (array $place) => [...$place, 'name' => trim($place['name'])], $this->places);

        $this->validate(
            [
                'places' => ['array', function (string $attribute, array $places, \Closure $fail) {
                    if (collect($places)->where('featured', true)->count() > DeliveryArea::MAX_FEATURED) {
                        $fail(__('Show at most :count places on the home page.', ['count' => DeliveryArea::MAX_FEATURED]));
                    }
                }],
                'places.*.name' => ['required', 'string', 'max:120', 'distinct:ignore_case'],
                'places.*.fee' => ['nullable', 'integer', 'min:0', 'max:10000000'],
                'places.*.featured' => ['boolean'],
            ],
            [
                'places.*.name.required' => __('Enter the name of the place.'),
                'places.*.name.distinct' => __('This place is already on the list.'),
            ],
            ['places.*.name' => __('place'), 'places.*.fee' => __('fee')],
        );

        DB::transaction(function () {
            $keptIds = collect($this->places)->pluck('id')->filter();

            DeliveryArea::query()->whereNotIn('id', $keptIds)->delete();

            // Names are unique, so free every kept place's name first: otherwise one
            // place taking a name another gives up in this save would clash with it.
            foreach ($keptIds as $id) {
                DeliveryArea::query()->whereKey($id)->update(['name' => "__renaming-{$id}"]);
            }

            foreach ($this->places as $index => $place) {
                DeliveryArea::updateOrCreate(['id' => $place['id']], [
                    'name' => $place['name'],
                    'fee' => filled($place['fee']) ? (int) $place['fee'] : null,
                    'is_featured' => (bool) $place['featured'],
                    'sort_order' => $index,
                ]);
            }
        });

        $this->loadPlaces();

        session()->flash('status', __('Saved. Checkout shows these places and fees now.'));
    }

    protected function loadPlaces(): void
    {
        $this->places = DeliveryArea::query()->ordered()->get()->map(fn (DeliveryArea $area) => [
            'id' => $area->id,
            'name' => $area->name,
            'fee' => $area->fee === null ? '' : (string) $area->fee,
            'featured' => $area->is_featured,
        ])->all();
    }

    public function render(): View
    {
        return view('livewire.admin.delivery');
    }
}
