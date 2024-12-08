<?php 

include 'connect.php';

$validFilterCodes = [
    "PTTVSPE", "PTTVPFE", "PSG-334/2", "DD120", "PD120",
    "4042010104", "4042010080", "G04260", "53214057", "53100002",
    "PARKER 9378590", "LG4-904-NH", "D-PPPB-2-A", "680-0600-A000", "FILT-EINS 3 PN:10.07.01.00060"
];

if (!in_array($FilterCode, $validFilterCodes)) {
    echo '<script>
            alert("ERROR: Invalid Filter Code.");
            window.location.href = "addInterface.php";
          </script>';
    exit();
}


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
        } elseif ($Quantity < 0) {
            echo '<script>
                    alert("ERROR: Quantity can not be lower than 0.");
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