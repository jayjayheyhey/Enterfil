<?php
include("connect.php");

$errorMessage = "";
$submittedData = [];

if (isset($_POST['submitButton']) || isset($_POST['draftButton'])) {    
    // Get form inputs
    $jobOrderNumber = $_POST['jobOrderNumber'];
    $dateCreated = date("Y-m-d"); // gets the current date
    $company = $_POST['company'];
    $items = $_POST['items'];
    $quantity = $_POST['quantity'];
    $requiredDate = $_POST['requiredDate'];
    
    // Get components and their UOMs
    $cap = $_POST['cap'];
    $capUOM = $_POST['capUOM'];
    
    $size = $_POST['size'];
    $sizeUOM = $_POST['sizeUOM'];
    
    $gasket = $_POST['gasket'];
    $gasketUOM = $_POST['gasketUOM'];
    
    $oring = $_POST['oring'];
    $oringUOM = $_POST['oringUOM'];
    
    $filterMedia = $_POST['filterMedia'];
    $filterMediaUOM = $_POST['filterMediaUOM']; 
    
    $insideSupport = $_POST['insideSupport'];
    $insideSupportUOM = $_POST['insideSupportUOM']; 
    
    $outsideSupport = $_POST['outsideSupport'];
    $outsideSupportUOM = $_POST['outsideSupportUOM']; 
    
    $brand = $_POST['brand'];
    $price = $_POST['price'];

    $status = (isset($_POST['draftButton'])) ? 'draft' : 'active';

    
    // Initialize variables
    $filterDrawing = null;
    $uploadError = false;
    
    // Check if file was uploaded
    if (isset($_FILES['filterDrawing']) && $_FILES['filterDrawing']['error'] === 0) {
        // Debug information
        // echo "<pre>"; print_r($_FILES['filterDrawing']); echo "</pre>";
        
        // Use addslashes() to escape binary data for MySQL
        $tmpName = $_FILES['filterDrawing']['tmp_name'];
        $filterDrawing = file_get_contents($tmpName);
        
        if (!$filterDrawing) {
            $uploadError = true;
            $errorMessage = "Failed to read uploaded file";
        }
    } else {
        // Identify the specific upload error
        $uploadError = true;
        switch($_FILES['filterDrawing']['error']) {
            case UPLOAD_ERR_INI_SIZE:
                $errorMessage = "The uploaded file exceeds the upload_max_filesize directive in php.ini";
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $errorMessage = "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";
                break;
            case UPLOAD_ERR_PARTIAL:
                $errorMessage = "The uploaded file was only partially uploaded";
                break;
            case UPLOAD_ERR_NO_FILE:
                $errorMessage = "No file was uploaded. Please select a sketch file.";
                break;
            default:
                $errorMessage = "Unknown upload error";
                break;
        }
    }

    $submittedData = compact(
        'jobOrderNumber', 'company', 'items', 'quantity', 'requiredDate', 
        'cap', 'capUOM', 'size', 'sizeUOM', 'gasket', 'gasketUOM', 'oring', 'oringUOM',
        'filterMedia', 'filterMediaUOM', 'insideSupport', 'insideSupportUOM', 
        'outsideSupport', 'outsideSupportUOM', 'brand', 'price', 'dateCreated'
    );

    // Only proceed if there was no upload error
    if (!$uploadError) {
        $checkCode = "SELECT * FROM order_form WHERE jobOrderNumber = ?";
        $checkStmt = $conn->prepare($checkCode);
        $checkStmt->bind_param("i", $jobOrderNumber); // Bind as integer
        $checkStmt->execute();
        $result = $checkStmt->get_result();
        
        if ($result->num_rows > 0) {
            $errorMessage = "Order code already exists.";
        } else {
            $stmt = $conn->prepare("INSERT INTO order_form 
                (jobOrderNumber, company, items, quantity, requiredDate, 
                cap, capUOM, size, sizeUOM, gasket, gasketUOM, oring, oringUOM, 
                filterMedia, filterMediaUOM, insideSupport, insideSupportUOM, outsideSupport, outsideSupportUOM, 
                brand, price, filterDrawing, dateCreated, status) 
                VALUES (?, ?, ?, ?, ?, 
                        ?, ?, ?, ?, ?, 
                        ?, ?, ?, ?, ?, 
                        ?, ?, ?, ?, 
                        ?, ?, ?, ?, ?)");
            
            if (!$stmt) {
                die("Prepare failed: " . $conn->error);
            }
            
            // Bind parameters - updated for new UOM fields
            $null = NULL; // For binding BLOB data
            $stmt->bind_param(
                "ississssssssssssssssdbss",
                $jobOrderNumber, $company, $items, $quantity, $requiredDate,
                $cap, $capUOM, $size, $sizeUOM, $gasket,
                $gasketUOM, $oring, $oringUOM, $filterMedia, $filterMediaUOM,
                $insideSupport, $insideSupportUOM, $outsideSupport, $outsideSupportUOM, 
                $brand, $price, $null, $dateCreated, $status
            );
            
            // Need to bind BLOB data separately
            $stmt->send_long_data(21, $filterDrawing);
      
            if ($stmt->execute()) {
                $successLabel = ($status == 'draft') ? 'draft' : 'active order';
                $errorMessage = "<span id='success'>Order form successfully added as $successLabel!</span>";
                
                // Redirect to dashboard after successful submission, with the appropriate tab
                header("Location: orderFormDashboard.php?tab=" . $status);
                exit;
            } else {
                $errorMessage = "Error: " . $stmt->error;
            }
            
            $stmt->close();
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
    <link rel="stylesheet" href="addOrderInterface4.css">
    <link rel="stylesheet" href="font.css">
    <title>Step 1</title>
</head>
<body>
    <div class="container" id="addOrderInterface">
        <a href="orderFormDashboard.php" class="back-btn"><i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="form-title">Step 1: Order Creation</h1>
        
        <?php if (!empty($errorMessage)): ?>
            <p class="popup"><?php echo $errorMessage; ?></p>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data">
            <div class="form-row">
                <div class="form-col">
                    <div class="input-group">
                        <i class="fas fa-hashtag"></i>
                        <input type="number" name="jobOrderNumber" id="jobOrderNumber" placeholder="Job Order Number" required value="<?php echo isset($submittedData['jobOrderNumber']) ? htmlspecialchars($submittedData['jobOrderNumber']) : ''; ?>">
                        <label for="jobOrderNumber">Job Order No.</label>
                    </div>
                    <div class="input-group">
                        <i class="fas fa-building"></i>
                        <input type="text" name="company" id="company" placeholder="Company" required value="<?php echo isset($submittedData['company']) ? htmlspecialchars($submittedData['company']) : ''; ?>">
                        <label for="company">Company Name</label>
                    </div>
                    <div class="input-group">
                        <i class="fas fa-boxes"></i>
                        <input type="text" name="items" id="items" placeholder="Items" required value="<?php echo isset($submittedData['items']) ? htmlspecialchars($submittedData['items']) : ''; ?>">
                        <label for="items">Items</label>
                    </div>
                </div>
                
                <div class="form-col">
                    <div class="input-group">
                        <i class="fas fa-sort-numeric-up"></i>
                        <input type="number" name="quantity" id="quantity" placeholder="Quantity" required value="<?php echo isset($submittedData['quantity']) ? htmlspecialchars($submittedData['quantity']) : ''; ?>">
                        <label for="quantity">Order Quantity</label>
                    </div>
                    <div class="input-group">
                        <i class="fas fa-calendar-alt"></i>
                        <input type="date" name="requiredDate" id="requiredDate" placeholder="Required Date" required value="<?php echo isset($submittedData['requiredDate']) ? htmlspecialchars($submittedData['requiredDate']) : ''; ?>">
                        <label for="requiredDate">Date Required</label>
                    </div>
                </div>
            </div>
            
            <div class="divider"></div>
            <div class="section-title">Order Information</div>
            
            <div class="form-row">
                <div class="form-col">
                    <!-- Cap with UOM -->
                    <div class="input-with-dropdown">
                        <div class="input-group">
                            <i class="fas fa-cog"></i>
                            <input type="text" name="cap" id="cap" placeholder="Cap" required value="<?php echo isset($submittedData['cap']) ? htmlspecialchars($submittedData['cap']) : ''; ?>">
                            <label for="cap">Cap</label>
                        </div>
                        <select name="capUOM" id="capUOM">
                            <option value="mm" <?php echo (isset($submittedData['capUOM']) && $submittedData['capUOM'] == 'mm') ? 'selected' : ''; ?>>mm</option>
                            <option value="cm" <?php echo (isset($submittedData['capUOM']) && $submittedData['capUOM'] == 'cm') ? 'selected' : ''; ?>>cm</option>
                            <option value="in" <?php echo (isset($submittedData['capUOM']) && $submittedData['capUOM'] == 'in') ? 'selected' : ''; ?>>in</option>
                        </select>
                    </div>

                    <!-- Size with UOM -->
                    <div class="input-with-dropdown">
                        <div class="input-group">
                            <i class="fas fa-ruler"></i>
                            <input type="text" name="size" id="size" placeholder="Size" required value="<?php echo isset($submittedData['size']) ? htmlspecialchars($submittedData['size']) : ''; ?>">
                            <label for="size">Size</label>
                        </div>
                        <select name="sizeUOM" id="sizeUOM">
                            <option value="mm" <?php echo (isset($submittedData['sizeUOM']) && $submittedData['sizeUOM'] == 'mm') ? 'selected' : ''; ?>>mm</option>
                            <option value="cm" <?php echo (isset($submittedData['sizeUOM']) && $submittedData['sizeUOM'] == 'cm') ? 'selected' : ''; ?>>cm</option>
                            <option value="in" <?php echo (isset($submittedData['sizeUOM']) && $submittedData['sizeUOM'] == 'in') ? 'selected' : ''; ?>>in</option>
                        </select>
                    </div>

                    <!-- Gasket with UOM -->
                    <div class="input-with-dropdown">
                        <div class="input-group">
                            <i class="fas fa-dot-circle"></i>
                            <input type="text" name="gasket" id="gasket" placeholder="Gasket" required value="<?php echo isset($submittedData['gasket']) ? htmlspecialchars($submittedData['gasket']) : ''; ?>">
                            <label for="gasket">Gasket</label>
                        </div>
                        <select name="gasketUOM" id="gasketUOM">
                            <option value="mm" <?php echo (isset($submittedData['gasketUOM']) && $submittedData['gasketUOM'] == 'mm') ? 'selected' : ''; ?>>mm</option>
                            <option value="cm" <?php echo (isset($submittedData['gasketUOM']) && $submittedData['gasketUOM'] == 'cm') ? 'selected' : ''; ?>>cm</option>
                            <option value="in" <?php echo (isset($submittedData['gasketUOM']) && $submittedData['gasketUOM'] == 'in') ? 'selected' : ''; ?>>in</option>
                        </select>
                    </div>
                    
                    <!-- O-Ring with UOM -->
                    <div class="input-with-dropdown">
                        <div class="input-group">
                            <i class="fas fa-ring"></i>
                            <input type="text" name="oring" id="oring" placeholder="O-Ring" required value="<?php echo isset($submittedData['oring']) ? htmlspecialchars($submittedData['oring']) : ''; ?>">
                            <label for="oring">O-Ring</label>
                        </div>
                        <select name="oringUOM" id="oringUOM">
                            <option value="mm" <?php echo (isset($submittedData['oringUOM']) && $submittedData['oringUOM'] == 'mm') ? 'selected' : ''; ?>>mm</option>
                            <option value="cm" <?php echo (isset($submittedData['oringUOM']) && $submittedData['oringUOM'] == 'cm') ? 'selected' : ''; ?>>cm</option>
                            <option value="in" <?php echo (isset($submittedData['oringUOM']) && $submittedData['oringUOM'] == 'in') ? 'selected' : ''; ?>>in</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-col">
                    <!-- Filter Media with UOM -->
                    <div class="input-with-dropdown">
                        <div class="input-group">
                            <i class="fas fa-filter"></i>
                            <input type="text" name="filterMedia" id="filterMedia" placeholder="Filter Media" required value="<?php echo isset($submittedData['filterMedia']) ? htmlspecialchars($submittedData['filterMedia']) : ''; ?>">
                            <label for="filterMedia">Filter Media</label>
                        </div>
                        <select name="filterMediaUOM" id="filterMediaUOM">
                            <option value="mm" <?php echo (isset($submittedData['filterMediaUOM']) && $submittedData['filterMediaUOM'] == 'mm') ? 'selected' : ''; ?>>mm</option>
                            <option value="cm" <?php echo (isset($submittedData['filterMediaUOM']) && $submittedData['filterMediaUOM'] == 'cm') ? 'selected' : ''; ?>>cm</option>
                            <option value="in" <?php echo (isset($submittedData['filterMediaUOM']) && $submittedData['filterMediaUOM'] == 'in') ? 'selected' : ''; ?>>in</option>
                        </select>
                    </div>
                    
                    <!-- Inside Support with UOM -->
                    <div class="input-with-dropdown">
                        <div class="input-group">
                            <i class="fas fa-arrow-down"></i>
                            <input type="text" name="insideSupport" id="insideSupport" placeholder="Inside Support" required value="<?php echo isset($submittedData['insideSupport']) ? htmlspecialchars($submittedData['insideSupport']) : ''; ?>">
                            <label for="insideSupport">Inside Support</label>
                        </div>
                        <select name="insideSupportUOM" id="insideSupportUOM">
                            <option value="mm" <?php echo (isset($submittedData['insideSupportUOM']) && $submittedData['insideSupportUOM'] == 'mm') ? 'selected' : ''; ?>>mm</option>
                            <option value="cm" <?php echo (isset($submittedData['insideSupportUOM']) && $submittedData['insideSupportUOM'] == 'cm') ? 'selected' : ''; ?>>cm</option>
                            <option value="in" <?php echo (isset($submittedData['insideSupportUOM']) && $submittedData['insideSupportUOM'] == 'in') ? 'selected' : ''; ?>>in</option>
                        </select>
                    </div>
                    
                    <!-- Outside Support with UOM -->
                    <div class="input-with-dropdown">
                        <div class="input-group">
                            <i class="fas fa-arrow-up"></i>
                            <input type="text" name="outsideSupport" id="outsideSupport" placeholder="Outside Support" required value="<?php echo isset($submittedData['outsideSupport']) ? htmlspecialchars($submittedData['outsideSupport']) : ''; ?>">
                            <label for="outsideSupport">Outside Support</label>
                        </div>
                        <select name="outsideSupportUOM" id="outsideSupportUOM">
                            <option value="mm" <?php echo (isset($submittedData['outsideSupportUOM']) && $submittedData['outsideSupportUOM'] == 'mm') ? 'selected' : ''; ?>>mm</option>
                            <option value="cm" <?php echo (isset($submittedData['outsideSupportUOM']) && $submittedData['outsideSupportUOM'] == 'cm') ? 'selected' : ''; ?>>cm</option>
                            <option value="in" <?php echo (isset($submittedData['outsideSupportUOM']) && $submittedData['outsideSupportUOM'] == 'in') ? 'selected' : ''; ?>>in</option>
                        </select>
                    </div>
                    
                    <div class="input-group">
                        <i class="fas fa-tag"></i>
                        <input type="text" name="brand" id="brand" placeholder="Brand" required value="<?php echo isset($submittedData['brand']) ? htmlspecialchars($submittedData['brand']) : ''; ?>">
                        <label for="brand">Brand</label>
                    </div>
                </div>
            </div>
            
            <div class="bottom-row">
                <div class="price-container">
                    <div class="input-group">
                        <i class="fas fa-money-bill"></i>
                        <input type="number" step="0.01" name="price" id="price" placeholder="Price (₱)" required value="<?php echo isset($submittedData['price']) ? htmlspecialchars($submittedData['price']) : ''; ?>">
                        <label for="price">Price</label>
                    </div>
                </div>
                
                <div class="filter-sketch-container">
                    <div class="filter-sketch" id="filterSketchBox" onclick="document.getElementById('filterDrawingInput').click()">
                        Insert sketch
                    </div>
                    <input type="file" id="filterDrawingInput" name="filterDrawing" accept="image/*" required style="display: none;" onchange="updateFileName(this)">
                    <div id="fileName"></div>
                </div>
                
                <div class="buttons-container">
                    <button type="submit" class="draft-btn" name="draftButton" id="statusButton">SAVE AS DRAFT</button>
                    <button type="submit" class="done-btn" name="submitButton" id="statusButton">SUBMIT ORDER</button>
                </div>
            </div>
        </form>
    </div>    

    <script>
        function updateFileName(input) {
            var fileName = input.files[0] ? input.files[0].name : "";
            document.getElementById('fileName').textContent = fileName;
            
            // Update the filter sketch box to show it's been selected
            var filterSketchBox = document.getElementById('filterSketchBox');
            if (fileName) {
                filterSketchBox.innerHTML = "File selected";
                filterSketchBox.classList.add("file-selected");
            } else {
                filterSketchBox.innerHTML = "Insert sketch";
                filterSketchBox.classList.remove("file-selected");
            }
        }
    </script>
</body>
</html>