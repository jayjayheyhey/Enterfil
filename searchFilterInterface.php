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
    <title>Search Filter</title>
</head>
<body>
    <div class="container" id="searchInterface" style="display:block;">
        <h1 class="form-title">Search Filter Code</h1>
        <form method="post" action="searchFilter.php">
          <div class="input-group">
             <i class="fas fa-lock"></i>
             <input type="number" name="fCode" id="fCode" placeholder="Filter Code" required>
             <label for="fCode">Filter Code</label>
          </div>
         <input type="submit" class="btn" value="Search" name="searchButton">
        </form>
      </div>
    </form>
</body>