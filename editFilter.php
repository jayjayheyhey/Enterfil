<?php 
include 'connect.php';

if(isset($_POST['searchButton'])){
    $FilterCode = $_POST['fCode'];

    $query = "SELECT * FROM filters WHERE FilterCode = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $FilterCode);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        header("Location: searchFilterInterface.php?error=1");
        exit;
    }
    $stmt->close();

}

?>

<!DOCTYPE html>
<html lang="en">    
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="edit.css">
    <link rel="stylesheet" href="select.css">
    <title>Edit Filter</title>
</head>
<body>

<?php 
// Only show the form if filter code exists
if(isset($row) && !empty($row)) { 
?>
    <div class="container" id="editInterface" style="display:block;">
        <h1 class="form-title">UPDATE Filter</h1>
        <form method="post" action="updateFilter.php">
          <div class="input-group">
             <i class="fas fa-lock"></i>
             <input type="text" name="fCode" id="fCode" placeholder="Filter Code" required value="<?php echo isset($row['FilterCode']) ? $row['FilterCode'] : ''; ?>" disabled>
             <label for="fCode">Filter Code:</label>
          </div>
          
            <!-- Hidden field to submit the FilterCode -->
            <input type="hidden" name="fCode" value="<?php echo isset($row['FilterCode']) ? $row['FilterCode'] : ''; ?>">

            <div class="input-group">
              <i class="fas fa-book"></i>
              <input type="text" name="pName" id="pName" placeholder="Part Number" required value="<?php echo isset($row['PartNumber']) ? $row['PartNumber'] : ''; ?>">
              <label for="pName">Part Number:</label>
            </div>
            <div class="input-group">
              <i class="fas fa-book"></i>
              <input type="text" name="fName" id="fName" placeholder="Filter Name" required value="<?php echo isset($row['FilterName']) ? $row['FilterName'] : ''; ?>">
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
              <i class="fas fa-cog"></i>
              <input type="number" name="quantity" id="quantity" placeholder="Quantity" required value="<?php echo isset($row['Quantity']) ? $row['Quantity'] : ''; ?>">
              <label for="password">Quantity</label>
          </div>
          <div class="input-group">
              <i class="fas fa-clipboard"></i>
              <input type="number" name="maxStock" id="maxStock" placeholder="Maximum Stock Level" required value="<?php echo isset($row['MaxStock']) ? $row['MaxStock'] : ''; ?>">
              <label for="password">Maximum Stock Level</label>
          </div>
          <div class="input-group">
              <i class="fas fa-clipboard"></i>
              <input type="number" name="lowStock" id="lowStock" placeholder="Low Stock Signal" required value="<?php echo isset($row['LowStockSignal']) ? $row['LowStockSignal'] : ''; ?>">
              <label for="password">Low Stock Signal</label>
          </div>
         <input type="submit" class="btn" value="Update Filter" name="updateButton">
        </form>
      </div>

      <?php 
} 
?>
</body>
</html>