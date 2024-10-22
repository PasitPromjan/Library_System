<?php
function random_number($count)
{
    $rn = '';
    for ($i = 0; $i < $count; $i++) {
        $n = rand(0, 9);
        $rn .= "$n";
    }
    return $rn;
}
function random_char($count)
{
    $ch = '';
    $char_arr = range('A', 'Z');
    for ($i = 0; $i < $count; $i++) {
        $c = $char_arr[rand(0, 25)];
        $ch .= "$c";
    }
    return $ch;
}
