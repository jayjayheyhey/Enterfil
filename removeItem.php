<?php
session_start();
include("connect.php"); // Include database connection
include("filters_table.php");

$message = ""; // Initialize the message variable
$FilterCode = ""; // Initialize FilterCode variable
$confirmation = false; // To track whether the confirmation step should be shown

// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['FilterCode'])) {
        // Sanitize user input
        $FilterCode = $conn->real_escape_string($_POST['FilterCode']);

        // Check if the filter exists in the database
        $check_sql = "SELECT * FROM filters WHERE FilterCode = '$FilterCode'";
        $check_result = $conn->query($check_sql);

        if ($check_result->num_rows > 0) {
            $row = $check_result->fetch_assoc();
            $FilterName = $row['FilterName']; 
            // If filter exists, proceed to confirmation
            if (isset($_POST['confirmDelete']) && $_POST['confirmDelete'] == 'Yes') {
                // Perform the delete action after confirmation
                $sql = "DELETE FROM filters WHERE FilterCode = '$FilterCode'";
                if ($conn->query($sql) === TRUE) {
                    $message = "Data removed successfully";
                } else {
                    $message = "Error deleting filter: " . $conn->error;
                }
            } else {
                // Show confirmation message if not already confirmed
                $confirmation = true;
            }
        } else {
            // If filter does not exist, show error message
            echo '<script>alert("FILTER NOT FOUND");</script>';
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
    <link rel="stylesheet" href="tablestyle.css">
    <title>Remove Filter</title>
</head>
<body>
    <div class="ShowTableContainer" id="removeItem">
        <h1 class="form-title">Remove Filter</h1>
        
        <!-- Display success/error message -->
        <?php if (!empty($message)) { ?>
            <p class="error-message"><?php echo $message; ?></p>
        <?php } ?>
        
        <!-- Show the confirmation form after FilterCode input -->
        <?php if ($confirmation) { ?>
            <p>Are you sure you want to delete this filter?</p>
            <p><strong>Filter Code:</strong> <?php echo htmlspecialchars($FilterCode); ?></p>
            <p><strong>Filter Name:</strong> <?php echo htmlspecialchars($FilterName); ?></p>
            <form method="post" action="">
                <input type="hidden" name="FilterCode" value="<?php echo htmlspecialchars($FilterCode); ?>">
                <input type="submit" class="btn" value="Yes" name="confirmDelete">
            </form>
            <form method="post" action="homepage.php">
                <input type="hidden" name="FilterCode" value="<?php echo htmlspecialchars($FilterCode); ?>">
                <button type="submit" class="btn">No</button>
            </form>
        <?php } else { ?>
            <!-- Form for entering the FilterCode -->
            <form method="post" action="">
                <input type="text" name="FilterCode" id="FilterCode" placeholder="Filter Code" required>
                <label for="FilterCode">Filter Code</label>
                <input type="submit" class="btn" value="Delete Filter">
            </form>
        <?php } ?>
        <form method="post" action="homepage.php">
            <input type="submit" class="btn" value="Back to Dashboard">
        </form>

        <!--Display Filters Table-->
        <?php
         renderFiltersTable($conn);
         ?>

        <!-- Return to homepage button if filter is not found -->
        <?php if ($message === "Filter not found") { ?>
            <form method="get" action="homepage.php">
                <button type="submit" class="btn">Return to Homepage</button>
            </form>
        <?php } ?>
    </div>
</body>
</html>