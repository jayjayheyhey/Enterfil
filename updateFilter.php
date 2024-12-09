<?php 
include 'connect.php';

if(isset($_POST['updateButton'])){
    $FilterCode = $_POST['fCode'];
    $PartNumber = $_POST['pName'];
    $FilterName = $_POST['fName'];
    $Quantity = $_POST['quantity'];
    $MaxStock = $_POST['maxStock'];
    $LowStockSignal = $_POST['lowStock'];

    $Length = $_POST['length'];
    $LengthUnit = $_POST['lengthUnit'];

    $Width = $_POST['width'];
    $WidthUnit = $_POST['widthUnit'];

    $Height = $_POST['height'];
    $HeightUnit = $_POST['heightUnit'];


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
                            PartNumber = '$PartNumber', 
                            Length = '$Length',
                            LengthUnit = '$LengthUnit',
                            Width = '$Width',
                            WidthUnit = '$WidthUnit',
                            Height = '$Height', 
                            HeightUnit = '$HeightUnit', 
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