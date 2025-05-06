<?php
include("connect.php");

$jobOrderNumber = $_GET['jobOrderNumber'] ?? null;
$errorMessage = "";
$submittedData = [];

if (!$jobOrderNumber) {
    die("Job Order Number not specified.");
}

// Fetch existing data
$stmt = $conn->prepare("SELECT * FROM order_form WHERE jobOrderNumber = ?");
$stmt->bind_param("s", $jobOrderNumber);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    die("Job Order not found.");
}

$data = $result->fetch_assoc();
$submittedData = $data;

// Handle update submission
if (isset($_POST['updateButton'])) {
    $company = $_POST['company'];
    $items = $_POST['items'];
    $quantity = $_POST['quantity'];
    $requiredDate = $_POST['requiredDate'];
    $cap = $_POST['cap'];
    $size = $_POST['size'];
    $gasket = $_POST['gasket'];
    $oring = $_POST['oring'];
    $filterMedia = $_POST['filterMedia'];
    $insideSupport = $_POST['insideSupport'];
    $outsideSupport = $_POST['outsideSupport'];
    $brand = $_POST['brand'];
    $price = $_POST['price'];

    // Check if a new image is uploaded
    $updateImage = isset($_FILES['filterDrawing']) && $_FILES['filterDrawing']['size'] > 0;
    if ($updateImage) {
        $filterDrawing = file_get_contents($_FILES['filterDrawing']['tmp_name']);
        $stmt = $conn->prepare("UPDATE order_form SET company=?, items=?, quantity=?, requiredDate=?, cap=?, size=?, gasket=?, oring=?, filterMedia=?, insideSupport=?, outsideSupport=?, brand=?, price=?, filterDrawing=? WHERE jobOrderNumber=?");
        $stmt->bind_param("ssisssssssssdbs", $company, $items, $quantity, $requiredDate, $cap, $size, $gasket, $oring, $filterMedia, $insideSupport, $outsideSupport, $brand, $price, $filterDrawing, $jobOrderNumber);
    } else {
        $stmt = $conn->prepare("UPDATE order_form SET company=?, items=?, quantity=?, requiredDate=?, cap=?, size=?, gasket=?, oring=?, filterMedia=?, insideSupport=?, outsideSupport=?, brand=?, price=? WHERE jobOrderNumber=?");
        $stmt->bind_param("ssisssssssssds", $company, $items, $quantity, $requiredDate, $cap, $size, $gasket, $oring, $filterMedia, $insideSupport, $outsideSupport, $brand, $price, $jobOrderNumber);
    }

    if ($stmt->execute()) {
        $errorMessage = "<span id='success'>Order successfully updated!</span>";
        // Refresh data
        $stmt = $conn->prepare("SELECT * FROM order_form WHERE jobOrderNumber = ?");
        $stmt->bind_param("s", $jobOrderNumber);
        $stmt->execute();
        $result = $stmt->get_result();
        $submittedData = $result->fetch_assoc();
    } else {
        $errorMessage = "Error updating record: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="addOrderInterface2.css">
    <title>Edit Order Form</title>
</head>
<body>
    <div class="container" id="editOrderInterface">
        <a href="orderFormDashboard.php" class="back-btn" title="Back">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="form-title">Edit Order: <?= htmlspecialchars($jobOrderNumber) ?></h1>
        <?php if (!empty($errorMessage)): ?>
            <p class="popup"><?= $errorMessage ?></p>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data">
            <div class="form-row">
                <div class="form-col">
                    <div class="input-group">
                        <i class="fas fa-building"></i>
                        <input type="text" name="company" id="company" placeholder="Company" required value="<?php echo isset($submittedData['company']) ? $submittedData['company'] : ''; ?>">
                        <label for="company">Company</label>
                    </div>
                    <div class="input-group">
                        <i class="fas fa-boxes"></i>
                        <input type="text" name="items" id="items" placeholder="Items" required value="<?php echo isset($submittedData['items']) ? $submittedData['items'] : ''; ?>">
                        <label for="items">Items</label>
                    </div>
                </div>

                <div class="form-col">
                    <div class="input-group">
                        <i class="fas fa-sort-numeric-up"></i>
                        <input type="number" name="quantity" id="quantity" placeholder="Quantity" required value="<?php echo isset($submittedData['quantity']) ? $submittedData['quantity'] : ''; ?>">
                        <label for="quantity">Quantity</label>
                    </div>
                    <div class="input-group">
                        <i class="fas fa-calendar-alt"></i>
                        <input type="date" name="requiredDate" id="requiredDate" placeholder="Required Date" required value="<?php echo isset($submittedData['requiredDate']) ? $submittedData['requiredDate'] : ''; ?>">
                        <label for="requiredDate">Required Date</label>
                    </div>
                </div>
            </div>

            <div class="divider"></div>
            <div class="section-title">Order Information</div>

            <div class="form-row">
                <div class="form-col">
                    <!-- Cap input with UOM dropdown -->
                    <div class="input-with-dropdown">
                        <div class="input-group">
                            <i class="fas fa-cog"></i>
                            <input type="text" name="cap" id="cap" placeholder="Cap" required value="<?php echo isset($submittedData['cap']) ? $submittedData['cap'] : ''; ?>">
                            <label for="cap">Cap</label>
                        </div>
                        <select name="capUOM" id="capUOM">
                            <option value="mm" <?= (isset($submittedData['capUOM']) && $submittedData['capUOM'] == 'mm') ? 'selected' : ''; ?>>mm</option>
                            <option value="cm" <?= (isset($submittedData['capUOM']) && $submittedData['capUOM'] == 'cm') ? 'selected' : ''; ?>>cm</option>
                            <option value="in" <?= (isset($submittedData['capUOM']) && $submittedData['capUOM'] == 'in') ? 'selected' : ''; ?>>in</option>
                        </select>
                    </div>

                    <!-- Size input with UOM dropdown -->
                    <div class="input-with-dropdown">
                        <div class="input-group">
                            <i class="fas fa-ruler"></i>
                            <input type="text" name="size" id="size" placeholder="Size" required value="<?php echo isset($submittedData['size']) ? $submittedData['size'] : ''; ?>">
                            <label for="size">Size</label>
                        </div>
                        <select name="sizeUOM" id="sizeUOM">
                            <option value="mm" <?= (isset($submittedData['sizeUOM']) && $submittedData['sizeUOM'] == 'mm') ? 'selected' : ''; ?>>mm</option>
                            <option value="cm" <?= (isset($submittedData['sizeUOM']) && $submittedData['sizeUOM'] == 'cm') ? 'selected' : ''; ?>>cm</option>
                            <option value="in" <?= (isset($submittedData['sizeUOM']) && $submittedData['sizeUOM'] == 'in') ? 'selected' : ''; ?>>in</option>
                        </select>
                    </div>

                    <!-- Gasket input with UOM dropdown -->
                    <div class="input-with-dropdown">
                        <div class="input-group">
                            <i class="fas fa-dot-circle"></i>
                            <input type="text" name="gasket" id="gasket" placeholder="Gasket" required value="<?php echo isset($submittedData['gasket']) ? $submittedData['gasket'] : ''; ?>">
                            <label for="gasket">Gasket</label>
                        </div>
                        <select name="gasketUOM" id="gasketUOM">
                            <option value="mm" <?= (isset($submittedData['gasketUOM']) && $submittedData['gasketUOM'] == 'mm') ? 'selected' : ''; ?>>mm</option>
                            <option value="cm" <?= (isset($submittedData['gasketUOM']) && $submittedData['gasketUOM'] == 'cm') ? 'selected' : ''; ?>>cm</option>
                            <option value="in" <?= (isset($submittedData['gasketUOM']) && $submittedData['gasketUOM'] == 'in') ? 'selected' : ''; ?>>in</option>
                        </select>
                    </div>

                    <!-- O-Ring input with UOM dropdown -->
                    <div class="input-with-dropdown">
                        <div class="input-group">
                            <i class="fas fa-ring"></i>
                            <input type="text" name="oring" id="oring" placeholder="O-Ring" required value="<?php echo isset($submittedData['oring']) ? $submittedData['oring'] : ''; ?>">
                            <label for="oring">O-Ring</label>
                        </div>
                        <select name="oringUOM" id="oringUOM">
                            <option value="mm" <?= (isset($submittedData['oringUOM']) && $submittedData['oringUOM'] == 'mm') ? 'selected' : ''; ?>>mm</option>
                            <option value="cm" <?= (isset($submittedData['oringUOM']) && $submittedData['oringUOM'] == 'cm') ? 'selected' : ''; ?>>cm</option>
                            <option value="in" <?= (isset($submittedData['oringUOM']) && $submittedData['oringUOM'] == 'in') ? 'selected' : ''; ?>>in</option>
                        </select>
                    </div>
                </div>

                <div class="form-col">
                    <!-- Other inputs for filter media, inside support, outside support, etc. can go here -->
                    <!-- Example for filter media -->
                    <div class="input-group">
                        <i class="fas fa-palette"></i>
                        <input type="text" name="filterMedia" id="filterMedia" placeholder="Filter Media" required value="<?php echo isset($submittedData['filterMedia']) ? $submittedData['filterMedia'] : ''; ?>">
                        <label for="filterMedia">Filter Media</label>
                    </div>
                    <div class="input-group">
                        <i class="fas fa-arrow-down"></i>
                        <input type="text" name="insideSupport" id="insideSupport" placeholder="Inside Support" required value="<?php echo isset($submittedData['insideSupport']) ? $submittedData['insideSupport']: ''; ?>">
                        <label for ="insideSupport">Inside Support</label>
                    </div>
                    <div class="input-group">
                        <i class="fas fa-arrow-up"></i>
                        <input type="text" name="outsideSupport" id="outsideSupport"placeholder="Outside Support" required value="<?php echo isset($submittedData['outsideSupport']) ? $submittedData['outsideSupport']: ''; ?>">
                        <label for ="outsideSupport">Ouside Support</label>
                    </div>

                    <div class="input-group">
                        <i class="fas fa-tag"></i>
                        <input type="text" name="brand" id="brand" placeholder="Brand" required value="<?php echo isset($submittedData['brand']) ? $submittedData['brand']: ''; ?>">
                        <label for ="brand">Brand</label>
                    </div>
                </div>
                
                
            </div>
            <div class="bottom-row">
                    <div class="input-group">
                        <i class="fas fa-money-bill"></i>
                        <input type="number" step="0.01" name="price" id="price" placeholder="Price (₱)" required value="<?php echo isset($submittedData['price']) ? $submittedData['price']: ''; ?>">
                        <label for ="price">Price</label>
                    </div>
                    
                    
                    <button type="submit" class="done-btn" name="updateButton">Update Order</button>
            </div>
        </form>
    </div>
</body>
</html>
