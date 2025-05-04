<?php
include("connect.php");

$errorMessage = "";
$submittedData = [];

if (isset($_POST['submitButton'])) {
    // Get form inputs
    $jobOrderNumber = $_POST['jobOrderNumber'];
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
    $filterDrawing = file_get_contents($_FILES['filterDrawing']['tmp_name']);

    $submittedData = compact(
        'jobOrderNumber', 'company', 'items', 'quantity', 'requiredDate', 'cap', 'size', 'gasket', 'oring',
        'filterMedia', 'insideSupport', 'outsideSupport', 'brand', 'price'
    );

    $checkCode = "SELECT * FROM order_form WHERE jobOrderNumber = '$jobOrderNumber'";
    if ($conn->query($checkCode)->num_rows > 0) {
        $errorMessage = "Order code already exists.";
    } else {
        $stmt = $conn->prepare("INSERT INTO order_form 
        (jobOrderNumber, company, items, quantity, requiredDate, cap, size, gasket, `o-ring`, filterMedia, insideSupport, outsideSupport, brand, price, filterDrawing) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ississsssssssbd", $jobOrderNumber, $company, $items, $quantity, $requiredDate, $cap, $size, $gasket, $oring, $filterMedia, $insideSupport, $outsideSupport, $brand, $price, $filterDrawing);

        if ($stmt->execute()) {
            $errorMessage = "<span id='success'>Order form successfully added!</span>";
            $submittedData = [];
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
    <link rel="stylesheet" href="font.css">
    <title>Add Filter</title>
</head>
<body>
    <div class="container" id="addOrderInterface" style="display:block;">
        <h1 class="form-title">Add Order</h1>
        <?php if (!empty($errorMessage)): ?>
            <p class="popup"><?php echo $errorMessage; ?></p>
        <?php endif; ?>

        <form method="post" action="addOrder.php">
            <div class="input-group">
                <i class="fas fa-hashtag"></i>
                <input type="text" name="jobOrderNumber" id="jobOrderNumber" placeholder="Job Order Number" required value="<?php echo isset($submittedData['jobOrderNumber']) ? $submittedData['jobOrderNumber'] : ''; ?>">
                <label for="jobOrderNumber">Job Order Number</label>
            </div>
            <div class="input-group">
                <i class="fas fa-building"></i>
                <input type="text" name="company" id="company" placeholder="Company" required value="<?php echo isset($submittedData['company']) ? $submittedData['company'] : ''; ?>">
                <label for="company">Company</label>
            </div>
            <div class="input-group">
                <i class="fas fa-boxes"></i>
                <input type="text" name="items" id="items" placeholder=" Items" required value="<?php echo isset($submittedData['items']) ? $submittedData['items'] : ''; ?>">
                <label for="items"> Items</label>
            </div>

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
            <div class="input-group">
                <i class="fas fa-cog"></i>
                <input type="text" name="cap" id="cap" placeholder="Cap" required value="<?php echo isset($submittedData['cap']) ? $submittedData['cap'] : ''; ?>">
                <label for="cap">Cap</label>
            </div>
            
            <div class="input-group">
                <i class="fas fa-ruler"></i>
                <input type="text" name="size" id= "size" placeholder="Size" required value="<?php echo isset($submittedData['size']) ? $submittedData['size']: ''; ?>">
                <label for ="size">Size</label>
            </div>

            <div class="input-group">
                <i class="fas fa-dot-circle"></i>
                <input type="text" name="gasket" id ="gasket" placeholder="Gasket" required value="<?php echo isset($submittedData['gasket']) ? $submittedData['gasket']: ''; ?>">
                <label for ="gasket">Gasket</label>
            </div>

            <div class="input-group">
                <i class="fas fa-ring"></i>
                <input type="text" name="oring" id ="oring" placeholder="O-Ring" required value="<?php echo isset($submittedData['oring']) ? $submittedData['oring']: ''; ?>">
                <label for ="oring">O-Ring</label>
            </div>

            <div class="input-group">
                <i class="fas fa-filter"></i>
                <input type="text" name="filterMedia" id="filterMedia" placeholder="Filter Media" required value="<?php echo isset($submittedData['filterMedia']) ? $submittedData['filterMedia']: ''; ?>">
                <label for ="filterMedia">Filter Media</label>
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
                <label for ="brand">O-Ring</label>
            </div>

            <div class="input-group">
                <i class="fas fa-money-bill"></i>
                <input type="number" step="0.01" name="price" id="price" placeholder="Price (₱)" required value="<?php echo isset($submittedData['price']) ? $submittedData['price']: ''; ?>">
                <label for ="price">Price</label>
            </div>

            <div class="input-group">
                <i class="fas fa-image"></i>
                <input type="file" name="filterDrawing" accept="image/*" required>
                <label for ="filterDrawing">Filter Drawing</label>
            </div>
            
            <input type="submit" class="btn" value="Submit Filter" name="submitButton">
                
        <form method="post" action="orderFormDashboard.php">
            <input type="submit" class="btn" value="Back to Dashboard">
        </form> 
    </div>
    
</body>
</html>
