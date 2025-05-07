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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="search.css">
    <link rel="stylesheet" href="border.css">
    <link rel="stylesheet" href="tablestyle2.css">
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

        .downloadlink {
            font-size:16px;          /* Standard font size */
            color: rgb(113, 113, 181);           /* Blue color for the link */
            text-decoration: none;    /* Remove the underline */
            font-weight: normal;      /* Default weight */
            cursor: pointer;         /* Change cursor to indicate clickable link */
            text-decoration: underline;
        }

        .downloadlink:hover {
            text-decoration: underline; /* Underline when hovered */
            font-weight: bold;          /* Make the text bold when hovered */
            color: rgb(82, 82, 139);           /* Blue color for the link */
        }

    </style>
</head>
<body>
    
    <div class="container">
        <div class="right-align" style="margin-top: 10px; margin-bottom:20px;">
            <i class="fas fa-clipboard"></i>
            <a href="generateOrderForm.php?jobOrderNumber=<?= $order['jobOrderNumber'] ?>" 
            id="downloadLink" 
            class="downloadlink" 
            style="font-family: Arial, sans-serif;">
                <span class="emphasize">Download Order Form</span>
            </a>
        </div>


        <div class="row job-order-header">
            <div><span class="label">Job Order No.</span> <?= $order['jobOrderNumber'] ?></div>
            <div><span class="label">Date Created:</span> <?= $order['dateCreated'] ?></div>
        </div>
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

        <div class="description"><span class="label">Cap:</span> <?= $order['cap'] . $order['capUOM'] ?></div>
        <div class="description"><span class="label">Size:</span> <?= $order['size'] . $order['sizeUOM'] ?></div>
        <div class="description"><span class="label">Gasket:</span> <?= $order['gasket'] . $order['gasketUOM'] ?></div>
        <div class="description"><span class="label">O-Ring:</span> <?= $order['oring'] . $order['oringUOM'] ?></div>
        <div class="description"><span class="label">Filter Media:</span> <?= $order['filterMedia'] . $order['filterMediaUOM'] ?></div>
        <div class="description"><span class="label">Inside Support:</span> <?= $order['insideSupport'] . $order['insideSupportUOM'] ?></div>
        <div class="description"><span class="label">Outside Support:</span> <?= $order['outsideSupport'] . $order['outsideSupportUOM']?></div>
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

        <a class="back-link" href="orderFormDashboard.php">Back to Dashboard</a>
    </div>
</body>
</html>