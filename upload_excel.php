<?php
require 'vendor/autoload.php';
require_once("include/connect.php");
use PhpOffice\PhpSpreadsheet\IOFactory;

$today = date('Ymd');

function clean($value)
{
    $value = preg_replace('/[\x00-\x1F\x7F]/u', '', $value);
    $value = str_replace(' ', '', $value);
    return trim($value);
}

$message = '';
$messageType = '';

if (isset($_FILES['excel_file']) && $_FILES['excel_file']['error'] === UPLOAD_ERR_OK) {
    $filePath = $_FILES['excel_file']['tmp_name'];

    $spreadsheet = IOFactory::load($filePath);
    $sheet = $spreadsheet->getActiveSheet();
    $rows = $sheet->toArray();

    $header = $rows[0];
    $dataRows = array_slice($rows, 1);

    foreach ($dataRows as $row) {

        // ดึงข้อมูลพื้นฐาน
        $order_no_raw = $row[array_search('受注管理NO', $header)];
        $customer     = clean($row[array_search('得意先略称', $header)]);
        $nameproduct  = clean($row[array_search('商品名1', $header)]);
        $nameproduct1 = clean($row[array_search('商品名2', $header)]);
        $duedate      = clean($row[array_search('指定納期', $header)]);

        // ข้ามถ้าวันกำหนดส่งน้อยกว่าวันนี้
        if (!empty($duedate) && $duedate < $today) {
            continue;
        }

        $order_no = clean(substr($order_no_raw, 1));
        $main     = clean($row[array_search('棚番', $header)]);
        $qtymain  = clean($row[array_search('受注数量', $header)]);

        // ดึง quantity แต่ละคอลัมน์
        $nt_qty = clean($row[array_search('NT数量', $header)]);
        $w_qty  = clean($row[array_search('W数量', $header)]);
        $sw_qty = clean($row[array_search('SW数量', $header)]);
        $tw_qty = clean($row[array_search('TW数量', $header)]);
        $cs_qty = clean($row[array_search('CS数量', $header)]);

        // เช็คถ้าทุก quantity เป็น '0' ข้ามแถวนี้เลย
        $all_zero = ($nt_qty === '0' && $w_qty === '0' && $sw_qty === '0' && $tw_qty === '0' && $cs_qty === '0');
        if ($all_zero) {
            continue;
        }

        // กำหนดค่า shelf ตามเงื่อนไข quantity
        $nt = ($nt_qty === '0') ? '' : clean($row[array_search('NT棚番', $header)]);
        $w  = ($w_qty === '0')  ? '' : clean($row[array_search('W棚番', $header)]);
        $sw = ($sw_qty === '0') ? '' : clean($row[array_search('SW棚番', $header)]);
        $tw = ($tw_qty === '0') ? '' : clean($row[array_search('TW棚番', $header)]);
        $cs = ($cs_qty === '0') ? '' : clean($row[array_search('CS棚番', $header)]);

        // Insert ลง DB
        $stmt = $conn->prepare("INSERT INTO order_parts (
            customer, nameproduct, nameproduct1, duedate, order_no, main, qtymain, nt, w, sw, tw, cs
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->bind_param(
            "ssssssssssss",
            $customer, $nameproduct, $nameproduct1, $duedate, $order_no,
            $main, $qtymain, $nt, $w, $sw, $tw, $cs
        );

        $stmt->execute();
    }

    $message = 'データのインポートが完了しました！';
    $messageType = 'success';
} else {
    $message = 'ファイルのアップロードに問題がありました。';
    $messageType = 'error';
}

$conn->close();
?>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    Swal.fire({
        icon: '<?= $messageType ?>',
        title: '<?= $messageType === "success" ? "成功" : "エラー" ?>',
        text: '<?= $message ?>',
        confirmButtonText: 'OK'
    }).then(() => {
        location.href = 'check_barcode.php';
    });
});
</script>
