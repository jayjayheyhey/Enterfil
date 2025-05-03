<?php
include("connect.php");

$errorMessage = "";
$submittedData = []; // To hold user-submitted data

if (isset($_POST['submitButton'])) {
    $jobOrderNumber = $_POST['jobOrderNumber'];
    $company = $_POST['company'];
    $items = $_POST['items'];
    $quantity = $_POST['quantity'];

    // Store submitted data so we can re-populate the form if needed
    $submittedData = [
        'jobOrderNumber' => $jobOrderNumber,
        'company' => $company,
        'items' => $items,
        'quantity' => $quantity,
    ];

    // Validate inputs
    $checkCode = "SELECT * FROM order_form WHERE jobOrderNumber = '$jobOrderNumber'";

    if ($conn->query($checkCode)->num_rows > 0) {
        $errorMessage = "Order code already exists.";
        }else {
        // Insert the new filter if validation passes
        $insertQuery = "INSERT INTO order_form (jobOrderNumber, company, items, quantity)
                        VALUES ('$jobOrderNumber', '$company', '$items', '$quantity')";

        if ($conn->query($insertQuery) === TRUE) {
            $errorMessage = "<span id='success'>Order form successfully added!</span>";
            $submittedData = []; // Clear form data on success
        } else {
            $errorMessage = "Error: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="style2.css">
    <link rel="stylesheet" href="select.css">
    <link rel="stylesheet" href="font.css">
    <title>Add Filter</title>
</head>
<body>
    <div class="container" id="addOrderInterface" style="display:block;">
        <h1 class="form-title">Add Order</h1>
        <?php if (!empty($errorMessage)): ?>
            <p class="popup"><?php echo $errorMessage; ?></p>
        <?php endif; ?>

        <form method="post" action="addOrder.php">
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="text" name="jobOrderNumber" id="jobOrderNumber" placeholder="Job Order Number" required value="<?php echo isset($submittedData['jobOrderNumber']) ? $submittedData['jobOrderNumber'] : ''; ?>">
                <label for="jobOrderNumber">Job Order Number</label>
            </div>
            <div class="input-group">
                <i class="fas fa-book"></i>
                <input type="text" name="company" id="company" placeholder="Company" required value="<?php echo isset($submittedData['company']) ? $submittedData['company'] : ''; ?>">
                <label for="company">Company</label>
            </div>
            <div class="input-group">
                <i class="fas fa-book"></i>
                <input type="text" name="items" id="items" placeholder="Items" required value="<?php echo isset($submittedData['items']) ? $submittedData['items'] : ''; ?>">
                <label for="items">Items</label>
            </div>

            <div class="input-group">
                <i class="fas fa-book"></i>
                <input type="text" name="quantity" id="quantity" placeholder="Quantity" required value="<?php echo isset($submittedData['quantity']) ? $submittedData['quantity'] : ''; ?>">
                <label for="quantity">Quantity</label>
            </div>

            <input type="submit" class="btn" value="Submit Filter" name="submitButton">
            
        <form method="post" action="orderFormDashboard.php">
            <input type="submit" class="btn" value="Back to Dashboard">
        </form> 
    </div>
    
</body>
</html>
