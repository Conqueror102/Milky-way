<?php

namespace App\Enums;

/**
 * Where a single payment attempt stands. An order's own PaymentStatus sums these up.
 */
enum TransactionStatus: string
{
    case Pending = 'pending';
    case Successful = 'successful';
    case Failed = 'failed';
    case Abandoned = 'abandoned';

    public function label(): string
    {
        return ucfirst($this->value);
    }

    public function color(): string
    {
        return match ($this) {
            self::Successful => 'green',
            self::Failed => 'red',
            self::Abandoned => 'amber',
            self::Pending => 'zinc',
        };
    }
}
