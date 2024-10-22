<?php

require_once('./config/config_db.php');
require_once('./function/total.php');
require_once('./function/date.php');
require_once('./function/number_format.php');
require_once('./function/function.php');



$today_str = date('Y-m-d');
$_dt = $_GET['date'] ?? date('d');
$_m = $_GET['m'] ??  date('m');
$_y = $_GET['y']   ?? date('Y');


$start_dt = $_GET['start_dt']  ?? date('Y-m-d');
$end_dt = $_GET['end_dt']  ?? date('Y-m-d');

if (isset($_GET['m']) || isset($_GET['y'])) {
    $today_str = "$_y-$_m-$_dt";
} else if (isset($_GET['date'])) {
    $today_str = $_dt;
}

$dt = (int)date('d', strtotime($today_str));
$m = (int) date('m', strtotime($today_str));
$m_count = date('t', strtotime($today_str));
$y = date('Y', strtotime($today_str));
$w_day = date('w', strtotime($today_str));

$today = date('Y-m-d', strtotime($today_str));
$today = "%$today%";

$sw  = $dt;
$ew = $dt;
$sw_m = $m;
$ew_m = $m;
$sw_y = $y;
$ew_y = $y;


switch ($w_day) {
    case 0:
        $ew = $dt;
        $sw = $dt - 6;
        break;
    case 1:
        $ew = $dt + 6;
        $sw = $dt;
        break;
    case 2:
        $ew = $dt + 5;
        $sw = ($dt - 1);
        break;
    case 3:
        $ew = $dt + 4;
        $sw = ($dt - 2);
        break;
    case 4:
        $ew = $dt + 3;
        $sw = ($dt - 3);
        break;

    case 5:
        $ew = $dt + 2;
        $sw = ($dt - 4);
        break;
    case 6:
        $ew = $dt + 1;
        $sw = $dt - 5;
        break;
}
if ($sw < 0) {
    $sw_m = $m - 1;
    $m_count_after = date('t', strtotime("$sw_y-$sw_m-01"));
    $sw = (int) $m_count_after - abs($sw);
}
if ($ew > $m_count) {
    $ew_m = $m + 1;
    $ew -= $m_count;
}
if ($ew_m > 12) {
    $sw_y + 1;
    $ew_m = 1;
}

$sw_str = get_countdate($sw);
$ew_str = get_countdate($ew);
$sw_mstr = get_countdate($sw_m);
$ew_mstr = get_countdate($ew_m);
$week_start = "$sw_y-$sw_mstr-$sw_str ";
$week_end = "$sw_y-$ew_mstr-$ew_str";
$this_ms = "$y-" . get_countdate($m) . "-01";
$this_me = "$y-" . get_countdate($m) . "-$m_count";
$this_ys = "$y-01-01";
$this_ye = "$y-12-31";
$sql_str = "SELECT book.*, book_borrow.*, book_category.*,COUNT(borrow_id) as count FROM book_borrow ";
$sql_str .= "INNER JOIN book ON book_borrow.bookname = book.book_id ";
$sql_str .= "INNER JOIN book_category ON book.book_category = book_category.category_id";
$today_sql = $sql_str . " WHERE book_borrow.create_at LIKE ? ";
$today_total = get_total($today_sql, array("%$today%"));
$week_sql = $sql_str . " WHERE book_borrow.create_at BETWEEN ? AND ? ";
$week_total = get_total($week_sql, array($week_start, $week_end));
$month_sql = $sql_str . " WHERE book_borrow.create_at BETWEEN ? AND ? ";
$month_total = get_total($month_sql, array($this_ms, $this_me));
$year_sql = $sql_str . " WHERE book_borrow.create_at BETWEEN ? AND ? ";
$year_total = get_total($month_sql, array($this_ys, $this_ye));


$data_by_book = [
    'book_id' => [],
    'book_name' => [],
    'count' => [],
    'percent' => []
];


$data_by_category = [
    'category_id' => [],
    'category' => [],
    'count' => [],
    'percent' => []
];
$sql = "SELECT book.book_id,book.book_name,book.book_category ,book_borrow.borrow_id,book_borrow.bookname,";
$sql .= "book_borrow.create_at FROM book_borrow ";
$sql .= "INNER JOIN book ON book_borrow.bookname = book.book_id ";
$sql .= " WHERE book_borrow.create_at BETWEEN ? AND ? LIMIT 0,25 ";

