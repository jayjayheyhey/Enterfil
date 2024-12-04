<?php 
include 'connect.php';

if(isset($_POST['updateButton'])){
    $FilterCode = $_POST['fCode'];
    $FilterName = $_POST['fName'];
    $Materials = $_POST['materials'];
    $Quantity = $_POST['quantity'];
    $MaxStock = $_POST['maxStock'];
    $LowStockSignal = $_POST['lowStock'];

    if ($Quantity > $MaxStock) {
        echo '<script>
                alert("ERROR: Quantity can not be larger than the maximum stock.");
                window.location.href = "addInterface.php";
            </script>';
    }elseif ($Quantity < 0) {
        echo '<script>
                alert("ERROR: Quantity can not be lower than 0.");
                window.location.href = "addInterface.php";
            </script>';
    }else {
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
}
?>