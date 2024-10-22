<?php
require_once('../config/config_db.php');
require_once('../function/date.php');
require_once('../function/create_random.php');
require_once('../function/function.php');

$act = $_POST['act'] ?? '';
$id = $_POST['id'] ?? '';
$update_at = create_date();
$sql = '';
if ($act == 'delete') {
    $sql = "UPDATE report_file SET soft_delete=?,";
    $sql .= "update_at=? WHERE file_id = ?";
    $params = ['true', $update_at, $id];
}

if (!empty($sql)) {
    try {
        $response = ['result' => true, 'status' => 'success'];
        $stmt = connect_db()->prepare($sql);
        $stmt->execute($params);
        echo json_encode($response);
    } catch (PDOException $e) {
        echo json_encode([
            'result' => false,
            'status' => 'error', 'err' => $e->getMessage()
        ]);
        http_response_code(500);
    }
}
