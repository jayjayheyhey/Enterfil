<?php 

include 'connect.php';

if(isset($_POST['submitButton'])){
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

    // Check if Filter Code already exists
    $checkCode="SELECT * From filters where FilterCode='$FilterCode'";
     $checkCode2="SELECT * From filters where FilterName='$FilterName'";
     $checkCode3="SELECT * From filters where PartNumber='$PartNumber'";
     $result=$conn->query($checkCode);
     if ($result->num_rows > 0) {
        header("Location: addInterface.php?add=code");
        exit();
    } else {
        $result = $conn->query($checkCode2);
        if ($result->num_rows > 0) {
            header("Location: addInterface.php?add=name");
            exit();
        } else {
            $result = $conn->query($checkCode3);
            if ($result->num_rows > 0) {
                header("Location: addInterface.php?add=part");
                exit();
            } else {
                if ($Quantity > $MaxStock) {
                    header("Location: addInterface.php?add=toolarge");
                    exit();
                } else if ($Quantity < 0 || $Quantity >= 10000 ) {
                    header("Location: addInterface.php?add=toolow");
                    exit();
                } elseif ($MaxStock < 5 || $MaxStock >= 10000 ) {
                    header("Location: addInterface.php?add=maxStock");
                    exit();
                } elseif ($LowStockSignal < 0 || $LowStockSignal >= 10000 ) {
                    header("Location: addInterface.php?add=lowStock");
                    exit();
                } elseif ($Length < 0 || $Length >= 10000) {
                    $errorMessage = "Invalid Length amount.";
                } elseif ($Width < 0 || $Width >= 10000) {
                    $errorMessage = "Invalid Width amount.";
                } elseif ($Height < 0 || $Height >= 10000) {
                    $errorMessage = "Invalid Height amount.";
                }else {
                    $insertQuery = "INSERT INTO filters (FilterCode, PartNumber, FilterName, Length, LengthUnit, Width, WidthUnit, Height, HeightUnit, Quantity, MaxStock, LowStockSignal)
                            VALUES ('$FilterCode', '$PartNumber', '$FilterName', '$Length', '$LengthUnit', '$Width', '$WidthUnit', '$Height', '$HeightUnit', '$Quantity', '$MaxStock', '$LowStockSignal')";
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
