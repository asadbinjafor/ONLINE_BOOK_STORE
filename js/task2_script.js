
function deleteCustomer(userId, name)
{
    if (!confirm('Remove customer "' + name + '"? This cannot be undone.')) {
        return;
    }
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
            var data = JSON.parse(this.responseText);
            alert(data.message);
            if (data.success) {
                location.reload();
            }
        }
    };
    xhttp.open("POST", "../control/customer_delete_api.php", true);
    xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhttp.send("user_id=" + encodeURIComponent(userId));
}

function validateBookForm()
{
    var title = document.getElementById("title").value.trim();
    var author = document.getElementById("author").value.trim();
    var price = parseFloat(document.getElementById("price").value);
    var stock = parseInt(document.getElementById("stock").value, 10);
    var category = document.getElementById("category_id").value;

    if (title === "") { alert("Title is required"); return false; }
    if (author === "") { alert("Author is required"); return false; }
    if (isNaN(price) || price <= 0) { alert("Price must be greater than 0"); return false; }
    if (category === "" || category === "0") { alert("Select a category"); return false; }
    if (isNaN(stock) || stock < 0) { alert("Stock must be 0 or more"); return false; }
    return true;
}

function confirmDelete(itemName)
{
    return confirm("Are you sure you want to delete this " + itemName + "?");
}

function validateAdminUserForm()
{
    var name = document.getElementById("user_name").value.trim();
    var email = document.getElementById("user_email").value.trim();
    var password = document.getElementById("user_password").value;
    var role = document.getElementById("user_role").value;
    var address = document.getElementById("user_address").value.trim();
    var phone = document.getElementById("user_phone").value.trim();

    if (name === "") { alert("Name is required"); return false; }
    if (email === "" || email.indexOf("@") === -1) { alert("Valid email is required"); return false; }
    if (password.length < 8) { alert("Password must be at least 8 characters"); return false; }
    if (role !== "admin" && role !== "customer") { alert("Select a valid role"); return false; }
    if (address === "") { alert("Address is required"); return false; }
    if (phone === "" || !/^[0-9]+$/.test(phone)) { alert("Phone must contain digits only"); return false; }
    return true;
}

function updateOrderStatus(orderId, status)
{
    var labels = { confirmed: "confirm", shipped: "mark as shipped", delivered: "mark as delivered" };
    var label = labels[status] || status;
    if (!confirm("Are you sure you want to " + label + " order #" + orderId + "?")) {
        return;
    }

    var msgDiv = document.getElementById("orderMsg");
    if (msgDiv) {
        msgDiv.innerHTML = "";
    }

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState === 4) {
            if (this.status === 200) {
                var data = JSON.parse(this.responseText);
                if (data.success) {
                    var badge = document.getElementById("status-badge-" + orderId);
                    var actionCell = document.getElementById("action-cell-" + orderId);
                    if (badge) {
                        badge.className = "order-status-badge status-" + data.status;
                        badge.textContent = data.status.charAt(0).toUpperCase() + data.status.slice(1);
                    }
                    if (actionCell) {
                        if (data.status === "confirmed") {
                            actionCell.innerHTML = '<button class="btn-action btn-accept-sm" onclick="updateOrderStatus(' + orderId + ', \'shipped\')">Ship</button>';
                        } else if (data.status === "shipped") {
                            actionCell.innerHTML = '<button class="btn-action btn-accept-sm" onclick="updateOrderStatus(' + orderId + ', \'delivered\')">Deliver</button>';
                        } else {
                            actionCell.innerHTML = '<span class="text-muted">—</span>';
                        }
                    }
                    if (msgDiv) {
                        msgDiv.innerHTML = "<div class='msg-success'>Order #" + orderId + " has been " + data.status + ".</div>";
                    }
                } else if (msgDiv) {
                    msgDiv.innerHTML = "<div class='msg-error'>" + (data.message || "Failed") + "</div>";
                } else {
                    alert(data.message || "Failed");
                }
            } else if (msgDiv) {
                msgDiv.innerHTML = "<div class='msg-error'>Server error.</div>";
            }
        }
    };
    xhttp.open("POST", "../control/admin_order_status_api.php", true);
    xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhttp.send("order_id=" + encodeURIComponent(orderId) + "&status=" + encodeURIComponent(status));
}
