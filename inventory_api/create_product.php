<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

include_once 'db.php';
$data = json_decode(file_get_contents("php://input"));

if(!empty($data->name) && !empty($data->price) && isset($data->stock)) {
    // เพิ่ม category เข้าไปในคำสั่ง SQL
    $query = "INSERT INTO products SET name=:name, category=:category, price=:price, stock=:stock, image=:image";
    $stmt = $conn->prepare($query);

    // ทำความสะอาดข้อมูล
    $name = htmlspecialchars(strip_tags($data->name));
    $category = isset($data->category) ? htmlspecialchars(strip_tags($data->category)) : 'อื่นๆ';
    $price = htmlspecialchars(strip_tags($data->price));
    $stock = htmlspecialchars(strip_tags($data->stock));
    $image = isset($data->image) ? htmlspecialchars(strip_tags($data->image)) : null;

    // ผูกค่าตัวแปร
    $stmt->bindParam(":name", $name);
    $stmt->bindParam(":category", $category);
    $stmt->bindParam(":price", $price);
    $stmt->bindParam(":stock", $stock);
    $stmt->bindParam(":image", $image);

    if($stmt->execute()) {
        http_response_code(201);
        echo json_encode(array("message" => "เพิ่มสินค้าและรูปภาพเรียบร้อย!"));
    } else {
        http_response_code(503);
        echo json_encode(array("message" => "ไม่สามารถเพิ่มสินค้าได้"));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "ข้อมูลไม่ครบถ้วน"));
}
?>