$start_dt_q = date('Y-m-01', strtotime($start_dt));
$end_dt_q = date('Y-m-t', strtotime($end_dt));

$stmt = connect_db()->prepare($sql);
$stmt->bindParam(1, $start_dt_q);
$stmt->bindParam(2, $end_dt_q);
$stmt->execute();
$borrow_count = $stmt->rowCount();
$_s = explode('-', $start_dt_q);
$_e = explode('-', $end_dt_q);
$last_day_month = date('t', strtotime("$_e[0]-$_e[1]-01"));
$my_start = date('Y-m-d', strtotime("$_s[0]-$_s[1]-01"));
$my_end = date('Y-m-d', strtotime("$_e[0]-$_e[1]-$last_day_month"));

$s = (int) $_s[1];
$e = (int) $_e[1];
$sy = $_s[0];
$ey = $_e[0];
$count_y = $ey - $sy;
$r = $count_y > 0 ?  $e + 12 : $e;


$__m = $s;
$ry = (int) $_e[0] - (int)$_s[0];

$data_by_category_m = [];
$data_by_book_m = [];
$__ms = $s;
$__me = $e;
$__y = $sy;
for ($i = $s; $i <= $r; $i++) {
    $m = $i;
    $y = $sy;
    if ($i > 12) {
        $m = $i - 12;
        $y = $sy + 1;
    }
    $fm = get_countdate($m);
    $fm_thai = getMonthThai($m) . " " . getYeatThai($y);
    $__date = "$y-$fm";
    $_data = [
        'date_thai' => $fm_thai,
        'date' => "$y-$fm",
        'category' => [],
        'category_id' => [],
        'count' => [],
        'percent' => []
    ];
    $data_by_book_m[$__date] = [
        'date_thai' => $fm_thai,
        'date' => "$y-$fm",
        'bookname' => [],
        'book_id' => [],
        'count' => [],
        'percent' => []
    ];
    $data_by_category_m[$__date] = $_data;
}


while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $book_id = $row['book_id'];
    $bookname = $row['book_name'];
    $create_at = date('Y-m', strtotime($row['create_at']));
    $book_key = array_keys($data_by_book_m);
    $book_key_index = array_search($create_at, $book_key);
    $has_book =   array_search($book_id, $data_by_book['book_id']);
    $is_book = gettype($has_book);

    $ca_str_sql =   converttosqlstr($row['book_category']);
    $bc = "SELECT category_name,category_id  FROM book_category WHERE category_id IN ($ca_str_sql)";
    $bstmt = connect_db()->prepare($bc);
    $bstmt->execute();
    while ($category_r = ($bstmt->fetch(PDO::FETCH_ASSOC))) {
        $category_id = $category_r['category_id'];
        $category = $category_r['category_name'];
        $category_key = array_keys($data_by_category_m);
        $category_key_index = array_search($create_at, $category_key);
        $has_category = array_search($category_id, $data_by_category['category_id']);
        $is_has_category = gettype($has_category);
        if (gettype($category_key_index) == 'integer') {
            $has_sort = array_search($category_id, $data_by_category_m[$create_at]['category_id']);
            switch (gettype($has_sort)) {
                case 'integer':
                    $data_by_category_m[$create_at]['count'][$has_sort]++;
                    break;
                case 'boolean':
                    array_push($data_by_category_m[$create_at]['category_id'], $category_id);
                    array_push($data_by_category_m[$create_at]['category'], $category);
                    array_push($data_by_category_m[$create_at]['count'], 1);
                    break;
            }
        }
        switch ($is_has_category) {
            case 'integer':
                $data_by_category['count'][$has_category]++;
                break;
            case 'boolean':
                array_push($data_by_category['category_id'], $category_id);
                array_push($data_by_category['category'], $category);
                array_push($data_by_category['count'], 1);
                break;
        }
    }



    if (gettype($book_key_index) == 'integer') {
        $book_has_sort = array_search($book_id, $data_by_book_m[$create_at]['book_id']);
        switch (gettype($book_has_sort)) {
            case 'integer':
                $data_by_book_m[$create_at]['count'][$book_has_sort]++;
                break;
            case 'boolean':
                array_push($data_by_book_m[$create_at]['book_id'], $book_id);
                array_push($data_by_book_m[$create_at]['bookname'], $bookname);
                array_push($data_by_book_m[$create_at]['count'], 1);
                break;
        }
    }

    switch ($is_book) {
        case 'integer':
            $data_by_book['count'][$has_book]++;
            break;
        case 'boolean':
            array_push($data_by_book['book_id'], $book_id);
            array_push($data_by_book['book_name'], $bookname);
            array_push($data_by_book['count'], 1);
            break;
    }
}
for ($i = 0; $i < count($data_by_book['book_id']); $i++) {
    $c = $data_by_book['count'][$i];
    $percent = get_percent_total($borrow_count, $c);
    $data_by_book['percent'][$i] = $percent;
}
for ($i = 0; $i < count($data_by_category['category_id']); $i++) {
    $_c = $data_by_category['count'][$i];
    $percent = get_percent_total($borrow_count, $_c);
    $data_by_category['percent'][$i] = $percent;
}

