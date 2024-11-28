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
    <link rel="stylesheet" href="dashboard.css">
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
            <input type="submit" class="btn" value="Remove Item" name="removeFilterButton">
        </form>
        <form method ="post" action="searchFilterInterface.php">
            <input type="submit" class="btn" value="Edit Item" name="editFitlterButton">
        </form>
        <form method ="post">
            <input type="text" class="form-control" id="live_search" autocomplete="off"
                placeholder="Search ... ">
        </form>
        <div id="searchresult"></div>

        
         <!-- Display Filters Table -->
        <div id="filters_table">
            <table>
                <thead>
                    <tr>
                        <th>Filter Code</th>
                        <th>Filter Name</th>
                        <th>Materials</th>
                        <th>Quantity</th>
                        <th>Max Stock</th>
                        <th>Low Stock Signal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetch data from filters table
                    $sql = "SELECT * FROM filters"; // Updated table name
                    $result = $conn->query($sql);

                    if ($result && $result->num_rows > 0) {
                        // Output data of each row
                        while ($row = $result->fetch_assoc()) {
                            // Determine the stock status
                            $quantityClass = 'quantity-high'; // Default to high
                            if ($row['Quantity'] <= $row['LowStockSignal']) {
                                $quantityClass = 'quantity-low';
                            } elseif ($row['Quantity'] < $row['MaxStock'] / 2) {
                                $quantityClass = 'quantity-medium';
                            }

                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['FilterCode'] ?? 'N/A') . "</td>";
                            echo "<td>" . htmlspecialchars($row['FilterName'] ?? 'N/A') . "</td>";
                            echo "<td>" . htmlspecialchars($row['Materials'] ?? 'N/A') . "</td>";
                            echo "<td class='$quantityClass'>" . htmlspecialchars($row['Quantity'] ?? 'N/A') . "</td>";
                            echo "<td>" . htmlspecialchars($row['MaxStock'] ?? 'N/A') . "</td>";
                            echo "<td>" . htmlspecialchars($row['LowStockSignal'] ?? 'N/A') . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6'>No filters found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <script type="text/javascript">
            $(document).ready(function () {

                $("#live_search").on("keyup", function () {
                    var input = $(this).val().trim(); // Get and trim the input value

                    if (input.length > 0) {
                        $.ajax({
                            url: "livesearch.php",
                            method: "POST",
                            data: { input: input },
                            success: function (data) {
                                $("#searchresult").html(data); 
                                $("#searchresult").css("display", "block"); //kapag may data, ipakita yung search result
                                $("#filters_table").hide(); //tago muna yung filters_table
                            }
                        });
                    } else {
                        $("#searchresult").html(""); 
                        $("#searchresult").css("display", "none"); //kapag ala na yung data, tago na yung search result
                        $("#filters_table").show(); //ngayon, yung filters table naman ang ipapakita para mapakita lahat ng filter
                    }
                });
            });
    </script>
</body>
</html>