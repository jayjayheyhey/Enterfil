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

// Check for messages in the URL
$message = '';
$messageClass = '';

if (isset($_GET['success'])) {
    $messageClass = 'success';
    if ($_GET['success'] == 'marked_done') {
        $message = 'Order successfully marked as done!';
    }
}

if (isset($_GET['error'])) {
    $messageClass = 'error';
    switch ($_GET['error']) {
        case 'no_order_specified':
            $message = 'No order specified.';
            break;
        case 'order_not_found':
            $message = 'Order not found.';
            break;
        case 'invalid_status':
            $message = 'Only active orders can be marked as done.';
            break;
        case 'update_failed':
            $message = 'Failed to update order status.';
            break;
        default:
            $message = 'An error occurred.';
    }
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
        /* Additional styles for the message display */
        .message {
            padding: 10px 15px;
            margin: 10px 0;
            border-radius: 4px;
            font-weight: 500;
        }
        .success {
            background-color: #e0f2e9;
            color: #1e7e34;
            border: 1px solid #b8e0ca;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        /* Styles for the mark-as-done button */
        .btn-done {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
            font-size: 0.9em;
            text-decoration: none;
            display: inline-block;
            margin-top: 5px;
        }
        .btn-done:hover {
            background-color: #218838;
        }
        
        /* Style for the buttons container to allow multiple buttons */
        .card-buttons {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }
        
        /* Finished status indicator */
        .status-finished {
            background-color: #28a745;
            color: white;
        }
        
        /* Completion date info */
        .completion-date {
            font-style: italic;
            margin-top: 5px;
            font-size: 0.9em;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Order Dashboard</h1>
        
        <?php if (!empty($message)): ?>
            <div class="message <?php echo $messageClass; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <div class="tabs">
            <a href="?tab=draft" class="tab <?php echo $activeTab == 'draft' ? 'active' : ''; ?>">Draft Orders</a>
            <a href="?tab=active" class="tab <?php echo $activeTab == 'active' ? 'active' : ''; ?>">Active Orders</a>
            <a href="?tab=finished" class="tab <?php echo $activeTab == 'finished' ? 'active' : ''; ?>">Finished Orders</a>
        </div>
        
        <div class="action-buttons">
            <a href="addOrder.php" class="btn btn-primary">Add Order</a>
            <a href="manageOrder.php" class="btn btn-secondary">Manage Orders</a>
            <a href="salesReport.php" class="btn btn-tertiary">Sales Report</a>
            <a href="homepage.php" class="btn btn-quarternary">Inventory</a>
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
                        
                        <?php if ($activeTab == 'finished' && isset($row['completionDate'])): ?>
                            <div class="order-detail completion-date"><strong>Completed:</strong> <?php echo htmlspecialchars($row['completionDate']); ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="card-buttons">
                        <?php
                            // Define button properties based on status
                            $buttonLink = ($row['status'] == 'draft') 
                                ? "editOrder.php?jobOrderNumber={$row['jobOrderNumber']}" 
                                : "orderDetails.php?jobOrderNumber={$row['jobOrderNumber']}";
                            
                            $buttonText = ($row['status'] == 'draft') ? "Edit" : "View";
                        ?>
                        <a class="btn btn-primary" href="<?= $buttonLink ?>"><?= $buttonText ?></a>
                        
                        <?php if ($activeTab == 'active'): ?>
                            <a class="btn btn-done" href="markAsDone.php?jobOrderNumber=<?= $row['jobOrderNumber'] ?>" 
                               onclick="return confirm('Are you sure you want to mark this order as done?');">
                                <i class="fas fa-check"></i> Mark as Done
                            </a>
                        <?php endif; ?>
                    </div>
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
    
    <script>
        // Auto-hide messages after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const message = document.querySelector('.message');
            if (message) {
                setTimeout(function() {
                    message.style.opacity = '0';
                    setTimeout(function() {
                        message.style.display = 'none';
                    }, 500);
                }, 5000);
            }
        });
    </script>
</body>
</html>