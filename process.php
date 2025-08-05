<?php
if (isset($_POST['items'])) {
    $items = $_POST['items'];
    $file = 'packing_log.txt';
    $entry = "📦 รายการสแกนเมื่อ " . date('Y-m-d H:i:s') . PHP_EOL;
    foreach ($items as $item) {
        $entry .= "- " . $item . PHP_EOL;
    }
    $entry .= str_repeat("-", 40) . PHP_EOL;
    file_put_contents($file, $entry, FILE_APPEND);
    echo "บันทึกรายการ " . count($items) . " รายการแล้ว";
} else {
    echo "ไม่พบรายการ";
}
?>
