<?php
include 'connect.php';

$errorMessage = '';

if (isset($_POST['updateButton'])) {
    $FilterCode = $_POST['fCode'];
    $PartNumber = $_POST['pName'];
    $FilterName = $_POST['fName'];
    $Materials = $_POST['materials'];
    $Quantity = $_POST['quantity'];
    $MaxStock = $_POST['maxStock'];
    $LowStockSignal = $_POST['lowStock'];

    // Validate filter name and part number
    $checkCode2 = "SELECT * FROM filters WHERE FilterName='$FilterName'";
    $checkCode3 = "SELECT * FROM filters WHERE PartNumber='$PartNumber'";
    
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
        } else {
            // If no errors, perform the update
            $updateQuery = "UPDATE filters 
                            SET FilterName = '$FilterName', 
                                PartNumber = '$PartNumber', 
                                Materials = '$Materials', 
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
        $Materials = $row['Materials'];
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

            <div class="input-group">
                <textarea id="materials" name="materials" placeholder="Materials" rows="4" cols="49"><?php echo isset($Materials) ? $Materials : ''; ?></textarea>
                <label for="materials">Materials</label>
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