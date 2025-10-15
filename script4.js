// Data menu dan harga
const menu = {
    burger: { name: "Burger", price: 25000 },
    sandwich: { name: "Sandwich", price: 20000 },
};

const cart = { burger: 0, sandwich: 0 };

function updateCart(item, change) {
    cart[item] = Math.max(0, cart[item] + change);
    document.getElementById(`${item}-count`).textContent = cart[item];
    updateBill();
}

function updateBill() {
    const billList = document.getElementById('bill-items');
    billList.innerHTML = '';
    let total = 0;

    for (let key in cart) {
        if (cart[key] > 0) {
            const li = document.createElement('li');
            const itemTotal = cart[key] * menu[key].price;
            li.textContent = `${menu[key].name} x ${cart[key]} = Rp ${itemTotal.toLocaleString()}`;
            billList.appendChild(li);
            total += itemTotal;
        }
    }

    document.getElementById('total').textContent = total.toLocaleString();
}
