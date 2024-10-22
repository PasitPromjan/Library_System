<?php
require_once('./config/config_db.php');
require_once('./function/function.php');
require_once('./pag.php');

// สำหรับการค้นหา
$start_dt = $_GET['start_dt'] ?? '';
$end_dt = $_GET['end_dt'] ?? '';
$status = $_GET['status'] ?? '';
$params = ['true'];
$paginate = paginate();
$page = $paginate['page'];
$per_page = $paginate['per_page'];

$sql = "SELECT * FROM book_borrow WHERE soft_delete !=?";
if (!empty($start_dt) && !empty($end_dt)) {
    $sql .= " AND (create_at BETWEEN ? AND ?) ";
    array_push($params, $start_dt);
    array_push($params, $end_dt);
}

if (!empty($status) && $status != 'all') {
    $sql .= " AND status =? ";
    array_push($params, $status);
}

$all = getDataCountAll($sql, 'book_borrow.borrow_id', $params, $page, $per_page);

$row_count = $all['row_count'];
$page_all = $all['page_all'];
$start_row = $all['start_row'];
$sql .= "ORDER BY create_at LIMIT ?,?";
array_push($params, $start_row);
array_push($params, $per_page);
$row = getDataAll($sql, $params);
?>
<div class="row">
    <div class="col-auto">
        <?php echo entries_row_query($per_page) ?>
    </div>
</div>

<div class="row">
    <div class="col-auto">
        <input type="date" class="form-control" value="<?php echo $start_dt ?>" id="reportStartDate" placeholder="ค้นหา">
        <p class="err-validate" id="validateStartDate"></p>
    </div>
    <div class="col-auto">
        <input type="date" class="form-control" value="<?php echo $end_dt ?>" id="reportEndDate" placeholder="ค้นหา">
        <p class="err-validate" id="validateEndDate"></p>
    </div>
    <div class="col-auto">
        <div class="form-group my-1 d-flex align-items-center">
            <label class="mr-1">สถานะ</label>
            <select data-status="<?php echo $status ?>" class="custom-select" id="status">
                <option value="" selected>เลือก</option>
                <option value="borrow">ยืม</option>
                <option value="return">คืนแล้ว</option>
                <option value="all">ทั้งหมด</option>
            </select>
        </div>
    </div>
    <div class="col-auto">
        <a href="./?r=report" class="btn btn-sm btn-secondary m-1">
            ค่าเริ่มต้น
        </a>
        <button class="btn btn-sm bg-dark m-1" id="findReportBtn">
            <i class="fa-solid fa-magnifying-glass"></i>
            <span class="ml-1">ค้นหา</span>
        </button>
        <button name="report-to-file" data-file="pdf" class="btn btn-sm bg-danger m-1">
            <i class="fa-solid fa-file-pdf"></i>
            <span class="ml-1">PDF</span>
        </button>
        <button name="report-to-file" data-file="excel" class="btn btn-sm bg-dark m-1">
            <i class="fa-solid fa-file-excel"></i>
            <span class="ml-1">Excel</span>
        </button>
    </div>
</div>

<div class="row p-1">
    <?php if (isset($_GET['status'])) { ?>
        <div class="col-auto">
            <span class="text-secondary">สถานะ</span>
            <span class="text-danger"><?php echo getBorrowStatus($status) ?></span>
        </div>
    <?php } ?>
    <?php if (isset($_GET['name'])) { ?>
        <div class="col-auto">
            <span class="text-secondary">คำค้นหา</span>
            <span class="text-danger"><?php echo $name ?></span>
        </div>
    <?php } ?>
    <?php if (isset($_GET['start_dt'])) { ?>
        <div class="col-auto">
            <span>ตั้งแต่วันที่ </span>
            <strong class="text-danger"><?php echo $start_dt ?></strong>
            <span>ถึงวันที่ </span>
            <strong class="text-danger"><?php echo $end_dt ?></strong>
        </div>
    <?php } ?>
</div>

<div class="table-responsive">
    <table class="table table-hover">
        <thead class="bg-gradient-danger text-white">
            <tr class="align-middle">
                <th class="text-center" style="width: 5%;">ลำดับ</th>
                <th style="width: 15%;">เลขที่ยืม</th>
                <th style="width: 15%;">ชื่อ - นามสกุล</th>
                <th style="width: 18%;">อาชีพ</th>
                <th style="width: 18%;" scope="col">วันที่ยืม - กำหนดคืน</th>
                <th style="width: 12%;">เบอร์ติดต่อ</th>
                <th style="width: 17%;">รหัสพนักงาน (ยืม - คืน)</th>
            </tr>
        </thead>
        <tbody class="table-bordered">
            <?php
            $idx_start = ($page * $per_page) + 1;
            $idx_end = ($idx_start + $per_page) - 1;
            $idx = $start_row + 1;
            foreach ($row as $borrow) { ?>
                <tr class="align-middle">
                    <td class="text-center"><?php echo $idx++ ?></td>
                    <td><?php echo $borrow['borrow_id'] ?></td>
                    <td><?php echo $borrow['borrow_fname'] . " " . $borrow['borrow_lname'] ?></td>
                    <td>
                        <p class="m-0 text-teal"><?php echo getOccupation($borrow['occupation']) ?></p>
                        <p class="m-0"><?php echo getEducationLevel($borrow['education_level']) ?></p>
                        <?php if (!empty($borrow['year_class'])) { ?>
                            <p class="m-0 text-muted"><?php echo "ชั้นปี " . $borrow['year_class'] ?></p>
                        <?php } ?>
                    </td>
                    <td>
                        <?php echo date('Y-m-d H:i', strtotime($borrow['borrow_date'])) ?>
                        <p class="m-0 text-danger font-weight-bold"><?php echo date('Y-m-d H:i', strtotime($borrow['return_date'])) ?></p>
                    </td>
                    <td><?php echo $borrow['contact_number'] ?></td>
                    <td>
                        <p class="m-0 text-danger"><?php echo $borrow['borrow_officer'] ?></p>
                        <p class="m-0"><?php echo $borrow['return_officer'] ?></p>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<style>
    .bg-gradient-danger {
        background: linear-gradient(to right, #F95454, #C62E2E);
    }

    .table-hover tr:hover {
        background-color: rgba(255, 0, 0, 0.1);
        transition: background-color 0.3s;
    }
</style>







<?php

$route = "r=report";
$route .= !empty($status) ? "&status=$status" : '';
$route .= !empty($name) ? "&name=$name" : '';
$route .= !empty($start_dt) && !empty($end_dt) ? "&start_dt=$start_dt&end_dt=$end_dt" : '';

$idx_end = $idx_end < $idx - 1 ?  $idx_end : $idx - 1;
echo create_pagination($page, $page_all, $route, $row_count, $idx_start, $idx_end);
?>
<?php require_once('./part/modal/borrow_modal.php') ?>



<script src="./assets/js/report.js"></script>
<script src="./assets/js/pagination.js"></script>