<?php
include("connect.php");

if (isset($_GET['jobOrderNumber'])) {
    $jobOrderNumber = $_GET['jobOrderNumber'];

    $stmt = $conn->prepare("SELECT * FROM order_form WHERE jobOrderNumber = ?");
    $stmt->bind_param("i", $jobOrderNumber);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();
} else {
    echo "No job order number provided.";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Order Details</title>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
    <div class="container">
        <h2>Job Order Details for #<?= $order['jobOrderNumber'] ?></h2>
        <ul>
            <li>Company: <?= $order['company'] ?></li>
            <li>Items: <?= $order['items'] ?></li>
            <li>Quantity: <?= $order['quantity'] ?></li>
            <li>Required Date: <?= $order['requiredDate'] ?></li>
            <li>Cap: <?= $order['cap'] ?></li>
            <li>Size: <?= $order['size'] ?></li>
            <li>Gasket: <?= $order['gasket'] ?></li>
            <li>O-Ring: <?= $order['o-ring'] ?></li>
            <li>Filter Media: <?= $order['filterMedia'] ?></li>
            <li>Inside Support: <?= $order['insideSupport'] ?></li>
            <li>Outside Support: <?= $order['outsideSupport'] ?></li>
            <li>Brand: <?= $order['brand'] ?></li>
            <li>Price: ₱<?= number_format($order['price'], 2) ?></li>
            <li>Drawing: 
                <?php
                    if (!empty($order['filterDrawing'])) {
                        echo '<img src="data:image/jpeg;base64,' . base64_encode($order['filterDrawing']) . '" width="200"/>';
                    } else {
                        echo 'No drawing uploaded.';
                    }
                ?>
            </li>
        </ul>
        <a href="orderFormDashboard.php">← Back to Dashboard</a>
    </div>
</body>
</html>
