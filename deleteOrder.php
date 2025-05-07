<?php
include("connect.php");

// Initialize variables
$singleDeletion = false;
$searchQuery = '';
$statusFilter = isset($_GET['status']) ? $_GET['status'] : 'all';
$errorMessage = isset($_GET['error']) ? $_GET['error'] : '';
$successMessage = '';

// Process single deletion request (from direct job order number)
if (isset($_GET['jobOrderNumber']) && !empty($_GET['jobOrderNumber'])) {
    $jobOrderNumber = $_GET['jobOrderNumber'];

    // Fetch the order to confirm the job order number exists
    $stmt = $conn->prepare("SELECT * FROM order_form WHERE jobOrderNumber = ?");
    $stmt->bind_param("s", $jobOrderNumber);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $order = $result->fetch_assoc();
        $singleDeletion = true;
    } else {
        $errorMessage = "Job Order Number not found";
    }

    // Handle single deletion after confirmation
    if (isset($_POST['delete_single'])) {
        $stmtDelete = $conn->prepare("DELETE FROM order_form WHERE jobOrderNumber = ?");
        $stmtDelete->bind_param("s", $jobOrderNumber);
        $stmtDelete->execute();

        if ($stmtDelete->affected_rows > 0) {
            header("Location: orderFormDashboard.php?tab=draft&deleted=1");
            exit();
        } else {
            $errorMessage = "Failed to delete the order";
        }
    }
}

// Handle search request
if (isset($_POST['search']) && !empty($_POST['search_query'])) {
    $searchQuery = trim($_POST['search_query']);
}

// Process batch deletion
if (isset($_POST['delete_selected']) && isset($_POST['orders']) && is_array($_POST['orders'])) {
    $selectedOrders = $_POST['orders'];
    $deleteCount = 0;
    
    // Begin transaction for multiple deletions
    $conn->begin_transaction();
    
    try {
        $stmtDelete = $conn->prepare("DELETE FROM order_form WHERE jobOrderNumber = ?");
        
        foreach ($selectedOrders as $orderNumber) {
            $stmtDelete->bind_param("s", $orderNumber);
            $stmtDelete->execute();
            $deleteCount += $stmtDelete->affected_rows;
        }
        
        // Commit the transaction
        $conn->commit();
        
        // Set success message
        $successMessage = "{$deleteCount} order(s) successfully deleted";
    } catch (Exception $e) {
        // Rollback the transaction on error
        $conn->rollback();
        $errorMessage = "Error deleting orders: " . $e->getMessage();
    }
}

