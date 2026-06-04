
function validateCheckout()
{
    var address = document.getElementById("shipping_address");
    if (address && address.value.trim() === "") {
        alert("Delivery address is required");
        return false;
    }
    return true;
}

function validatePayment()
{
    var methods = document.getElementsByName("payment_method");
    for (var i = 0; i < methods.length; i++) {
        if (methods[i].checked) {
            return true;
        }
    }
    alert("Please select a payment method");
    return false;
}

function addToCartFromDetail(bookId)
{
    var qtyInput = document.getElementById("detailQty");
    var quantity = qtyInput ? parseInt(qtyInput.value, 10) : 1;
    var stock = parseInt(qtyInput ? qtyInput.getAttribute("data-stock") : "0", 10);

    if (isNaN(quantity) || quantity <= 0) {
        alert("Quantity must be at least 1");
        return;
    }
    if (quantity > stock) {
        alert("Only " + stock + " units available in stock");
        return;
    }

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
            var data = JSON.parse(this.responseText);
            if (data.success) {
                var navCount = document.getElementById("navCartCount");
                if (navCount) {
                    navCount.textContent = data.cart_count;
                }
                alert(data.message);
            } else {
                alert(data.message || "Failed to add to cart");
            }
        }
    };
    xhttp.open("POST", "../control/cart_add_api.php", true);
    xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhttp.send("book_id=" + encodeURIComponent(bookId) + "&quantity=" + encodeURIComponent(quantity));
}

function addToCart(bookId)
{
    var qtyInput = document.getElementById("qty-input-" + bookId);
    var quantity = qtyInput ? parseInt(qtyInput.value, 10) : 1;
    var stock = parseInt(qtyInput ? qtyInput.getAttribute("data-stock") : "0", 10);

    if (isNaN(quantity) || quantity <= 0) {
        alert("Quantity must be at least 1");
        return;
    }
    if (quantity > stock) {
        alert("Only " + stock + " units available in stock");
        return;
    }

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
            var data = JSON.parse(this.responseText);
            if (data.success) {
                var navCount = document.getElementById("navCartCount");
                if (navCount) {
                    navCount.textContent = data.cart_count;
                }
                alert(data.message);
            } else {
                alert(data.message || "Failed to add to cart");
            }
        }
    };

    xhttp.open("POST", "../control/cart_add_api.php", true);
    xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhttp.send("book_id=" + encodeURIComponent(bookId) + "&quantity=" + encodeURIComponent(quantity));
}

function updateCart(cartId, newQty, maxStock)
{
    if (newQty <= 0) {
        removeFromCart(cartId);
        return;
    }
    if (newQty > maxStock) {
        alert("Only " + maxStock + " units available");
        return;
    }

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
            var data = JSON.parse(this.responseText);
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || "Failed to update");
            }
        }
    };
    xhttp.open("POST", "../control/cart_update_api.php", true);
    xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhttp.send("cart_id=" + encodeURIComponent(cartId) + "&quantity=" + encodeURIComponent(newQty));
}

function removeFromCart(cartId)
{
    if (!confirm("Remove this item from cart?")) {
        return;
    }

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
            var data = JSON.parse(this.responseText);
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || "Failed to remove");
            }
        }
    };
    xhttp.open("POST", "../control/cart_remove_api.php", true);
    xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhttp.send("cart_id=" + encodeURIComponent(cartId));
}

function addCartButtonsToBookCards(books)
{
    for (var i = 0; i < books.length; i++) {
        var b = books[i];
        if (parseInt(b.stock, 10) <= 0) {
            continue;
        }
        var card = document.querySelector('.book-card[data-book-id="' + b.id + '"]');
        if (!card || card.querySelector(".add-cart-form")) {
            continue;
        }
        var cartForm = document.createElement("div");
        cartForm.className = "add-cart-form";
        var qtyInput = document.createElement("input");
        qtyInput.type = "number";
        qtyInput.className = "add-cart-qty";
        qtyInput.id = "qty-input-" + b.id;
        qtyInput.value = "1";
        qtyInput.min = "1";
        qtyInput.max = b.stock;
        qtyInput.setAttribute("data-stock", b.stock);
        var btn = document.createElement("button");
        btn.type = "button";
        btn.className = "btn-add-cart";
        btn.textContent = "Add to Cart";
        btn.onclick = function (id) {
            return function () { addToCart(id); };
        }(b.id);
        cartForm.appendChild(qtyInput);
        cartForm.appendChild(btn);
        card.appendChild(cartForm);
    }
}

if (typeof renderBookCards !== "undefined") {
    var task1RenderBookCards = renderBookCards;
    renderBookCards = function (books) {
        task1RenderBookCards(books);
        addCartButtonsToBookCards(books);
    };
}
