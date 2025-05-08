<?php
include("connect.php");

// Check if job order number is provided
if (!isset($_GET['jobOrderNumber'])) {
    header("Location: orderFormDashboard.php?tab=active&error=no_order_specified");
    exit;
}

$jobOrderNumber = $_GET['jobOrderNumber'];

// Verify the order exists and is active
$stmt = $conn->prepare("SELECT * FROM order_form WHERE jobOrderNumber = ?");
$stmt->bind_param("i", $jobOrderNumber); // Changed from "s" to "i" for INT type
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    header("Location: orderFormDashboard.php?tab=active&error=order_not_found");
    exit;
}

$order = $result->fetch_assoc();

// Check if order is active
if ($order['status'] !== 'active') {
    header("Location: orderFormDashboard.php?tab=active&error=invalid_status");
    exit;
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if materials were submitted
    if (!isset($_POST['materials']) || empty($_POST['materials']) || 
        !isset($_POST['quantities']) || empty($_POST['quantities'])) {
        $error = "Please add at least one raw material used.";
    } else {
        $materials = $_POST['materials'];
        $quantities = $_POST['quantities'];
        
        // Begin transaction
        $conn->begin_transaction();
        try {
            // Update order status to finished
            $updateStmt = $conn->prepare("UPDATE order_form SET status = 'finished', completionDate = CURRENT_DATE() WHERE jobOrderNumber = ?");
            $updateStmt->bind_param("i", $jobOrderNumber); // Changed from "s" to "i" for INT type
            $updateStmt->execute();
            
            // Insert materials used records
            $insertStmt = $conn->prepare("INSERT INTO materials_used (jobOrderNumber, filterCode, quantity, usageDate) VALUES (?, ?, ?, CURRENT_DATE())");
            
            foreach ($materials as $index => $filterCode) {
                if (!empty($filterCode) && isset($quantities[$index]) && is_numeric($quantities[$index])) {
                    $quantity = floatval($quantities[$index]);
                    $insertStmt->bind_param("isd", $jobOrderNumber, $filterCode, $quantity); // Changed first param from "s" to "i" for INT type
                    $insertStmt->execute();
                }
            }

            $updateFilterStmt = $conn->prepare("UPDATE filters SET Quantity = Quantity - ? WHERE FilterCode = ?");

            foreach ($materials as $index => $filterCode) {
                if (!empty($filterCode) && isset($quantities[$index]) && is_numeric($quantities[$index])) {
                    $quantity = floatval($quantities[$index]);
                    
                    // First check if we have enough quantity to deduct
                    $checkStmt = $conn->prepare("SELECT Quantity FROM filters WHERE FilterCode = ?");
                    $checkStmt->bind_param("s", $filterCode);
                    $checkStmt->execute();
                    $checkResult = $checkStmt->get_result();
                    $currentQty = $checkResult->fetch_assoc()['Quantity'];
                    
                    if ($currentQty < $quantity) {
                        throw new Exception("Insufficient quantity available for filter: " . $filterCode);
                    }
                    
                    // Update the filters table to deduct the quantity
                    $updateFilterStmt->bind_param("ds", $quantity, $filterCode);
                    $updateFilterStmt->execute();
                }
            }
            
            // Commit transaction
            $conn->commit();
            
            // Redirect to dashboard with success message
            header("Location: orderFormDashboard.php?tab=finished&success=marked_done");
            exit;
            
        } catch (Exception $e) {
            // Rollback in case of error
            $conn->rollback();
            $error = "Error updating order: " . $e->getMessage();
        }
    }
}

// Get all available filters from the database
$filtersQuery = "SELECT FilterCode, PartNumber, FilterName FROM filters ORDER BY FilterName";
$filtersResult = $conn->query($filtersQuery);
$filters = [];

if ($filtersResult && $filtersResult->num_rows > 0) {
    while ($row = $filtersResult->fetch_assoc()) {
        $filters[] = $row;
    }
}

// Convert filters to JSON for JavaScript
$filtersJson = json_encode($filters);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Record Raw Materials Used</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css">
    <link rel="stylesheet" href="materials_form.css">
