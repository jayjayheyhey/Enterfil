<?php
include("connect.php");

if (isset($_GET['jobOrderNumber'])) {
    $jobOrderNumber = $_GET['jobOrderNumber'];

    // Fetch the order to confirm the job order number exists
    $stmt = $conn->prepare("SELECT * FROM order_form WHERE jobOrderNumber = ?");
    $stmt->bind_param("s", $jobOrderNumber);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $order = $result->fetch_assoc();
    } else {
        // Redirect if the job order number doesn't exist
        header("Location: searchToDelete.php?error=Job Order Number not found");
        exit();
    }

    // Handle deletion after confirmation
    if (isset($_POST['delete'])) {
        $stmtDelete = $conn->prepare("DELETE FROM order_form WHERE jobOrderNumber = ?");
        $stmtDelete->bind_param("s", $jobOrderNumber);
        $stmtDelete->execute();

        // Redirect to the dashboard after successful deletion
        header("Location: orderFormDashboard.php?tab=draft");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Confirm Deletion</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Are you sure you want to delete Job Order No. <?php echo htmlspecialchars($jobOrderNumber); ?>?</h1>
        
        <p><strong>Company:</strong> <?php echo htmlspecialchars($order['company']); ?></p>
        <p><strong>Quantity:</strong> <?php echo htmlspecialchars($order['quantity']); ?></p>
        <p><strong>Date Created:</strong> <?php echo htmlspecialchars($order['dateCreated']); ?></p>

        <form method="post">
            <input type="submit" class="btn btn-danger" name="delete" value="Yes, Delete">
            <a href="orderFormDashboard.php" class="btn">Cancel</a>
        </form>
    </div>
</body>
</html>
