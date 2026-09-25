<?php

if (! function_exists('rp_qty')) {
    function rp_qty($value): string
    {
        if ($value === null || $value === '') {
            return '0';
        }

        $number = is_numeric($value)
            ? (float) $value
            : (float) str_replace(',', '', (string) $value);

        return number_format($number);
    }
}

if (! function_exists('rp_amt')) {
    function rp_amt($value): string
    {
        if ($value === null || $value === '') {
            return '0.00';
        }

        $number = is_numeric($value)
            ? (float) $value
            : (float) str_replace(',', '', (string) $value);

        return number_format($number, 2);
    }
}
