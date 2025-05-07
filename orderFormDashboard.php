<?php
include("connect.php");

// Determine which tab is active
$activeTab = isset($_GET['tab']) ? $_GET['tab'] : 'draft';

// Function to get orders by status
function getOrdersByStatus($conn, $status) {
    $query = "SELECT * FROM order_form WHERE status = ? ORDER BY dateCreated DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $status);
    $stmt->execute();
    return $stmt->get_result();
}

// Get orders based on active tab
$orders = [];
if ($activeTab == 'draft') {
    $orders = getOrdersByStatus($conn, 'draft');
} elseif ($activeTab == 'active') {
    $orders = getOrdersByStatus($conn, 'active');
} elseif ($activeTab == 'finished') {
    $orders = getOrdersByStatus($conn, 'finished');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="orderFormDashboard2.css">

    <title>Order Dashboard</title>
    <style>
       
    </style>
</head>
<body>
    <div class="container">
        <h1>Order Dashboard</h1>
        
        <div class="tabs">
            <a href="?tab=draft" class="tab <?php echo $activeTab == 'draft' ? 'active' : ''; ?>">Draft Orders</a>
            <a href="?tab=active" class="tab <?php echo $activeTab == 'active' ? 'active' : ''; ?>">Active Orders</a>
            <a href="?tab=finished" class="tab <?php echo $activeTab == 'finished' ? 'active' : ''; ?>">Finished Orders</a>
        </div>
        
        <div class="action-buttons">
            <a href="addOrder.php" class="btn btn-primary">Add Order</a>
            <a href="searchJobOrder.php" class="btn btn-secondary">Edit Order</a>
            <a href="deleteOrder.php" class="btn btn-secondary">Remove Order</a>
            <a href="homepage.php" class="btn btn-tertiary">Inventory</a>
        </div>
        
        <div class="order-cards">
            <?php 
            if ($orders->num_rows > 0) {
                while ($row = $orders->fetch_assoc()) {
                    $statusClass = "status-" . $activeTab;
                    $statusLabel = ucfirst($activeTab);
            ?>
                <div class="order-card">
                    <div class="order-header">
                        <div class="order-number">Job Order No. <?php echo htmlspecialchars($row['jobOrderNumber']); ?></div>
                        <span class="order-status <?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span>
                    </div>
                    <div class="order-details">
                        <div class="order-detail"><strong>Company:</strong> <?php echo htmlspecialchars($row['company']); ?></div>
                        <div class="order-detail"><strong>Quantity:</strong> <?php echo htmlspecialchars($row['quantity']); ?></div>
                        <div class="order-detail"><strong>Date Created:</strong> <?php echo htmlspecialchars($row['dateCreated']); ?></div>
                        <div class="order-detail"><strong>Required Date:</strong> <?php echo htmlspecialchars($row['requiredDate']); ?></div>
                    </div>

                    <?php
                        // Define button properties based on status
                        $buttonLink = ($row['status'] == 'draft') 
                            ? "editOrder.php?jobOrderNumber={$row['jobOrderNumber']}" 
                            : "orderDetails.php?jobOrderNumber={$row['jobOrderNumber']}";
                        
                        $buttonText = ($row['status'] == 'draft') ? "Edit" : "View";
                    ?>
                    <a class="btn btn-primary" href="<?= $buttonLink ?>"><?= $buttonText ?></a>

                </div>
            <?php 
                }
            } else {
            ?>
                <div class="empty-state">
                    <i class="fas fa-clipboard-list"></i>
                    <p>No <?php echo $activeTab; ?> orders found</p>
                </div>
            <?php
            }
            ?>
        </div>
    </div>
</body>
</html>