<?php
require_once('./config/config_db.php');
$n = isset($_GET['n']) ? str_ireplace('-', ' ', $_GET['n']) :  '';
$soft_delete = 'true';
$params = ['true'];
$paginate = paginate();
$page = $paginate['page'];
$per_page = $paginate['per_page'];

$sql = "SELECT * FROM book WHERE soft_delete !=? ";
if (!empty($n)) {
    $result = findByName(['book_name'], $n, $params);
    $sql .= $result['sql'];
    $params = $result['params'];
}

$all = getDataCountAll($sql, 'book_id', $params, $page, $per_page);
$row_count = $all['row_count'];
$page_all = $all['page_all'];
$start_row = $all['start_row'];
$sql .= "ORDER BY create_at LIMIT ?,?";
array_push($params, $start_row);
array_push($params, $per_page);
$row = getDataAll($sql, $params)
?>


<div class="row">
    <div class="col-auto">
        <?php echo entries_row_query($per_page) ?>
    </div>
    <div class="col-auto">
        <a href="./?r=book_form" class="my-1 btn btn-sm btn-dark" id="book-reset">
            <i class="fa-solid fa-plus"></i>
            <span class="ml-1">เพิ่มหนังสือ</span>
        </a>
    </div>
    <div class="col-auto">
        <a href="./?r=book_data" class="my-1 btn btn-sm btn-dark" id="book-reset">
            ค่าเริ่มต้น
        </a>
    </div>
</div>
<div class="row">
    <div class="col-md-8">
        <div class="input-group my-1">
            <input type="text" value="<?php echo $n ?>" class="form-control" id="book-search" placeholder="ป้อนชื่อหนังสือ">
            <button class="btn btn-dark" id="book-submit">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
        </div>
    </div>
</div>


<div class="table-responsive">
    <table class="table table-striped table-hover table-bordered">
        <thead class="bg-gradient-danger text-white">
            <tr class="align-middle">
                <th style="width: 5%;" class="text-center">ลำดับ</th>
                <th style="width: 10%;" class="text-center">ภาพ</th>
                <th style="width: 45%;">ชื่อหนังสือ</th>
                <th style="width: 15%;">ครั้งที่พิมพ์ ครั้งที่</th>
                <th style="width: 25%;" class="text-center"></th>
            </tr>
        </thead>
        <tbody class="table-hover-effect">
            <?php
            $idx_start = ($page * $per_page) + 1;
            $idx_end = ($idx_start + $per_page) - 1;
            $idx = $start_row + 1;
            foreach ($row as $book) { ?>
                <tr class="align-middle">
                    <td class="text-center"><?php echo $idx++  ?></td>
                    <td class="text-center">
                        <img src="<?php echo "./assets/book_img/" . $book['book_img'] ?>" class="img-thumbnail" style="width: 50px; height: 50px;">
                    </td>
                    <td>
                        <p class="m-0 font-weight-bold"><?php echo $book['book_name'] ?></p>
                        <p class="m-0 text-muted"><?php echo date("Y/m/d", strtotime($book['create_at'])) ?></p>
                    </td>
                    <td>
                        <p class="m-0"><?php echo $book['year_of_publication'] ?></p>
                        <p class="m-0 text-danger">ครั้งที่ <?php echo $book['edition'] ?></p>
                    </td>
                    <td class="text-center text-nowrap">
                        <button name="book-info" class="btn btn-sm btn-info btn-hover-effect m-1" data-id="<?php echo $book['book_id'] ?>">
                            <i class="fa-solid fa-info-circle"></i>
                            ข้อมูล
                        </button>
                        <a class="btn btn-sm btn-warning btn-hover-effect m-1" href="./?r=book_form&act=<?php echo "update&id=$book[book_id]" ?>" data-edit="<?php echo $book['book_id'] ?>">
                            <i class="fa-solid fa-edit"></i>
                            แก้ไข
                        </a>
                        <button name="book-remove" class="btn btn-sm btn-danger btn-hover-effect m-1" data-id="<?php echo $book['book_id'] ?>">
                            <i class="fa-solid fa-trash"></i>
                            ลบ
                        </button>
                    </td>
                </tr>
            <?php  }
            ?>
        </tbody>
    </table>
</div>

<style>
    .table-hover-effect tr:hover {
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
        transition: all 0.3s ease-in-out;
        background-color: #f8f9fa !important;
    }

    /* ปุ่มที่มีลูกเล่นเพิ่มเติม */
    .btn-hover-effect {
        position: relative;
        overflow: hidden;
        z-index: 1;
    }

    .btn-hover-effect::before {
        content: "";
        position: absolute;
        top: 0;
        left: -100%;
        width: 300%;
        height: 100%;
        background: rgba(255, 255, 255, 0.15);
        transform: skewX(25deg);
        transition: all 0.5s;
        z-index: -1;
    }

    .btn-hover-effect:hover::before {
        left: 100%;
    }
    .btn-hover-effect:hover {
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        transform: scale(1.05);
    }
    .bg-gradient-primary {
        background: linear-gradient(to right, #F95454, #C62E2E);
    }
    .img-thumbnail {
        border: 2px solid #fff;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    }
    .img-thumbnail:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        transform: scale(1.1);
        transition: all 0.3s ease-in-out;
    }
</style>





<?php
$route = "r=book_data&per_page=$per_page";
$route .= !empty($n) ? "&n=$n" : '';
$idx_end = $idx_end < $idx - 1 ?  $idx_end : $idx - 1;
echo create_pagination($page, $page_all, $route, $row_count, $idx_start, $idx_end);
?>
<?php require_once('./part/modal/bookInfo_modal.php') ?>
<script src="./assets/js/pagination.js"></script>
<script src="./assets/js/book_data.js"></script>