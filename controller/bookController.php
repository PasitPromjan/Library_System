<?php
require_once('../config/config_db.php');
require_once('../function/date.php');
require_once('../function/create_random.php');
require_once('../function/function.php');
$act = $_POST['act'] ?? '';
$id = $_POST['id'] ?? "ISBN" . date("Ymd") . random_char(4) . random_number(4);
$bname  = $_POST['bookname'] ?? '';
$book_category = $_POST['book_category'] ?? '';
$book_auther = $_POST['auther'] ?? '';
$book_edition = $_POST['edition'] ?? '';
$book_publisher = $_POST['publisher_name'] ?? '';
$book_pubyear = $_POST['year_of_publication'] ?? '';
$book_pagecount = $_POST['page_count'] ?? '';
$book_price = $_POST['price'] ?? '';
$isbn = $_POST['isbn'] ?? '';
$barcode_no = $_POST['barcode_no'] ?? '';
$create_at = create_date();
$update_at = create_date();


$search = $_POST['search'] ?? '';

$book_img = '';
$sql = "";
$params = [];

$book_img_dir = "../assets/book_img";
if (!is_dir($book_img_dir)) {
    mkdir($book_img_dir);
}
if (isset($_FILES['img'])) {
    $file = $_FILES['img'];
    $file_tmp_old = $file['tmp_name'];
    $filetype = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = date("Ymd") . random_char(4) . random_number(4);
    $book_img = "$filename.$filetype";
    $file_tmp_new = "$book_img_dir/$book_img";
    $m = move_uploaded_file($file_tmp_old, $file_tmp_new);
    if ($m != true) {
        echo json_encode([
            'result' => false,
            'status' => 'error',
            'msg' => 'อัพโหลดรูปภาพล้มเหลว'
        ]);
        http_response_code(500);
        return;
    }
}
if (empty($id) && empty($act) && empty($search)) {
    echo "ddd";
    return;
}

if (!empty($search)) {
    $sql = "SELECT book_id,book_name FROM book WHERE book_name LIKE  ? ";
    $sql .= "OR book_id=? LIMIT 0,10";
    $params = ["%$search%", $search];
    $stmt = connect_db()->prepare($sql);
    $stmt->execute($params);
    $_row = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        array_push($_row, $row);
    }
    $_row = array_map(function ($d) {
        return ['name' => $d['book_name'], 'id' => $d['book_id']];
    }, $_row);

    echo json_encode($_row);
    return;
}
// หากไม่มี id action หรือ search ให้ return


if (isset($_POST['id']) && empty($act)) {
    $sql = "SELECT * FROM book WHERE book_id=?";

    $row = getDataById($sql, [$id]);
    $category = [];
    $category_sql =   converttosqlstr($row['book_category']);
    $sql = "SELECT category_name,category_id FROM book_category WHERE category_id IN ($category_sql) ";
    $stmt = connect_db()->prepare($sql);
    $stmt->execute();

    while ($_c = $stmt->fetch(PDO::FETCH_ASSOC)) {
        array_push($category, $_c['category_name']);
    }
    $row['category'] = implode(',', $category);
    echo json_encode(['result' => true, 'status' => 'success', 'book' => $row]);
    return;
}
// สำหรับการค้นหา
// สำหรับ insert ข้อมูล
if ($act == 'insert') {
    $sql = "INSERT INTO book VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
    $params = [
        $id,
        $barcode_no,
        $isbn,
        $bname,
        $book_category,
        $book_auther,
        $book_edition,
        $book_publisher,
        $book_pubyear,
        $book_pagecount,
        $book_price,
        $book_img,
        $create_at,
        $update_at,
        'false'
    ];
}

if ($act == 'update') {
    $params = [
        $bname,
        $book_category,
        $book_auther,
        $book_edition,
        $book_publisher,
        $book_pubyear,
        $book_pagecount,
        $book_price,
        $isbn,
        $barcode_no
    ];
    $sql = "UPDATE book SET book_name=?,book_category=?,";
    $sql .= "auther=?,edition=?,publisher_name=?,";
    $sql .= "year_of_publication=?,page_count=?,";
    $sql .= "price=?,isbn=?,barcode_no=?";
    if (isset($_FILES['img'])) {
        $sql .= ",book_img=?";
        array_push($params, $book_img);
    }
    $sql .= ",update_at=? WHERE book_id=?";
    array_push($params, $update_at);
    array_push($params, $id);
}

if ($act == 'delete') {
    $sql = "UPDATE book SET soft_delete=?,";
    $sql .= "update_at=? WHERE book_id=?";
    $params = ['true', $update_at, $id];
}
if (!empty($sql)) {
    try {
        $response = ['result' => true, 'status' => 'success'];
        $stmt = connect_db()->prepare($sql);
        $stmt->execute($params);
        if ($act == 'update' && !empty($book_img)) {
            $old_img = $_POST['old_img'] ?? '';
            $path = "$book_img_dir/$old_img";
            if (file_exists($path)) {
                unlink($path);
            }
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
