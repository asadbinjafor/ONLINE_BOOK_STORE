<?php
include '../control/order_invoice_process.php';
include_once '../control/app.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #<?php echo $order["id"]; ?> - BookHub</title>
    <link rel="stylesheet" type="text/css" href="../css/task4_style.css">
</head>
<body class="invoice-page">

<div class="invoice-container" id="invoicePrintArea">

    <div class="invoice-header">
        <div>
            <h1>BookHub</h1>
            <p>Online book Shop</p>
        </div>
        <div class="invoice-meta">
            <h2>INVOICE</h2>
            <p><strong>Order #:</strong> <?php echo $order["id"]; ?></p>
            <p><strong>Date:</strong> <?php echo date("d M Y, h:i A", strtotime($order["order_date"])); ?></p>
            <p><strong>Status:</strong> <?php echo ucfirst(htmlspecialchars($order["status"])); ?></p>
        </div>
    </div>

    <div class="invoice-section">
        <h3>Customer Details</h3>
        <p><strong>Name:</strong> <?php echo htmlspecialchars($customer["name"]); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($customer["email"]); ?></p>
        <p><strong>Phone:</strong> <?php echo htmlspecialchars($customer["phone"]); ?></p>
        <p><strong>Shipping Address:</strong> <?php echo htmlspecialchars($order["shipping_address"]); ?></p>
    </div>

    <div class="invoice-section">
        <h3>Payment Information</h3>
        <p><strong>Method:</strong> <?php echo htmlspecialchars($order["payment_method"] ?? "â€”"); ?></p>
        <?php if($payment){ ?>
            <p><strong>Transaction ID:</strong> <?php echo htmlspecialchars($payment["transaction_id"] ?? "â€”"); ?></p>
            <p><strong>Amount Paid:</strong> BDT <?php echo number_format($payment["amount"], 2); ?></p>
            <p><strong>Payment Date:</strong> <?php echo date("d M Y, h:i A", strtotime($payment["payment_date"])); ?></p>
        <?php } ?>
    </div>

    <div class="invoice-section">
        <h3>Order Items</h3>
        <table class="invoice-table">
            <thead>
                <tr>
                    <th>book</th>
                    <th>Author</th>
                    <th>Qty</th>
                    <th>Unit Price</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php while($item = $orderItems->fetch_assoc()){ ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item["book_title"]); ?></td>
                        <td><?php echo htmlspecialchars($item["author"]); ?></td>
                        <td><?php echo $item["quantity"]; ?></td>
                        <td>BDT <?php echo number_format($item["unit_price"], 2); ?></td>
                        <td>BDT <?php echo number_format($item["quantity"] * $item["unit_price"], 2); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="invoice-total-label">Grand Total</td>
                    <td class="invoice-total-value">BDT <?php echo number_format($order["total_amount"], 2); ?></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <p class="invoice-footer">Thank you for shopping with BookHub!</p>

</div>

<div class="invoice-actions no-print">
    <button type="button" class="btn-primary" onclick="window.print()">Print Invoice</button>
    <a class="btn-secondary" href="customer_order_detail.php?order_id=<?php echo $order["id"]; ?>">Back to Order</a>
</div>

</body>
</html>
<?php $mydb->closeConn($conn); ?>

