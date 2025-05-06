<?php
session_start();
include("connect.php");
include("filters_table.php");

$fullURL = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$errorMessage = "";

if (strpos($fullURL, "code=0") !== false) { 
    $errorMessage = "Filter code not found.";
} else if (strpos($fullURL, "FilterCode=EXF001&submitFilterCode=Submit+Filter+Code?stock=anomaly") !== false) { 
    $errorMessage = "Insufficient amount for quantity.";
}else if (strpos($fullURL, "add=Success") !== false) {
    $errorMessage = "<span id='success'>Filter successfully added!</span>";
}


// Fetch Filter Codes from the database
$filterCodes = [];
$query = "SELECT DISTINCT FilterCode FROM filters";
$result = $conn->query($query);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $filterCodes[] = $row['FilterCode'];
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
    <link rel="stylesheet" href="tablestyle2.css">
    <link rel="stylesheet" href="font.css">

    <title>Search Filter</title>
</head>
<body>
    <div class="ShowTableContainer" id="searchInterface" style="display:block;">
        <h1 class="form-title">Edit Filter</h1>
        <?php if (!empty($errorMessage)): ?>
            <p class="popup"><?php echo $errorMessage; ?></p>
        <?php endif; ?>
        <form method="post" action="editFilter.php">
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input list="filterCodes" name="fCode" id="fCode" placeholder="Filter Code" required>
                <datalist id="filterCodes">
                    <?php foreach ($filterCodes as $code): ?>
                        <option value="<?php echo htmlspecialchars($code); ?>">
                    <?php endforeach; ?>
                </datalist>
                <label for="fCode">Filter Code</label>
            </div>
            <input type="submit" class="btn" value="Search" name="searchButton">
        </form>
        <form method="post" action="homepage.php">
            <input type="submit" class="btn" value="Back to Dashboard">
        </form>

        <!-- Display Filters Table -->
        <?php
         renderFiltersTable($conn);
        ?>
    </div>
</body>
</html>