<?php
include("connect.php");

$errorMessage = "";
$submittedData = []; // To hold user-submitted data

if (isset($_POST['submitButton'])) {
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

    // Store submitted data so we can re-populate the form if needed
    $submittedData = [
        'fCode' => $FilterCode,
        'pName' => $PartNumber,
        'fName' => $FilterName,
        'quantity' => $Quantity,
        'maxStock' => $MaxStock,
        'lowStock' => $LowStockSignal,
        'length' => $Length,
        'lengthUnit' => $LengthUnit,
        'width' => $Width,
        'widthUnit' => $WidthUnit,
        'height' => $Height,
        'heightUnit' => $HeightUnit,
    ];

    // Validate inputs
    $checkCode = "SELECT * FROM filters WHERE FilterCode = '$FilterCode'";
    $checkName = "SELECT * FROM filters WHERE FilterName = '$FilterName'";
    $checkPart = "SELECT * FROM filters WHERE PartNumber = '$PartNumber'";

    if ($conn->query($checkCode)->num_rows > 0) {
        $errorMessage = "Filter code already exists.";
    } elseif ($conn->query($checkName)->num_rows > 0) {
        $errorMessage = "Filter name already exists.";
    } elseif ($conn->query($checkPart)->num_rows > 0) {
        $errorMessage = "Part number already exists.";
    } elseif ($Quantity > $MaxStock) {
        $errorMessage = "Quantity cannot be larger than the maximum stock.";
    } elseif ($Quantity < 0) {
        $errorMessage = "Quantity cannot be negative.";
    } elseif ($MaxStock < 5) {
        $errorMessage = "Maximum Stock must be at least 5.";
    } elseif ($LowStockSignal < 0) {
        $errorMessage = "Low Stock Signal cannot be negative.";
    } else {
        // Insert the new filter if validation passes
        $insertQuery = "INSERT INTO filters (FilterCode, PartNumber, FilterName, Length, LengthUnit, Width, WidthUnit, Height, HeightUnit, Quantity, MaxStock, LowStockSignal)
                        VALUES ('$FilterCode', '$PartNumber', '$FilterName', '$Length', '$LengthUnit', '$Width', '$WidthUnit', '$Height', '$HeightUnit', '$Quantity', '$MaxStock', '$LowStockSignal')";

        if ($conn->query($insertQuery) === TRUE) {
            $errorMessage = "<span id='success'>Filter successfully added!</span>";
            $submittedData = []; // Clear form data on success
        } else {
            $errorMessage = "Error: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="style2.css">
    <link rel="stylesheet" href="select.css">
    <title>Add Filter</title>
</head>
<body>
    <div class="container" id="addInterface" style="display:block;">
        <h1 class="form-title">Add Filter</h1>
        <?php if (!empty($errorMessage)): ?>
            <p class="popup"><?php echo $errorMessage; ?></p>
        <?php endif; ?>

        <form method="post" action="addInterface.php">
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="text" name="fCode" id="fCode" placeholder="Filter Code" required value="<?php echo isset($submittedData['fCode']) ? $submittedData['fCode'] : ''; ?>">
                <label for="fCode">Filter Code</label>
            </div>
            <div class="input-group">
                <i class="fas fa-book"></i>
                <input type="text" name="pName" id="pName" placeholder="Part Number" required value="<?php echo isset($submittedData['pName']) ? $submittedData['pName'] : ''; ?>">
                <label for="pName">Part Number</label>
            </div>
            <div class="input-group">
                <i class="fas fa-book"></i>
                <input type="text" name="fName" id="fName" placeholder="Filter Name" required value="<?php echo isset($submittedData['fName']) ? $submittedData['fName'] : ''; ?>">
                <label for="fName">Filter Name</label>
            </div>

            <div class="input-group">
                <i class="fas fa-ruler"></i>
                <input type="number" name="length" id="length" placeholder="Length" required step="0.01" value="<?php echo isset($submittedData['length']) ? $submittedData['length'] : ''; ?>">
                <label for="length">Length</label>
                <select name="lengthUnit" id="lengthUnit" required>
                    <option value="cm" <?php echo isset($submittedData['lengthUnit']) && $submittedData['lengthUnit'] == 'cm' ? 'selected' : ''; ?>>cm</option>
                    <option value="in" <?php echo isset($submittedData['lengthUnit']) && $submittedData['lengthUnit'] == 'in' ? 'selected' : ''; ?>>in</option>
                    <option value="mm" <?php echo isset($submittedData['lengthUnit']) && $submittedData['lengthUnit'] == 'mm' ? 'selected' : ''; ?>>mm</option>
                    <option value="ft" <?php echo isset($submittedData['lengthUnit']) && $submittedData['lengthUnit'] == 'ft' ? 'selected' : ''; ?>>ft</option>
                </select>
            </div>

            <!-- Width -->
            <div class="input-group">
                <i class="fas fa-ruler"></i>
                <input type="number" name="width" id="width" placeholder="Width" required step="0.01" value="<?php echo isset($submittedData['width']) ? $submittedData['width'] : ''; ?>">
                <label for="width">Width</label>
                <select name="widthUnit" id="widthUnit" required>
                    <option value="cm" <?php echo isset($submittedData['widthUnit']) && $submittedData['widthUnit'] == 'cm' ? 'selected' : ''; ?>>cm</option>
                    <option value="in" <?php echo isset($submittedData['widthUnit']) && $submittedData['widthUnit'] == 'in' ? 'selected' : ''; ?>>in</option>
                    <option value="mm" <?php echo isset($submittedData['widthUnit']) && $submittedData['widthUnit'] == 'mm' ? 'selected' : ''; ?>>mm</option>
                    <option value="ft" <?php echo isset($submittedData['widthUnit']) && $submittedData['widthUnit'] == 'ft' ? 'selected' : ''; ?>>ft</option>
                </select>
            </div>

            <!-- Height -->
            <div class="input-group">
                <i class="fas fa-ruler"></i>
                <input type="number" name="height" id="height" placeholder="Height" required step="0.01" value="<?php echo isset($submittedData['height']) ? $submittedData['height'] : ''; ?>">
                <label for="height">Height</label>
                <select name="heightUnit" id="heightUnit" required>
                    <option value="cm" <?php echo isset($submittedData['heightUnit']) && $submittedData['heightUnit'] == 'cm' ? 'selected' : ''; ?>>cm</option>
                    <option value="in" <?php echo isset($submittedData['heightUnit']) && $submittedData['heightUnit'] == 'in' ? 'selected' : ''; ?>>in</option>
                    <option value="mm" <?php echo isset($submittedData['heightUnit']) && $submittedData['heightUnit'] == 'mm' ? 'selected' : ''; ?>>mm</option>
                    <option value="ft" <?php echo isset($submittedData['heightUnit']) && $submittedData['heightUnit'] == 'ft' ? 'selected' : ''; ?>>ft</option>
                </select>
            </div>

            <!-- Quantity -->
            <div class="input-group">
                <i class="fas fa-cubes"></i>
                <input type="number" name="quantity" id="quantity" placeholder="Quantity" required value="<?php echo isset($submittedData['quantity']) ? $submittedData['quantity'] : ''; ?>">
                <label for="quantity">Quantity</label>
            </div>

            <!-- Max Stock -->
            <div class="input-group">
                <i class="fas fa-boxes"></i>
                <input type="number" name="maxStock" id="maxStock" placeholder="Max Stock" required value="<?php echo isset($submittedData['maxStock']) ? $submittedData['maxStock'] : ''; ?>">
                <label for="maxStock">Max Stock</label>
            </div>

            <!-- Low Stock Signal -->
            <div class="input-group">
                <i class="fas fa-exclamation-triangle"></i>
                <input type="number" name="lowStock" id="lowStock" placeholder="Low Stock Signal" required value="<?php echo isset($submittedData['lowStock']) ? $submittedData['lowStock'] : ''; ?>">
                <label for="lowStock">Low Stock Signal</label>
            </div>


            <input type="submit" class="btn" value="Submit Filter" name="submitButton">
        </form>

        <form method="post" action="homepage.php">
            <input type="submit" class="btn" value="Back to Dashboard">
        </form> 
    </div>
</body>
</html>
