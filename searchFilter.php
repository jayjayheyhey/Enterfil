<?php 
include 'connect.php';

if(isset($_POST['searchButton'])){
    $FilterCode = $_POST['fCode'];

    $checkCode="SELECT * FROM filters WHERE FilterCode='$FilterCode'";
    $result = $conn->query($checkCode);
    if($result->num_rows > 0){
        $row = $result->fetch_assoc();
    }
    else{
        echo "Filter does not exist!";
        header("refresh:3;url=homepage.php");
        exit;
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
    <title>Edit Filter</title>
</head>
<body>

<?php 
// Only show the form if filter code exists
if(isset($row) && !empty($row)) { 
?>
    <div class="container" id="editInterface" style="display:block;">
        <h1 class="form-title">Edit Filter</h1>
        <form method="post" action="updateFilter.php">
          <div class="input-group">
             <i class="fas fa-lock"></i>
             <input type="number" name="fCode" id="fCode" placeholder="Filter Code" required value="<?php echo isset($row['FilterCode']) ? $row['FilterCode'] : ''; ?>" disabled>
             <label for="fCode">You have selected filter:</label>
          </div>
          
            <!-- Hidden field to submit the FilterCode -->
            <input type="hidden" name="fCode" value="<?php echo isset($row['FilterCode']) ? $row['FilterCode'] : ''; ?>">

          <div class="input-group">
              <i class="fas fa-book"></i>
              <input type="text" name="fName" id="fName" placeholder="Filter Name" required value="<?php echo isset($row['FilterName']) ? $row['FilterName'] : ''; ?>">
              <label for="fName">Update Filter Name</label>
          </div>
          <div class="input-group">
              <textarea id="materials" name="materials" placeholder="Materials" rows="4" cols="49"><?php echo isset($row['Materials']) ? $row['Materials'] : ''; ?></textarea>
              <label for="materials">Update Materials</label>
          </div>
          <div class="input-group">
              <i class="fas fa-cog"></i>
              <input type="number" name="quantity" id="quantity" placeholder="Quantity" required value="<?php echo isset($row['Quantity']) ? $row['Quantity'] : ''; ?>">
              <label for="password">Update Quantity</label>
          </div>
          <div class="input-group">
              <i class="fas fa-clipboard"></i>
              <input type="number" name="maxStock" id="maxStock" placeholder="Maximum Stock Level" required value="<?php echo isset($row['MaxStock']) ? $row['MaxStock'] : ''; ?>">
              <label for="password">Update Maximum Stock Level</label>
          </div>
          <div class="input-group">
              <i class="fas fa-clipboard"></i>
              <input type="number" name="lowStock" id="lowStock" placeholder="Low Stock Signal" required value="<?php echo isset($row['LowStockSignal']) ? $row['LowStockSignal'] : ''; ?>">
              <label for="password">Update Low Stock Signal</label>
          </div>
         <input type="submit" class="btn" value="Update Filter" name="updateButton">
        </form>
      </div>

      <?php 
} 
?>
</body>
</html>