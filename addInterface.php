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