</head>
<body>
    <div class="container">
        <h1>Record Raw Materials Used</h1>
        
        <div class="job-info">
            <h3>Job Order: <?php echo htmlspecialchars($jobOrderNumber); ?></h3>
            <p>Please record all raw materials used for this job before marking it as complete.</p>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="post">
            <div class="form-group">
                <label>Raw Materials Used:</label>
                <div id="materials-container">
                    <!-- Material rows will be added here -->
                </div>
                <button type="button" class="btn-add" id="add-material">+ Add Material</button>
            </div>
            
            <div class="buttons">
                <a href="orderFormDashboard.php?tab=active" class="btn-secondary" style="text-decoration: none;">Cancel</a>
                <button type="submit" class="btn-primary">Mark as Complete</button>
            </div>
        </form>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <script>
        $(document).ready(function() {
            // Store filters data from PHP
            const filters = <?php echo $filtersJson; ?>;
            const filterCodes = filters.map(filter => filter.FilterCode);
            
            // Function to check if material exists
            function checkMaterialExists(code) {
                return filterCodes.includes(code);
            }
            
            // Function to add new material row
            function addMaterialRow() {
                const rowIndex = $('.material-row').length;
                const row = `
                    <div class="material-row">
                        <div class="material-code">
                            <input type="text" name="materials[${rowIndex}]" class="filter-code" placeholder="Filter Code" required>
                        </div>
                        <div class="material-name">
                            <input type="text" class="filter-name" placeholder="Filter Name" disabled>
                        </div>
                        <div class="material-quantity">
                            <input type="number" name="quantities[${rowIndex}]" min="0.01" step="0.01" placeholder="Qty" required>
                        </div>
                        <div class="material-action">
                            <button type="button" class="btn-remove">×</button>
                        </div>
                    </div>
                `;
                $('#materials-container').append(row);
                
                // Set up autocomplete for the new row
                const $filterCode = $('.filter-code').last();
                const $filterName = $('.filter-name').last();
                
                $filterCode.autocomplete({
                    source: filterCodes,
                    minLength: 1,
                    select: function(event, ui) {
                        const selectedFilter = filters.find(f => f.FilterCode === ui.item.value);
                        if (selectedFilter) {
                            $filterName.val(`${selectedFilter.FilterName} (${selectedFilter.PartNumber})`);
                        }
                        
                        // Check validity after selection
                        validateFilterCode($(this));
                    }
                });
                
                // Add blur event to validate
                $filterCode.on('blur', function() {
                    validateFilterCode($(this));
                });
            }
            
            // Function to validate filter code
            function validateFilterCode($input) {
                const value = $input.val().trim();
                if (value && !checkMaterialExists(value)) {
                    $input.addClass('invalid-material');
                } else {
                    $input.removeClass('invalid-material');
                    
                    // If valid, update the filter name
                    if (value) {
                        const selectedFilter = filters.find(f => f.FilterCode === value);
                        if (selectedFilter) {
                            $input.closest('.material-row').find('.filter-name')
                                .val(`${selectedFilter.FilterName} (${selectedFilter.PartNumber})`);
                        }
                    }
                }
            }
            
            // Add first row by default
            addMaterialRow();
            
            // Add material button click handler
            $('#add-material').on('click', function(e) {
                e.preventDefault();
                addMaterialRow();
            });
            
            // Remove material button click handler (delegated)
            $(document).on('click', '.btn-remove', function() {
                $(this).closest('.material-row').remove();
                
                // If all rows are removed, add one back
                if ($('.material-row').length === 0) {
                    addMaterialRow();
                }
            });
            
            // Form submission validation
            $('form').on('submit', function(e) {
                // Check if any materials are invalid
                if ($('.invalid-material').length > 0) {
                    e.preventDefault();
                    alert('Please correct invalid material codes highlighted in red.');
                    return false;
                }
                
                // Check if at least one material is added
                if ($('.material-row').length === 0) {
                    e.preventDefault();
                    alert('Please add at least one material.');
                    return false;
                }
                
                // Check if all fields are filled
                let isValid = true;
                $('.material-row').each(function() {
                    const code = $(this).find('.filter-code').val().trim();
                    const qty = $(this).find('input[type="number"]').val().trim();
                    
                    if (!code || !qty) {
                        isValid = false;
                        return false;
                    }
                });
                
                if (!isValid) {
                    e.preventDefault();
                    alert('Please fill in all material fields.');
                    return false;
                }
            });
        });
    </script>
</body>
</html>