<?php
session_start();
include("connect.php");

$query = "SELECT jobOrderNumber, quantity, requiredDate FROM order_form";
$result = mysqli_query($conn, $query);
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
    <title>Order Form Dashboard</title>

    
</head>
<body>
    <div class="container" id="dashboard">
        <h1 class="form-title">Order Form Dashboard</h1>
        <form method="post" action="addOrder.php">
            <input type="submit" class="btn" value="Add Order" name="addOrderButton">
        </form>

        <table class="styled-table">
            <thead>
                <tr>
                    <th></th>
                    <th>Job Order Number</th>
                    <th>Quantity</th>
                    <th>Required Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><a href="orderDetails.php?jobOrderNumber=<?= $row['jobOrderNumber'] ?>">View</a></td>
                        <td><?= $row['jobOrderNumber'] ?></td>
                        <td><?= $row['quantity'] ?></td>
                        <td><?= $row['requiredDate'] ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>