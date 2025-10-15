<?php
include 'koneksi.php';

$query = "
    SELECT o.order_id, o.total_harga, o.metode_pembayaran, a.nomor_antrian,
           oi.qty, oi.harga_satuan, m.nama AS nama_menu
    FROM orders o
    JOIN antrian a ON o.order_id = a.order_id
    JOIN order_items oi ON o.order_id = oi.order_id
    JOIN menu m ON oi.menu_id = m.menu_id
    WHERE o.status IN ('masuk', 'disajikan')
    ORDER BY a.nomor_antrian ASC
";

$result = mysqli_query($conn, $query);

$orders = [];

while ($row = mysqli_fetch_assoc($result)) {
    $id = $row['order_id'];
    if (!isset($orders[$id])) {
        $orders[$id] = [
            'order_id' => $row['order_id'],
            'nomor_antrian' => $row['nomor_antrian'],
            'metode_pembayaran' => $row['metode_pembayaran'],
            'total_harga' => $row['total_harga'],
            'items' => []
        ];
    }

    $orders[$id]['items'][] = [
        'nama' => $row['nama_menu'],
        'qty' => $row['qty'],
        'harga_satuan' => $row['harga_satuan']
    ];
}

// Ubah ke array numerik untuk frontend
echo json_encode(array_values($orders));
?>
