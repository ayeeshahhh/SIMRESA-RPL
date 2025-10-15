<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Struk Belanja</title>

  <!-- CSS Utama -->
  <style>
    body {
      font-family: sans-serif;
      padding: 16px;
      background-color: #f9f9f9;
      font-size: 18px;
    }

    h2 {
      text-align: center;
      font-size: 24px;
    }

    h3 {
      text-align: center;
      font-size: 20px;
      margin-top: 40px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
      font-size: 18px;
      text-align: center;
    }

    table, th, td {
      border: 1px dashed #888;
    }

    th, td {
      padding: 12px;
      text-align: center;
    }

    .harga {
     white-space: nowrap; /* Mencegah pemisahan ke baris baru */
    }

    .total-row {
      background-color: #d4edda; /* Hijau muda */
      font-weight: bold;
      font-size: 20px;
    }

    .total-row td {
      border-top: 2px solid #28a745; /* Garis atas hijau */
    }
  </style>

  <!-- External CSS jika diperlukan -->
  <link rel="stylesheet" href="styless2.css" />
</head>
<body>

  <h2>STRUK BELANJA</h2>

  <?php
  include 'koneksi.php';
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
      $cart = json_decode($_POST['cart'], true);
      $prices = json_decode($_POST['prices'], true);
      $totalHarga = $_POST['totalHarga'];
      $nama = htmlspecialchars($_POST['name']);
      $metode = $_POST['payment'];
      $query = "SELECT * FROM menu WHERE status ='aktif'";
      $result = mysqli_query($conn, $query);
      $query_count_antrian = "SELECT * FROM antrian";
      $count_antrian = mysqli_num_rows(mysqli_query($conn, $query_count_antrian));
      $antrian_baru = $count_antrian + 1;
      $itemNames = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $itemNames[$row['menu_id']] = $row['nama'];
        }
      ?>

        <p><strong>Nama: </strong><?php echo $nama ?><br>
        <strong>Metode Pembayaran: </strong><?php echo $metode ?></p>
        <p><strong>Nomor Antrian: </strong><?php echo $antrian_baru ?></p>

        <table width="100%">
              <tr>
                <th width="20%">Barang</th>
                <th width="10%">@</th>
                <th width="30%">Harga Satuan</th>
                <th width="30%">Subtotal</th>
              </tr><tbody>
<?php
      $grandTotal = 0;

      foreach ($cart as $item => $jumlah) {
          if ($jumlah > 0) {
              $harga = $prices[$item];
              $subtotal = $harga * $jumlah;
              $grandTotal += $subtotal;
              $itemName = ucfirst($itemNames[$item]);
              $hargaFormat = number_format($harga, 0, ',', '.');
              $subtotalFormat = number_format($subtotal, 0, ',', '.');

              echo "<tr>
                      <td>$itemName</td>
                      <td>$jumlah</td>
                      <td>Rp $hargaFormat</td>
                      <td>Rp $subtotalFormat</td>
                    </tr>";
          }
      }

      $grandTotalFormat = number_format($grandTotal, 0, ',', '.');

      echo "<tr class='total-row'>
              <td colspan='3'>Total</td>
              <td>Rp $grandTotalFormat</td>
            </tr>";

      echo "</tbody></table>";
  } else {
      echo "<p>Tidak ada data yang diterima.</p>";
  }
  ?>

  <h3>TERIMAKASIH</h3>

</body>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
      window.onload = () => {
        html2pdf().from(document.body).save('struk.pdf');
      };
    </script>
</html>
<?php
    $query = "SELECT * FROM orders";
    $countId = mysqli_num_rows(mysqli_query($conn, $query));

    $order_id = $countId + 1;

    $query_input_order = "INSERT INTO orders (order_id, total_harga, metode_pembayaran) VALUES ($order_id, $grandTotal, $metode)";$query_input_order = "INSERT INTO orders (order_id, total_harga, metode_pembayaran) VALUES ($order_id, $grandTotal, '".$metode."')";
    try {
      mysqli_query($conn, $query_input_order);
    } catch (mysqli_sql_exception $e) {
        echo "Caught exception: " . $e->getMessage();
    } 
    
    $insert_item = $conn->prepare("INSERT INTO order_items (order_id, menu_id, qty) VALUES (?, ?, ?)");
    $insert_item->bind_param("iii", $order_id, $menu_id, $qty);
    foreach ($cart as $menu_id => $qty) {
      if ($qty > 0) {
        $insert_item->execute();
      }
    }
    $insert_item->close();



    $query_antrian = "INSERT INTO antrian (nomor_antrian, order_id) VALUES ($antrian_baru, $order_id)";



    try {
      mysqli_query($conn, $query_antrian);
    } catch (mysqli_sql_exception $e) {
        echo "Caught exception: " . $e->getMessage(); //
  }
 ?>

