<?php
session_start();
include("connect.php");

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <title>Homepage</title>
</head>
<body>
    <div class="container" id=dashboard>
        <h1 class="form-title">Raw Materials Module Main Dashboard</h1>
        <form method ="post" action="addInterface.php">
            <input type="submit" class="btn" value="Add Item" name="addItemButton">
        </form>
        <form method ="post" action="changeQuantity.php">
            <input type="submit" class="btn" value="Add/Subtract Quantity" name="editQuantityButton">
        </form>
        <form method="post" action="removeItem.php">
            <input type="submit" class="btn" value="Remove Filter" name="removeFilterButton">
        </form>
        <form method ="post" action="searchFilterInterface.php">
            <input type="submit" class="btn" value="Edit Filter" name="editFitlterButton">
        </form>
    </div>
</body>
</html>