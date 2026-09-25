<?php

namespace App\Support;

class Money
{
    /**
     * Format a whole-naira amount for display, e.g. 12500 becomes "₦12,500".
     */
    public static function format(int $amount): string
    {
        return '₦'.number_format($amount);
    }
}
