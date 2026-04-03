<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once 'db.php';

$data = json_decode(file_get_contents("php://input"));

if(!empty($data->id)) {
    // คำสั่ง SQL สำหรับลบข้อมูลตาม ID
    $query = "DELETE FROM products WHERE id=:id";
    $stmt = $conn->prepare($query);
    
    $id = htmlspecialchars(strip_tags($data->id));
    $stmt->bindParam(":id", $id);

    if($stmt->execute()) {
        http_response_code(200);
        echo json_encode(array("message" => "ลบสินค้าออกจากระบบเรียบร้อยแล้ว!"));
    } else {
        http_response_code(503);
        echo json_encode(array("message" => "ไม่สามารถลบสินค้าได้"));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "กรุณาระบุ ID ของสินค้าที่ต้องการลบ"));
}
?>