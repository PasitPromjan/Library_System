<?php
require_once  "../assets/lib/mpdf/vendor/autoload.php";
require_once('../config/config_db.php');
require_once('../function/date.php');
require_once('../function/bath_format.php');
require_once('../function/function.php');


$start =  (isset($_POST['start_dt']) && !empty($_POST['start_dt']))
    ? $_POST['start_dt'] : date('Y-m-d');


$end =  (isset($_POST['end_dt']) && !empty($_POST['end_dt']))
    ? $_POST['end_dt'] : date('Y-m-d');
$table = '<table class="table table-bordered">
<thead>
<tr class="align-middle" style="border:1px solid #000;">
  <th class="text-center" style="width: 5%;" scope="col">ลำดับ</th>
  <th style="width: 15%;border:1px solid #000;" scope="col">เลขที่</th>
  <th style="width: 19%;border:1px solid #000;" scope="col">ชื่อหนังสือ</th>
  <th style="width: 19%;border:1px solid #000;" scope="col">ชื่อ - นามสกุล</th>
  <th style="width: 15%;border:1px solid #000;" scope="col">อาชีพ - ตำแหน่ง</th>
  <th style="width: 14%;border:1px solid #000;" scope="col">วันเวลา ยืม-คืน</th>
  <th style="width: 15%;border:1px solid #000;" scope="col">รหัสเจ้าหน้าที่</th>
</tr>
</thead>
<tbody>
';


