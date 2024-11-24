<?php
session_start();
include('connect.php');

// Check if FilterCode is passed in the URL
if (isset($_GET['FilterCode'])) {
    $FilterCode = $_GET['FilterCode'];

    // Query to get the current quantity for the given FilterCode
    $sql = "SELECT * FROM filters WHERE FilterCode='$FilterCode'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $currentQuantity = $row['Quantity'];
    } else {
        echo "Filter Code not found.";
        exit();
    }
}

// Process form submission when quantity is updated
if (isset($_POST['submitQuantityButton'])) {
    $quantityChangeAdd = $_POST['quantityAdd']; // Quantity to add
    $quantityChangeSubtract = $_POST['quantitySubtract']; // Quantity to subtract

// Validate input for adding or subtracting
if ($quantityChangeAdd || $quantityChangeSubtract) {
    $newQuantity = $currentQuantity + $quantityChangeAdd - $quantityChangeSubtract;
    
    // Ensure the new quantity is not negative
    if ($newQuantity < 0) {
        echo "The resulting quantity cannot be negative.";
        exit();
    }
} else {
    echo "Please enter values for 'Add' and/or 'Subtract' quantities.";
    exit();
}


    // Update the database with the new quantity
    $updateQuery = "UPDATE filters SET Quantity='$newQuantity' WHERE FilterCode='$FilterCode'";
    if ($conn->query($updateQuery) === TRUE) {
        echo "Quantity updated successfully!";
        header("Location: homepage.php");  // Redirect to the dashboard or wherever you want
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Quantity</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <div class="container" id="enterFilterCode" style="display:block;">    
        <h1 class="form-title">Enter Filter Code</h1>
        <form method="post">
            <div class="input-group">
                <i class="fas fa-clipboard"></i>
                <input type="text" name="FilterCode" id="FilterCode" placeholder="Filter Code" required>
                <label for="FilterCode">Filter Code </label>
            </div>
            <input type="submit" class="btn" value="Submit Filter Code" name="submitFilterCodeButton    ">
        </form>
    </div>

    <div class="container" id=updateQuantity style="display:none;">
        <h1 class="form-title">Change Quantity</h1>
        <p><strong>Code:</strong> <?php echo $FilterCode; ?></p>
        <p><strong>Current Quantity:</strong> <?php echo $currentQuantity; ?></p><br>

        <form method="post" action="changeQuantity.php?FilterCode=<?php echo $FilterCode; ?>">
            <div class="input-group">
                <i class="fas fa-calculator"></i>
                <input type="number" name="quantityAdd" id="quantityAdd" placeholder="Add Quantity" required>
                <label for="quantityAdd">Add Quantity</label>
            </div>
            <div class="input-group">
                <i class="fas fa-calculator"></i>
                <input type="number" name="quantitySubtract" id="quantitySubtract" placeholder="Subtract Quantity" required>
                <label for="quantitySubtract">Subtract Quantity</label>
            </div>
            <input type="submit" class="btn" value="Submit Quantity Change" name="submitQuantityButton">
        </form>
    </div>
    <script src="script.js"></script>
</body>
</html>
