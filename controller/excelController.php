<?php
require_once '../assets/lib/phpspreadsheet/vendor/autoload.php';
require_once('../config/config_db.php');
require_once('../function/function.php');
require_once('../function/bath_format.php');
require_once('../function/date.php');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Xls\Color;

function getStyle($textStyle, $align, $is_bold, $is_border)
{
    $font_size = ['title' => 14, 'subtitle' => 13, 'item' => 10];
    $fontname = 'SF Thonburi';
    $alignList = [
        'right' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
        'center' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
        'left' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
    ];
    $style = [
        'alignment' => [
            'horizontal' => $alignList[$align],
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
        ],
        'font' => [
            'bold' => $is_bold,
            'size' => $font_size[$textStyle],
            'name'  =>  $fontname
        ],

    ];
    if ($is_border == true) {
        $style['borders'] =  [
            'top' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            ],
            'bottom' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            ],
            'right' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            ],
            'left' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            ]
        ];
    }
    return  $style;
}
$start =  (isset($_POST['start_dt']) && !empty($_POST['start_dt']))
    ? $_POST['start_dt'] : date('Y-m-d');


$end =  (isset($_POST['end_dt']) && !empty($_POST['end_dt']))
    ? $_POST['end_dt'] : date('Y-m-d');

try {
    $data_items = 10000;
    $params = ['true', "$start 00:00:00", "$end 23:59:59"];
    $sql = "SELECT book_borrow.*,book.* FROM book_borrow LEFT JOIN  ";
    $sql .= "book ON book_borrow.bookname=book.book_id ";
    $sql .= "WHERE book_borrow.soft_delete != ? ";
    $sql .= " AND  (book_borrow.create_at BETWEEN ? AND ?)";
    $sql_all = str_replace('book_borrow.*,book.*', ' COUNT(*) AS count ', $sql);
    $stmt = connect_db()->prepare($sql_all);
    $stmt->execute($params);
    $all_result = $stmt->fetch(PDO::FETCH_ASSOC);
    $all_row = $all_result['count'];
    $row_start = 4;
    if ($all_row > $data_items) {
        echo json_encode(['result' => true, 'status' => 'ok', 'data_item' => $all_row]);
    } else {
        $stmt = connect_db()->prepare($sql);
        $stmt->execute($params);
        $idx = 1;
        $sum_total = 0;
        $sum_qty = 0;
        $rowCount = $stmt->rowCount();
        if ($rowCount == 0) {
        }

        $sdt_th = date('j', strtotime($start));
        $sm_th = getMonthThai(date('m', strtotime($start)));
        $sy_th = getYeatThai(date('Y', strtotime($start)));

        $edt_th = date('j', strtotime($end));
        $em_th = getMonthThai(date('m', strtotime($end)));
        $ey_th = getYeatThai(date('Y', strtotime($end)));
        $start_th = "วันที่ $sdt_th $sm_th $sy_th";
        $end_th = "วันที่ $edt_th $em_th $ey_th";
        $report_title = "ตั้งแต่ $start_th ถึง $end_th";
        $arrayData = [
            ['รายงาน'],
            [$report_title],
            ['', '', "", "", "", "", "", ""],
            ['', '', "", "", "", "", "", ""],
            [
                'ลำดับ', 'เลขที่', 'ชื่อหนังสือ', 'ชื่อ - นามสกุล ผู้ยืม', 'เบอร์ติดต่อ', 'อาชีพ - ตำแหน่ง', 'วันเวลา ยืม-คืน', 'รหัสเจ้าหน้าที่ ยืม-คืน',
            ],
        ];
        $idx = 1;
        $qty = 0;
        $total = 0;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            $borrow_date = $row['borrow_date'] . "\n" . $row['return_date'];
            $officer_name = $row['borrow_officer'] . "\n" . $row['return_officer'];
            $education_level = getEducationLevel($row['education_level']);
            $year_class = $row['year_class'];
            $occup_text = '';
            $occup_text .= getOccupation($row['occupation']) . "\n";
            if (!empty($row['education_level'])) {
                $occup_text .= $education_level . "\n";
            }
            if (!empty($year_class)) {
                $occup_text .= "ชั้นปีที่ ".$year_class . "\n";
            }
            $r = [
                $idx++,
                $row['borrow_id'],
                $row['book_name'],
                $row['borrow_fname'] . " " . $row['borrow_lname'],
                $row['contact_number'],
                $occup_text,
                $borrow_date,
                $officer_name
            ];
            array_push($arrayData, $r);
        }
        $total_format = number_format($total, 2);
        $total_th_format = ConvertToBathFormat($total);
        $arrayData[2] = ['รวม', "$rowCount", "", "", "", ""];

        $spreadsheet = new Spreadsheet();
        \PhpOffice\PhpSpreadsheet\Cell\Cell::setValueBinder(new \PhpOffice\PhpSpreadsheet\Cell\AdvancedValueBinder());

        $spreadsheet->getActiveSheet()->getStyle('A5:F5')
            ->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK);
        $spreadsheet->getActiveSheet()->getStyle('A5:H5')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFFF00');
        $spreadsheet->getActiveSheet()->mergeCells("A1:F1");
        $spreadsheet->getActiveSheet()->mergeCells("A2:F2");
        $spreadsheet->getActiveSheet()->mergeCells("A4:B4");
        $spreadsheet->getActiveSheet()->mergeCells("C4:D4");
        $activeWorksheet = $spreadsheet->getActiveSheet();




        # กำหนด ขนาดหัว column
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(100, 'px');
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(300, 'px');
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(270, 'px');
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(450, 'px');
        $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(150, 'px');
        $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(280, 'px');
        $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(220, 'px');
        $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(180, 'px');

        # กำหนด width 4 แถวแรกของหน้า
        $spreadsheet->getActiveSheet()->getRowDimension('1')->setRowHeight(60, 'px');
        $spreadsheet->getActiveSheet()->getRowDimension('2')->setRowHeight(35, 'px');
        $spreadsheet->getActiveSheet()->getRowDimension('3')->setRowHeight(35, 'px');
        $spreadsheet->getActiveSheet()->getRowDimension('4')->setRowHeight(35, 'px');
        # กำหนดค่า row 1 
        $spreadsheet->getActiveSheet()->getStyle('A1:F1')->applyFromArray(getStyle('title', 'center', true, false));
        # กำหนดค่า row 2
        $spreadsheet->getActiveSheet()->getStyle('A2:F2')->applyFromArray(getStyle('subtitle', 'center', true, false));
        # กำหนดค่า row 3 column A 
        $spreadsheet->getActiveSheet()->getStyle('A3')->applyFromArray(getStyle('subtitle', 'left', false, true));
        # กำหนดค่า row 3 column B 
        $spreadsheet->getActiveSheet()->getStyle('B3')->applyFromArray(getStyle('subtitle', 'left', true, true));
        # กำหนดค่า row 4 column A-B 
        $spreadsheet->getActiveSheet()->getStyle('A4:B4')->applyFromArray(getStyle('subtitle', 'left', false, true));
        # กำหนดค่า row 4 column C-D
        $spreadsheet->getActiveSheet()->getStyle('C4:D4')->applyFromArray(getStyle('subtitle', 'left', true, true));

        # กำหนดค่า หัว column
        $spreadsheet->getActiveSheet()->getStyle('A5')->applyFromArray(getStyle('item', 'center', true, true));
        $spreadsheet->getActiveSheet()->getStyle('B5')->applyFromArray(getStyle('item', 'left', true, true));
        $spreadsheet->getActiveSheet()->getStyle('C5')->applyFromArray(getStyle('item', 'left', true, true));
        $spreadsheet->getActiveSheet()->getStyle('D5')->applyFromArray(getStyle('item', 'left', true, true));
        $spreadsheet->getActiveSheet()->getStyle('E5')->applyFromArray(getStyle('item', 'left', true, true));
        $spreadsheet->getActiveSheet()->getStyle('F5')->applyFromArray(getStyle('item', 'left', true, true));
        $spreadsheet->getActiveSheet()->getStyle('G5')->applyFromArray(getStyle('item', 'left', true, true));
        $spreadsheet->getActiveSheet()->getStyle('H5')->applyFromArray(getStyle('item', 'left', true, true));
        # กำหนดค่า column ตามจำนวนแถว แต่ละแถว
        for ($i = 6; $i < $rowCount + 6; $i++) {
            $spreadsheet->getActiveSheet()->getStyle("A" . $i)->applyFromArray(getStyle('item', 'center', false, true));
            $spreadsheet->getActiveSheet()->getStyle("B" . $i)->applyFromArray(getStyle('item', 'left', false, true));
            $spreadsheet->getActiveSheet()->getStyle("C" . $i)->applyFromArray(getStyle('item', 'left', false, true));
            $spreadsheet->getActiveSheet()->getStyle("D" . $i)->applyFromArray(getStyle('item', 'left', false, true));
            $spreadsheet->getActiveSheet()->getStyle("E" . $i)->applyFromArray(getStyle('item', 'left', false, true));
            $spreadsheet->getActiveSheet()->getStyle("F" . $i)->applyFromArray(getStyle('item', 'left', false, true));
            $spreadsheet->getActiveSheet()->getStyle("G" . $i)->applyFromArray(getStyle('item', 'left', false, true));
            $spreadsheet->getActiveSheet()->getStyle("H" . $i)->applyFromArray(getStyle('item', 'left', false, true));
        }
        $spreadsheet->getActiveSheet()
            ->fromArray(
                $arrayData,  // The data to set
            );


        $writer = new Xlsx($spreadsheet);
        $report_created = date_stamp_id();
        $start = str_replace('-', '', $start);
        $end = str_replace('-', '', $end);
        $report_filename = "EXC$report_created" . "S$start" . "E$end.xlsx";
        $xlsx_created = create_date();
        $file_location = "../assets/xlsx/$report_filename";


        $start = str_replace('-', '', $start);
        $end = str_replace('-', '', $end);
        # บันทึกข้อมูลลง db 
        $sql = "INSERT INTO report_file VALUES (?,?,?,?,?,?,?)";
        $stmt = connect_db()->prepare($sql);
        $excel_id = "EXC" . date_stamp_id();
        $params = [
            $excel_id,
            $report_filename,
            'xlsx',
            substr($file_location, 2),
            create_date(),
            create_date(),
            'false'
        ];
        $stmt->execute($params);
        $writer->save($file_location);
        echo json_encode(
            [
                'result' => true, 'status' => 'success',
                'file_target' => "xlsx/$report_filename"
            ]
        );
    }
} catch (PDOException $e) {
    echo json_encode(['result' => false, 'status' => 'error', 'err' => $e->getMessage()]);
    http_response_code(500);
}
