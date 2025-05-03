<?php
session_start();
include("connect.php");
include("filters_table.php");

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="search.css">
    <link rel="stylesheet" href="border.css">
    <link rel="stylesheet" href="tablestyle2.css">
    <title>Homepage</title>

    
</head>
<body>
    <div class="container" id=dashboard>
        <h1 class="form-title" style="font-family: Arial, sans-serif";>Order Form Dashboard</h1>
        <form method ="post" action="addOrder.php">
            <input type="submit" class="btn" value="Add Order" name="addOrderButton">
        </form>
    </div>

</body>
</html>