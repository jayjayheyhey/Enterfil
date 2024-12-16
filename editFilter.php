<?php
include 'connect.php';

$errorMessage = '';

if (isset($_POST['updateButton'])) {
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

    // Validate filter name and part number
    $checkCode2 = "SELECT * FROM filters WHERE FilterName='$FilterName' AND FilterCode != '$FilterCode'";
    $checkCode3 = "SELECT * FROM filters WHERE PartNumber='$PartNumber' AND FilterCode != '$FilterCode'";
    
    $result = $conn->query($checkCode2);
    if ($result->num_rows > 0) {
        $errorMessage = "Filter name already exists.";
    } else {
        $result = $conn->query($checkCode3);
        if ($result->num_rows > 0) {
            $errorMessage = "Part number already exists.";
        } elseif ($Quantity > $MaxStock) {
            $errorMessage = "Quantity cannot be larger than the maximum stock.";
        } elseif ($Quantity < 0 || $Quantity >= 10000) {
            $errorMessage = "Invalid quantity amount.";
        } elseif ($MaxStock < 5 || $MaxStock >= 10000) {
            $errorMessage = "Invalid maximum stock level.";
        } elseif ($LowStockSignal < 0 || $LowStockSignal >= 10000) {
            $errorMessage = "Invalid low stock signal amount.";
        } elseif ($Length < 0 || $Length >= 10000) {
            $errorMessage = "Invalid Length amount.";
        } elseif ($Width < 0 || $Width >= 10000) {
            $errorMessage = "Invalid Width amount.";
        } elseif ($Height < 0 || $Height >= 10000) {
            $errorMessage = "Invalid Height amount.";
        }else {
            // If no errors, perform the update
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

            if ($conn->query($updateQuery) === TRUE) {
                header("Location: searchFilterInterface.php?add=Success");
                exit();
            } else {
                echo "Error: " . $conn->error;
            }
        }
    }
}

if(isset($_POST['searchButton'])){
    $FilterCode = $_POST['fCode'];

    $query = "SELECT * FROM filters WHERE FilterCode = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $FilterCode);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $PartNumber = $row['PartNumber'];
        $FilterName = $row['FilterName'];
        $Quantity = $row['Quantity'];
        $MaxStock = $row['MaxStock'];
        $LowStockSignal = $row['LowStockSignal'];
    } else {
        header("Location: searchFilterInterface.php?code=0");
        exit();
    }
    $stmt->close();

}
?>

<!-- Your HTML form code remains mostly the same -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="style2.css">
    <link rel="stylesheet" href="select.css">
    <link rel="stylesheet" href="font.css">

    <title>Edit Filter</title>
</head>
<body>
    <div class="container" id="editInterface" style="display:block;">
        <h1 class="form-title">UPDATE Filter</h1>
        <!-- Display the error message -->
        <?php if (!empty($errorMessage)): ?>
            <p class="popup"><?php echo $errorMessage; ?></p>
        <?php endif; ?>

        <form method="post" action="editFilter.php">
            <div class="input-group">
                <input type="text" name="fCode" id="fCode" placeholder="Filter Code" value="<?php echo isset($FilterCode) ? $FilterCode : ''; ?>" disabled>
                <input type="hidden" name="fCode" value="<?php echo isset($FilterCode) ? $FilterCode : ''; ?>">
                <label for="fCode">Filter Code:</label>
            </div>

            <div class="input-group">
                <input type="text" name="pName" id="pName" placeholder="Part Number" required value="<?php echo isset($PartNumber) ? $PartNumber : ''; ?>">
                <label for="pName">Part Number:</label>
            </div>

            <div class="input-group">
                <input type="text" name="fName" id="fName" placeholder="Filter Name" required value="<?php echo isset($FilterName) ? $FilterName : ''; ?>">
                <label for="fName">Filter Name:</label>
            </div>

            <!-- Input Length -->
          <div class="input-group">
              <i class="fas fa-ruler"></i>
              <input type="number" name="length" id="length" placeholder="Length" required step="0.01" value="<?php echo isset($row['Length']) ? $row['Length'] : ''; ?>">
              <label for="length">Length</label>
              <select name="lengthUnit" id="lengthUnit" required value="<?php echo isset($row['LengthUnit']) ? $row['LengthUnit'] : ''; ?>">
                  <option value="cm">cm</option>
                  <option value="in">in</option>
                  <option value="mm">mm</option>
                  <option value="ft">ft</option>
              </select>
          </div>

          <!-- Input Width -->
          <div class="input-group">
              <i class="fas fa-ruler-horizontal"></i>
              <input type="number" name="width" id="length" placeholder="Width" required step="0.01" value="<?php echo isset($row['Width']) ? $row['Width'] : ''; ?>">
              <label for="width">Width</label>
              <select name="widthUnit" id="widthUnit" required value="<?php echo isset($row['WidthUnit']) ? $row['WidthUnit'] : ''; ?>">
                  <option value="cm">cm</option>
                  <option value="in">in</option>
                  <option value="mm">mm</option>
                  <option value="ft">ft</option>
              </select>
          </div>

          <!-- Input Height -->
          <div class="input-group">
              <i class="fas fa-ruler-vertical"></i>
              <input type="number" name="height" id="height" placeholder="Height" required step="0.01" value="<?php echo isset($row['Height']) ? $row['Height'] : ''; ?>">
              <label for="height">Height</label>
              <select name="heightUnit" id="heightUnit" required value="<?php echo isset($row['HeightUnit']) ? $row['HeightUnit'] : ''; ?>">
                  <option value="cm">cm</option>
                  <option value="in">in</option>
                  <option value="mm">mm</option>
                  <option value="ft">ft</option>
              </select>
          </div>

            <div class="input-group">
                <input type="number" name="quantity" id="quantity" placeholder="Quantity" required value="<?php echo isset($Quantity) ? $Quantity : ''; ?>">
                <label for="quantity">Quantity</label>
            </div>

            <div class="input-group">
                <input type="number" name="maxStock" id="maxStock" placeholder="Maximum Stock Level" required value="<?php echo isset($MaxStock) ? $MaxStock : ''; ?>">
                <label for="maxStock">Maximum Stock Level</label>
            </div>

            <div class="input-group">
                <input type="number" name="lowStock" id="lowStock" placeholder="Low Stock Signal" required value="<?php echo isset($LowStockSignal) ? $LowStockSignal : ''; ?>">
                <label for="lowStock">Low Stock Signal</label>
            </div>

            <input type="submit" class="btn" value="Update Filter" name="updateButton">
        </form>
    </div>
</body>
</html>