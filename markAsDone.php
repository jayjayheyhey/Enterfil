<?php
include("connect.php");

// Check if job order number is provided
if (!isset($_GET['jobOrderNumber'])) {
    header("Location: orderFormDashboard.php?tab=active&error=no_order_specified");
    exit;
}

$jobOrderNumber = $_GET['jobOrderNumber'];

// Verify the order exists and is active
$stmt = $conn->prepare("SELECT status FROM order_form WHERE jobOrderNumber = ?");
$stmt->bind_param("s", $jobOrderNumber);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    header("Location: orderFormDashboard.php?tab=active&error=order_not_found");
    exit;
}

$order = $result->fetch_assoc();

// Check if order is active
if ($order['status'] !== 'active') {
    header("Location: orderFormDashboard.php?tab=active&error=invalid_status");
    exit;
}

// Update status to finished
$updateStmt = $conn->prepare("UPDATE order_form SET status = 'finished', completionDate = CURRENT_DATE() WHERE jobOrderNumber = ?");
$updateStmt->bind_param("s", $jobOrderNumber);

if ($updateStmt->execute()) {
    // Success
    header("Location: orderFormDashboard.php?tab=finished&success=marked_done");
    exit;
} else {
    // Error
    header("Location: orderFormDashboard.php?tab=active&error=update_failed");
    exit;
}
?>