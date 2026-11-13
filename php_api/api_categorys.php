
<?php
include 'condb.php';
header('Content-Type: application/json; charset=utf-8');

$action = $_POST['action'] ?? '';

try {

    // ดึงข้อมูลประเภทสินค้าทั้งหมด
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {

        $stmt = $conn->prepare("
            SELECT category_id, category_name
            FROM categorys
            ORDER BY category_id DESC
        ");

        $stmt->execute();
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            "success" => true,
            "data" => $categories
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    // เพิ่มประเภทสินค้า
    if ($action === 'add') {

        $category_name = $_POST['category_name'];

        $stmt = $conn->prepare("
            INSERT INTO categorys (category_name)
            VALUES (?)
        ");

        $stmt->execute([$category_name]);

        echo json_encode([
            "success" => true,
            "message" => "เพิ่มประเภทสินค้าเรียบร้อย"
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    // แก้ไขประเภทสินค้า
    if ($action === 'update') {

        $id = $_POST['category_id'];
        $category_name = $_POST['category_name'];

        $stmt = $conn->prepare("
            UPDATE categorys
            SET category_name = ?
            WHERE category_id = ?
        ");

        $stmt->execute([$category_name, $id]);

        echo json_encode([
            "success" => true,
            "message" => "แก้ไขประเภทสินค้าสำเร็จ"
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    // ลบประเภทสินค้า
    if ($action === 'delete') {

        $id = $_POST['category_id'];

        $stmt = $conn->prepare("
            DELETE FROM categorys
            WHERE category_id = ?
        ");

        $stmt->execute([$id]);

        echo json_encode([
            "success" => true,
            "message" => "ลบประเภทสินค้าเรียบร้อย"
        ], JSON_UNESCAPED_UNICODE);

        exit;
    }

    echo json_encode([
        "success" => false,
        "message" => "ไม่พบ action ที่ต้องการ"
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {

    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);

}
?>
