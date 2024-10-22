<?php
require_once('./config/config_db.php');
require_once('./function/function.php');
require_once('./function/date.php');
$id = $_GET['id'] ?? '';
$act = $_GET['act'] ?? 'insert';
$row = [];
$book_name = '';
$book_id = '';
$currenttime =time();
$borrow_dt = date("Y-m-d",$currenttime);
$borrow_hour = date("H",$currenttime);
$borrow_minute = date("i",$currenttime);
$return_dt = '';
$return_hour = '';
$return_minute = '';
$occup = '';
$is_occup = false;
$is_occup_disabled = 'disabled';
$is_branch = 'disabled';
$rowCount = 0;
if (!empty($id)) {
    $sql = "SELECT book.book_id,book.book_name,book_borrow.* ";
    $sql .= " FROM book_borrow LEFT JOIN book ON ";
    $sql .= "book_borrow.bookname=book.book_id ";
    $sql .= " WHERE book_borrow.borrow_id=?";
    $params = [$id];
    $row = getDataById($sql, $params);
    $rowCount = count($row);
    $borrow = explode(' ', $row['borrow_date']);
    $borrow_dt = $borrow[0];
    $borrow_time = explode(':', $borrow[1]);
    $borrow_hour = $borrow_time[0];
    $borrow_minute = $borrow_time[1];
    $return = explode(' ', $row['return_date']);
    $return_dt = $return[0];
    $return_time = explode(':', $return[1]);
    $return_hour = $borrow_time[0];
    $return_minute = $borrow_time[1];
    $occup = $row['occupation'];
    $is_occup = isOccuption($occup);
    $is_occup_disabled = !$is_occup ? '' : 'disabled';
    $is_branch = empty($row['branch']) ? 'disabled' : '';
    $book_name = $row['book_name'];
    $book_id = $row['book_id'];
}

?>
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label class="my-1">หนังสือ</label>
            <select data-bookname="<?php echo $row['bookname'] ?? '' ?>" class="form-control select2 select2-teal" data-dropdown-css-class="select2-teal" id="bookname">
                <option value="" selected>เลือกหนังสือ</option>
                <?php if (!empty($book_id)) { ?>
                    <option value="<?php echo $book_id ?>" selected>
                        <?php echo $book_name ?>
                    </option>
                <?php } ?>

            </select>
        </div>
    </div>
</div>

<p class="err-validate" id="validate-bookname"></p>
<div class="form-group">
    <label class="my-1">ผู้ยืม</label>
    <div class="row">
        <div class="col-6">
            <input id="borrowfname" value="<?php echo $row['borrow_fname'] ?? '' ?>" class="form-control p-4 input-focus-effect" type="text" placeholder="ชื่อ">
            <p class="err-validate" id="validate-borrowfname"></p>
        </div>
        <div class="col-6">
            <input id="borrowlname" value="<?php echo $row['borrow_lname'] ?? '' ?>" class="form-control p-4 input-focus-effect" type="text" placeholder="นามสกุล">
            <p class="err-validate" id="validate-borrowlname"></p>
        </div>
    </div>
</div>

<div class="form-group">
    <input type="hidden" id="occupRetain" value="<?php echo $is_occup ? $occup : 'other' ?>">
    <label class="my-1">อาชีพ</label>
    <div class="row">
        <div class="col-auto">
            <div class="custom-control custom-radio">
                <input class="custom-control-input bg-danger" value="teacher" type="radio" id="teacher" name="occup-opt">
                <label for="teacher" class="custom-control-label">ครู-อาจารย์</label>
            </div>
        </div>
        <div class="col-auto">
            <div class="custom-control custom-radio">
                <input class="custom-control-input bg-danger" value="student" type="radio" id="student" name="occup-opt">
                <label for="student" class="custom-control-label">นักเรียน-นักศึกษา</label>
            </div>
        </div>
        <div class="col-auto">
            <div class="custom-control custom-radio">
                <input class="custom-control-input bg-danger" value="other" type="radio" id="other" name="occup-opt">
                <label for="other" class="custom-control-label">อื่นๆ บุคคลภายนอก</label>
            </div>
        </div>
    </div>
</div>
<div class="group-group">
    <label class="my-1">อาชีพอื่นๆ ระบุ</label>
    <input type="text" <?php echo $is_occup_disabled ?> value="<?php echo !isOccuption($occup) ? $occup : ''  ?>" class="input-focus-effect form-control p-4" placeholder="ระบุอาชีพ" id="occup">
</div>
<p class="err-validate" id="validate-occup"></p>

