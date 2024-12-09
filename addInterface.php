<?php
session_start();
include("connect.php");

$fullURL = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$errorMessage = "";

if (strpos($fullURL, "code=used") !== false) { 
    $errorMessage = "Filter code already exists.";
} else if (strpos($fullURL, "name=used") !== false) {
    $errorMessage = "Filter name already exists.";
} else if (strpos($fullURL, "part=used") !== false) {
    $errorMessage = "Part number already exists.";
} else if (strpos($fullURL, "stock=toolarge") !== false) {
    $errorMessage = "Quantity cannot be larger than the maximum stock.";
} else if (strpos($fullURL, "stock=toolow") !== false) {
    $errorMessage = "Insufficient amount for Quantity.";
} else if (strpos($fullURL, "maxStock=toolow") !== false) {
    $errorMessage = "Insufficient amount for Maximum Stock Level.";
} else if (strpos($fullURL, "signal=toolow") !== false) {
    $errorMessage = "Insufficient amount for Low Stock Signal.";
} else if (strpos($fullURL, "add=Success") !== false) {
    $errorMessage = "<span id='success'>Filter successfully added!</span>";
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
    <title>AddItem</title>
</head>
<body>
    <div class="container" id="addInterface" style="display:block;">
        <h1 class="form-title">Add Item</h1>
        <?php if (!empty($errorMessage)): ?>
            <p class="popup"><?php echo $errorMessage; ?></p>
        <?php endif; ?>

        <form method="post" action="submitItem.php">
          <div class="input-group">
             <i class="fas fa-lock"></i>
             <input type="text" name="fCode" id="fCode" placeholder="Filter Code" required>
             <label for="fCode">Filter Code</label>
          </div>
          <div class="input-group">
              <i class="fas fa-book"></i>
              <input type="text" name="pName" id="pName" placeholder="Part Number" required>
              <label for="pName">Part Number</label>
          </div>
          <div class="input-group">
              <i class="fas fa-book"></i>
              <input type="text" name="fName" id="fName" placeholder="Filter Name" required>
              <label for="fName">Filter Name</label>
          </div>
          <div class="input-group">
              <textarea id="materials" name="materials" placeholder="Materials" rows="4" cols="49"></textarea>
              <label for="materials">Materials</label>
          </div>
          <div class="input-group">
              <i class="fas fa-cog"></i>
              <input type="number" name="quantity" id="quantity" placeholder="Quantity" required>
              <label for="password">Quantity</label>
          </div>
          <div class="input-group">
              <i class="fas fa-clipboard"></i>
              <input type="number" name="maxStock" id="maxStock" placeholder="Maximum Stock Level" required>
              <label for="password">Maximum Stock Level</label>
          </div>
          <div class="input-group">
              <i class="fas fa-clipboard"></i>
              <input type="number" name="lowStock" id="lowStock" placeholder="Low Stock Signal" required>
              <label for="password">Low Stock Signal</label>
          </div>
         <input type="submit" class="btn" value="Submit Item" name="submitButton">
        </form>
        <form method="post" action="homepage.php">
            <input type="submit" class="btn" value="Back to Dashboard">
        </form> 
      </div>
</body>