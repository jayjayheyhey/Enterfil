<?php
include("connect.php");

$errorMessage = "";
$submittedData = [];

if (isset($_POST['submitButton'])) {
    // Get form inputs
    $jobOrderNumber = $_POST['jobOrderNumber'];
    $dateCreated = date("Y-m-d"); // gets the current date
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
    
    // Initialize variables
    $filterDrawing = null;
    $uploadError = false;
    
    // Check if file was uploaded
    if (isset($_FILES['filterDrawing']) && $_FILES['filterDrawing']['error'] === 0) {
        $filterDrawing = file_get_contents($_FILES['filterDrawing']['tmp_name']);
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
        'jobOrderNumber', 'company', 'items', 'quantity', 'requiredDate', 'cap', 'size', 'gasket', 'oring',
        'filterMedia', 'insideSupport', 'outsideSupport', 'brand', 'price','dateCreated'
    );

    // Only proceed if there was no upload error
    if (!$uploadError) {
        $checkCode = "SELECT * FROM order_form WHERE jobOrderNumber = '$jobOrderNumber'";
        if ($conn->query($checkCode)->num_rows > 0) {
            $errorMessage = "Order code already exists.";
        } else {
            $stmt = $conn->prepare("INSERT INTO order_form 
                (jobOrderNumber, company, items, quantity, requiredDate, cap, size, gasket, oring, filterMedia, insideSupport, outsideSupport, brand, price, filterDrawing) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            // Bind parameters
            $stmt->bind_param(
                "ississsssssssdb",
                $jobOrderNumber, $company, $items, $quantity, $requiredDate,
                $cap, $size, $gasket, $oring, $filterMedia,
                $insideSupport, $outsideSupport, $brand, $price,
                $null  // Placeholder for the BLOB data
            );
            
            // Stream in the blob data
            $stmt->send_long_data(14, $filterDrawing);  // Zero-based index
      
            if ($stmt->execute()) {
                $errorMessage = "<span id='success'>Order form successfully added!</span>";
                $submittedData = [];
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
    <link rel="stylesheet" href="addOrderInterface.css">
    <link rel="stylesheet" href="font.css">
    <title>Add Filter</title>
</head>
<body>
    <div class="container" id="addOrderInterface">
        <a href="orderFormDashboard.php" class="back-btn"><i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="form-title">Add Order</h1>
        
        <?php if (!empty($errorMessage)): ?>
            <p class="popup"><?php echo $errorMessage; ?></p>
        <?php endif; ?>

        <form method="post" action="addOrder.php" enctype="multipart/form-data">
            <div class="form-row">
                <div class="form-col">
                    <div class="input-group">
                        <i class="fas fa-hashtag"></i>
                        <input type="text" name="jobOrderNumber" id="jobOrderNumber" placeholder="Job Order Number" required value="<?php echo isset($submittedData['jobOrderNumber']) ? htmlspecialchars($submittedData['jobOrderNumber']) : ''; ?>">
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
                    <div class="input-group">
                        <i class="fas fa-cog"></i>
                        <input type="text" name="cap" id="cap" placeholder="Cap" required value="<?php echo isset($submittedData['cap']) ? htmlspecialchars($submittedData['cap']) : ''; ?>">
                        <label for="cap">Cap</label>
                    </div>
                    <div class="input-group">
                        <i class="fas fa-ruler"></i>
                        <input type="text" name="size" id="size" placeholder="Size" required value="<?php echo isset($submittedData['size']) ? htmlspecialchars($submittedData['size']): ''; ?>">
                        <label for="size">Size</label>
                    </div>
                    <div class="input-group">
                        <i class="fas fa-dot-circle"></i>
                        <input type="text" name="gasket" id="gasket" placeholder="Gasket" required value="<?php echo isset($submittedData['gasket']) ? htmlspecialchars($submittedData['gasket']): ''; ?>">
                        <label for="gasket">Gasket</label>
                    </div>
                    <div class="input-group">
                        <i class="fas fa-ring"></i>
                        <input type="text" name="oring" id="oring" placeholder="O-Ring" required value="<?php echo isset($submittedData['oring']) ? htmlspecialchars($submittedData['oring']): ''; ?>">
                        <label for="oring">O-Ring</label>
                    </div>
                </div>
                
                <div class="form-col">
                    <div class="input-group">
                        <i class="fas fa-filter"></i>
                        <input type="text" name="filterMedia" id="filterMedia" placeholder="Filter Media" required value="<?php echo isset($submittedData['filterMedia']) ? htmlspecialchars($submittedData['filterMedia']): ''; ?>">
                        <label for="filterMedia">Filter Media</label>
                    </div>
                    <div class="input-group">
                        <i class="fas fa-arrow-down"></i>
                        <input type="text" name="insideSupport" id="insideSupport" placeholder="Inside Support" required value="<?php echo isset($submittedData['insideSupport']) ? htmlspecialchars($submittedData['insideSupport']): ''; ?>">
                        <label for="insideSupport">Inside Support</label>
                    </div>
                    <div class="input-group">
                        <i class="fas fa-arrow-up"></i>
                        <input type="text" name="outsideSupport" id="outsideSupport" placeholder="Outside Support" required value="<?php echo isset($submittedData['outsideSupport']) ? htmlspecialchars($submittedData['outsideSupport']): ''; ?>">
                        <label for="outsideSupport">Outside Support</label>
                    </div>
                    <div class="input-group">
                        <i class="fas fa-tag"></i>
                        <input type="text" name="brand" id="brand" placeholder="Brand" required value="<?php echo isset($submittedData['brand']) ? htmlspecialchars($submittedData['brand']): ''; ?>">
                        <label for="brand">Brand</label>
                    </div>
                </div>
            </div>
            
            <div class="bottom-row">
                <div class="price-container">
                    <div class="input-group">
                        <i class="fas fa-money-bill"></i>
                        <input type="number" step="0.01" name="price" id="price" placeholder="Price (₱)" required value="<?php echo isset($submittedData['price']) ? htmlspecialchars($submittedData['price']): ''; ?>">
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
                
                <button type="submit" class="done-btn" name="submitButton">DONE</button>
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