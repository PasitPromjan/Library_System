<?php
$dir = __DIR__;
$path_len = strlen(__DIR__);
$base_url =  substr($dir, 0, $path_len - strlen('function'));
require_once("$base_url/config/config_db.php");


function get_percent_total($sum, $amount)
{
    $percent = (float) $amount * 100 / (float) $sum;
    return number_format($percent, 2);
}


function is_params_empty($params)
{
    $new_filter = [];
    foreach ($params as $p) {
        if (!empty($p) || $p == 0) {
            array_push($new_filter, $p);
        }
    };
    return $new_filter;
}

function get_roleName($role)
{
    $roleList =   [
        "librarian" => 'บรรณารักษ์',
        "admin" => 'ผู้ดูแลระบบ'
    ];
    return $roleList[$role];
}




function paginate()
{
    $page = isset($_GET['page']) ? (int)  $_GET['page'] : 0;
    $per_page = isset($_GET['per_page']) ? (int)  $_GET['per_page'] : 10;
    return ['page' => $page, 'per_page' => $per_page];
}

function getDataCountAll($sql, $column, $params, $page, $per_page)
{
    $_s = stripos($sql, "SELECT");
    $_s_len = strlen("SELECT");
    $_pos = stripos($sql, "FROM");
    $_sub =  substr($sql, 0 + $_s_len, $_pos - strlen("FROM") - 2);
    $sql = str_replace($_sub, " COUNT($column) AS count ", $sql);
    $stmt = connect_db()->prepare($sql);
    $stmt->execute($params);
    $row =   $stmt->fetchAll();
    $row_count = (int)$row[0]['count'];
    $page_all = (int) ceil($row_count / $per_page);
    $start = (int) $page * $per_page;
    return [
        'row_count' => $row_count,
        'page_all' => $page_all,
        'start_row' => $start
    ];
}
function getPageAll($row_all, $page, $per_page)
{
    $page_all = (int) ceil($row_all / $per_page);
    $start = (int) $page * $per_page;
    return [
        'page_all' => $page_all,
        'start_row' => $start
    ];
}
function getDataAll($sql, $params)
{
    $row = [];
    $count_params =  count($params);
    $stmt = connect_db()->prepare($sql);
    for ($i = 0; $i < count($params); $i++) {
        if ($i == $count_params - 1 || $i == $count_params - 2) {
            $stmt->bindParam($i + 1, $params[$i], PDO::PARAM_INT);
        } else {
            $stmt->bindParam($i + 1, $params[$i]);
        }
    }
    // for ($i = 0; $i < count(array_keys($params)); $i++) {
    //     $k = ":" . array_keys($params)[$i];
    //     $v = array_values($params)[$i];
    //     echo "<br> $k=>$v";
    //     if ($k == ':start_row' || $k == ':per_page') {
    //         $v = (int) $v;
    //         $stmt->bindParam($k, $v, PDO::PARAM_INT);
    //     } else {
    //         $stmt->bindParam($k, $v);
    //     }
    //     echo "ddd $i <br>";
    // }
    $stmt->execute();
    $row =  $stmt->fetchAll();
    return $row;
}

function getDataById($sql, $params)
{
    $stmt = connect_db()->prepare($sql);
    $stmt->execute($params);
    $row =  $stmt->fetchAll();
    return  $row[0];
}

function isOccuption($occup)
{

    return in_array($occup, ['student', 'teacher']);
}


function getOccupation($occup)
{
    $occupList = [
        'student' => 'นักเรียน-นักศึกษา',
        'teacher' => 'ครู - อาจารย์'
    ];
    return $occupList[$occup] ?? $occup;
}

function getOfficerRole($role)
{

    $roleList = [
        'librarian' => 'บรรณารักษ์',
        'admin' => 'ผู้ดูแลระบบ'
    ];
    return $roleList[$role] ?? '';
}

function getEducationLevel($level)
{
    $educationList = [
        'elementary' => 'ประถมศึกษา',
        'secondaryEducation' => 'มัธยมศึกษา',
        'bachelor' => 'ปริญญาตรี',
        'master' => 'ปริญญาโท',
        'philosophy' => 'ปริญญาเอก',
        'vocationalCertificate' => 'ระดับประกาศนียบัตรวิชาชีพ',
        'higherVocationalCertificate' => 'ประกาศนียบัตรวิชาชีพชั้นสูง'
    ];

    return $educationList[$level] ?? '';
}

function getBorrowStatus($status)
{
    $statusList = [
        'borrow' => 'ยืม',
        'return' => 'คืนแล้ว'
    ];
    return $statusList[$status] ?? '';
}


function converttosqlstr($data_string)
{
    $list = explode(',', $data_string);
    $list_map_string = array_map(function ($d) {
        return "'$d'";
    }, $list);

    $str = implode(',', $list_map_string);
    return $str;
}


function findByName($column, $name, $params)
{
    $column_str = '';
    foreach ($column as $i => $col) {
        $column_str .= "$col LIKE ?";
        if ($i < count($column) - 1) $column_str .= " OR ";
    }
    $name_list = explode(' ', $name);
    $str_sql = ' AND (';
    foreach ($name_list as $i => $_n) {
        $str_sql .= " ($column_str) ";
        if ($i < count($name_list) - 1)  $str_sql .= " OR ";
        for ($p = 0; $p < count($column); $p++) {
            array_push($params, "%$_n%");
        }
    }
    $str_sql .= " ) ";
    return ['sql' => $str_sql, 'params' => $params];
}
