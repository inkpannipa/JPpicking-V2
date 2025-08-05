<?php
header('Content-Type: application/json');
include("include/connect.php"); // ตัวแปร $conn พร้อมใช้งาน

$data = json_decode(file_get_contents('php://input'), true);

$order_no = $data['order_no'] ?? '';
$part_type = strtolower(trim($data['part_type'] ?? '')); // เช่น main, nt, ...
$scanned_value = $data['scanned_value'] ?? '';
$status = $data['status'] ?? '';

// กำหนดชื่อคอลัมน์แบบเต็มตามที่คุณแจ้ง (เติม status ต่อท้าย)
$allowedColumns = [
    'main' => 'mainstatus',
    'nt'   => 'ntstatus',
    'w'    => 'wstatus',
    'sw'   => 'swstatus',
    'tw'   => 'twstatus',
    'cs'   => 'csstatus',
];

if (!$order_no || !$part_type || !$scanned_value) {
    echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ครบ']);
    exit;
}

if (!array_key_exists($part_type, $allowedColumns)) {
    echo json_encode(['success' => false, 'message' => 'part_type ไม่ถูกต้อง']);
    exit;
}

$column = $allowedColumns[$part_type]; // เช่น mainstatus

// เตรียมคำสั่ง SQL สำหรับอัปเดตพร้อมกันทั้ง 2 คอลัมน์
if (!empty($status)) {
    $sql = "UPDATE order_parts SET `$column` = ?, status_check = ? WHERE order_no = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'SQL Error: ' . $conn->error]);
        exit;
    }
    $stmt->bind_param("sss", $scanned_value, $status, $order_no);
} else {
    $sql = "UPDATE order_parts SET `$column` = ? WHERE order_no = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'SQL Error: ' . $conn->error]);
        exit;
    }
    $stmt->bind_param("ss", $scanned_value, $order_no);
}

$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'ไม่พบข้อมูลหรือไม่มีการเปลี่ยนแปลง']);
}

$stmt->close();
$conn->close();
?>
