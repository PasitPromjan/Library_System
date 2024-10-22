<?php
require_once('../config/config_db.php');
require_once('../function/date.php');
require_once('../function/create_random.php');
$act = $_POST['act'] ?? '';
$publisher_id = $_POST['publisher_id'] ?? '';
$publisher_name = $_POST['publisher_name'] ?? '';
$create_at = create_date();
$update_at = create_date();

$search = $_POST['search'] ?? '';
$sql = "";
$params = [];
function getPublisher($publisher_name)
{
    $sql = "SELECT publisher_name FROM book_publisher WHERE publisher_name=? AND soft_delete!=?";
    $stmt = connect_db()->prepare($sql);
    $stmt->execute([$publisher_name, 'true']);
    return $stmt->rowCount();
}

if (!empty($search)) {
    $sql = "SELECT publisher_id,publisher_name FROM book_publisher ";
    $sql .= "WHERE publisher_name LIKE  ? LIMIT 0,10";
    $params = ["%$search%"];
    $stmt = connect_db()->prepare($sql);
    $stmt->execute($params);
    $_row = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        array_push($_row, $row);
    }
    $_row = array_map(function ($d) {
        return ['name' => $d['publisher_name'], 'id' => $d['publisher_id']];
    }, $_row);

    echo json_encode($_row);
    return;
}



if (!empty($publisher_id) && empty($act)) {
    $sql = "SELECT * FROM book_publisher WHERE soft_delete!=?";
    $sql .= " AND publisher_id=?";
    array_push($params, "true");
    array_push($params, $publisher_id);
}

if ($act == 'insert' || $act == 'update') {
    $row_count = getPublisher($publisher_name);
    if ($row_count > 0) {
        echo  json_encode([
            'result' => true, 'status' => 'ok',
            'isValidate' => false
        ]);
        http_response_code(400);
        return;
    }
}
if ($act == 'insert') {
    $sql = "INSERT INTO book_publisher VALUES (?,?,?,?,?)";
    $publisher_id = "P-BSH" . random_number(6) . random_char(2);
    array_push($params, $publisher_id);
    array_push($params, $publisher_name);
    array_push($params, $create_at);
    array_push($params, $update_at);
    array_push($params, "false");
}

if ($act == 'update') {
    $sql = "UPDATE book_publisher SET publisher_name=?,";
    $sql .= "update_at=? WHERE publisher_id=?";
    array_push($params, $publisher_name);
    array_push($params, $update_at);
    array_push($params, $publisher_id);
}

if ($act == 'delete') {
    $sql = "UPDATE book_publisher SET soft_delete=?,";
    $sql .= "update_at=? WHERE publisher_id=?";
    array_push($params, 'true');
    array_push($params, $update_at);
    array_push($params, $publisher_id);
}
if (!empty($sql)) {
    try {
        $response = ['result' => true, 'status' => 'success'];
        $stmt = connect_db()->prepare($sql);
        $stmt->execute($params);
        if (!empty($publisher_id && empty($act))) {
            $data =   $stmt->fetchAll();
            $response['publisher'] = $data;
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
