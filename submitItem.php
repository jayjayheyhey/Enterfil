<?php 

include 'connect.php';

if(isset($_POST['submitButton'])){
    $FilterCode=$_POST['fCode'];
    $PartNumber=$_POST['pName'];
    $FilterName=$_POST['fName'];
    $Materials=$_POST['materials'];
    $Quantity=$_POST['quantity'];
    $MaxStock=$_POST['maxStock'];
    $LowStockSignal=$_POST['lowStock'];


     $checkCode="SELECT * From filters where FilterCode='$FilterCode'";
     $checkCode2="SELECT * From filters where FilterName='$FilterName'";
     $checkCode3="SELECT * From filters where PartNumber='$PartNumber'";
     $result=$conn->query($checkCode);
     if ($result->num_rows > 0) {
        header("Location: addInterface.php?code=used");
        exit();
    } else {
        $result = $conn->query($checkCode2);
        if ($result->num_rows > 0) {
            header("Location: addInterface.php?name=used");
            exit();
        } else {
            $result = $conn->query($checkCode3);
            if ($result->num_rows > 0) {
                header("Location: addInterface.php?part=used");
                exit();
            } else {
                if ($Quantity > $MaxStock) {
                    header("Location: addInterface.php?stock=toolarge");
                    exit();
                } else if ($Quantity < 0 || $Quantity >= 10000 ) {
                    header("Location: addInterface.php?stock=toolow");
                    exit();
                } elseif ($MaxStock < 5 || $MaxStock >= 10000 ) {
                    header("Location: addInterface.php?maxStock=toolow");
                    exit();
                } elseif ($LowStockSignal < 0 || $LowStockSignal >= 10000 ) {
                    header("Location: addInterface.php?signal=toolow");
                    exit();
                } else {
                    $insertQuery = "INSERT INTO filters(FilterCode, PartNumber, FilterName, Materials, Quantity, MaxStock, LowStockSignal)
                                    VALUES ('$FilterCode', '$PartNumber', '$FilterName', '$Materials', '$Quantity', '$MaxStock', '$LowStockSignal')";
                    if ($conn->query($insertQuery) == TRUE) {
                        header("Location: addInterface.php?add=Success");
                        exit();
                    } else {
                        echo "Error: " . $conn->error;
                    }
                }
            }
        }
    }
    
}

?>