$_sum = summation($data_by_category['count']);
for ($i = 0; $i < count($data_by_category['category_id']); $i++) {
    $_c = $data_by_category['count'][$i];
    $percent = get_percent_total($_sum, $_c);
    $data_by_category['percent'][$i] = $percent;
}
function summation($num)
{
    $sum = 0;
    foreach ($num as $n) {
        $sum += $n;
    }
    return $sum;
}
$data_by_category_mkey = array_keys($data_by_category_m);
$sum_c = 0;
foreach ($data_by_category_mkey  as $_d) {
    $_sum = summation($data_by_category_m[$_d]['count']);
    $sum_c += $_sum;
}
foreach ($data_by_category_mkey  as $_d) {
    $sum = summation($data_by_category_m[$_d]['count']);
    for ($i = 0; $i < count($data_by_category_m[$_d]['count']); $i++) {
        $_c = $data_by_category_m[$_d]['count'][$i];
        $percent = get_percent_total($sum_c, $_c);
        array_push($data_by_category_m[$_d]['percent'], $percent);
    }
}
$data_by_book_mkey = array_keys($data_by_book_m);
foreach ($data_by_book_mkey  as $_d) {
    $sum = summation($data_by_book_m[$_d]['count']);
    for ($i = 0; $i < count($data_by_book_m[$_d]['count']); $i++) {
        $_c = $data_by_book_m[$_d]['count'][$i];
        $percent = get_percent_total($sum, $_c);
        array_push($data_by_book_m[$_d]['percent'], $percent);
    }
}

?>

<div class="row my-1 p-1 ">
    <div class="col-auto">
        <div class="form-group">
            <label>เริ่มต้น</label>
            <input type="date" value="<?php echo $start_dt ?>" class="form-control" id="startDate">
        </div>
    </div>
    <div class="col-auto">
        <div class="form-group">
            <label>ถึง</label>
            <input type="date" value="<?php echo $end_dt ?>" class="form-control" id="endDate">
        </div>
    </div>

    <div class="col-auto" style="margin-top:26px;">
            <button class="btn bg-info" id="findDashboardBtn">
                <i class="fa-solid fa-magnifying-glass "></i>
                <span>ค้นหา</span>
            </button>       
    </div>
    <div class="col-auto" style="margin-top:26px;">
            <button class="btn bg-danger" id="resetDashboardBtn" onclick="location.assign('./index.php?r=dashboard')">
                <i class="fa-solid fa-xmark"></i>
                <span>ล้าง</span>
            </button>
        
    </div>
    <script>
        const retainMonth = $('#findDashboardByMonth').attr('data-month')
        $.each($('#findDashboardByMonth').children(), (i, opt) => {
            if (retainMonth == $(opt).val()) {
                $(opt).prop('selected', true)
            }
        })
        $('#findDashboardBtn').click(function() {
            const startDate = $('#startDate').val().trim()
            const endDate = $('#endDate').val().trim()
            const ds = startDate.split('-')
            const ys = Number(ds[0])
            const ms = Number(ds[1])
            const de = endDate.split('-')
            const me = Number(de[1])
            const ye = Number(de[0])
            const diff = (me - ms)
            const diff_year = ye - ys
            const m_ofyear = 12
            const count_m = ((m_ofyear * diff_year) + me) - ms
            let is_validate = true
            let msg = ''
            if (startDate != '' && endDate != '') {
                const start_stamp = getTimeStampNumber(startDate)
                const end_stamp = getTimeStampNumber(endDate)
                if (start_stamp > end_stamp) {
                    msg = 'เวลาเริ่มต้นต้องน้อยกว่า เวลาสิ้นสุด'
                    is_validate = false
                }
                if (end_stamp >= start_stamp) {
                    if (count_m > 5) {
                        is_validate = false
                        msg = 'สามารถเลือกเวลามากที่สุด 6 เดือน'
                    }
                }
            }
            if (!is_validate) errDialog('แจ้งเตือน', msg, '')

            if (is_validate) {
                let r = ``
                r += `&start_dt=${startDate}`
                r += `&end_dt=${endDate}`
                if (r != '') {
                    r = `./index.php?r=dashboard${r}`
                    location.assign(r)
                }
            }
        })
    </script>
