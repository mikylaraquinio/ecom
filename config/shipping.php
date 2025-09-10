<?php

return [
    'same_town' => [
        ['max' => 5, 'rate' => 50],
        ['max' => null, 'rate' => 50, 'extra_per_5kg' => 10], 
    ],
    'other_town' => [
        ['max' => 5, 'rate' => 80],
        ['max' => null, 'rate' => 80, 'extra_per_5kg' => 20],
    ],
];

