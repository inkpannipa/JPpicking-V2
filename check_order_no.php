<?php
include("include/connect.php"); // ใช้ $conn จาก connect.php
header('Content-Type: application/json');

// อ่านข้อมูล JSON จากคำขอ
$data = json_decode(file_get_contents('php://input'), true);

// ตรวจสอบว่าได้รับ order_no มาหรือยัง
if (!isset($data['order_no']) || empty($data['order_no'])) {
    echo json_encode(['exists' => false, 'message' => 'ไม่พบ order_no ที่ส่งมา']);
    exit;
}

$order_no = $data['order_no'];

// ใช้ prepared statement เพื่อป้องกัน SQL Injection
$stmt = $conn->prepare("SELECT * FROM order_parts WHERE order_no = ?");
$stmt->bind_param("s", $order_no);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // ถ้ามีข้อมูล ให้ส่งข้อมูลแถวแรกกลับไปด้วย
    $row = $result->fetch_assoc();
    echo json_encode([
        'exists' => true,
        'data' => $row
    ]);
} else {
    echo json_encode([
        'exists' => false,
        'message' => 'ไม่พบข้อมูลในฐานข้อมูล'
    ]);
}

$stmt->close();
$conn->close();
