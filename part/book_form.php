<?php
require_once('./config/config_db.php');
require_once('./function/function.php');
$act = $_GET['act'] ?? 'insert';
$id = $_GET['id'] ?? '';
$row = [];
$category_opt = [];
if (!empty($id)) {
    $sql = "SELECT book.*,book_publisher.* FROM book ";
    $sql .= " LEFT JOIN book_publisher ON book.publisher_name=book_publisher.publisher_id";
    $sql .= " WHERE book.book_id=? AND book.soft_delete != ?";
    $params = [$id, 'true'];
    $row = getDataById($sql, $params);


    $book_category = explode(',', $row['book_category']);
    $book_category_map = array_map(function ($d) {
        return "'$d'";
    }, $book_category);

    $book_category_str = implode(',', $book_category_map);
    $book_category_str = "$book_category_str";
    $category_sql = "SELECT category_name,category_id FROM book_category";
    $category_sql .= " WHERE category_id IN ($book_category_str)";
    $stmt = connect_db()->prepare($category_sql);
    $stmt->execute();
    while ($opt = $stmt->fetch(PDO::FETCH_ASSOC)) {
        array_push($category_opt, $opt);
    }
}

?>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label>บาร์โค้ด</label>
            <input type="text" id="barcode" value="<?php echo $row['barcode_no'] ?? '' ?>" class="form-control p-4 input-focus-effect" placeholder="ป้อนเลขบาร์โค้ด">
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label>ISBN</label>
            <input type="text" id="isbn" value="<?php echo $row['isbn'] ?? '' ?>" class="form-control p-4 input-focus-effect" placeholder="ป้อนเลข ISBN">
        </div>
    </div>
</div>

<div class="form-group">
    <label>ชื่อหนังสือ</label>
    <input type="text" value="<?php echo $row['book_name'] ?? '' ?>" id="bookname" placeholder="ป้อนชื่อหนังสือ" class="form-control p-4 input-focus-effect">
</div>
<p class="err-validate" id="validate-bookname"></p>

<div class="form-group">
    <label>ผู้แต่ง</label>
    <input type="text" id="auther" value="<?php echo $row['auther'] ?? '' ?>" class="form-control p-4 input-focus-effect" placeholder="ชื่อ-นามสกุล ผู้แต่ง">
</div>
<p class="err-validate" id="validate-auther"></p>

<div class="row">
    <div class="col-md-8 select2-teal">
        <div class="form-group">
            <label>หมวดหมู่</label>
            <div>
                <select class="custom-select select2 select2-teal input-focus-effect" id="bookCategory" multiple>
                    <?php
                    if ($act == 'update') {
                        foreach ($category_opt as $opt) { ?>
                            <option selected value="<?php echo $opt['category_id'] ?>">
                                <?php echo $opt['category_name'] ?>
                            </option>
                    <?php }
                    } ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <input type="text" placeholder="ป้อนหมวดหมู่เพิ่มเติม" class="form-control more-input input-focus-effect">
        </div>
        <p class="err-validate" id="validate-bookCategory"></p>
    </div>

    <div class="col-auto">
        <div class="form-group">
            <label>ครั้งที่พิมพ์</label>
            <input type="number" value="<?php echo $row['edition'] ?? '' ?>" min="1" class="form-control input-focus-effect" id="edition" placeholder="ป้อนครั้งที่พิมพ์">
        </div>
        <p class="err-validate" id="validate-edition"></p>
    </div>

    <div class="col-md-8">
        <div class="form-group">
            <label>สำนักพิมพ์</label>
            <div class="select2-teal">
                <select class="custom-select select2 select2-teal input-focus-effect" id="publisherName">
                    <option selected value="">เลือกสำนักพิมพ์</option>
                    <?php if (isset($row['publisher_id'])) { ?>
                        <option selected value="<?php echo $row['publisher_id'] ?>">
                            <?php echo $row['publisher_name'] ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <p class="err-validate" id="validate-publisherName"></p>
    </div>

    <div class="col-auto">
        <div class="form-group">
            <label>ปีที่พิมพ์</label>
            <input type="number" value="<?php echo $row['year_of_publication'] ?? '' ?>" min="1" maxlength="4" id="yearOfPublication" placeholder="ป้อนปีที่พิมพ์" class="form-control input-focus-effect">
        </div>
        <p class="err-validate" id="validate-yearOfPublication"></p>
    </div>

    <div class="col-auto">
        <div class="form-group">
            <label>จำนวนหน้า</label>
            <input type="number" value="<?php echo $row['page_count'] ?? '' ?>" min="1" id="pageCount" placeholder="ป้อนจำนวนหน้า" class="form-control input-focus-effect">
        </div>
        <p class="err-validate" id="validate-pageCount"></p>
    </div>

    <div class="col-auto">
        <div class="form-group">
            <label>ราคา</label>
            <input type="number" value="<?php echo $row['price'] ?? '' ?>" min="1" placeholder="ป้อนราคา" id="price" class="form-control input-focus-effect">
        </div>
        <p class="err-validate" id="validate-bookPrice"></p>
    </div>
</div>

<label for="bookImg" class="my-3 btn btn-red-gradient">
    เลือกรูปปกหนังสือ
    <input type="file" class="d-none" id="bookImg">
</label>

<div class="row">
    <div class="col-md-3">
        <p>รูปภาพเดิม</p>
        <?php if (isset($row['book_img'])) { ?>
            <div class="preview-img">
                <img src="./assets/book_img/<?php echo $row['book_img'] ?>" class="book-preview">
            </div>
        <?php } ?>
    </div>
    <div class="col-md-3">
        <p>รูปภาพใหม่</p>
        <div id="bookImagePreview" class="preview-img"></div>
    </div>
</div>

<p class="err-validate" id="validate-bookImg"></p>

<div class="my-2">
    <button class="btn btn-red-gradient" id="bookHandleSubmit" data-id="<?php echo $id ?>" data-oldimg="<?php echo $row['book_img'] ?? '' ?>" data-act="<?php echo $act ?>">
        บันทึก
    </button>
</div>

<style>
    .input-focus-effect:focus {
        background-color: #f8d7da;
        border-color: #f5c6cb;
        box-shadow: 0 0 5px rgba(255, 0, 0, 0.5);
        transition: 0.3s;
    }

    .btn-red-gradient {
        background: linear-gradient(45deg, #ff1744, #ff8a80);
        color: white;
        border: none;
        box-shadow: 0 4px 10px rgba(255, 23, 68, 0.5);
        transition: all 0.3s ease;
    }

    .btn-red-gradient:hover {
        background: linear-gradient(45deg, #d50000, #ff5252);
        box-shadow: 0 6px 15px rgba(255, 23, 68, 0.7);
        transform: scale(1.05);
    }

    .form-control {
        border-radius: 10px;
    }

    .preview-img {
        height: 100px;
        width: 100px;
        border: 2px solid #f5c6cb;
        padding: 5px;
    }

    .preview-img img {
        width: 100%;
        height: auto;
    }

    .err-validate {
        color: #d32f2f;
    }

    /* เพิ่มเอฟเฟกต์เมื่อโฟกัสอินพุต */
    .input-focus-effect {
        transition: background-color 0.3s, box-shadow 0.3s;
    }

    .input-focus-effect:focus {
        background-color: #fce4ec;
        border-color: #f48fb1;
    }
</style>


<script src="./assets/AdminLTE-3.2.0/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

<script src="./assets/AdminLTE-3.2.0/plugins/select2/js/select2.full.min.js"></script>
<script src="./assets/js/book_form.js"></script>
