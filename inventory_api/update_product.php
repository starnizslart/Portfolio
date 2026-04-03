<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: PUT");

include_once 'db.php';
$data = json_decode(file_get_contents("php://input"));

if(!empty($data->id) && !empty($data->name) && !empty($data->price) && isset($data->stock)) {
    // เพิ่ม category เข้าไปในคำสั่ง SQL
    $query = "UPDATE products SET name=:name, category=:category, price=:price, stock=:stock, image=:image WHERE id=:id";
    $stmt = $conn->prepare($query);

    // ทำความสะอาดข้อมูล
    $name = htmlspecialchars(strip_tags($data->name));
    $category = isset($data->category) ? htmlspecialchars(strip_tags($data->category)) : 'อื่นๆ';
    $price = htmlspecialchars(strip_tags($data->price));
    $stock = htmlspecialchars(strip_tags($data->stock));
    $id = htmlspecialchars(strip_tags($data->id));
    $image = isset($data->image) ? htmlspecialchars(strip_tags($data->image)) : null;

    // ผูกค่าตัวแปร
    $stmt->bindParam(":name", $name);
    $stmt->bindParam(":category", $category);
    $stmt->bindParam(":price", $price);
    $stmt->bindParam(":stock", $stock);
    $stmt->bindParam(":image", $image);
    $stmt->bindParam(":id", $id);

    if($stmt->execute()) {
        http_response_code(200);
        echo json_encode(array("message" => "อัปเดตข้อมูลสำเร็จ!"));
    } else {
        http_response_code(503);
        echo json_encode(array("message" => "ไม่สามารถอัปเดตได้"));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "ข้อมูลไม่ครบถ้วน"));
}
?>