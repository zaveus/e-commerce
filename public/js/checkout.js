
const checkoutForm = document.getElementById("checkout-form");
const checkoutMessage = document.getElementById("checkout-message");
const submitButton = checkoutForm.querySelector("button");

checkoutForm.addEventListener("submit", async function (event) {
    event.preventDefault();

    submitButton.disabled = true;
    submitButton.textContent = "Processing...";

    try {
        const response = await fetch("api/orders.php", {
            method: "POST",
            body: new FormData(checkoutForm)
        });

        const data = await response.json();

        if (data.success) {
            checkoutForm.hidden = true;

            checkoutMessage.textContent =
                `Order #${data.order_id} placed successfully!`;

            checkoutMessage.className = "checkout-success";
        } else {
            checkoutMessage.textContent = data.message;
            checkoutMessage.className = "checkout-error";
        }

    } catch (error) {
        checkoutMessage.textContent =
            "Unable to confirm the order. Check whether it was placed before retrying.";

        checkoutMessage.className = "checkout-error";

    } finally {
        submitButton.disabled = false;
        submitButton.textContent = "Place Order";
    }
});
