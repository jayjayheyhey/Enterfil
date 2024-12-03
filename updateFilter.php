<?php 
include 'connect.php';

if(isset($_POST['updateButton'])){
    $FilterCode = $_POST['fCode'];
    $FilterName = $_POST['fName'];
    $Materials = $_POST['materials'];
    $Quantity = $_POST['quantity'];
    $MaxStock = $_POST['maxStock'];
    $LowStockSignal = $_POST['lowStock'];


    $updateQuery = "UPDATE filters 
                    SET FilterName = '$FilterName', 
                        Materials = '$Materials', 
                        Quantity = '$Quantity', 
                        MaxStock = '$MaxStock', 
                        LowStockSignal = '$LowStockSignal'
                    WHERE FilterCode = '$FilterCode'";

    if($conn->query($updateQuery) === TRUE){
        echo '<script>
            alert("Filter successfully updated");
            window.location.href = "homepage.php";
          </script>';
        exit();
    } else {
        echo "Error: " . $conn->error; 
    }
}
?>