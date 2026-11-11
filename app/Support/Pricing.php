<?php

namespace App\Support;

use App\Models\PricingRule;
use App\Models\Setting;
use Carbon\Carbon;

/**
 * Single source of truth for rates, booking fee and opening hours.
 * Hours are "service hours": 24 = 12 AM, 25 = 1 AM, 26 = 2 AM (next day).
 */
class Pricing
{
    protected static ?array $cache = null;

    public static function flush(): void
    {
        static::$cache = null;
    }

    public static function hourLabel(int $hour): string
    {
        return Carbon::createFromTime($hour % 24, 0)->format('g:i A');
    }

    protected static function money($n): string
    {
        return '₱' . rtrim(rtrim(number_format((float) $n, 2), '0'), '.');
    }

    public static function data(): array
    {
        if (static::$cache !== null) {
            return static::$cache;
        }

        $rules = [];
        $settings = collect();

        // Never let a missing table (migration not run yet) take the site down
        try {
            $rules = PricingRule::orderBy('start_hour')->get()->map(fn ($r) => [
                'label'      => $r->label,
                'start_hour' => (int) $r->start_hour,
                'end_hour'   => (int) $r->end_hour,
                'rate'       => (float) $r->rate,
            ])->all();
            $settings = Setting::pluck('value', 'key');
        } catch (\Throwable $e) {
            report($e);
        }

        if (empty($rules)) {
            $rules = [
                ['label' => 'Morning',   'start_hour' => 4,  'end_hour' => 12, 'rate' => 250.0],
                ['label' => 'Afternoon', 'start_hour' => 12, 'end_hour' => 17, 'rate' => 300.0],
                ['label' => 'Evening',   'start_hour' => 17, 'end_hour' => 26, 'rate' => 350.0],
            ];
        }

        $open  = (int) ($settings['open_hour'] ?? 4);
        $close = (int) ($settings['close_hour'] ?? 2);
        $fee   = (float) ($settings['booking_fee'] ?? 20);
        $closeService = $close <= $open ? $close + 24 : $close;

        foreach ($rules as &$r) {
            $r['range_label'] = static::hourLabel($r['start_hour']) . ' – ' . static::hourLabel($r['end_hour']);
            $r['rate_label']  = static::money($r['rate']);
        }
        unset($r);

        $rates = array_column($rules, 'rate');
        $min = min($rates);
        $max = max($rates);

        return static::$cache = [
            'rules'         => $rules,
            'booking_fee'   => $fee,
            'fee_label'     => static::money($fee),
            'open_hour'     => $open,
            'close_hour'    => $close,
            'close_service' => $closeService,
            'open_label'    => static::hourLabel($open),
            'close_label'   => static::hourLabel($close),
            'min_rate'      => $min,
            'max_rate'      => $max,
            'range_label'   => $min == $max ? static::money($min) : static::money($min) . '–' . static::money($max),
        ];
    }

    public static function fee(): float
    {
        return static::data()['booking_fee'];
    }

    public static function rateForHour(int $hour): float
    {
        $d = static::data();
        if ($hour < $d['open_hour']) {
            $hour += 24; // after-midnight slots belong to the previous night
        }
        foreach ($d['rules'] as $r) {
            if ($hour >= $r['start_hour'] && $hour < $r['end_hour']) {
                return $r['rate'];
            }
        }
        return end($d['rules'])['rate'];
    }

    public static function isOpenNow(): bool
    {
        $d = static::data();
        $h = (int) now()->hour;
        if ($h < $d['open_hour']) {
            $h += 24;
        }
        return $h < $d['close_service'];
    }
}