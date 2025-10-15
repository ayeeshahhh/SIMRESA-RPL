
// Objek untuk menyimpan jumlah item
const itemCount = parseInt(document.getElementById("count-item").textContent);
let cart = {};
for (let i = 1; i <= itemCount; i++) {
  let itemId = document.getElementById(`item-id-${i}`).textContent;
  cart[itemId] = 0;
}

let prices = {};
for (let i = 1; i <= itemCount; i++) {
  let itemId = document.getElementById(`item-id-${i}`).textContent;
  let itemPrice = parseInt(document.getElementById(`item-price-${i}`).textContent.replace(/\./g, ''));
  prices[itemId] = itemPrice;
}

let itemNames = {};
for (let i = 1; i <= itemCount; i++) {
  let itemId = document.getElementById(`item-id-${i}`).textContent;
  let itemName = document.getElementById(`item-name-${i}`).textContent;
  itemNames[itemId] = itemName;
}

// Fungsi hitung total jumlah item dan update tampilan 🛒
function updateCartTotal() {
  let totalItems = 0;
  for (let item in cart) {
    totalItems += cart[item];
  }

  // Update tampilan angka di sebelah 🛒
  document.getElementById("cart-total").textContent = `(${totalItems})`;
}

  // const prices = {
  //   burger: parseInt(document.getElementById("burger-price").textContent.replace(/\./g, '')),
  //   sandwich: parseInt(document.getElementById("sandwich-price").textContent.replace(/\./g, '')),
  //   sprite: parseInt(document.getElementById("sprite-price").textContent.replace(/\./g, '')),
  // };

// (Opsional) Buat keranjang tampilkan daftar + total harga
function updateBill() {
  const billList = document.getElementById("bill-items");
  const totalSpan = document.getElementById("total");

  billList.innerHTML = ""; // kosongkan dulu
  let totalHarga = 0;

  for (let item in cart) {
    const count = cart[item];
    if (count > 0) {
      const hargaItem = prices[item] * count;
      totalHarga += hargaItem;

      const li = document.createElement("li");
      console.log(itemNames[item]);
      li.textContent = `${capitalize(itemNames[item])} x ${count} - Rp ${hargaItem.toLocaleString("id-ID")}`;
      billList.appendChild(li);
    }
  }

  totalSpan.textContent = `Rp ${totalHarga.toLocaleString("id-ID")}`;
}

// Biar tampilan nama makanan rapi
function capitalize(str) {
  return str.charAt(0).toUpperCase() + str.slice(1);
}

// Fungsi navigasi antar halaman
function showPage(pageId, element) {
  // Sembunyikan semua halaman
  const pages = document.querySelectorAll(".page");
  pages.forEach(page => page.classList.remove("active"));

  // Tampilkan halaman yang dipilih
  document.getElementById(pageId).classList.add("active");

  // Update menu aktif
  const navItems = document.querySelectorAll(".nav-item");
  navItems.forEach(nav => nav.classList.remove("active"));
  element.classList.add("active");
      }



document.getElementById('checkout-btn').addEventListener('click', function () {
  // Simpan keranjang ke localStorage
  localStorage.setItem('cart', JSON.stringify(cart));

  // Ambil total harga dari elemen #total (misalnya: "Rp 130.000") lalu simpan
  const totalHarga = document.getElementById("total").textContent;
  localStorage.setItem('totalHarga', totalHarga);

  localStorage.setItem('prices', JSON.stringify(prices));
  localStorage.setItem('itemNames', JSON.stringify(itemNames));

  // Arahkan ke halaman billing
  window.location.href = 'billing.php';
});


// Fungsi untuk update cart per item
function updateCart(itemName, amount) {
  if (!cart[itemName]) cart[itemName] = 0;

  cart[itemName] += amount;

  // Jangan biarkan jumlah negatif
  if (cart[itemName] < 0) cart[itemName] = 0;

  // Update tampilan jumlah per item
  document.getElementById(`count-${itemName}`).textContent = cart[itemName];

  // Update total item otomatis di 🛒
  updateCartTotal();
  updateBill();
}

