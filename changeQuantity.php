<?php
session_start();
include('connect.php');
include("filters_table.php");


if(isset($_GET['error']) && $_GET['error'] == 1) {
    echo '<script>alert("ERROR: The resulting quantity cannot be less than 0.");</script>';
} else if(isset($_GET['error']) && $_GET['error'] == 2) {
    echo '<script>alert("ERROR: The resulting quantity cannot exceed the maximum stock.");</script>';
} 

// Initialize variables
$FilterCode = '';
$currentQuantity = 0;
$maxStock = 0;

if (isset($_GET['FilterCode']) && !empty($_GET['FilterCode'])) {
    $FilterCode = $_GET['FilterCode'];

    // Fetch current quantity and max stock
    $stmt = $conn->prepare("SELECT Quantity, MaxStock FROM filters WHERE FilterCode = ?");
    $stmt->bind_param("s", $FilterCode);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $currentQuantity = $row['Quantity'];
        $maxStock = $row['MaxStock'];
    } else {
        echo "Filter Code not found.";
        exit();
    }
}

// Process form submission for quantity update
if (isset($_POST['submitQuantityButton'])) {
    // Ensure FilterCode is passed
    if (empty($_GET['FilterCode'])) {
        echo "Invalid request. Filter Code is missing.";
        exit();
    }

    // Get the values from the form
    $quantityChangeAdd = isset($_POST['quantityAdd']) ? (int)$_POST['quantityAdd'] : 0;
    $quantityChangeSubtract = isset($_POST['quantitySubtract']) ? (int)$_POST['quantitySubtract'] : 0;

    // Calculate the new quantity
    $newQuantity = $currentQuantity + $quantityChangeAdd - $quantityChangeSubtract;

    // Validate the new quantity
    if ($newQuantity < 0) {
        header("Location: changeQuantity.php?error=1");
        exit();
    }
    if ($newQuantity > $maxStock) {
        header("Location: changeQuantity.php?error=2");
        exit();
    }

    // Update the database
    $stmt = $conn->prepare("UPDATE filters SET Quantity = ? WHERE FilterCode = ?");
    $stmt->bind_param("is", $newQuantity, $FilterCode);

    if ($stmt->execute()) {
        echo "Quantity updated successfully!";
        header("Location: homepage.php"); // Redirect to dashboard
        exit();
    } else {
        echo "Error updating quantity: " . $conn->error;
        exit();
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
    <link rel="stylesheet" href="tablestyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <div class="ShowTableContainer" id="enterFilterCode" style="<?php echo empty($FilterCode) ? 'display:block;' : 'display:none;'; ?>">
        <h1 class="form-title">Edit Quantity</h1>
        <form method="get" action="changeQuantity.php">
            <div class="input-group">
                <i class="fas fa-clipboard"></i>
                <input type="text" name="FilterCode" id="FilterCode" placeholder="Filter Code" required>
                <label for="FilterCode">Filter Code</label>
            </div>
            <input type="submit" class="btn" value="Submit Filter Code" name="submitFilterCode">
        </form>
        <form method="post" action="homepage.php">
            <input type="submit" class="btn" value="Back to Dashboard">
        </form>
        <!--Display Filters Table-->
        <?php
            renderFiltersTable($conn);
        ?>
    </div>

    <div class="container" id="updateQuantity" style="<?php echo !empty($FilterCode) ? 'display:block;' : 'display:none;'; ?>">
        <h1 class="form-title">Change Quantity</h1>
        <p><strong>Code:</strong> <?php echo htmlspecialchars($FilterCode); ?></p>
        <p><strong>Current Quantity:</strong> <?php echo htmlspecialchars($currentQuantity); ?></p>
        <p><strong>Max Stock:</strong> <?php echo htmlspecialchars($maxStock); ?></p><br>

        <form method="post" action="changeQuantity.php?FilterCode=<?php echo urlencode($FilterCode); ?>">
            <div class="input-group">
                <i class="fas fa-calculator"></i>
                <input type="number" name="quantityAdd" id="quantityAdd" placeholder="Add Quantity" min="0">
                <label for="quantityAdd">Add Quantity</label>
            </div>
            <div class="input-group">
                <i class="fas fa-calculator"></i>
                <input type="number" name="quantitySubtract" id="quantitySubtract" placeholder="Subtract Quantity" min="0">
                <label for="quantitySubtract">Subtract Quantity</label>
            </div>
            <input type="submit" class="btn" value="Submit Quantity Change" name="submitQuantityButton">
        </form>
    </div>
</body>
</html>