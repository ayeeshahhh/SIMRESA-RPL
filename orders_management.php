<?php
session_start();
require_once 'koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Handle status update
if ($_POST && isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['status'];
    
    $query = "UPDATE orders SET status_orderan = ? WHERE order_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "si", $new_status, $order_id);
    
    if (mysqli_stmt_execute($stmt)) {
        $success_message = "Status pesanan berhasil diubah!";
    } else {
        $error_message = "Gagal mengubah status pesanan.";
    }
    mysqli_stmt_close($stmt);
}

// Get all orders with details
$query = "SELECT o.order_id, o.total_harga, o.metode_pembayaran, o.status_orderan, o.created_at,
                 a.nomor_antrian, a.waktu_pemesanan
          FROM orders o 
          LEFT JOIN antrian a ON o.order_id = a.order_id 
          ORDER BY o.created_at DESC";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pesanan - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        .sidebar {
            min-height: 100vh;
            background-color: #2c3e50;
            color: white;
        }
        .sidebar h5 {
            color: white;
            border-bottom: 1px solid #34495e;
            padding-bottom: 10px;
        }
        .sidebar .nav-link {
            color: #bdc3c7;
            padding: 12px 16px;
            border-radius: 5px;
            margin-bottom: 5px;
            transition: all 0.3s ease;
        }
        .sidebar .nav-link:hover {
            background-color: #34495e;
            color: white;
        }
        .sidebar .nav-link.active {
            background-color: #3498db;
            color: white;
        }
        .sidebar .nav-link.text-danger:hover {
            background-color: #e74c3c;
            color: white;
        }
        .status-badge {
            font-size: 0.8rem;
        }
        .order-card {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            margin-bottom: 15px;
            transition: box-shadow 0.3s ease;
        }
        .order-card:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .order-card.orderan_diproses {
            border-left: 4px solid #f39c12;
        }
        .order-card.orderan_selesai {
            border-left: 4px solid #27ae60;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar p-3">
                <h5 class="mb-4">Admin Dashboard</h5>
                <nav class="nav flex-column">
                    <a class="nav-link" href="admin_dashboard.php">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                    <a class="nav-link" href="menu.html">
                        <i class="bi bi-menu-button-wide"></i> Kelola Menu
                    </a>
                    <a class="nav-link active" href="orders_management.php">
                        <i class="bi bi-receipt"></i> Kelola Pesanan
                    </a>
                    <a class="nav-link" href="billing.php">
                        <i class="bi bi-credit-card"></i> Pembayaran
                    </a>
                    <hr>
                    <a class="nav-link text-danger" href="logout.php">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2>Kelola Pesanan</h2>
                        <p class="text-muted mb-0">Pantau dan kelola status pesanan restoran</p>
                    </div>
                    <div class="btn-group">
                        <button class="btn btn-outline-primary" onclick="location.reload()" title="Refresh Data">
                            <i class="bi bi-arrow-clockwise"></i> Refresh
                        </button>
                        <a href="admin_dashboard.php" class="btn btn-outline-secondary" title="Kembali ke Dashboard">
                            <i class="bi bi-house"></i> Dashboard
                        </a>
                    </div>
                </div>

                <?php if (isset($success_message)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($success_message) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($error_message)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($error_message) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Filter Status -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Filter Status:</label>
                        <select class="form-select" id="statusFilter" onchange="filterOrders()">
                            <option value="">Semua Status</option>
                            <option value="orderan_diproses">Sedang Diproses</option>
                            <option value="orderan_selesai">Selesai</option>
                        </select>
                    </div>
                    <div class="col-md-8">
                        <div class="d-flex align-items-end h-100">
                            <div class="me-3">
                                <small class="text-muted">
                                    <i class="bi bi-circle-fill text-warning"></i> Sedang Diproses
                                </small>
                            </div>
                            <div>
                                <small class="text-muted">
                                    <i class="bi bi-circle-fill text-success"></i> Selesai
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Orders List -->
                <div class="row" id="ordersContainer">
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while ($order = mysqli_fetch_assoc($result)): ?>
                            <?php
                            // Get order items
                            $items_query = "SELECT oi.qty, m.nama, m.harga 
                                          FROM order_items oi 
                                          JOIN menu m ON oi.menu_id = m.menu_id 
                                          WHERE oi.order_id = ?";
                            $items_stmt = mysqli_prepare($conn, $items_query);
                            mysqli_stmt_bind_param($items_stmt, "i", $order['order_id']);
                            mysqli_stmt_execute($items_stmt);
                            $items_result = mysqli_stmt_get_result($items_stmt);
                            ?>
                            
                            <div class="col-md-6 order-item" data-status="<?= $order['status_orderan'] ?>">
                                <div class="order-card <?= $order['status_orderan'] ?> p-3">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="mb-0">
                                            Pesanan #<?= $order['nomor_antrian'] ?: $order['order_id'] ?>
                                        </h6>
                                        <span class="badge status-badge <?= $order['status_orderan'] == 'orderan_selesai' ? 'bg-success' : 'bg-warning text-dark' ?>">
                                            <?= $order['status_orderan'] == 'orderan_selesai' ? 'Selesai' : 'Sedang Diproses' ?>
                                        </span>
                                    </div>
                                    
                                    <div class="mb-2">
                                        <small class="text-muted">
                                            <i class="bi bi-clock"></i> 
                                            <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?>
                                        </small>
                                    </div>

                                    <div class="mb-2">
                                        <strong>Metode Pembayaran:</strong> 
                                        <span class="badge bg-info"><?= $order['metode_pembayaran'] ?></span>
                                    </div>

                                    <!-- Order Items -->
                                    <div class="mb-3">
                                        <strong>Item Pesanan:</strong>
                                        <ul class="list-unstyled mt-1 mb-0">
                                            <?php while ($item = mysqli_fetch_assoc($items_result)): ?>
                                                <li class="small">
                                                    <?= $item['qty'] ?>x <?= htmlspecialchars($item['nama']) ?> 
                                                    <span class="text-muted">
                                                        (Rp <?= number_format($item['harga'], 0, ',', '.') ?>)
                                                    </span>
                                                </li>
                                            <?php endwhile; ?>
                                        </ul>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center">
                                        <strong class="text-primary">
                                            Total: Rp <?= number_format($order['total_harga'], 0, ',', '.') ?>
                                        </strong>
                                        
                                        <?php if ($order['status_orderan'] == 'orderan_diproses'): ?>
                                            <div class="btn-group">
                                                <form method="POST" class="d-inline me-1">
                                                    <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                                                    <input type="hidden" name="status" value="orderan_selesai">
                                                    <button type="submit" name="update_status" class="btn btn-success btn-sm">
                                                        <i class="bi bi-check-circle"></i> Selesaikan
                                                    </button>
                                                </form>
                                            </div>
                                        <?php else: ?>
                                            <div class="btn-group">
                                                <form method="POST" class="d-inline">
                                                    <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                                                    <input type="hidden" name="status" value="orderan_diproses">
                                                    <button type="submit" name="update_status" class="btn btn-warning btn-sm">
                                                        <i class="bi bi-arrow-clockwise"></i> Proses Ulang
                                                    </button>
                                                </form>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <?php mysqli_stmt_close($items_stmt); ?>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="alert alert-info text-center">
                                <i class="bi bi-info-circle"></i>
                                Belum ada pesanan yang masuk.
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function filterOrders() {
            const filter = document.getElementById('statusFilter').value;
            const orders = document.querySelectorAll('.order-item');
            let visibleCount = 0;
            
            orders.forEach(order => {
                const status = order.getAttribute('data-status');
                if (filter === '' || status === filter) {
                    order.style.display = 'block';
                    visibleCount++;
                } else {
                    order.style.display = 'none';
                }
            });
            
            // Update count
            updateOrderCount(visibleCount, orders.length);
        }
        
        function updateOrderCount(visible, total) {
            const title = document.querySelector('h2');
            title.innerHTML = `Kelola Pesanan <small class="text-muted">(${visible} dari ${total} pesanan)</small>`;
        }

        // Initialize count on load
        document.addEventListener('DOMContentLoaded', function() {
            const totalOrders = document.querySelectorAll('.order-item').length;
            updateOrderCount(totalOrders, totalOrders);
        });

        // Auto refresh setiap 30 detik dengan notifikasi
        let refreshInterval = setInterval(function() {
            const notice = document.createElement('div');
            notice.className = 'alert alert-info alert-dismissible fade show position-fixed';
            notice.style.top = '20px';
            notice.style.right = '20px';
            notice.style.zIndex = '9999';
            notice.innerHTML = `
                <i class="bi bi-arrow-clockwise"></i> Memuat ulang data...
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.body.appendChild(notice);
            
            setTimeout(() => {
                location.reload();
            }, 1000);
        }, 30000);

        // Stop auto refresh when user is interacting
        let lastActivity = Date.now();
        document.addEventListener('click', () => lastActivity = Date.now());
        document.addEventListener('keypress', () => lastActivity = Date.now());
        
        // Pause refresh if user hasn't been active for 2 minutes
        setInterval(() => {
            if (Date.now() - lastActivity > 120000) {
                clearInterval(refreshInterval);
                console.log('Auto refresh paused due to inactivity');
            }
        }, 60000);
    </script>
</body>
</html>

<?php mysqli_close($conn); ?>