<?php
@session_start();
$officer_id = $_SESSION['officer_id'];
require_once('../config/config_db.php');
require_once('../function/date.php');
require_once('../function/create_random.php');
require_once('../function/function.php');
$act = $_POST['act'] ?? '';
$id = $_POST['id'] ?? "l" . date("Ymd") . random_char(4) . random_number(4);
$bookname = $_POST['bookname'] ?? '';
$borrow_fname = $_POST['borrow_fname'] ?? '';
$borrow_lname = $_POST['borrow_lname'] ?? '';
$branch = $_POST['branch'] ?? '';
$occup = $_POST['occup'] ?? '';
$contact_number = $_POST['contact_number'] ?? '';
$education_level = $_POST['education_level'] ?? '';
$year_class = $_POST['year_class'] ?? '';
$borrow_date = $_POST['borrow_date'] ?? '';
$borrow_time = $_POST['borrow_time'] ?? '';
$return_date = $_POST['return_date'] ?? '';
$return_time = $_POST['return_time'] ?? '';
$create_at = create_date();
$update_at = create_date();
$sql = "";
$params = [];
$book_img_dir = "../assets/book_img";
if (!is_dir($book_img_dir)) {
    mkdir($book_img_dir);
}



if (isset($_POST['id']) && empty($act)) {
    $sql = "SELECT book.book_id,book.book_name,book_borrow.* ";
    $sql .= " FROM book_borrow LEFT JOIN book ON ";
    $sql .= "book_borrow.bookname=book.book_id ";
    $sql .= " WHERE book_borrow.borrow_id=?";
    $borrow = getDataById($sql, [$id]);
    $borrow_officer = $borrow['borrow_officer'];
    $return_officer = $borrow['return_officer'];
    $_list = explode(",", "$borrow_officer,$return_officer");
    $sql = "SELECT * FROM officer WHERE officer_id IN (";
    foreach ($_list as $i => $l) {
        array_push($params, "$l");
        $sql .= "?";
        if ($i < count($_list) - 1) $sql .= ",";
    }
    $sql .= ")";
    try {
        $stmt = connect_db()->prepare($sql);
        $stmt->bindParam(1, $params[0]);
        $stmt->bindParam(2, $params[1]);
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $name = $row['officer_fname'] . " " . $row['officer_lname'];
            if ($row['officer_id'] == $borrow_officer) {
                $borrow['borrow_officer'] = $name;
            }
            if ($row['officer_id'] == $return_officer) {
                $borrow['return_officer'] = $name;
            }
        }

        echo json_encode([
            'result' => true,
            'status' => 'success', 'borrow' => $borrow
        ]);
    } catch (PDOException $e) {
        echo json_encode([
            'result' => false,
            'status' => 'error', 'err' => $e->getMessage()
        ]);
        http_response_code(500);
    }
    return;
}

if ($act == 'insert') {
    $sql = "INSERT INTO book_borrow VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
    $params = [
        $id,
        $bookname,
        $borrow_fname,
        $borrow_lname,
        $occup,
        $education_level,
        $year_class,
        $branch,
        $contact_number,
        "$borrow_date $borrow_time",
        "$return_date $return_time",
        $officer_id,
        '',
        'borrow',
        $create_at,
        $update_at,
        'false'
    ];
}

if ($act == 'update') {

    $params = [
        $bookname,
        $borrow_fname,
        $borrow_lname,
        $occup,
        $education_level,
        $year_class,
        $branch,
        $contact_number,
        "$borrow_date $borrow_time:00",
        "$return_date $return_time:00",
        $officer_id,
        'borrow',
        $update_at,
        $id,
    ];
    $sql = "UPDATE book_borrow SET bookname=?,borrow_fname=?,";
    $sql .= "borrow_lname=?,occupation=?,education_level=?,";
    $sql .= "year_class=?,branch=?,contact_number=?,borrow_date=?,";
    $sql .= "return_date=?,return_officer=?,status=?,";
    $sql .= "update_at=? WHERE borrow_id=?";
}

if ($act == 'delete') {
    $sql = "UPDATE book_borrow SET soft_delete=?,";
    $sql .= "update_at=? WHERE borrow_id=?";
    $params = ['true', $update_at, $id];
}
if ($act == 'return') {
    $sql = "UPDATE book_borrow SET status=?,";
    $sql .= "update_at=?,return_officer=? WHERE borrow_id=?";
    $params = ['return', $update_at, $officer_id, $id];
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
