<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Court;
use App\Models\PricingRule;
use App\Models\Setting;
use App\Support\Pricing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminPricingController extends Controller
{
    public function update(Request $request)
    {
        $data = $request->validate([
            'open_hour'              => 'required|integer|between:0,23',
            'close_hour'             => 'required|integer|between:0,23',
            'booking_fee'            => 'required|numeric|min:0|max:100000',
            'rules'                  => 'required|array|min:1|max:12',
            'rules.*.label'          => 'required|string|max:40',
            'rules.*.start_hour'     => 'required|integer|between:0,47',
            'rules.*.end_hour'       => 'required|integer|between:1,48',
            'rules.*.rate'           => 'required|numeric|min:0|max:100000',
        ]);

        $open  = (int) $data['open_hour'];
        $close = (int) $data['close_hour'];
        $closeService = $close <= $open ? $close + 24 : $close;

        $rules = collect($data['rules'])
            ->map(fn ($r) => [
                'label'      => $r['label'],
                'start_hour' => (int) $r['start_hour'],
                'end_hour'   => (int) $r['end_hour'],
                'rate'       => (float) $r['rate'],
            ])
            ->sortBy('start_hour')
            ->values();

        // Rate ranges must cover opening -> closing with no gaps or overlaps,
        // otherwise some hour would have no price.
        $cursor = $open;
        foreach ($rules as $r) {
            if ($r['start_hour'] !== $cursor || $r['end_hour'] <= $cursor) {
                return $this->fail('Rate ranges must run back-to-back from opening time to closing time with no gaps or overlaps.');
            }
            $cursor = $r['end_hour'];
        }
        if ($cursor !== $closeService) {
            return $this->fail('The last rate range must end exactly at your closing time.');
        }

        DB::transaction(function () use ($rules, $data, $open, $close, $closeService) {
            PricingRule::query()->delete();
            foreach ($rules as $r) {
                PricingRule::create($r);
            }

            Setting::put('open_hour', $open);
            Setting::put('close_hour', $close);
            Setting::put('booking_fee', $data['booking_fee']);

            // Keep every court's slot hours in sync (last slot starts 1 hour before closing)
            Court::query()->update([
                'operating_hours_start' => sprintf('%02d:00', $open),
                'operating_hours_end'   => sprintf('%02d:00', ($closeService - 1) % 24),
            ]);
        });

        Pricing::flush();

        return back()->with('success', 'Pricing, booking fee and hours updated. New bookings use these immediately.');
    }

    private function fail(string $message)
    {
        return back()->with('error', $message)->withErrors(['pricing' => $message])->withInput();
    }
}