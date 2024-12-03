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
        echo '<script>
                    alert("ERROR: Filter Code Already Exists.");
                    window.location.href = "addInterface.php";
                </script>';
     } else{
        if ($Quantity > $MaxStock) {
            echo '<script>
                    alert("ERROR: Quantity can not be larger than the maximum stock.");
                    window.location.href = "addInterface.php";
                </script>';
        } elseif ($Quantity < $LowStockSignal) {
            echo '<script>
                    alert("ERROR: Quantity can not be smaller than the low stock indicator.");
                    window.location.href = "addInterface.php";
                </script>';
        } else{
            $insertQuery="INSERT INTO filters(FilterCode,FilterName,Materials,Quantity,MaxStock,LowStockSignal)
                        VALUES ('$FilterCode','$FilterName','$Materials','$Quantity','$MaxStock','$LowStockSignal')";
                if($conn->query($insertQuery)==TRUE){
                    echo '<script>
                            alert("Filter successfully updated");
                            window.location.href = "homepage.php";
                        </script>';
                    exit();
                }   
                else{
                    echo "Error:".$conn->error;
                }
        }
    }
}

?>