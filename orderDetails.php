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
    <link rel="stylesheet" href="orderDetails4.css">

</head>
<body>
    
    <div class="container">
    <div class="backEdit">
        <a href="javascript:history.back()" class="back-btn"><i class="fas fa-arrow-left"></i></a>
        <a href="editOrder.php?jobOrderNumber=<?php echo htmlspecialchars($order['jobOrderNumber']); ?>" class="btn btn-sm btn-secondary"
        <?php echo ($order['status'] == 'finished') ? 'hidden' : ''; ?> >
            <i class="fas fa-edit"></i> Edit
        </a>
    </div>

    <div class="backEdit" style="margin-top: 10px; margin-bottom:20px;">
        <div class="download"><i class="fas fa-clipboard"></i>
        <a href="generateOrderForm.php?jobOrderNumber=<?= $order['jobOrderNumber'] ?>" 
        id="downloadLink" 
        class="downloadlink" 
        style="font-family: Arial, sans-serif;">
            <span class="emphasize">Download Order Form</span>
        </a>
        </div>
        
        <?php if (isset($_GET['showMarkAsDone']) && $_GET['showMarkAsDone'] == 'true' && $order['status'] != 'finished'): ?>
            <div class="mark-done-container">
                <a href="markAsDone.php?jobOrderNumber=<?php echo htmlspecialchars($order['jobOrderNumber']); ?>" class="btn-done">
                    <i class="fas fa-check"></i> Mark as Done
                </a>
            </div>
        <?php endif; ?>
    </div>


        <div class="row job-order-header">
            <div><span class="label">Job Order No.</span> <?= $order['jobOrderNumber'] ?></div>
            <div><span class="label">Date Created:</span> <?= $order['dateCreated'] ?></div>
            <div id= "completed" <?php echo ($order['status'] != 'finished') ? 'hidden' : ''; ?>>
                <span class="label">Date Completed:</span> <?= $order['completionDate'] ?>
            </div>
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

    </div>
</body>
</html>