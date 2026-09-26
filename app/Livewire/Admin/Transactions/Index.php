<?php

namespace App\Livewire\Admin\Transactions;

use App\Enums\TransactionStatus;
use App\Models\Payment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Transactions')]
class Index extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $status = '';

    #[Url]
    public string $from = '';

    #[Url]
    public string $to = '';

    public function updated(string $property): void
    {
        if (in_array($property, ['search', 'status', 'from', 'to'], true)) {
            $this->resetPage();
        }
    }

    public function clearFilters(): void
    {
        $this->reset('search', 'status', 'from', 'to');
        $this->resetPage();
    }

    /**
     * @return LengthAwarePaginator<int, Payment>
     */
    #[Computed]
    public function payments(): LengthAwarePaginator
    {
        return $this->filtered()
            ->with('order')
            ->latest()
            ->latest('id')
            ->paginate(25);
    }

    /**
     * Totals for whatever the filters currently match.
     *
     * @return array{count: int, successful: int, received: int, failed: int}
     */
    #[Computed]
    public function summary(): array
    {
        $successful = $this->filtered()->where('status', TransactionStatus::Successful);

        return [
            'count' => $this->filtered()->count(),
            'successful' => (clone $successful)->count(),
            'received' => (int) $successful->sum('amount_paid'),
            'failed' => $this->filtered()->where('status', TransactionStatus::Failed)->count(),
        ];
    }

    /**
     * @return Builder<Payment>
     */
    private function filtered(): Builder
    {
        $search = trim($this->search);

        return Payment::query()
            ->when(TransactionStatus::tryFrom($this->status), fn (Builder $query, TransactionStatus $status) => $query->where('status', $status))
            ->when($this->date($this->from), fn (Builder $query, Carbon $from) => $query->where('created_at', '>=', $from->startOfDay()))
            ->when($this->date($this->to), fn (Builder $query, Carbon $to) => $query->where('created_at', '<=', $to->endOfDay()))
            ->when($search !== '', function (Builder $query) use ($search) {
                $like = '%'.str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $search).'%';

                $query->where(fn (Builder $query) => $query
                    ->where('reference', 'like', $like)
                    ->orWhereHas('order', fn (Builder $order) => $order
                        ->where('reference', 'like', $like)
                        ->orWhere('customer_name', 'like', $like)
                        ->orWhere('customer_email', 'like', $like)
                        ->orWhere('customer_phone', 'like', $like)));
            });
    }

    private function date(string $value): ?Carbon
    {
        try {
            return $value === '' ? null : Carbon::createFromFormat('Y-m-d', $value);
        } catch (\Throwable) {
            return null;
        }
    }

    public function render(): View
    {
        return view('livewire.admin.transactions.index');
    }
}
