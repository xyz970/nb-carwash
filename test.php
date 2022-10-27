<?php

$array = [
    'adi','arya','rifki','maya'
];

// $pos = 0;
// for ($i=1; $i < count($array); $i++) {
//     echo $array[$i];
//     if ($array[$i] == 'arya') {
//         $pos = $i;
//     }
// }
echo array_search('arya',$array)+1;