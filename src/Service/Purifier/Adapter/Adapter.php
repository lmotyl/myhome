<?php

namespace App\Service\Purifier\Adapter;

abstract class Adapter implements AdapterInterface
{

    const COMPARISON_ABOVE = 'above';
    const COMPARISON_BELOW = ' below';

    const MODE_AUTO = 'auto';
    const MODE_MANUAL = 'manual';

    const LEVEL_QUIET = 'quiet';
    const LEVEL_LOW = 'low';
    const LEVEL_MEDIUM = 'medium';
    const LEVEL_HIGH = 'high';
    const LEVEL_VERY_HIGH = 'very-high';
    const LEVEL_MAX = 'max';

    


//    - { comparison: 'below', pollution: '5', mode: 'auto', level: 'quiet' }
//    - { comparison: 'above', pollution: '5', mode: 'manual', level: 'low' }
//    - { comparison: 'above', pollution: '10', mode: 'manual', level: 'medium' }
//    - { comparison: 'above', pollution: '15', mode: 'manual', level: 'high' }
//    - { comparison: 'above', pollution: '25', mode: 'manual', level: 'very-high' }
//    - { comparison: 'above', pollution: '30', mode: 'manual', level: 'max' }


    
}