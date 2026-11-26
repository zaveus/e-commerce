
let updatingCart = false;

async function updateCart(bookId, action) {

    if (updatingCart) return;

    updatingCart = true;

    try {
        const response = await fetch("api/cart.php", {
            method: "POST",

            body: new URLSearchParams({
                book_id: bookId,
                action: action
            })
        });

        const data = await response.json();

        if (!response.ok || !data.success) {
            alert(data.message || "Unable to update cart.");
            return;
        }

        // Update homepage cart counter
        const cartCount = document.getElementById("cart-count");

        if (cartCount) {
            cartCount.textContent = data.count;
        }

        // Update shopping cart page
        updateCartDisplay(data.cart);

    } catch (error) {

        console.error(error);
        alert("Something went wrong.");

    } finally {

        updatingCart = false;

    }
}

function updateCartDisplay(cart) {

    const items = document.querySelectorAll(".cart-item");

    let subtotal = 0;

    items.forEach(item => {

        const bookId = item.dataset.bookId;
        const priceCents = Number(item.dataset.priceCents);
        const quantity = cart[bookId] ?? 0;

        // Remove item from HTML if quantity is zero
        if (quantity === 0) {
            item.remove();
            return;
        }

        // Calculate individual item total
        const itemTotal = priceCents * quantity;

        // Update quantity
        item.querySelector(".cart-quantity").textContent =
            quantity;

        // Update individual total
        item.querySelector(".cart-item-total").textContent =
            (itemTotal / 100).toFixed(2);

        subtotal += itemTotal;

    });

    // Update subtotal
    const subtotalElement =
        document.getElementById("cart-subtotal");

    if (subtotalElement) {
        subtotalElement.textContent =
            (subtotal / 100).toFixed(2);
    }

    // Display empty cart message
    const emptyMessage =
        document.getElementById("empty-cart-message");

    if (emptyMessage) {
        emptyMessage.hidden =
            document.querySelectorAll(".cart-item").length > 0;
    }
}
