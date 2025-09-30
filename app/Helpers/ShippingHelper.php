<?php

namespace App\Helpers;

class ShippingHelper
{
    const BASE_FEE = 16;     // ₱
    const PER_KM   = 12;     // ₱/km

    // Approx coords for Pangasinan LGUs (add/adjust as needed)
    // Format: 'key' => [lat, lng]
    private static array $COORDS = [
        'dagupan'       => [16.043, 120.333],
        'calasiao'      => [16.012, 120.357],
        'lingayen'      => [16.021, 120.231],
        'san carlos'    => [15.928, 120.348],
        'urdaneta'      => [15.976, 120.571],
        'mangaldan'     => [16.069, 120.402],
        'san fabian'    => [16.146, 120.400],
        'binmaley'      => [16.032, 120.269],
        'malasiqui'     => [15.919, 120.406],
        'manaoag'       => [16.043, 120.486],
        'mapandan'      => [16.000, 120.455],
        'sta barbara'   => [16.001, 120.401], // approximate
        'binalonan'     => [16.050, 120.600],
        'pozorrubio'    => [16.113, 120.543],
        'asingan'       => [16.005, 120.669],
        'sta maria'     => [15.980, 120.710],
        'tayug'         => [16.026, 120.743],
        'villasis'      => [15.900, 120.588],
        'rosales'       => [15.894, 120.632],
        'sison'         => [16.173, 120.528],
        'san manuel'    => [16.061, 120.665],
        'san nicolas'   => [16.056, 120.770],
        'alaminos'      => [16.155, 119.981],
        'bolinao'       => [16.377, 119.898],
        'burgos'        => [16.107, 119.850],
        'dasol'         => [15.989, 119.880],
        'agno'          => [16.135, 119.803],
        'anda'          => [16.298, 119.996],
        'infanta'       => [15.735, 119.906],
        'bugallon'      => [15.985, 120.236],
        'labrador'      => [16.027, 120.137],
        'sual'          => [16.069, 120.095],
        'urbiztondo'    => [15.822, 120.333],
        'bayambang'     => [15.812, 120.459],
        'basista'       => [15.837, 120.402],
        'bautista'      => [15.789, 120.473],
        'san jacinto'   => [16.073, 120.437],
        'san quintin'   => [15.984, 120.821],
        'natividad'     => [16.042, 120.791],
        // add other barangays/cities if needed…
    ];

    public static function calculate(?string $buyerCity, ?string $sellerCity, $weight = null): int
    {
        $from = self::normalize($sellerCity);
        $to   = self::normalize($buyerCity);

        if (!$from || !$to) {
            logger()->warning('ShippingHelper: empty city', compact('buyerCity','sellerCity'));
            // Either return 0 or keep a small default — choose your policy
            return self::BASE_FEE + self::PER_KM * 10; // old behaviour (136)
        }

        $km = self::distanceKm($from, $to);

        if ($km === null) {
            logger()->warning('ShippingHelper: unknown city pair', compact('from','to'));
            return self::BASE_FEE + self::PER_KM * 10; // fallback (avoid 136 if you want)
        }

        // Round up to the next km if you charge per started km
        $km = (int) ceil($km);

        return (int) (self::BASE_FEE + self::PER_KM * $km);
    }

    private static function normalize(?string $s): ?string
    {
        if (!$s) return null;
        $s = mb_strtolower(trim($s));
        // Replace multiple spaces, strip trailing ' city'
        $s = preg_replace('/\s+city$/', '', $s);
        $s = preg_replace('/\s+/', ' ', $s);
        // common abbreviations
        $s = str_replace(['sta. ', 'sta ', 'st. '], ['sta ', 'sta ', 'saint '], $s);
        return $s;
    }

    private static function distanceKm(string $fromKey, string $toKey): ?float
    {
        $a = self::$COORDS[$fromKey] ?? null;
        $b = self::$COORDS[$toKey] ?? null;
        if (!$a || !$b) return null;

        [$lat1, $lon1] = $a;
        [$lat2, $lon2] = $b;

        // Haversine
        $R = 6371; // km
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $la1  = deg2rad($lat1);
        $la2  = deg2rad($lat2);

        $h = sin($dLat/2)**2 + cos($la1)*cos($la2)*sin($dLon/2)**2;
        $c = 2 * atan2(sqrt($h), sqrt(1-$h));
        return $R * $c;
    }
}
