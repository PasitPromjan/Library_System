<?php
require_once('./config/config_db.php');

$params = ['true'];
$paginate = paginate();
$page = $paginate['page'];
$per_page = $paginate['per_page'];

$sql = "SELECT * FROM officer WHERE soft_delete !=? ";
$all = getDataCountAll($sql, 'officer_id', $params, $page, $per_page);
$row_count = $all['row_count'];
$page_all = $all['page_all'];
$start_row = $all['start_row'];
$sql .= "ORDER BY create_at LIMIT ?,?";
array_push($params, $start_row);
array_push($params, $per_page);
$row = getDataAll($sql, $params)
?>
<div class="row align-items-center mb-4">
    <div class="col-auto">
        <?php echo entries_row_query($per_page) ?>
    </div>
    <div class="col-auto">
        <button class="btn btn-sm btn-dark" name="open-officer-modal" data-act="insert">
            เพิ่ม
        </button>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-hover">
        <thead class="bg-gradient-danger text-white">
            <tr class="align-middle">
                <th style="width: 5%;" class="text-center">ลำดับ</th>
                <th style="width: 25%;">บัญชีผู้ใช้งาน</th>
                <th style="width: 35%;">ชื่อ - นามสกุล</th>
                <th style="width: 15%;">วันที่สร้าง</th>
                <th style="width: 20%;"></th>
            </tr>
        </thead>
        <tbody>
            <?php
            $idx_start = ($page * $per_page) + 1;
            $idx_end = ($idx_start + $per_page) - 1;
            $idx = $start_row + 1;
            foreach ($row as $officer) { ?>
                <tr class="align-middle">
                    <td class="text-center"><?php echo $idx++ ?></td>
                    <td>
                        <p class="m-0 font-weight-bold"><?php echo $officer['username'] ?></p>
                        <p class="m-0 text-muted"><?php echo $officer['officer_id'] ?></p>
                    </td>
                    <td>
                        <?php echo $officer['officer_fname'] . " " . $officer['officer_lname'] ?>
                        <p class="m-0 text-teal"><?php echo getOfficerRole($officer['role']) ?></p>
                    </td>
                    <td>
                        <?php echo date('Y-m-d', strtotime($officer['create_at'])) ?>
                        <p class="m-0"><?php echo date('H:i:s', strtotime($officer['create_at'])) ?></p>
                    </td>
                    <td class="text-center">
                        <button data-act="update" name="open-officer-modal" class="btn btn-sm btn-info" data-id="<?php echo $officer['officer_id'] ?>">
                            <i class="fa-solid fa-pen"></i>
                            <span class="ml-1">แก้ไข</span>
                        </button>

                        <?php if ($officer['role'] != 'admin') { ?>
                            <button name="officer-remove" class="btn btn-sm btn-danger" data-id="<?php echo $officer['officer_id'] ?>">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        <?php } ?>

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
$route = "r=m_officer&per_page=$per_page";
$route .= !empty($n) ? "&n=$n" : '';
$idx_end = $idx_end < $idx - 1 ?  $idx_end : $idx - 1;
echo create_pagination($page, $page_all, $route, $row_count, $idx_start, $idx_end);
?>

<?php require_once('./part/modal/officer_modal.php')  ?>
<script src="./assets/js/pagination.js"></script>
<script src="./assets/js/officer.js"></script>