</div>
<?php
$start_dt_thai = (int) date('d', strtotime($start_dt));
$start_m_thai = getMonthThai(date('m', strtotime($start_dt)));
$start_y_thai = getYeatThai(date('Y', strtotime($start_dt)));
$start_date_thai = "$start_dt_thai $start_m_thai $start_y_thai";

$end_dt_thai = (int) date('d', strtotime($end_dt));
$end_m_thai = getMonthThai(date('m', strtotime($end_dt)));
$end_y_thai = getYeatThai(date('Y', strtotime($end_dt)));
$end_date_thai = "$end_dt_thai $end_m_thai $end_y_thai";

$today_dt_thai = (int) date('d', strtotime($today_str));
$today_m_thai = getMonthThai(date('m', strtotime($today_str)));
$today_y_thai = getYeatThai(date('Y', strtotime($today_str)));
$today_date_thai = "$today_dt_thai $today_m_thai $today_y_thai";
?>

<h3 class="text-center">ข้อมูลการยืมช่วงเวลานี้</h3>
<div class="info-box">
  <span class="info-box-icon bg-info"><i class="far fa-bookmark"></i></span>
  <div class="info-box-content">
    <h5 class="m-0 p-0 font-weight-bold"><?php echo $start_date_thai. " ถึง ". $end_date_thai ?></h5>
    <span class="info-box-number"><?php  echo $today_total ?></span>
    <div class="progress">
      <div class="progress-bar bg-info" style="width: <?php echo $today_total/$year_total*100 ?>%"></div>
    </div>
    <span class="progress-description">
        <?php echo $today_total/$year_total*100 ?> %
    </span>
  </div>
</div>
<div class="row">
    <div class="col-sm-6 col-12">
        <div class="info-box border-0 bg-warning">
            <span class="info-box-icon">
                <i class="fa-solid fa-book-open  h2"></i>
            </span>
            <div class="info-box-content">
                <strong class="info-box-text h5 pt-2">
                    <h5 class="m-0 p-0 font-weight-bold">วันนี้</h5>
                    <?php
                    $today_str_dthai = date('j', strtotime($today_str));
                    $today_str_mthai = getMonthThai(date('m', strtotime($today_str)));
                    $today_str_ythai = getYeatThai(date('Y', strtotime($today_str)));
                    ?>
                    <p class="m-0 text-muted">
                        <?php echo "$today_str_dthai $today_str_mthai $today_str_ythai"  ?>
                    </p>
                </strong>
                <strong class="info-box-number text-muted m-0"><?php echo $today_total ?></strong>
                <div class="progress">
                    <div class="progress-bar bg-dark" style="width: <?php echo $today_total/$year_total*100 ?>%"></div>
                </div>
                <span class="progress-description">
                    <?php echo $today_total/$year_total*100 ?> %
                </span>
            </div>
        </div>

    </div>
    <div class="col-sm-6 col-12">
        <div class="info-box bg-danger">
            <span class="info-box-icon text-light">
                <i class="fa-solid fa-book  h2"></i>
            </span>
            <div class="info-box-content">
                <strong class="info-box-text h5 pt-2">
                    <?php
                    $sw_str_thai = getFullThaiDate($week_start);
                    $ew_str_thai = getFullThaiDate($week_end);
                    ?>
                    <h5 class="m-0 p-0 font-weight-bold">ช่วงสัปดาห์นี้</h5>
                    <p class="m-0  text-light">
                        <span><?php echo $sw_str_thai  ?></span>
                        <span> - </span>
                        <span><?php echo $ew_str_thai  ?></span>

                    </p>
                </strong>
                <strong class="info-box-number text-light m-0"><?php echo $week_total ?></strong>
                <div class="progress">
                    <div class="progress-bar bg-light" style="width: <?php echo $week_total/$year_total*100 ?>%"></div>
                </div>
                <span class="progress-description">
                    <?php echo $week_total/$year_total*100 ?> %
                </span>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-12">
        <div class="info-box bg-primary">
            <span class="info-box-icon text-light">
                <i class="fa-solid fa-book-open  h2"></i>
            </span>
            <div class="info-box-content">
                <strong class="info-box-text h5 pt-2">
                    <h5 class="m-0 p-0 font-weight-bold">เดือนนี้</h5>
                    <p class="m-0 text-light">
                        <?php echo getMonthAndYearThai("$_y-$sw_m")  ?>
                    </p>
                </strong>
                <strong class="info-box-number text-light m-0"><?php echo $month_total ?></strong>
                <div class="progress">
                    <div class="progress-bar bg-light" style="width: <?php echo $month_total/$year_total*100 ?>%"></div>
                </div>
                <span class="progress-description">
                    <?php echo $month_total/$year_total*100 ?> %
                </span>
            </div>

        </div>

    </div>
    <div class="col-sm-6 col-12">
        <div class="info-box bg-secondary  ">
            <span class="info-box-icon text-light">
                <i class="fa-solid fa-book-open  h2"></i>
            </span>
            <div class="info-box-content">
                <strong class="info-box-text h5 pt-2">
                    <h5 class="m-0 p-0 font-weight-bold">ปีนี้</h5>
                    <p class="m-0 text-light"><?php echo "ปี " . getYeatThai($_y)  ?></p>
                </strong>
                <strong class="info-box-number text-light m-0"><?php echo $year_total ?></strong>
            </div>
        </div>
    </div>