<label class="my-1">ระดับชั้น (สำหรับนักเรียน นักศึกษา)</label>
<input type="hidden" value="<?php echo $row['education_level'] ?? '' ?>" id="educationLevelRetain">
<div class="form-group">
    <div class="row">
        <div class="col-auto">
            <div class="custom-control custom-radio">
                <input disabled class="custom-control-input" value="elementary" type="radio" id="elementary" name="education-level">
                <label for="elementary" class="custom-control-label ">ประถมศึกษา</label>
            </div>
        </div>
        <div class="col-auto">
            <div class="custom-control custom-radio">
                <input disabled class="custom-control-input" type="radio" value="secondaryEducation" id="secondaryEducation" name="education-level">
                <label for="secondaryEducation" class="custom-control-label">มัธยมศึกษา</label>
            </div>
        </div>
        <div class="col-auto">
            <div class="custom-control custom-radio">
                <input disabled class="custom-control-input" type="radio" value="vocationalCertificate" id="vocationalCertificate" name="education-level">
                <label for="vocationalCertificate" class="custom-control-label">ระดับประกาศนียบัตรวิชาชีพ</label>
            </div>
        </div>
        <div class="col-auto">
            <div class="custom-control custom-radio">
                <input disabled class="custom-control-input" type="radio" value="higherVocationalCertificate" id="higherVocationalCertificate" name="education-level">
                <label for="higherVocationalCertificate" class="custom-control-label">ประกาศนียบัตรวิชาชีพชั้นสูง</label>
            </div>
        </div>
        <div class="col-auto">
            <div class="custom-control custom-radio">
                <input disabled class="custom-control-input" type="radio" value="bachelor" id="bachelor" name="education-level">
                <label for="bachelor" class="custom-control-label">ปริญญาตรี</label>
            </div>
        </div>
        <div class="col-auto">
            <div class="custom-control custom-radio">
                <input disabled class="custom-control-input" value="master" type="radio" id="master" name="education-level">
                <label for="master" class="custom-control-label">ปริญญาโท</label>
            </div>
        </div>
        <div class="col-auto">
            <div class="custom-control custom-radio">
                <input disabled class="custom-control-input" type="radio" id="philosophy" name="education-level">
                <label for="philosophy" class="custom-control-label">ปริญญาเอก</label>
            </div>
        </div>
    </div>
    <p class="err-validate" id="validate-education"></p>

</div>
<input type="hidden" id="yearClassRetain" value="<?php echo $row['year_class'] ?? '' ?>">
<div class="form-group">
    <div class="row">
        <div class="col-auto">
            <div class="custom-control custom-radio">
                <input disabled class="custom-control-input" value="1" type="radio" id="firstYear" name="year-class">
                <label for="firstYear" class="custom-control-label">ปี1</label>
            </div>
        </div>
        <div class="col-auto">
            <div class="custom-control custom-radio">
                <input disabled class="custom-control-input" value="2" type="radio" id="secondYear" name="year-class">
                <label for="secondYear" class="custom-control-label">ปี2</label>
            </div>
        </div>
        <div class="col-auto">
            <div class="custom-control custom-radio">
                <input disabled class="custom-control-input" value="3" type="radio" id="thirdYear" name="year-class">
                <label for="thirdYear" class="custom-control-label">ปี3</label>
            </div>
        </div>
        <div class="col-auto">
            <div class="custom-control custom-radio">
                <input disabled class="custom-control-input" value="4" type="radio" id="forthYear" name="year-class">
                <label for="forthYear" class="custom-control-label">ปี4</label>
            </div>
        </div>
        <div class="col-auto">
            <div class="custom-control custom-radio">
                <input disabled class="custom-control-input" value="5" type="radio" id="fifthYear" name="year-class">
                <label for="fifthYear" class="custom-control-label">ปี5</label>
            </div>
        </div>
        <div class="col-auto">
            <div class="custom-control custom-radio">
                <input disabled class="custom-control-input" value="6" type="radio" id="sixthYear" name="year-class">
                <label for="sixthYear" class="custom-control-label">ปี6</label>
            </div>
        </div>
    </div>
    <p class="err-validate" id="validate-yearclass"></p>
</div>

