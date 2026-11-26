async function updateCart(bookId, action) {
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

        document.getElementById("cart-count").textContent =
            data.count;

    } catch (error) {
        console.error(error);
        alert("Something went wrong.");
    }
}