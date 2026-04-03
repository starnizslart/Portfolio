<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

// กำหนดโฟลเดอร์ปลายทาง
$target_dir = "uploads/";

// เช็คว่ามีการส่งไฟล์มาหรือไม่
if(isset($_FILES["product_image"])) {
    // สร้างชื่อไฟล์ใหม่ไม่ให้ซ้ำกัน (ใช้เวลาปัจจุบันต่อด้วยชื่อไฟล์เดิม)
    $file_extension = pathinfo($_FILES["product_image"]["name"], PATHINFO_EXTENSION);
    $new_filename = time() . '_' . uniqid() . '.' . $file_extension;
    $target_file = $target_dir . $new_filename;
    
    // เช็คว่าเป็นไฟล์รูปจริงไหม
    $check = getimagesize($_FILES["product_image"]["tmp_name"]);
    if($check !== false) {
        // ทำการย้ายไฟล์จาก Temp ไปยังโฟลเดอร์ uploads ของเรา
        if (move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file)) {
            http_response_code(200);
            echo json_encode(array(
                "message" => "อัปโหลดรูปภาพสำเร็จ",
                "image_path" => $target_file // ส่ง path กลับไปให้ Frontend
            ));
        } else {
            http_response_code(500);
            echo json_encode(array("message" => "เกิดข้อผิดพลาดในการบันทึกไฟล์"));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "ไฟล์ที่อัปโหลดไม่ใช่รูปภาพ"));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "ไม่มีการส่งไฟล์เข้ามา"));
}
?>