<label class="my-1">สาขาเรียน (สำหรับนักเรียน นักศึกษา)</label>
<div class="form-group">
    <div class="row">
        <div class="col-auto">
            <div class="custom-control custom-radio">
                <input class="custom-control-input" value="false" type="radio" id="noBranch" name="branch-option">
                <label for="noBranch" class="custom-control-label">ไม่ระบุ</label>
            </div>
        </div>
        <div class="col-auto">
            <div class="custom-control custom-radio">
                <input class="custom-control-input" value="true" type="radio" id="addBranch" name="branch-option">
                <label for="addBranch" class="custom-control-label">ระบุ</label>
            </div>
        </div>
    </div>
    <input <?php echo $is_branch ?> value="<?php echo $row['branch'] ?? '' ?>" class="form-control p-4" type="text" id="branch" placeholder="ป้อนสาขา หรือ คณะที่เรียน หรือ สายที่เรียน">
</div>
<p class="err-validate" id="validate-branch"></p>


<div class="group-group">
    <label class="my-1"> ติดต่อ</label>
    <input type="text" value="<?php echo $row['contact_number'] ?? '' ?>" class="input-focus-effect form-control p-4" placeholder="เบอร์ที่ใช้ติดต่อ" id="contact">
</div>
<p class="err-validate" id="validate-contact"></p>
<div class="form-group">
    <label class="my-1">วันที่ และเวลายืม</label>
    <div class="row">
        <div class="col-auto">
            <div class="form-group">
                <input type="date" value="<?php echo $borrow_dt ?? ''  ?>" id="borrowDate" class="form-control "disabled>
                <p class="err-validate" id="validate-borrowDate"></p>
            </div>
        </div>
        <div class="col-auto">
            <div class="row">
                <div class="col-auto">
                    <select class="select2 select2-teal" data-dropdown-css-class="select2-teal" id="borrowHour" disabled>
                        <option value="">เวลา</option>
                        <?php for ($i = 0; $i <= 23; $i++) {
                            $is_selected = getCountDate($i) == $borrow_hour ? 'selected' : '';
                        ?>
                            <option <?php echo $is_selected ?> value="<?php echo getCountDate($i)  ?>">
                                <?php echo  getCountDate($i)  ?>
                            </option>
                        <?php    } ?>
                    </select>
                </div>
                <div class="col-auto">
                    <select class="select2 select2-teal" data-dropdown-css-class="select2-teal" id="borrowMinute"disabled>
                        <option value="">นาที</option>
                        <?php for ($i = 0; $i <= 59; $i++) {
                            $is_selected = getCountDate($i) == $borrow_minute ? 'selected' : '';
                        ?>
                            <option <?php echo $is_selected ?> value="<?php echo getCountDate($i)  ?>">
                                <?php echo  getCountDate($i)  ?>
                            </option>
                        <?php    } ?>
                    </select>
                </div>
            </div>
            <p class="err-validate" id="validate-borrowTime"></p>
        </div>



    </div>
</div>

<div class="form-group">
    <label>วันและ เวลากำหนดคืน</label>
    <div class="row">
        <div class="auto">
            <div class="form-group">
                <input type="date" value="<?php echo $return_dt ?? ''  ?>" id="returnDate" class="form-control">
                <p class="err-validate" id="validate-returnDate"></p>
            </div>
        </div>

        <div class="col-auto">
            <div class="row">
                <div class="col-auto">
                    <select class="form-control select2 select2-teal" data-dropdown-css-class="select2-teal" id="returnHour">
                        <option value="">เวลา</option>
                        <?php for ($i = 0; $i <= 23; $i++) {
                            $is_selected = getCountDate($i) == $return_hour ? 'selected' : ''; ?>
                            <option <?php echo $is_selected ?> value="<?php echo getCountDate($i)  ?>">
                                <?php echo  getCountDate($i)  ?>
                            </option>
                        <?php    } ?>
                    </select>
                </div>
                <div class="col-auto">
                    <select class="form-control p-3 select2 select2-teal" data-dropdown-css-class="select2-teal" id="returnMinute">
                        <option value="">นาที</option>
                        <?php for ($i = 0; $i <= 59; $i++) {
                            $is_selected = getCountDate($i) == $return_minute ? 'selected' : '';
                        ?>
                            <option <?php echo $is_selected ?> value="<?php echo getCountDate($i)  ?>">
                                <?php echo  getCountDate($i)  ?>
                            </option>
                        <?php    } ?>
                    </select>
                </div>


            </div>
            <p class="err-validate" id="validate-returnTime"></p>
        </div>



    </div>
</div>

<button class="btn btn-red-gradient" id="borrowHandleSubmit" data-act="<?php echo $act ?>" data-id="<?php echo $id ?>">
    บันทึก
</button>

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
<script src="./assets/js/borrow.js"></script>