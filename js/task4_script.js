
function validateOrderFilters()
{
    var q = document.getElementById("orderSearch");
    var from = document.getElementById("fromDate");
    var to = document.getElementById("toDate");
    if (q && q.value.trim().length > 100) {
        alert("Search text must be 100 characters or less");
        return false;
    }
    if (from && from.value !== "" && to && to.value !== "" && from.value > to.value) {
        alert("From date cannot be after to date");
        return false;
    }
    return true;
}

function searchOrders()
{
    if (!validateOrderFilters()) {
        return;
    }
    var q = document.getElementById("orderSearch").value.trim();
    var status = document.getElementById("statusFilter").value;
    var from = document.getElementById("fromDate").value;
    var to = document.getElementById("toDate").value;
    var url = "../control/orders_search_api.php?status=" + encodeURIComponent(status) +
        "&from=" + encodeURIComponent(from) + "&to=" + encodeURIComponent(to) + "&q=" + encodeURIComponent(q);

    var tbody = document.getElementById("ordersTableBody");
    if (tbody) {
        tbody.innerHTML = "<tr><td colspan='6' class='text-center'>Searching...</td></tr>";
    }

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
            var data = JSON.parse(this.responseText);
            if (data.success) {
                renderOrderRows(data.orders);
            } else if (tbody) {
                tbody.innerHTML = "<tr><td colspan='6' class='text-center'>" + (data.message || "Search failed") + "</td></tr>";
            }
        }
    };
    xhttp.open("GET", url, true);
    xhttp.send();
}

function renderOrderRows(orders)
{
    var tbody = document.getElementById("ordersTableBody");
    if (!tbody) {
        return;
    }
    tbody.innerHTML = "";
    if (!orders || orders.length === 0) {
        tbody.innerHTML = "<tr><td colspan='6' class='text-center'>No orders found.</td></tr>";
        return;
    }
    for (var i = 0; i < orders.length; i++) {
        var o = orders[i];
        var tr = document.createElement("tr");
        tr.innerHTML =
            "<td>#" + o.id + "</td>" +
            "<td>" + o.order_date + "</td>" +
            "<td>" + parseFloat(o.total_amount).toFixed(2) + "</td>" +
            "<td>" + (o.payment_method || "—") + "</td>" +
            "<td><span class='order-status-badge status-" + o.status + "'>" +
            o.status.charAt(0).toUpperCase() + o.status.slice(1) + "</span></td>" +
            "<td class='order-actions'>" +
            "<a class='btn-link' href='customer_order_detail.php?order_id=" + o.id + "'>View</a> " +
            "<a class='btn-link' href='order_invoice.php?order_id=" + o.id + "' target='_blank'>Invoice</a>" +
            "</td>";
        tbody.appendChild(tr);
    }
}

function resetOrderFilters()
{
    document.getElementById("orderSearch").value = "";
    document.getElementById("statusFilter").value = "";
    document.getElementById("fromDate").value = "";
    document.getElementById("toDate").value = "";
    searchOrders();
}

function reorderPurchase(orderId)
{
    if (!confirm("Add available items from this order to your cart?")) {
        return;
    }
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
            var data = JSON.parse(this.responseText);
            var msg = data.message;
            if (data.warnings && data.warnings.length > 0) {
                msg += "\n" + data.warnings.join("\n");
            }
            alert(msg);
            if (data.success && data.cart_count !== undefined) {
                var nav = document.getElementById("navCartCount");
                if (nav) {
                    nav.textContent = data.cart_count;
                }
            }
        }
    };
    xhttp.open("POST", "../control/customer_order_reorder_api.php", true);
    xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhttp.send("order_id=" + encodeURIComponent(orderId));
}

function showOrderDetailMsg(message, type)
{
    var el = document.getElementById("orderDetailMsg");
    if (el) {
        el.innerHTML = "<div class='msg-" + type + "'>" + message + "</div>";
    }
}

function placeOrderAjax()
{
    return placeOrder();
}
