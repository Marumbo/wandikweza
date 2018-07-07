<?php
/*
$textExplode =array('1','2');

$countOfArray =count($textExplode);
print ($textExplode[$countOfArray-1]);
*/

$textExplode = array('1','2','3','#','4','5','#');
$countOfArray =count($textExplode);
echo " initial array and last value: \n";
print_r ($textExplode);
echo "last value \n";
print ($textExplode[$countOfArray-1]);
echo "\n";

//print_r($textExplode[$countOfArray-1]);
//print($textExplode[$countOfArray-1]);

function removeHash($textExplode)
{
    if (($key = array_search('#', $textExplode)) !== false) {
        unset($textExplode[$key]);
    }
    return $textExplode;
}

$textExplode1 = array_diff($textExplode,array("#"));
$textExplode =array_values($textExplode1);
$countOfArray =count($textExplode);

print_r($textExplode);
echo "\n";
echo "last value in array diff out put: \n";
print ($textExplode[$countOfArray-1]);
echo "\n";

/*
$textExplode = removeHash($textExplode);
$countOfArray =count($textExplode);
echo "array after remove hash function";
print_r($textExplode);
echo "\n";
echo "last value in array";
print($textExplode[$countOfArray]);
*/