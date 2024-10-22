<?php
require_once('./config/config_db.php');
require_once('./function/function.php');
require_once('./pag.php');

$soft_delete = 'true';
$params = [$soft_delete];
$start_dt = $_GET['start_dt'] ?? '';
$end_dt = $_GET['end_dt'] ?? '';
$status = $_GET['status'] ?? 'borrow';
$name = $_GET['name'] ?? '';
$n = '';
$paginate = paginate();
$page = $paginate['page'];
$per_page = $paginate['per_page'];
$sql = "SELECT * FROM book_borrow WHERE soft_delete !=? ";
if (!empty($start_dt) && !empty($end_dt)) {
    $sql .= " AND (create_at BETWEEN ? AND ?) ";
    array_push($params, $start_dt);
    array_push($params, $end_dt);
}
if (!empty(trim($name))) {
    $name = str_replace('-', ' ', $name);
    $result = findByName(['borrow_fname', 'borrow_lname'], $name, $params);

    $sql .= $result['sql'];
    $params = $result['params'];
    $n = str_replace('-', '', $name);
}
if (!empty($status) && $status != 'all') {
    $sql .= " AND status =? ";
    array_push($params, $status);
};
$all = getDataCountAll($sql, "borrow_id", $params, $page, $per_page);
$row_count = $all['row_count'];
$page_all = $all['page_all'];
$start_row = $all['start_row'];
$sql .= "ORDER BY create_at LIMIT ?,?";
array_push($params, $start_row);
array_push($params, $per_page);
$row = getDataAll($sql, $params);
?>

<div class="row align-items-center mb-4">
    <div class="col-auto">
        <?php echo entries_row_query($per_page) ?>
    </div>
    <div class="col-auto">
        <a href="./?r=mborrow_book" class="btn btn-sm btn-dark btn-hover-effect">
            ค่าเริ่มต้น
        </a>
    </div>
</div>

<div class="row my-1">
    <div class="col-md-6">
        <input type="search" value="<?php echo $n ?>" class="form-control border-dark" id="borrowByname" placeholder="ค้นหา">
    </div>
    <div class="col-auto">
        <input type="date" class="form-control border-dark" value="<?php echo $start_dt ?>" id="startDate">
        <p class="err-validate" id="validateStartDate"></p>
    </div>
    <div class="col-auto">
        <input type="date" class="form-control border-dark" value="<?php echo $end_dt ?>" id="endDate">
        <p class="err-validate" id="validateEndDate"></p>
    </div>
    <div class="col-auto">
        <div class="form-group my-1 d-flex align-items-center">
            <label class="mr-1">สถานะ</label>
            <select class="custom-select border-dark" data-status="<?php echo $status ?>" id="status">
                <option value="" selected>เลือก</option>
                <option value="borrow">ยืม</option>
                <option value="return">คืนแล้ว</option>
                <option value="all">ทั้งหมด</option>
            </select>
        </div>
    </div>
    <div class="col-auto">
        <button class="btn btn-sm btn-dark btn-hover-effect" id="findBorrowData">
            <i class="fa-solid fa-magnifying-glass"></i>
        </button>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-striped table-hover table-bordered">
        <thead class="bg-gradient-danger text-white">
            <tr class="align-middle">
                <th class="text-center" style="width: 5%;">ลำดับ</th>
                <th style="width: 15%;">เลขที่ยืม</th>
                <th style="width: 20%;">ชื่อ - นามสกุล</th>
                <th style="width: 15%;" scope="col">ยืม - กำหนดคืน</th>
                <th style="width: 10%;">สถานะ</th>
                <th style="width: 5%;">คืน</th>
                <th style="width: 20%;"></th>
            </tr>
        </thead>
        <tbody class="table-hover-effect">
            <?php
            $idx_start = ($page * $per_page) + 1;
            $idx_end = ($idx_start + $per_page) - 1;
            $idx = $start_row + 1;
            foreach ($row as $borrow) { ?>
                <tr class="align-middle">
                    <td class="text-center"><?php echo $idx++ ?></td>
                    <td><?php echo $borrow['borrow_id'] ?></td>
                    <td>
                        <?php echo $borrow['borrow_fname'] . " " . $borrow['borrow_lname'] ?>
                        <p class="m-0 text-danger"><?php echo getOccupation($borrow['occupation']) ?></p>
                    </td>
                    <td>
                        <p class="m-0"><?php echo date('Y-m-d H:i', strtotime($borrow['borrow_date'])) ?></p>
                        <p class="m-0 text-danger font-weight-bold"><?php echo date('Y-m-d H:i', strtotime($borrow['return_date'])) ?></p>
                    </td>
                    <td>
                        <p class="m-0 p-1 badge <?php echo $borrow['status'] == 'borrow' ? 'bg-danger' : 'bg-success' ?>">
                            <?php echo $borrow['status'] == 'borrow' ? 'ยืม' : 'คืนแล้ว' ?>
                        </p>
                    </td>
                    <td>
                        <?php if ($borrow['status'] == 'borrow') { ?>
                            <button name="borrow-return" class="btn btn-success btn-light btn-hover-effect" data-id="<?php echo $borrow['borrow_id'] ?>">
                                <i class="fa-solid fa-check"></i>
                            </button>
                        <?php } ?>
                    </td>
                    <td>
                        <button name="borrow-info" class="btn btn-sm btn-danger btn-hover-effect" data-id="<?php echo $borrow['borrow_id'] ?>">
                            <i class="fa-solid fa-info"></i>
                            <span class="ml-1">ข้อมูล</span>
                        </button>
                        <a class="btn btn-sm btn-light btn-hover-effect" href="./?r=borrow_b&act=update&id=<?php echo $borrow['borrow_id'] ?>">
                            <i class="fa-solid fa-pen"></i>
                            <span class="ml-1">แก้ไข</span>
                        </a>
                        <button name="borrow-remove" class="btn btn-sm btn-light btn-hover-effect" data-id="<?php echo $borrow['borrow_id'] ?>">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </td>
                </tr>
            <?php } ?>
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

    .bg-gradient-danger {
        background: linear-gradient(to right, #F95454, #C62E2E);
    }
</style>


<?php
$route = "r=mborrow_book";
$route .= !empty($status) ? "&status=$status" : '';
$route .= !empty($k) ? "&k=$k" : '';
$route .= !empty($dt) ? "&dt=$dt" : '';
$idx_end = $idx_end < $idx - 1 ?  $idx_end : $idx - 1;
echo create_pagination($page, $page_all, $route, $row_count, $idx_start, $idx_end);

?>

<?php require_once('./part/modal/borrow_modal.php') ?>

<script src="./assets/js/pagination.js"></script>
<script src="./assets/js/borrowbook_data.js"></script>