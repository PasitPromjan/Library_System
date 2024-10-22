<?php
require_once('./config/config_db.php');
$n = isset($_GET['n']) ? str_ireplace('-', ' ', $_GET['n']) :  '';
$paginate = paginate();
$page = $paginate['page'];
$per_page = $paginate['per_page'];
$params = ['true'];

$sql = "SELECT * FROM book_category WHERE soft_delete !=? ";
if (!empty($n)) {
    $result  =  findByName(['category_name'], $n, $params);
    $params = $result['params'];
    $sql .= $result['sql'];
}
$all = getDataCountAll($sql, 'category_id', $params, $page, $per_page);
$row_count = $all['row_count'];
$page_all = $all['page_all'];
$start_row = $all['start_row'];
$sql .= "ORDER BY create_at LIMIT ?,?";
array_push($params, $start_row);
array_push($params, $per_page);
$row = getDataAll($sql, $params);
?>
<div class="text-right my-1"></div>

<div class="row align-items-center">
    <div class="col-auto">
        <?php echo entries_row_query($per_page) ?>
    </div>
    <div class="col-auto">
        <button data-act="insert" class="btn btn-sm bg-dark" name="open-cat-modal">
            <i class="fa-solid fa-plus"></i>
            <span class="ml-1">เพิ่มหมวดหมู่</span>
        </button>
    </div>
    <div class="col-auto">
        <a class="btn btn-sm bg-dark" href="./?r=mcat_b">ค่าเริ่มต้น</a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="input-group my-1">
            <input type="text" value="<?php echo $n ?>" class="form-control" id="findByName" placeholder="ป้อนชื่อหมวดหมู่">
            <button class="btn bg-dark input-group-text" id="findByNameBtn">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
        </div>
    </div>
</div>

<div class="table-responsive">
    <table class="table">
        <thead class="bg-gradient-danger text-white">
            <tr class="align-middle">
                <th style="width: 5%;" scope="col">ลำดับ</th>
                <th style="width: 40%;" scope="col">หมวดหมู่</th>
                <th style="width: 20%;" scope="col">วันที่เพิ่ม</th>
                <th style="width: 20%;" scope="col">วันแก้ไขล่าสุด</th>
                <th style="width: 15%;" scope="col">แก้ไข / ลบ</th>
            </tr>
        </thead>
        <tbody class="table-bordered">
            <?php if ($row_count == 0) { ?>
                <tr>
                    <td colspan="5">ไม่มีข้อมูล</td>
                </tr>
            <?php } ?>
            <?php
            $idx_start = ($page * $per_page) + 1;
            $idx_end = ($idx_start + $per_page) - 1;
            $idx = $start_row + 1;
            foreach ($row as $category) { ?>
                <tr class="align-middle">
                    <td class="text-center" scope="row"><?php echo $idx++ ?></td>
                    <td><?php echo $category['category_name'] ?></td>
                    <td><?php echo date('Y-m-d', strtotime($category['create_at'])) ?></td>
                    <td><?php echo date('Y-m-d', strtotime($category['update_at'])) ?></td>
                    <td>
                        <button name="open-cat-modal" data-act="update" class="btn btn-sm bg-gradient-teal" data-id="<?php echo $category['category_id'] ?>">
                            <i class="fa-solid fa-pen"></i>
                            <span class="m-1">แก้ไข</span>
                        </button>
                        <button name="category-remove" class="btn bg-gradient-light" data-id="<?php echo $category['category_id'] ?>">
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

    .bg-gradient-teal {
        background: linear-gradient(to right, #1abc9c, #16a085);
    }

    .bg-gradient-light {
        background: linear-gradient(to right, #f1f1f1, #e0e0e0);
    }

    .table-hover tr:hover {
        background-color: rgba(255, 0, 0, 0.1);
        transition: background-color 0.3s;
    }
</style>





<?php
$r = "r=mcat_b&per_page=$per_page";
$idx_end = $idx_end < $idx - 1 ?  $idx_end : $idx - 1;
echo create_pagination($page, $page_all, $r, $row_count, $idx_start, $idx_end);
?>




<?php require_once('./part/modal/categorybook_modal.php') ?>
<script src="./assets/js/pagination.js"></script>
<script src="./assets/js/categorybook.js"></script>