</div>


<p>
    <span class="text-danger">หมายเหตุ</span>
    <span>สำหรับการคิดร้อยละ หรือ เปอร์เซ็นต์ของหมวดหมู่หนังสือ</span>
    <span>
        เนื่องในหนังสือหนึ่งเล่มสามารถระบุหมวดหมู่ได้มากกว่า 1 หมวดหมู่
        สำหรับการคิดดเปอร์ของหมวดหมู่จะคิด โดย หากมีเพิ่ม 1 หมวดหมู่ เพิ่มการยืมอีก 1 ครั้ง
        แล้วนำมารวมกันแล้วเฉลี่ย ทำให้จำนวนครั้งในการยืมไม่ต้องกับจำนวนการยืมจริง
    </span>
</p>

<div class="my-1 p-1 text-muted text-center">
    <span class="h3">ข้อมูลวันที่ </span>
    <span class="h3"><?php echo $today_date_thai ?></span>

</div>
<div class="table-responsive">
    <table class="table">
        <thead class="bg-teal">
            <tr>
                <th class="text-center" style="width: 5%;" scope="col">รายการ</th>
                <th style="width: 70%;" scope="col">หมวดหมู่</th>
                <th style="width: 10%;" class="text-center " scope="col">จำนวน</th>
                <th style="width: 15%;" class="text-center" scope="col">Percent</th>
            </tr>
        </thead>
        <tbody class="table-bordered">
            <?php for ($i = 0; $i < count($data_by_category['category']); $i++) {
                $bg = $i % 2 == 0 ? 'tr-bg-teal' : ''
            ?>
                <tr class="<?php echo $bg ?>">
                    <th class="text-center" scope="row"><?php echo $i + 1 ?></th>
                    <td><?php echo $data_by_category['category'][$i]  ?></td>
                    <td class="text-center text-muted ">
                        <strong> <?php echo $data_by_category['count'][$i]  ?></strong>
                    </td>

                    <td class="text-center text-muted">
                        <strong><?php echo $data_by_category['percent'][$i] . "%"  ?></strong>
                    </td>
                </tr>
            <?php     } ?>
        </tbody>
    </table>
</div>




<div class="table-responsive">
    <table class="table">
        <thead class="bg-teal">
            <tr>
                <th class="text-center" style="width: 5%;" scope="col">รายการ</th>
                <th style="width: 70%;" scope="col">ชื่อหนังสือ</th>
                <th style="width: 10%;" class="text-center" scope="col">จำนวน</th>
                <th style="width: 15%;" class="text-center" scope="col">ร้อยละ</th>
            </tr>
        </thead>
        <tbody class="table-bordered">
            <?php for ($i = 0; $i < count($data_by_book['book_id']); $i++) {
                $bg = $i % 2 == 0 ? 'tr-bg-teal' : ''
            ?>
                <tr class="<?php echo $bg ?>">
                    <th class="text-center" scope="row"><?php echo $i + 1 ?></th>
                    <td><?php echo $data_by_book['book_name'][$i]  ?></td>
                    <td class="text-center">
                        <strong> <?php echo $data_by_book['count'][$i]  ?></strong>
                    </td>

                    <td class="text-center">
                        <strong><?php echo $data_by_book['percent'][$i] . "%"  ?></strong>
                    </td>
                </tr>
            <?php     } ?>
        </tbody>
    </table>