try {
    $data_items = 10000;
    $params = ['true', "$start 00:00:00", "$end 23:59:59"];
    $sql = "SELECT book_borrow.*,book.* FROM book_borrow LEFT JOIN  ";
    $sql .= "book ON book_borrow.bookname=book.book_id ";
    $sql .= "WHERE book_borrow.soft_delete != ? ";
    $sql .= " AND  (book_borrow.create_at BETWEEN ? AND ?)";
    $sql_all_pdf = str_replace('book_borrow.*,book.*', ' COUNT(*) AS count ', $sql);
    $stmt = connect_db()->prepare($sql_all_pdf);
    $stmt->execute($params);
    $pdf_all_result = $stmt->fetch(PDO::FETCH_ASSOC);
    $pdf_all_row = $pdf_all_result['count'];
    if ($pdf_all_row > $data_items) {
        echo json_encode(['result' => true, 'status' => 'ok', 'data_item' => $pdf_all_row]);
    } else {
        $stmt = connect_db()->prepare($sql);
        $stmt->execute($params);
        $idx = 1;
        $return_count = 0;
        $borrow_count = 0;
        $rowCount = $stmt->rowCount();
        if ($rowCount == 0) {
            $table .= '<tr class="align-middle" style="border:1px solid #000;">' .
                '<td colspan="8" style="border:1px solid #000;">' . "ไม่มีข้อมูล" . '</td>' .
                '</tr>';
        }
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            if ($row['status'] == 'return') $return_count++;
            if ($row['status'] == 'borrow') $borrow_count++;
            $name = '';
            $occup = getOccupation($row['occupation']);
            $education_level = getEducationLevel($row['education_level']);
            $year_class = $row['year_class'];
            $occup_text = '';
            $date_text = '';



            $name .= "<p class='m-0'>";
            $name .= "$row[borrow_fname] $row[borrow_lname]";
            $name .= "</p>";
            $name .= "<p class='m-0 fw-bold'>";
            $name .= $row['contact_number'];
            $name .= "</p>";

            $officer_text = '';
            $officer_text .= "<p class='m-0'>";
            $officer_text .= $row['borrow_officer'];
            $officer_text .= "</p>";
            $officer_text .= "<p class='m-0'>";
            $officer_text .= $row['return_officer'];
            $officer_text .= "</p>";


            $date_text .= "<p class='m-0'>";
            $date_text .= $row['borrow_date'];
            $date_text .= "</p>";
            $date_text .= "<p class='m-0'>";
            $date_text .= $row['return_date'];
            $date_text .= "</p>";

            $occup_text .= "<p class='m-0'>";
            $occup_text .= $occup;
            $occup_text .= "</p>";
            if (!empty($education_level)) {
                $occup_text .= "<p class='m-0'>";
                $occup_text .= $education_level;
                $occup_text .= "</p>";
            }
            if (!empty($year_class)) {
                $occup_text .= "<p class='m-0'>";
                $occup_text .= "ชั้นปี " . $row['year_class'];
                $occup_text .= "</p>";
            }


            $table .= '<tr class="align-middle" style="border:1px solid #000;">' .
                '<td class="text-center" style="border:1px solid #000;">' . ($idx++) . '</td>' .
                '<td style="border:1px solid #000;">' . $row['borrow_id'] . '</td>' .
                '<td style="border:1px solid #000;">' . $row['book_name'] . '</td>' .
                '<td style="border:1px solid #000;">' . $name . '</td>' .
                '<td style="border:1px solid #000;">' . $occup_text . '</td>' .
                '<td style="border:1px solid #000;">' . $date_text . '</td>' .
                '<td style="border:1px solid #000;">' . $officer_text . '</td>' .
                '</tr>';
        }
        $table .= '</tbody></table>';
        $table .= "<p class='text-bold'>สรุป</p>";
        $table .= '<table class="table">
  <tbody>
    <tr style="border:1px solid #000;">
      <td class="text-center text-bold" style="border:1px solid #000;">ยังไม่คืน</td>
      <td class="text-center text-bold" style="border:1px solid #000;">คืนแล้ว</td>
      <td class="text-center text-bold" style="border:1px solid #000;">ทั้งหมด</td>
      </tr>
    <tr style="border:1px solid #000;">
    <td style="border:1px solid #000;" class="text-center">' . $return_count . "รายการ" . '</td>'
            . '<td style="border:1px solid #000;" class="text-center">' . $borrow_count . '</td>
      <td style="border:1px solid #000;" class="text-center">' . $rowCount  . '</td>
    </tr>
  </tbody>
  </table>';
        $m_thai_start = getMonthThai(date("m", strtotime($start)));
        $y_thai_start = getYeatThai(date("Y", strtotime($start)));
        $dt_thai_start = date("j", strtotime($start));
        $m_thai_end = getMonthThai(date("m", strtotime($end)));
        $y_thai_end = getYeatThai(date("Y", strtotime($end)));
        $dt_thai_end = date("j", strtotime($end));
        $start_dt_thai = "$dt_thai_start $m_thai_start $y_thai_start";
        $end_dt_thai = "$dt_thai_end $m_thai_end $y_thai_end";
        $title = "รายงาน";
        $subTitle = "ตั้งแต่วันที่ $start_dt_thai ถึง $end_dt_thai";
        $header = 'รายงานการยืม';
        $footer = 'รายงาน' . date('วันที่ d-m-Y', strtotime($start));
        $footer .= date(' ถึง วันที่ d-m-Y ', strtotime($end));
        $footer .= date('สร้างเมื่อ Y-m-d เวลา H:i:s');
        $mpdf = new \Mpdf\Mpdf(
            [
                'default_font' => 'sf-thonburi',
                'format' => [297, 210]
            ]
        );

        $stylesheet = file_get_contents('../assets/bootstrap-5.2.3-dist/css/bootstrap.min.css');

        $mpdf_style = file_get_contents('../assets/css/mpdf.css');
        $mpdf->defaultheaderfontsize = 10;
        $mpdf->defaultheaderfontstyle = 'B';
        $mpdf->defaultheaderline = 0;
        $mpdf->defaultfooterfontsize = 10;
        $mpdf->defaultfooterfontstyle = 'B';
        $mpdf->defaultfooterline = 0;

        $mpdf->SetHeader($header);
        $mpdf->SetFooter($footer);
        $mpdf->SetTitle($title);
        $mpdf->SetSubject($subTitle);
        $mpdf->WriteHTML($stylesheet, \Mpdf\HTMLParserMode::HEADER_CSS);
        $mpdf->WriteHTML($mpdf_style, \Mpdf\HTMLParserMode::HEADER_CSS);
        $mpdf->WriteHTML($table);
        $report_created = date_stamp_id();
        $start = str_replace('-', '', $start);
        $end = str_replace('-', '', $end);
        $report_filename = "PDF$report_created" . "S$start" . "E$end.pdf";
        $sql = "INSERT INTO report_file VALUES (?,?,?,?,?,?,?)";

        $stmt = connect_db()->prepare($sql);
        $pdf_id = "PDF" . date_stamp_id();

        $file_location = "../assets/pdf/$report_filename";
        $params = [$pdf_id, $report_filename, 'pdf', substr($file_location, 2), create_date(), create_date(), 'false'];
        $stmt->execute($params);
        $mpdf->Output($file_location, 'F');
        echo json_encode([
            'result' => true, 'status' => 'success',
            'file_target' => "pdf/$report_filename"
        ]);
    }
} catch (PDOException $e) {
    echo json_encode(['result' => false, 'status' => 'error', 'err' => $e->getMessage()]);
    http_response_code(500);
}
