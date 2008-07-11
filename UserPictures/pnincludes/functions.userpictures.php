<?php
/**
 * This function switches the value of two given positions
 * in a given array
 *
 * @param 	$array	array
 * @param	$pos1	int
 * @param 	$pos2	int
 * @return 	array
 */
function switchArrayElements($array,$pos1,$pos2) {
    $cache = $array[$pos1];
    $array[$pos1] = $array[$pos2];
    $array[$pos2] = $cache;
    return $array;
}
?>