</div>


<input type="hidden" id="categorySortByMonth" value="<?php echo base64_encode(json_encode($data_by_category_m)) ?>">
<div class="my-1 p-1 text-muted text-center">
    <span class="h3">ข้อมูลวันที่ </span>
    <span class="h3"><?php echo $start_date_thai ?></span>
    <span class="h3">ถึง</span>
    <span class="h3"><?php echo $end_date_thai ?></span>
</div>
<?php
foreach ($data_by_category_m as $dt => $data) { ?>
    <div class="my-1">

        <h4 class="m-0">จำนวนการยืมตามหมวดหมู่</h4>
        <p class="m-0 text-purple font-weight-bold"><?php echo $data['date_thai'] ?></p>
    </div>


    <table class="table">
        <thead class="bg-purple">
            <tr>
                <th style="width: 5%;" scope="col">ลำดับ</th>
                <th style="width: 70%;" scope="col">หมวดหมู่</th>
                <th class="text-center" style="width: 15%;" scope="col">จำนวน</th>
                <th class="text-center" style="width: 10%;" scope="col">ร้อยละ</th>
            </tr>
        </thead>
        <tbody class="table-bordered">
            <?php if (count($data['category_id']) == 0) {
            ?>
                <tr class="tr-bg-purple">
                    <td colspan="4">ไม่มีข้อมูล</td>
                </tr>
            <?php  } ?>
            <?php
            if (count($data['category_id']) > 0) {
                for ($i = 0; $i < count($data['category_id']); $i++) {
                    $bg = $i % 2 == 0 ? 'tr-bg-purple' : '';
            ?>
                    <tr class="<?php echo $bg ?>">
                        <th scope="row"><?php echo $i + 1 ?></th>
                        <td><?php echo $data['category'][$i] ?></td>
                        <td class="text-center"><?php echo $data['count'][$i] ?></td>
                        <td class="text-center"><?php echo $data['percent'][$i] ?></td>
                    </tr>
            <?php  }
            } ?>


        </tbody>
    </table>

<?php } ?>

<input type="hidden" id="dataBookSortByMonth" value="<?php echo base64_encode(json_encode($data_by_book_m)) ?>">
<?php
foreach ($data_by_book_m as $dt => $data) { ?>
    <div class="my-1">
        <h4 class="m-0">จำนวนการยืมตามหนังสือ</h4>
        <p class="m-0 text-purple font-weight-bold"><?php echo $data['date_thai'] ?></p>
    </div>

    <table class="table">
        <thead class="bg-purple">
            <tr>
                <th style="width: 5%;" scope="col">ลำดับ</th>
                <th style="width: 70%;" scope="col">หมวดหมู่</th>
                <th class="text-center" style="width: 15%;" scope="col">จำนวน</th>
                <th class="text-center" style="width: 10%;" scope="col">ร้อยละ</th>
            </tr>
        </thead>
        <tbody class="table-bordered">
            <?php if (count($data['book_id']) == 0) { ?>
                <tr class="tr-bg-purple">
                    <td colspan="4">ไม่มีข้อมูล</td>
                </tr>
            <?php  } ?>
            <?php
            if (count($data['book_id']) > 0) {
                for ($i = 0; $i < count($data['book_id']); $i++) {
                    $bg = $i % 2 == 0 ? 'tr-bg-purple' : '';
            ?>
                    <tr class="<?php echo $bg ?>">
                        <th scope="row"><?php echo $i + 1 ?></th>
                        <td><?php echo $data['bookname'][$i] ?></td>
                        <td class="text-center"><?php echo $data['count'][$i] ?></td>
                        <td class="text-center"><?php echo $data['percent'][$i] ?></td>
                    </tr>
            <?php  }
            } ?>


        </tbody>
    </table>



<?php } ?>
<script src="./assets/js/dashboard.js"></script>