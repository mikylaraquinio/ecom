<?php

namespace App\Helpers;

class ShippingHelper
{
    public static function calculate($buyerTown, $sellerTown, $weightKg, $hasLivestock = false, $buyerAddress = null, $sellerAddress = null)
    {
        // ✅ Livestock: distance-based fee
        if ($hasLivestock && $buyerAddress && $sellerAddress) {
            $distance = self::getDistanceInKm($sellerAddress, $buyerAddress);

            $rates = config('shipping.livestock_rates');
            $blockSize = config('shipping.livestock_weight_blocks', 20);

            // Round up distance to nearest km
            $km = (int) ceil($distance);

            // If distance exceeds chart, use last available rate
            $base = $rates[$km] ?? end($rates);

            // Weight multiplier (1x per blockSize kg)
            $multiplier = max(1, ceil($weightKg / $blockSize));

            return $base * $multiplier;
        }

        // ✅ Normal items → fallback to same/other town rules
        $config = config('shipping');
        $zone = $buyerTown === $sellerTown ? 'same_town' : 'other_town';
        $rules = $config[$zone] ?? [];

        foreach ($rules as $rule) {
            if ($rule['max'] === null || $weightKg <= $rule['max']) {
                $base = $rule['rate'];

                if (isset($rule['extra_per_5kg']) && $weightKg > 5) {
                    $extraBlocks = ceil(($weightKg - 5) / 5);
                    $extra = $extraBlocks * $rule['extra_per_5kg'];
                    return $base + $extra;
                }

                return $base;
            }
        }

        return 0; // fallback
    }

    private static function getDistanceInKm($origin, $destination)
    {
        $apiKey = env('GOOGLE_MAPS_API_KEY');
        $url = "https://maps.googleapis.com/maps/api/distancematrix/json?units=metric"
            . "&origins=" . urlencode($origin)
            . "&destinations=" . urlencode($destination)
            . "&key=" . $apiKey;

        $response = @file_get_contents($url);
        if (!$response) {
            return 0;
        }

        $data = json_decode($response);

        if ($data && $data->status == 'OK') {
            return $data->rows[0]->elements[0]->distance->value / 1000;
        }

        return 0; // fallback
    }
}
