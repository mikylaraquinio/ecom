<?php

namespace App\Helpers;

class ShippingHelper
{
    public static function calculate($buyerTown, $sellerTown, $weightKg)
    {
        $config = config('shipping');
        $zone = $buyerTown === $sellerTown ? 'same_town' : 'other_town';
        $rules = $config[$zone] ?? [];

        foreach ($rules as $rule) {
            if ($rule['max'] === null || $weightKg <= $rule['max']) {
                $base = $rule['rate'];

                // Check if we need to add extra fee per 5kg
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

}
