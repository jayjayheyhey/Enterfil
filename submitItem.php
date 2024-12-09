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
    $checkCode = "SELECT * FROM filters WHERE FilterCode = '$FilterCode'";
    $result = $conn->query($checkCode);
    if($result->num_rows > 0){
        echo '<script>
                alert("ERROR: Filter Code Already Exists.");
                window.location.href = "addInterface.php";
              </script>';
    } else {
        if ($Quantity > $MaxStock) {
            echo '<script>
                    alert("ERROR: Quantity cannot be larger than the maximum stock.");
                    window.location.href = "addInterface.php";
                  </script>';
        } elseif ($Quantity < 0) {
            echo '<script>
                    alert("ERROR: Quantity cannot be lower than 0.");
                    window.location.href = "addInterface.php";
                  </script>';
        } else {
            // Insert data into the database
            $insertQuery = "INSERT INTO filters (FilterCode, PartNumber, FilterName, Length, LengthUnit, Width, WidthUnit, Height, HeightUnit, Quantity, MaxStock, LowStockSignal)
                            VALUES ('$FilterCode', '$PartNumber', '$FilterName', '$Length', '$LengthUnit', '$Width', '$WidthUnit', '$Height', '$HeightUnit', '$Quantity', '$MaxStock', '$LowStockSignal')";
            if($conn->query($insertQuery) === TRUE){
                echo '<script>
                        alert("Filter successfully added.");
                        window.location.href = "homepage.php";
                      </script>';
                exit();
            } else {
                echo "Error: " . $conn->error;
            }
        }
    }
}
?>
