<?php 
include 'connect.php';

if(isset($_POST['updateButton'])){
    $FilterCode = $_POST['fCode'];
    $PartNumber = $_POST['pName'];
    $FilterName = $_POST['fName'];
    $Materials = $_POST['materials'];
    $Quantity = $_POST['quantity'];
    $MaxStock = $_POST['maxStock'];
    $LowStockSignal = $_POST['lowStock'];

    $checkCode2="SELECT * From filters where FilterName='$FilterName'";
    $checkCode3="SELECT * From filters where PartNumber='$PartNumber'";
    $result = $conn->query($checkCode2);
    if ($result->num_rows > 0) {
        header("Location: editFilter.php?edit=name&fCode=$FilterCode&pName=$PartNumber&quantity=$Quantity&maxStock=$MaxStock&lowStock=$LowStockSignal");
        exit();
    } else {
        $result = $conn->query($checkCode3);
        if ($result->num_rows > 0) {
            header("Location: editFilter.php?edit=part&fCode=$FilterCode&fName=$FilterName&quantity=$Quantity&maxStock=$MaxStock&lowStock=$LowStockSignal");
            exit();
        } else {
            if ($Quantity > $MaxStock) {
                header("Location: editFilter.php?edit=large&fCode=$FilterCode&pName=$PartNumber&fName=$FilterName&maxStock=$MaxStock&lowStock=$LowStockSignal");
                exit();
            } else if ($Quantity < 0 || $Quantity >= 10000 ) {
                header("Location: editFilter.php?edit=low&fCode=$FilterCode&pName=$PartNumber&fName=$FilterName&maxStock=$MaxStock&lowStock=$LowStockSignal");
                exit();
            } elseif ($MaxStock < 5 || $MaxStock >= 10000 ) {
                header("Location: editFilter.php?edit=maxstock&fCode=$FilterCode&pName=$PartNumber&fName=$FilterName&quantity=$Quantity&lowStock=$LowStockSignal");
                exit();
            } elseif ($LowStockSignal < 0 || $LowStockSignal >= 10000 ) {
                header("Location: editFilter.php?edit=lowsignal&fCode=$FilterCode&pName=$PartNumber&fName=$FilterName&quantity=$Quantity&maxStock=$MaxStock");
                exit();
            } else {
                $updateQuery = "UPDATE filters 
                                SET FilterName = '$FilterName', 
                                    PartNumber = '$PartNumber', 
                                    Materials = '$Materials', 
                                    Quantity = '$Quantity', 
                                    MaxStock = '$MaxStock', 
                                    LowStockSignal = '$LowStockSignal'
                                WHERE FilterCode = '$FilterCode'";
        
                if($conn->query($updateQuery) === TRUE){
                    header("Location: searchFilterInterface.php?add=Success");
                    exit();
                } else {
                    echo "Error: " . $conn->error; 
                }
            }
        }
    }
}
?>