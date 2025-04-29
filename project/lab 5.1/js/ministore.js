// Define the products and their prices
const productPrices = {
    "Milk": 3,
    "Apple": 2,
    "Chips": 3,
    "Coke": 2,
    "Cookies": 2
    // Feel free to add more: "Banana": 4, "Chocolate": 5, etc.
  };
  
  // The shopping cart: it stores objects like { name: "Milk", price: 3 }
  let cart = [];
  
  // Get the page elements
  const itemInput = document.getElementById('itemInput');
  const addBtn = document.getElementById('addBtn');
  const checkoutBtn = document.getElementById('checkoutBtn');
  const cartList = document.getElementById('cartList');
  const totalPrice = document.getElementById('totalPrice');
  const paidBtn = document.getElementById('paidBtn');
  
  // Event listeners
  addBtn.addEventListener('click', addItemToCart);
  checkoutBtn.addEventListener('click', checkout);
  paidBtn.addEventListener('click', clearAll);
  
  // Press Enter to add item
  itemInput.addEventListener('keyup', function(e) {
    if (e.key === 'Enter') {
      addItemToCart();
    }
  });
  
  // Function to add an item to the cart
  function addItemToCart() {
    const itemName = itemInput.value.trim();
    if (!itemName) {
      return;
    }
  
    // Check if the item name is in our product list
    if (productPrices.hasOwnProperty(itemName)) {
      const price = productPrices[itemName];
      cart.push({ name: itemName, price: price });
      itemInput.value = '';
      updateCartList();
    } else {
      alert('Sorry, we do not have this product: ' + itemName);
    }
  }
  
  // Calculate total price, show "Paid" button if cart not empty
  function checkout() {
    if (cart.length === 0) {
      alert('Your cart is empty.');
      return;
    }
  
    let total = 0;
    cart.forEach(item => {
      total += item.price;
    });
    totalPrice.textContent = 'Total: $' + total;
    paidBtn.style.display = 'inline-block';
  }
  
  // Update the display of the cart items
  function updateCartList() {
    cartList.innerHTML = ''; // Clear the list
  
    cart.forEach((item, index) => {
      const li = document.createElement('li');
  
      // Display the product name + price
      const span = document.createElement('span');
      span.className = 'cart-item';
      span.textContent = `${item.name} - $${item.price}`;
  
      // Delete button
      const delBtn = document.createElement('button');
      delBtn.className = 'delete-btn';
      delBtn.textContent = 'Delete';
      delBtn.addEventListener('click', () => {
        removeItem(index);
      });
  
      li.appendChild(span);
      li.appendChild(delBtn);
      cartList.appendChild(li);
    });
  
    // If cart is empty, hide the "Paid" button
    if (cart.length === 0) {
      totalPrice.textContent = '';
      paidBtn.style.display = 'none';
    }
  }
  
  // Remove a specific item from the cart
  function removeItem(index) {
    cart.splice(index, 1);
    updateCartList();
  }
  
  // Clear the cart entirely, reset display
  function clearAll() {
    cart = [];
    updateCartList();
    totalPrice.textContent = '';
    paidBtn.style.display = 'none';
  }
  