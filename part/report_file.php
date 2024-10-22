<?php
require_once('./config/config_db.php');
$r = $_GET['r'];
$page_target = "?r=$r";
$page = $_GET['page'] ?? 0;
$per_page = $_GET['per_page'] ?? 10;
$index_start = (int)$page * (int)$per_page;

$_start_dt = $_GET['start_dt'] ?? '';
$_end_dt = $_GET['end_dt'] ?? '';
$end_dt = '';
$start_dt = '';
$filetype = $_GET['filetype'] ?? '';
$soft_delete = 'true';
$params = ['true'];

$sql = "SELECT * FROM report_file WHERE  soft_delete != ? ";
$is_start_dt = !empty($_GET['start_dt']) && isset($_GET['start_dt']);
$is_end_dt = !empty($_GET['end_dt']) && isset($_GET['end_dt']);

if ($is_start_dt && $is_end_dt) {
    $start_dt = $_start_dt;
    $end_dt = $_end_dt;
    $page_target .= "&start_dt=$_start_dt&end_dt=$_end_dt";
    $_start_dt = $_GET['start_dt'] . " 00:00:00";
    $_end_dt = $_GET['end_dt'] . " 23:59:59";
    $sql .= " AND ( create_at BETWEEN ? AND ? ) ";

    array_push($params, $_start_dt);
    array_push($params, $_end_dt);
}
if (!empty($filetype)) {
    $page_target .= "&filetype=$filetype";
    array_push($params, $filetype);
    $sql .= " AND filetype = ? ";
}


$all = getDataCountAll($sql, 'file_id', $params, $page, $per_page);
$row_count = $all['row_count'];
$start_row = $all['start_row'];
$page_all = $all['page_all'];
$sql .= " ORDER BY create_at DESC LIMIT ?,?";
array_push($params, $start_row);
array_push($params, $per_page);
$row = getDataAll($sql, $params);
?>
<div class="row align-items-center mb-4">
    <div class="col-auto">
        <?php echo entries_row_query($per_page) ?>
    </div>
</div>

<div class="row my-1">
    <div class="col-auto">
        <div class="form-group">
            <label>เริ่มต้น</label>
            <input type="date" class="form-control" value="<?php echo $is_start_dt ? explode(' ', $_start_dt)[0] : '' ?>" id="startDate">
        </div>
        <p class="err-validate" id="validateStartDate"></p>
    </div>
    <div class="col-auto">
        <div class="form-group">
            <label>สิ้นสุด</label>
            <input type="date" class="form-control" value="<?php echo $is_end_dt ? explode(' ', $_end_dt)[0] : '' ?>" id="endDate">
        </div>
        <p class="err-validate" id="validateEndDate"></p>
    </div>

    <div class="col-auto">
        <div class="d-flex align-items-center">
            <label class="mr-1">ประเภท</label>
            <select id="fileType" class="custom-select">
                <option value="" selected>ไฟล์</option>
                <?php
                $filetypeList = ['pdf', 'xlsx'];
                for ($i = 0; $i < count($filetypeList); $i++) { ?>
                    <option <?php echo $filetype == $filetypeList[$i] ? 'selected' : '' ?> value="<?php echo $filetypeList[$i] ?>">
                        <?php echo strtoupper($filetypeList[$i]) ?>
                    </option>
                <?php } ?>
            </select>
        </div>
    </div>

    <div class="col-auto">
        <button class="my-1 btn btn-sm bg-dark" id="findReportFileBtn">
            <i class="fa-solid fa-magnifying-glass"></i>
            <span class="ml-1">ค้นหา</span>
        </button>
    </div>
    <div class="col-auto">
        <a href="./index.php?r=report_file" class="btn btn-sm btn-dark my-1">ค่าเริ่มต้น</a>
    </div>
</div>

<div class="row my-1 p-1">
    <?php if (isset($_GET['start_dt'])) { ?>
        <div class="col-auto">
            <span>ค้นหา</span>
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
            <tr>
                <th style="width: 5%;" class="text-center" scope="col">ลำดับ</th>
                <th style="width: 18%;" scope="col">วันที่สร้าง</th>
                <th style="width: 35%;" scope="col">ชื่อไฟล์</th>
                <th class="text-center" style="width: 12%;" scope="col">ชนิดไฟล์</th>
                <th style="width: 30%;" class="text-center" scope="col"></th>
            </tr>
        </thead>
        <tbody class="table-bordered">
            <?php if ($row_count == 0) { ?>
                <tr>
                    <td class="text-center" colspan="5">ไม่มีข้อมูล</td>
                </tr>
            <?php } ?>
            <?php
            $idx_start = ($page * $per_page) + 1;
            $idx_end = ($idx_start + $per_page) - 1;
            $idx = ($page * $per_page) + 1;
            foreach ($row as $r) {
            ?>
                <tr>
                    <th class="text-center" scope="row"><?php echo $idx++ ?></th>
                    <td><?php echo $r['create_at'] ?></td>
                    <td><?php echo $r['filename'] ?></td>
                    <td class="text-center">
                        <p class="m-0 badge <?php echo strtolower($r['filetype']) == 'pdf' ? 'bg-danger' : 'text-dark' ?>">
                            <?php echo strtolower($r['filetype']) ?>
                        </p>
                    </td>

                    <td class="text-center">
                        <a class="btn btn-sm bg-gradient-light" target="_blank" href="./<?php echo $r['storage'] ?>">
                            <i class="fa-solid fa-down-long"></i>
                            <span class="ml-1">ดาวน์โหลด</span>
                        </a>
                        <button name="filereport-remove" data-id="<?php echo $r['file_id'] ?>" class="btn btn-sm btn-light">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
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
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
        transition: all 0.3s ease-in-out;
        background-color: #f8f9fa !important;
    }
</style>






<?php
$route = "r=report_file";
$route .= $is_start_dt && $is_end_dt ? "&start_dt=$start_dt&end_dt=$end_dt" : '';
$route .= !empty($filetype) ? "&filetype=$filetype" : '';
$idx_end = $idx_end < $idx - 1 ?  $idx_end : $idx - 1;
echo create_pagination($page, $page_all, $route, $row_count, $idx_start, $idx_end);
?>

<script src="./assets/js/pagination.js"></script>
<script src="./assets/js/report_file.js"></script>