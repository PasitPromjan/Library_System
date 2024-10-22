<?php
require_once('../config/config_db.php');
require_once('../function/date.php');
require_once('../function/create_random.php');

$act = $_POST['act'] ?? '';
$id = $_POST['id'] ?? "OA" . date("Ymd") . random_char(4) . random_number(4);
$username = $_POST['username'] ?? '';
$password = isset($_POST['password']) && !empty($_POST['password'])
    ? password_hash($_POST['password'], PASSWORD_BCRYPT)  : '';
$officer_fname = $_POST['officer_fname'] ?? '';
$officer_lname = $_POST['officer_lname'] ?? '';
$role = $_POST['role'] ?? '';
$create_at = create_date();
$update_at = create_date();
$sql = "";
$params = [];

function getOfficer($username)
{
    $sql = "SELECT username FROM officer WHERE username=?";
    $stmt = connect_db()->prepare($sql);
    $stmt->execute([$username]);
    return $stmt->rowCount();
}



if (isset($_POST['id']) && empty($act)) {
    $sql = "SELECT * FROM officer WHERE soft_delete!=?";
    $sql .= " AND officer_id=?";
    $params = ["true", $id];
}


if ($act == 'insert') {
    $row_count = getOfficer($username);
    if ($row_count > 0) {
        echo  json_encode([
            'result' => true, 'status' => 'ok',
            'isUsername' => true
        ]);
        http_response_code(400);
        return;
    }
    $sql = "INSERT INTO officer VALUES(?,?,?,?,?,?,?,?,?)";
    $params = [
        $id,
        $username,
        $password,
        $officer_fname,
        $officer_lname,
        $create_at,
        $update_at,
        $role,
        'false'
    ];
}

if ($act == 'update') {
    $params = [
        $officer_fname,
        $officer_lname,
        $role,
        $update_at
    ];
    if (!empty($password)) {
        array_push($params, $password);
    }
    array_push($params, $id);
    $sql = "UPDATE officer SET officer_fname=?,officer_lname=?,";
    $sql .= "role=?,update_at=?";
    if (!empty($password)) {
        $sql .= ",password=?";
    }
    $sql .= " WHERE officer_id=?";
}

if ($act == 'delete') {
    $sql = "UPDATE officer SET soft_delete=?,";
    $sql .= "update_at=? WHERE officer_id=?";
    $params = ['true', $update_at, $id];
}

if (isset($_SERVER['REQUEST_METHOD']) && !empty($id)) {
    try {
        $response = ['result' => true, 'status' => 'success'];
        $stmt = connect_db()->prepare($sql);
        $stmt->execute($params);
        // get Data สำหรับ มี id แต่ไม่มี action หมายถึง ให้ get ข้อมูลตาม id
        if (!empty($id) && empty($act)) {
            $response['officer'] = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        echo json_encode($response);
    } catch (PDOException $e) {
        echo json_encode([
            'result' => false,
            'status' => 'error', 'err' => $e->getMessage()
        ]);
        http_response_code(500);
    }
}
