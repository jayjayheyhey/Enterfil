<?php
include("connect.php");

$errorMessage = "";

if (isset($_POST['searchButton'])) {
    $jobOrderNumber = $_POST['jobOrderNumber'];

    $stmt = $conn->prepare("SELECT * FROM order_form WHERE jobOrderNumber = ?");
    $stmt->bind_param("s", $jobOrderNumber);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Redirect to edit page with jobOrderNumber
        header("Location: editOrder.php?jobOrderNumber=" . urlencode($jobOrderNumber));
        exit();
    } else {
        $errorMessage = "Job Order Number not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search Job Order</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container" id="searchForm">
        <h1 class="form-title">Enter Job Order Number to Edit</h1>

        <?php if (!empty($errorMessage)): ?>
            <p class="popup"><?php echo $errorMessage; ?></p>
        <?php endif; ?>

        <form method="post" action="searchJobOrder.php">
            <div class="input-group">
                <label for="jobOrderNumber">Job Order Number:</label>
                <input type="text" name="jobOrderNumber" id="jobOrderNumber" required>
            </div>
            <input type="submit" class="btn" value="Search" name="searchButton">
        </form>

        <form method="post" action="orderFormDashboard.php">
            <input type="submit" class="btn" value="Back to Dashboard">
        </form>
    </div>
</body>
</html>