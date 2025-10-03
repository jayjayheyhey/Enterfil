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
        <h1 class="form-title" style="font-family: Arial, sans-serif";>Raw Materials System Main Dashboard</h1>
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
        <div class="container2">
            <form method="post" action="generate_pdf.php" target="_blank" class="right-align">
                <i class="fas fa-clipboard"></i>
                <a href="generate_pdf.php" id="downloadLink" class="download-link" style="font-family: Arial, sans-serif";><span class="emphasize">Generate Inventory Report</span></a>
            </form>
            <div class="legend">
                <div class="legend-item">
                    <div class="color-box warning"></div>
                    <span class="status" >Warning</span>
                </div>
                <div class="legend-item">
                    <div class="color-box caution"></div>
                    <span class="status">Caution</span>
                </div>
                <div class="legend-item">
                    <div class="color-box good"></div>
                    <span class="status">Good</span>
                </div>
            </div>
        </div>
        
            <form method ="post" action="orderFormDashboard.php">
                <input type="submit" class="btn" value="Order Forms Dashboard" name="orderFormDashboard">
            </form>
        


        <div id="searchresult"></div>
         <!-- Display Filters Table -->
        <?php
         renderFiltersTable($conn);
         ?>

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