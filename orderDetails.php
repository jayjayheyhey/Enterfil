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
    <style>
        .section-line {
            border-top: 2px solid #000;
            margin: 20px 0;
        }

        .row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .label {
            font-weight: bold;
            min-width: 150px;
        }

        .description {
            margin-bottom: 12px;
        }

        .drawing {
            text-align: center;
            margin-top: 20px;
        }

        .drawing img {
            border: 1px solid #333;
            margin-top: 10px;
            max-width: 100%;
            height: auto;
        }

        .back-link {
            margin-top: 30px;
            display: inline-block;
            text-decoration: none;
            color: #fff;
            background-color: rgb(125, 125, 235);
            padding: 10px 20px;
            border-radius: 5px;
            transition: background 0.3s ease;
        }

        .back-link:hover {
            background-color: #07001f;
        }

        h3 {
            margin-top: 25px;
        }

        .description .label {
            display: inline-block;
            width: 180px;
        }

        .job-order-header {
            font-size: 1.3rem;
            font-weight: bold;
            margin-bottom: 10px;
            justify-content: space-between;
        }

    </style>
</head>
<body>
    <div class="container">
        <div class="job-order-header">Job Order No. <?= $order['jobOrderNumber'] ?></div>
        <div class ="job-order-header"><span class="label">Date Created:</span> <?= $order['dateCreated'] ?></div>
        <div class="section-line"></div>


        <div class="row">
            <div><span class="label">Company:</span> <?= $order['company'] ?></div>
            <div><span class="label">Quantity:</span> <?= $order['quantity'] ?></div>
        </div>

        <div class="row">
            <div><span class="label">Items:</span> <?= $order['items'] ?></div>
            <div><span class="label">Required Date:</span> <?= $order['requiredDate'] ?></div>
        </div>

        <div class="section-line"></div>
        <h3>Descriptions:</h3>

        <div class="description"><span class="label">Cap:</span> <?= $order['cap'] ?></div>
        <div class="description"><span class="label">Size:</span> <?= $order['size'] ?></div>
        <div class="description"><span class="label">Gasket:</span> <?= $order['gasket'] ?></div>
        <div class="description"><span class="label">O-Ring:</span> <?= $order['oring'] ?></div>
        <div class="description"><span class="label">Filter Media:</span> <?= $order['filterMedia'] ?></div>
        <div class="description"><span class="label">Inside Support:</span> <?= $order['insideSupport'] ?></div>
        <div class="description"><span class="label">Outside Support:</span> <?= $order['outsideSupport'] ?></div>
        <div class="description"><span class="label">Brand:</span> <?= $order['brand'] ?></div>
        <div class="description"><span class="label">Price:</span> ₱<?= number_format($order['price'], 2) ?></div>

        <div class="section-line"></div>
        <div class="drawing">
            <h3>Filter Drawing</h3>
            <?php if (!empty($order['filterDrawing'])): ?>
                <img src="data:image/jpeg;base64,<?= base64_encode($order['filterDrawing']) ?>" alt="Filter Drawing" />
            <?php else: ?>
                <p>No drawing uploaded.</p>
            <?php endif; ?>
        </div>

        <a class="back-link" href="orderFormDashboard.php">← Back to Dashboard</a>
    </div>
</body>
</html>
