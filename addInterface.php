<?php
session_start();
include("connect.php");
?>  

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="select.css">
    <title>AddItem</title>
</head>
<body>
    <div class="container" id="addInterface" style="display:block;">
        <h1 class="form-title">Add Item</h1>
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
    <i class="fas fa-ruler"></i>
    <input type="number" name="length" id="length" placeholder="Length" required step="0.01">
    <label for="length">Length</label>
    <select name="lengthUnit" id="lengthUnit" required>
        <option value="cm">cm</option>
        <option value="in">in</option>
        <option value="mm">mm</option>
        <option value="ft">ft</option>
    </select>
</div>

<div class="input-group">
    <i class="fas fa-ruler"></i>
    <input type="number" name="width" id="width" placeholder="Width" required step="0.01">
    <label for="width">Width</label>
    <select name="widthUnit" id="widthUnit" required>
        <option value="cm">cm</option>
        <option value="in">in</option>
        <option value="mm">mm</option>
        <option value="ft">ft</option>
    </select>
</div>

<div class="input-group">
    <i class="fas fa-ruler"></i>
    <input type="number" name="height" id="height" placeholder="Height" required step="0.01">
    <label for="height">Height</label>
    <select name="heightUnit" id="heightUnit" required>
        <option value="cm">cm</option>
        <option value="in">in</option>
        <option value="mm">mm</option>
        <option value="ft">ft</option>
    </select>
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