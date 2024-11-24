<?php 

include 'connect.php';

if(isset($_POST['submitButton'])){
    $FilterCode=$_POST['fCode'];
    $FilterName=$_POST['fName'];
    $Materials=$_POST['materials'];
    $Quantity=$_POST['quantity'];
    $MaxStock=$_POST['maxStock'];
    $LowStockSignal=$_POST['lowStock'];


     $checkCode="SELECT * From filters where FilterCode='$FilterCode'";
     $result=$conn->query($checkCode);
     if($result->num_rows>0){
        echo "Filter Code Already Exists !";
     }
     else{
        $insertQuery="INSERT INTO filters(FilterCode,FilterName,Materials,Quantity,MaxStock,LowStockSignal)
                       VALUES ('$FilterCode','$FilterName','$Materials','$Quantity','$MaxStock','$LowStockSignal')";
            if($conn->query($insertQuery)==TRUE){
                header("location: index.php");
            }
            else{
                echo "Error:".$conn->error;
            }
     }
}

?>