// Prepare the query based on status filter and search query
if (!$singleDeletion) {
    if (!empty($searchQuery)) {
        // Search for orders matching the search query
        $searchTerm = "%{$searchQuery}%";
        if ($statusFilter == 'all') {
            $query = "SELECT * FROM order_form WHERE 
                      jobOrderNumber LIKE ? OR 
                      company LIKE ? OR 
                      description LIKE ? 
                      ORDER BY dateCreated DESC";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
        } else {
            $query = "SELECT * FROM order_form WHERE 
                      (jobOrderNumber LIKE ? OR 
                      company LIKE ? OR 
                      description LIKE ?) AND
                      status = ?
                      ORDER BY dateCreated DESC";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ssss", $searchTerm, $searchTerm, $searchTerm, $statusFilter);
        }
    } else {
        // No search query, just filter by status
        if ($statusFilter == 'all') {
            $query = "SELECT * FROM order_form ORDER BY dateCreated DESC";
            $stmt = $conn->prepare($query);
        } else {
            $query = "SELECT * FROM order_form WHERE status = ? ORDER BY dateCreated DESC";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("s", $statusFilter);
        }
    }
    
    $stmt->execute();
    $orders = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Orders</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="orderFormDashboard2.css">
    <style>
        .delete-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .order-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .order-table th, .order-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .order-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        
        .order-table tr:hover {
            background-color: #f5f5f5;
        }
        
        .action-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 20px 0;
        }
        
        .status-filter {
            display: flex;
            gap: 10px;
        }
        
        .status-badge {
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 14px;
            color: white;
        }
        
        .status-draft { background-color: #6c757d; }
        .status-active { background-color: #007bff; }
        .status-finished { background-color: #28a745; }
        
        .select-all-container {
            margin-bottom: 10px;
        }
        
        .warning-text {
            color: #dc3545;
            font-weight: bold;
            margin-bottom: 20px;
        }
        
        .confirmation-box {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .no-orders {
            text-align: center;
            padding: 40px;
            color: #6c757d;
        }
        
        .search-box {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        
        .search-box input {
            flex-grow: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        
        .error {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        
        .success {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        
        /* Responsive design for mobile */
        @media (max-width: 768px) {
            .action-bar, .status-filter {
                flex-direction: column;
                gap: 5px;
            }
            
            .search-box {
                flex-direction: column;
            }
            
            .order-table {
                font-size: 14px;
            }
            
            .order-table th, .order-table td {
                padding: 8px;
            }
        }
    </style>
</head>
<body>
    <div class="<?php echo $singleDeletion ? 'container' : 'delete-container'; ?>">
        <?php if ($singleDeletion): ?>
            <!-- Single Order Deletion Confirmation -->
            <h1>Confirm Deletion</h1>
            <div class="confirmation-box">
                <h2>Are you sure you want to delete Job Order No. <?php echo htmlspecialchars($jobOrderNumber); ?>?</h2>
                
                <p><strong>Company:</strong> <?php echo htmlspecialchars($order['company']); ?></p>
                <p><strong>Quantity:</strong> <?php echo htmlspecialchars($order['quantity']); ?></p>
                <p><strong>Date Created:</strong> <?php echo htmlspecialchars($order['dateCreated']); ?></p>
                <p><strong>Status:</strong> <?php echo htmlspecialchars($order['status']); ?></p>

                <form method="post">
                    <input type="submit" class="btn btn-danger" name="delete_single" value="Yes, Delete">
                    <a href="deleteOrder.php" class="btn">Cancel</a>
                </form>
            </div>
        <?php else: ?>
            <!-- Multiple Order Management & Search Interface -->
            <h1>Manage and Delete Orders</h1>
            
            <?php if (!empty($errorMessage)): ?>
                <div class="message error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($errorMessage); ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($successMessage)): ?>
                <div class="message success">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($successMessage); ?>
                </div>
            <?php endif; ?>
            
            <!-- Search Form -->
            <form method="post" class="search-box">
                <input type="text" name="search_query" placeholder="Search by Job Order No., Company, or Description" value="<?php echo htmlspecialchars($searchQuery); ?>">
                <button type="submit" name="search" class="btn btn-primary">
                    <i class="fas fa-search"></i> Search
                </button>
                <?php if (!empty($searchQuery)): ?>
                    <a href="deleteOrder.php?status=<?php echo $statusFilter; ?>" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Clear
                    </a>
                <?php endif; ?>
            </form>
            
            <div class="action-bar">
                <div class="status-filter">
                    <a href="?status=all<?php echo !empty($searchQuery) ? '&search='.urlencode($searchQuery) : ''; ?>" class="btn <?php echo $statusFilter == 'all' ? 'btn-primary' : 'btn-secondary'; ?>">All Orders</a>
                    <a href="?status=draft<?php echo !empty($searchQuery) ? '&search='.urlencode($searchQuery) : ''; ?>" class="btn <?php echo $statusFilter == 'draft' ? 'btn-primary' : 'btn-secondary'; ?>">Draft</a>
                    <a href="?status=active<?php echo !empty($searchQuery) ? '&search='.urlencode($searchQuery) : ''; ?>" class="btn <?php echo $statusFilter == 'active' ? 'btn-primary' : 'btn-secondary'; ?>">Active</a>
                    <a href="?status=finished<?php echo !empty($searchQuery) ? '&search='.urlencode($searchQuery) : ''; ?>" class="btn <?php echo $statusFilter == 'finished' ? 'btn-primary' : 'btn-secondary'; ?>">Finished</a>
                </div>
                <a href="orderFormDashboard.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>
            
            <?php if (isset($orders) && $orders->num_rows > 0): ?>
                <form method="post" id="deleteForm">
                    <div class="select-all-container">
                        <input type="checkbox" id="select-all"> <label for="select-all">Select All</label>
                    </div>
                    
                    <table class="order-table">
                        <thead>
                            <tr>
                                <th width="5%">Select</th>
                                <th width="15%">Job Order No.</th>
                                <th width="25%">Company</th>
                                <th width="15%">Quantity</th>
                                <th width="15%">Date Created</th>
                                <th width="10%">Status</th>
                                <th width="15%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($order = $orders->fetch_assoc()): ?>
                                <tr>
                                    <td><input type="checkbox" name="orders[]" value="<?php echo htmlspecialchars($order['jobOrderNumber']); ?>" class="order-checkbox"></td>
                                    <td><?php echo htmlspecialchars($order['jobOrderNumber']); ?></td>
                                    <td><?php echo htmlspecialchars($order['company']); ?></td>
                                    <td><?php echo htmlspecialchars($order['quantity']); ?></td>
                                    <td><?php echo htmlspecialchars($order['dateCreated']); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo $order['status']; ?>">
                                            <?php echo ucfirst(htmlspecialchars($order['status'])); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="deleteOrder.php?jobOrderNumber=<?php echo htmlspecialchars($order['jobOrderNumber']); ?>" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i> Delete
                                        </a>
                                        <a href="orderDetails.php?jobOrderNumber=<?php echo htmlspecialchars($order['jobOrderNumber']); ?>" class="btn btn-sm btn-secondary">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                    
                    <div id="delete-selected-container" style="display: none; margin-top: 20px;">
                        <div class="warning-text">
                            <p><i class="fas fa-exclamation-triangle"></i> Warning: This action cannot be undone!</p>
                        </div>
                        <button type="submit" name="delete_selected" class="btn btn-danger" id="delete-selected-btn">
                            <i class="fas fa-trash"></i> Delete Selected Orders
                        </button>
                    </div>
                </form>
            <?php else: ?>
                <div class="no-orders">
                    <i class="fas fa-clipboard-list" style="font-size: 48px; margin-bottom: 20px;"></i>
                    <?php if (!empty($searchQuery)): ?>
                        <p>No orders found matching your search criteria</p>
                        <a href="deleteOrder.php" class="btn btn-primary">Show All Orders</a>
                    <?php else: ?>
                        <p>No orders found matching the selected status</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <script>
                // Select all functionality
                document.getElementById('select-all').addEventListener('change', function() {
                    var checkboxes = document.getElementsByClassName('order-checkbox');
                    for (var i = 0; i < checkboxes.length; i++) {
                        checkboxes[i].checked = this.checked;
                    }
                    updateDeleteButton();
                });
                
                // Update the visibility of the delete button based on selections
                function updateDeleteButton() {
                    var checkboxes = document.getElementsByClassName('order-checkbox');
                    var selected = false;
                    
                    for (var i = 0; i < checkboxes.length; i++) {
                        if (checkboxes[i].checked) {
                            selected = true;
                            break;
                        }
                    }
                    
                    document.getElementById('delete-selected-container').style.display = selected ? 'block' : 'none';
                }
                
                // Add event listeners to all checkboxes
                var checkboxes = document.getElementsByClassName('order-checkbox');
                for (var i = 0; i < checkboxes.length; i++) {
                    checkboxes[i].addEventListener('change', updateDeleteButton);
                }
                
                // Confirm deletion when form is submitted
                document.getElementById('deleteForm').addEventListener('submit', function(e) {
                    if (e.submitter && e.submitter.name === 'delete_selected') {
                        var checkboxes = document.getElementsByClassName('order-checkbox');
                        var checkedCount = 0;
                        
                        for (var i = 0; i < checkboxes.length; i++) {
                            if (checkboxes[i].checked) {
                                checkedCount++;
                            }
                        }
                        
                        if (checkedCount === 0) {
                            e.preventDefault();
                            alert('Please select at least one order to delete');
                            return;
                        }
                        
                        if (!confirm('Are you sure you want to delete ' + checkedCount + ' orders? This action cannot be undone.')) {
                            e.preventDefault();
                        }
                    }
                });
            </script>
        <?php endif; ?>
    </div>
</body>
</html>