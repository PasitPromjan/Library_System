<?php
require_once('./config/config_db.php');
require_once('./config/config_system.php');

function respond($result, $status, $extra_data = []) {
    echo json_encode(array_merge([
        'result' => $result,
        'status' => $status,
        'message' => $extra_data['message'] ?? ''
    ], $extra_data));
    exit;
}


@session_start();

$admin = $_POST['admin'] ?? null;
$password = $_POST['password'] ?? null;

if (!$admin || !$password) {
    respond(false, 'error', ['message' => 'Username or password not provided']);
}

$config = new CompareUsername();
$compare = $config->compare($admin, $password);

if ($compare !== false) {

    $_SESSION['officer_id'] = $compare['id'];
    $_SESSION['username'] = $compare['username'];
    $_SESSION['officer_role'] = $compare['role'];
    $_SESSION['name'] = $compare['name'];

    respond(true, 'success');
}

try {
    $sql = "SELECT * FROM officer WHERE username = ?";
    $stmt = connect_db()->prepare($sql);
    $stmt->bindParam(1, $admin);
    $stmt->execute();
    
    $officer = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$officer) {
        respond(false, 'error', ['is_username' => false, 'message' => 'ไม่พบชื่อ']);
    }

    $officer_id = $officer['officer_id'];
    $role = $officer['role'];
    $_pass = $officer['password'];

    
    if (!password_verify($password, $_pass)) {
        respond(false, 'error', ['is_password' => false, 'message' => 'รหัสผ่านไม่ถูกต้อง']);
    }
    

    $_SESSION['username'] = $admin;
    $_SESSION['officer_id'] = $officer_id;
    $_SESSION['officer_role'] = $role;
    $_SESSION['name'] = $officer['officer_fname'];

    respond(true, 'success');

} catch (PDOException $e) {
    respond(false, 'error', ['err' => $e->getMessage()]);
    http_response_code(500);
}
