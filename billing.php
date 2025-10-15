<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Halaman Billing</title>
  <style>
    body {
      font-family: sans-serif;
      padding: 20px;
      background-color: #f6f6f6;
    }
    form {
      max-width: 400px;
      margin: auto;
      background: white;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    label {
      display: block;
      margin-bottom: 6px;
      font-weight: bold;
    }
    input, textarea, select {
      width: 100%;
      padding: 8px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 4px;
      box-sizing: border-box;
    }
    button {
      background-color: #498c8a;
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 5px;
      cursor: pointer;
    }
    button:hover {
      background-color: #3b6f6e;
    }
  </style>
    <link rel="stylesheet" href="styless2.css" />
</head>
<body>
  <h2>Form Billing</h2>
  <form id="billing-form">
    <label for="name">Nama Lengkap</label>
    <input type="text" id="name" name="name" required />

    <label for="payment">Metode Pembayaran</label>
    <select id="payment" name="payment" required>
      <option value="">Pilih Metode</option>
      <option value="KASIR">Tunai</option>
      <option value="QRIS">QRIS</option>
    </select>
    <div>
    🛒 Keranjang <span id="cart-total">(0)</span><br>
    Total: <span id="total">Rp 0</span>
    <ul id="bill-items"></ul>
  </div>

    <button type="submit">Bayar</button>
  </form>

  <script>
document.getElementById('billing-form').addEventListener('submit', function(e) {
  e.preventDefault();

  const name = document.getElementById('name').value;
  const payment = document.getElementById('payment').value;
  const cart = JSON.parse(localStorage.getItem('cart')) || {};
  const prices = JSON.parse(localStorage.getItem('prices')) || {};
  const totalHarga = localStorage.getItem('totalHarga') || "Rp 0";

  // Buat form dinamis untuk kirim ke struk.php
  const form = document.createElement('form');
  form.method = 'POST';
  form.action = 'struk.php';

  function addField(name, value) {
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = name;
    input.value = value;
    form.appendChild(input);
  }

  addField('name', name);
  addField('payment', payment);
  addField('cart', JSON.stringify(cart));
  addField('prices', JSON.stringify(prices));
  addField('totalHarga', totalHarga);

  document.body.appendChild(form);
  form.submit();

  // Hapus localStorage setelah submit
  localStorage.removeItem('cart');
  localStorage.removeItem('totalHarga');
});
</script>

  
  <script> //otomatis dijalankan 
  const cart = JSON.parse(localStorage.getItem('cart')) || {}; //get untuk ambil data dari local storage
  const totalHarga = localStorage.getItem('totalHarga') || "Rp 0";
  const itemNames = JSON.parse(localStorage.getItem('itemNames')) ;
  const prices = JSON.parse(localStorage.getItem('prices')) ;
  // Update total harga
  document.getElementById('total').textContent = totalHarga;

  // Hitung total item untuk tampilkan di 🛒
  let totalItem = 0;
  for (let item in cart) {
    totalItem += cart[item];
  }
  document.getElementById('cart-total').textContent = `(${totalItem})`;

  // Tampilkan detail isi keranjang
  const billItems = document.getElementById('bill-items');
  for (let item in cart) {
    if (cart[item] > 0) {
      const jumlah = cart[item];
      const subtotal = prices[item] * jumlah;
      const li = document.createElement('li');
      li.textContent = `${capitalize(itemNames[item])} x ${jumlah} - Rp ${subtotal.toLocaleString("id-ID")}`;
      billItems.appendChild(li);
    }
  }

  // Format huruf kapital
  function capitalize(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
  }

  // Hapus data setelah submit
  document.getElementById('billing-form').addEventListener('submit', function () {
    localStorage.removeItem('cart');
    localStorage.removeItem('totalHarga');
  });
</script>

  
</body>
</html>
