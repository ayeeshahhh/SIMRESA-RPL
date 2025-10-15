<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="styless3.css">
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .sidebar {
            width: 250px;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            background-color: #f5f5f5;
            padding: 15px;
            box-sizing: border-box;
        }

        .main {
            margin-left: 250px;
            padding: 20px 40px 80px; /* Tambah padding agar tidak terlalu kiri */
        }

        .navbar {
            position: fixed;
            bottom: 0;
            left: 250px;
            right: 0;
            background-color: #333;
            color: white;
            padding: 12px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.2);
        }

        .navbar button {
        background-color: #4CAF50; /* hijau langsung */
        color: white;
        border: none;
        padding: 10px 16px;
        margin-right: 10px;
        border-radius: 5px;
        cursor: pointer;
}


        .navbar a.logout-btn {
            color: white;
            text-decoration: none;
            border: 1px solid white;
            padding: 8px 12px;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }

        .navbar a.logout-btn:hover {
            background-color: white;
            color: #333;
        }

        .order-item {
            background-color: #eee;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            cursor: pointer;
        }

        .order-item:hover {
            background-color: #ddd;
        }

        table.table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .table th,
        .table td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: left;
        }

        .total {
            margin-top: 10px;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h3>Pesanan Masuk</h3>
        <div id="order-list"></div>
    </div>

    <!-- Main Content -->
    <div class="main">
        <div id="order-detail">
            <p>Pilih pesanan dari sidebar untuk melihat detail.</p>
        </div>
    </div>

    <!-- Navbar di bawah -->
    <div class="navbar">
        <div>
            <button id="serveBtn" disabled>Pesanan Disajikan (Proses Ulang)</button>
            <button id="completeBtn" disabled>Pesanan Selesai</button>
            <a href="orders_management.php" class="btn btn-info text-white" style="margin-left: 10px;">
                Kelola Semua Pesanan
            </a>
        </div>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>

    <script>
    function fetchOrders() {
    fetch('get_orders.php')
    .then(res => res.json())
    .then(data => {
      renderOrders(data);
    })
    .catch(err => {
      console.error('Gagal ambil pesanan:', err);
    });
}

function renderOrders(orders) {
  // sama seperti sebelumnya, tapi here orders adalah array dari server
  const list = document.getElementById('order-list');
  list.innerHTML = '';

  if (!orders || orders.length === 0) {
    list.innerHTML = '<p>Tidak ada pesanan.</p>';
    return;
  }

  orders.forEach(order => {
    const div = document.createElement('div');
    div.className = 'order-item';
    div.innerText = `#${order.nomor_antrian} - ${order.metode_pembayaran} - Rp ${order.total_harga.toLocaleString()}`;
    div.onclick = () => showOrderDetail(order);
    list.appendChild(div);
  });
}

function showOrderDetail(order) {
  let html = `<h4>Pesanan #${order.nomor_antrian}</h4>`;
  html += `<p>Metode: ${order.metode_pembayaran}</p>`;
  html += `<table class="table"><tr><th>Nama</th><th>Qty</th><th>Harga</th></tr>`;
  let total = 0;
  order.items.forEach(item => {
    const sub = item.qty * item.harga_satuan;
    html += `<tr><td>${item.nama}</td><td>${item.qty}</td><td>Rp ${sub.toLocaleString()}</td></tr>`;
    total += sub;
  });
  html += `</table><div class="total">Total: Rp ${total.toLocaleString()}</div>`;
  document.getElementById('order-detail').innerHTML = html;

  window.selectedOrderId = order.order_id;
  document.getElementById('serveBtn').disabled = false;
  document.getElementById('completeBtn').disabled = false;
}

// event handler tombol
serveBtn.onclick = () => {
  if (!window.selectedOrderId) return;
  updateStatus(window.selectedOrderId, 'orderan_diproses');
};
completeBtn.onclick = () => {
  if (!window.selectedOrderId) return;
  updateStatus(window.selectedOrderId, 'orderan_selesai');
};

function updateStatus(orderId, status) {
  fetch('update_orders.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({order_id: orderId, status: status})
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      fetchOrders();
      document.getElementById('order-detail').innerHTML = '<p>Pilih pesanan dari sidebar untuk melihat detail.</p>';
      document.getElementById('serveBtn').disabled = true;
      document.getElementById('completeBtn').disabled = true;
      window.selectedOrderId = null;
    } else {
      alert('Gagal mengubah status: ' + (data.error || 'Unknown error'));
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('Terjadi kesalahan saat mengubah status');
  });
}

// jalankan fetch pertama kali dan refresh otomatis
fetchOrders();
setInterval(fetchOrders, 5000);
    </script>

</body>

</html>