<?php 

include "koneksi.php";

$sql = "SELECT * FROM menu WHERE status='aktif'";
$result = mysqli_query($conn, $sql);
$count = mysqli_num_rows($result);

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Mini Grab UI</title>
  <link rel="stylesheet" href="styless2.css" />
</head>

<body>
  <span id="count-item" hidden><?php echo $count ?></span>
  <!-- Bagian Menu Makanan -->
  <div id="makanan" class="page active">
    <h2 class="judul-section">Menu</h2>

  <?php
  $i = 0;
  foreach ($result as $row) {
    $i++;
  ?>
  <!-- Card menu -->
  <div class="menu-item" style="display: flex; align-items: center; margin-bottom: 20px;">
    <img src="gambar/<?php echo $row["nama"]; ?>.jpg" alt="<?php echo $row["nama"] ?>" width="200px" style="margin-right: 20px;">
  
    <div class="info" style="vertical-align: flex; align-items: center; gap: 20px;">
      <h3 style="margin: 0;" id="item-name-<?php echo $i?>"><?php echo $row["nama"] ?></h3><br>
      <p style="margin: 0;">Rp <span id="item-price-<?php echo $i?>"><?php echo number_format($row["harga"], 0, '', ".") ?></span></p><br>
      <p id="item-id-<?php echo $i?>" hidden><?php echo $row["menu_id"] ?></p>
      
      <div class="controls" style="display: flex; align-items: center; gap: 10px;">
        <button onclick="updateCart('<?php echo $row['menu_id']?>', -1)">−</button>
        <span id="count-<?php echo $row['menu_id'] ?>">0</span>
        <button onclick="updateCart('<?php echo $row['menu_id']?>', 1)">+</button>
      </div>
    </div>
  </div>
  <!-- End of card menu -->
   <?php } ?>

<!-- 
<div class="menu-item" style="display: flex; align-items: center; margin-bottom: 20px;">
  <img src="gambar/sandwich.jpg" alt="Sandwich" width="200px" style="margin-right: 20px;">
  
  <div class="info" style="vertical-align: flex; align-items: center; gap: 20px;">
    <h3 style="margin: 0;">Sandwich</h3><br>
    <p style="margin: 0;">Rp <span id="sandwich-price">30.000</span></p><br>
    <p id="item-id-2" hidden>2</p>
    
    <div class="controls" style="display: flex; align-items: center; gap: 10px;">
      <button onclick="updateCart('sandwich', -1)">−</button>
      <span id="sandwich-count">0</span>
      <button onclick="updateCart('sandwich', 1)">+</button>
    </div>
  </div>
</div>


  <div class="menu-item" style="display: flex; align-items: center; margin-bottom: 20px;">
  <img src="gambar/sprite.jpg" alt="Sprite" width="200px" style="margin-right: 20px;">
  
  <div class="info" style="vertical-align: flex; align-items: center; gap: 20px;">
    <h3 style="margin: 0;">Sprite</h3> 
    <p style="margin: 0;">Rp <span id="sprite-price">40.000</span></p>
    <p id="item-id-3" hidden>3</p>
    
    <div class="controls" style="display: flex; align-items: center; gap: 10px;">
      <button onclick="updateCart('sprite', -1)">−</button>
      <span id="sprite-count">0</span>
      <button onclick="updateCart('sprite', 1)">+</button>
    </div>
  </div>
</div> -->


 <!-- Navbar bawah yang SELALU terlihat -->
<div class="bottom-nav" style="position: fixed; bottom: 0; left: 0; width: 100%; background-color: #498c8a; color: white; display: flex; justify-content: space-between; align-items: center; padding: 10px;">
  
  <div>
    🛒 Keranjang <span id="cart-total">(0)</span><br>
    Total: <span id="total">Rp 0</span>
    <ul id="bill-items"></ul>
  </div>
    
  <button id="checkout-btn" style="
  position: fixed;
  bottom: 20px;
  right: 20px;
  padding: 10px 20px;
  background-color: white;
  color: #498c8a;
  font-weight: bold;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  z-index: 1000;
">Checkout</button>
</div>

<!-- <p style="font-weight: bold;">Total: <span id="total">Rp 0</span></p>*/ -->


  <script src="script2.js"></script>
</body>

</html>