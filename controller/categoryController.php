<?php
require_once('../config/config_db.php');
require_once('../function/date.php');
require_once('../function/create_random.php');
$act = $_POST['act'] ?? '';
$category_id = $_POST['id'] ?? '';
$category = $_POST['category'] ?? '';
$create_at = create_date();
$update_at = create_date();
$search = $_POST['search'] ?? '';
$sql = "";
$params = [];
function getCategory($category)
{
    $sql = "SELECT category_name FROM book_category WHERE category_name=? AND soft_delete !=?";
    $stmt = connect_db()->prepare($sql);
    $stmt->execute([$category,'true']);
    return $stmt->rowCount();
}
if (!empty($search)) {
    $sql = "SELECT category_id,category_name FROM book_category ";
    $sql .= "WHERE category_name LIKE  ? LIMIT 0,10";
    $params = ["%$search%"];
    $stmt = connect_db()->prepare($sql);
    $stmt->execute($params);
    $_row = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        array_push($_row, $row);
    }
    $_row = array_map(function ($d) {
        return ['name' => $d['category_name'], 'id' => $d['category_id']];
    }, $_row);

    echo json_encode($_row);
    return;
}

if (!empty($category_id) && empty($act)) {
    $sql = "SELECT * FROM book_category WHERE soft_delete!=?";
    $sql .= " AND category_id=?";
    array_push($params, "true");
    array_push($params, $category_id);
}

if ($act == 'insert' || $act == 'update') {
    $row_count = getCategory($category);
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
    $sql = "INSERT INTO book_category VALUES (?,?,?,?,?)";
    $category_id = "CTRB" . random_number(6) . random_char(2);
    array_push($params, $category_id);
    array_push($params, $category);
    array_push($params, $create_at);
    array_push($params, $update_at);
    array_push($params, "false");
}

if ($act == 'update') {
    $sql = "UPDATE category_name SET book_category=?,";
    $sql .= "update_at=? WHERE category_id=?";
    array_push($params, $category);
    array_push($params, $update_at);
    array_push($params, $category_id);
}

if ($act == 'delete') {
    $sql = "UPDATE book_category SET soft_delete=?,";
    $sql .= "update_at=? WHERE category_id=?";
    array_push($params, 'true');
    array_push($params, $update_at);
    array_push($params, $category_id);
}

if (!empty($sql)) {
    try {
        $response = ['result' => true, 'status' => 'success'];
        $stmt = connect_db()->prepare($sql);
        $stmt->execute($params);
        if (!empty($category_id && empty($act))) {
            $data =   $stmt->fetchAll();
            $response['category'] = $data;
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
