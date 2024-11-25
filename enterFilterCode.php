<?php
session_start();
include('connect.php');

if (isset($_POST['submitFilterCode'])) {
    $FilterCode = $_POST['FilterCode'];
    // Check if the FilterCode exists in the database
    $sql = "SELECT * FROM filters WHERE FilterCode='$FilterCode'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        // Redirect to the next page with the FilterCode to update quantity
        header("Location: changeQuantity.php?FilterCode=$FilterCode");
        exit();
    } else {
        echo "Filter Code not found!";
    }
}
?>
<!--
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enter Filter Code</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container" id="enterFilterCode">
        <h1 class="form-title">Enter Filter Code</h1>
        <form method="post" action="enterFilterCode.php">
            <div class="input-group">
                <i class="fas fa-clipboard"></i>
                <input type="text" name="FilterCode" id="FilterCode" placeholder="Filter Code" required>
                <label for="FilterCode">Filter Code </label>
            </div>
            <input type="submit" class="btn" value="Submit Filter Code" name="submitFilterCode">
        </form>
    </div>
</body>
</html>
