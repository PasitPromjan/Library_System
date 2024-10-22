<?php
require_once('./config/config_db.php');
$n = isset($_GET['n']) ? str_ireplace('-', ' ', $_GET['n']) :  '';
$name = "";
$params = ['true'];
$paginate = paginate();
$page = $paginate['page'];
$per_page = $paginate['per_page'];
$soft_delete = 'true';
$sql = "SELECT * FROM book_publisher WHERE soft_delete !=? ";
if (!empty($n)) {
    $result  =  findByName(['publisher_name'], $n, $params);
    $params = $result['params'];
    $sql .= $result['sql'];
}
$all = getDataCountAll($sql, 'publisher_id', $params, $page, $per_page);
$row_count = $all['row_count'];
$page_all = $all['page_all'];
$start_row = $all['start_row'];
$sql .= "ORDER BY create_at LIMIT ?,?";
array_push($params, $page);
array_push($params, $per_page);
$row = getDataAll($sql, $params);

?>
<div class="text-right my-1 p-1"></div>
<div class="row align-items-center mb-4">
    <div class="col-auto">
        <?php echo entries_row_query($per_page) ?>
    </div>
    <div class="col-auto">
        <button class="btn btn-sm btn-dark" name="open-publisher-modal" data-act="insert">
            <i class="fa-solid fa-plus"></i>
            <span class="ml-1">เพิ่มสำนักพิมพ์</span>
        </button>
    </div>
    <div class="col-auto">
        <a href="./?r=m_publisher" class="my-1 btn btn-sm btn-dark">
            ค่าเริ่มต้น
        </a>
    </div>
</div>
<div class="row my-1">
    <div class="col-md-8">
        <div class="input-group">
            <input type="text" class="form-control border-dark" id="publisher-search" placeholder="ป้อนชื่อสำนักพิมพ์">
            <button class="btn btn-dark" id="publisher-submit">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
        </div>
    </div>
</div>

<?php if (!empty($n)) { ?>
    <h6 class="search text-secondary">
        ค้นหา <?php echo "'$n'" ?>
    </h6>
    <h6 class="search">
        <span>ผลลัพธ์</span>
        <span class="ms-4 text-danger"><?php echo "'$row_count'" ?></span>
    </h6>
<?php } ?>

<div class="table-responsive">
    <table class="table table-striped table-hover table-bordered">
        <thead class="bg-gradient-danger text-white">
            <tr class="align-middle">
                <th style="width: 5%;" scope="col">ลำดับ</th>
                <th style="width: 30%;" scope="col">ชื่อสำนักงานพิมพ์</th>
                <th style="width: 20%;" scope="col">วันที่เพิ่ม</th>
                <th style="width: 20%;" scope="col">วันแก้ไขล่าสุด</th>
                <th style="width: 25%;" scope="col" class="text-center"></th>
            </tr>
        </thead>
        <tbody class="table-hover-effect">
            <?php if (count($row) == 0) { ?>
                <tr class="align-middle">
                    <td colspan="6">ไม่มีข้อมูล</td>
                </tr>
            <?php } ?>
            <?php
            $idx_start = ($page * $per_page) + 1;
            $idx_end = ($idx_start + $per_page) - 1;
            $idx = $start_row + 1;
            foreach ($row as $r) { ?>
                <tr>
                    <td class="text-center" scope="row"><?php echo $idx++ ?></td>
                    <td><?php echo $r['publisher_name'] ?></td>
                    <td><?php echo $r['create_at'] ?></td>
                    <td><?php echo $r['update_at'] ?></td>
                    <td class="text-center">
                        <button data-act="update" name="open-publisher-modal" class="btn btn-sm btn-warning btn-hover-effect m-1" data-id="<?php echo $r['publisher_id'] ?>">
                            <i class="fa-solid fa-pen"></i>
                            <span class="ml-1">แก้ไข</span>
                        </button>
                        <button name="publisher-remove" class="btn btn-sm btn-danger" data-id="<?php echo $r['publisher_id'] ?>">
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

    .bg-gradient-danger {
        background: linear-gradient(to right, #F95454, #C62E2E);
    }
</style>



<?php
$route = "r=m_publisher&per_page=$per_page";
$route .= !empty($n) ? "&n=$n" : '';
$idx_end = $idx_end < $idx - 1 ?  $idx_end : $idx - 1;
echo create_pagination($page, $page_all, $route, $row_count, $idx_start, $idx_end);
?>


<?php require_once('./part/modal/publisher_modal.php') ?>
<script src="./assets/js/pagination.js"></script>
<script src="./assets/js/publisher.js"></script>