<?php
date_default_timezone_set('ASIA/BANGKOK');
function create_date()
{
    return date('Y-m-d H:i:s');
}

function date_stamp_id()
{
    return date('YmdHis');
}

function date_stamp_number()
{
    return getdate()[0];
}
function getCountDate($d)
{
    return strlen((string)$d) == 2 ? $d : "0$d";
}


function getMonthThai($m)
{
    $month = [
        'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน',
        'พฤษภาคม', 'มิถุนายน', 'กรกฎาคม', 'สิงหาคม',
        'กันยนยน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'
    ];
    return $month[(int)$m - 1];
}

function getYeatThai($y)
{
    return (int)$y + 543;
}
function get_countdate($date)
{
    return  strlen((string)$date) == 1 ?  "0$date" : $date;
}

function getFullThaiDate($date)
{
    $dt = date('j', strtotime($date));
    $m = getMonthThai(date('m', strtotime($date)));
    $y = getYeatThai(date('Y', strtotime($date)));
    return "$dt $m $y";
}

function getMonthAndYearThai($date)
{
    $m = getMonthThai(date('m', strtotime($date)));
    $y = getYeatThai(date('Y', strtotime($date)));
    return "$m $y";
}
