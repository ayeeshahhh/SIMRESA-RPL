<?php
include 'koneksi.php';

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['order_id'], $data['status'])) {
    $order_id = (int)$data['order_id'];
    $status = mysqli_real_escape_string($conn, $data['status']);

    $query = "UPDATE orders SET status = '$status' WHERE order_id = $order_id";
    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => mysqli_error($conn)]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid data']);
}
?>
