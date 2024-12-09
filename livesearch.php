<?php

include("connect.php");

if (isset($_POST['input'])) {
    $input = $_POST['input'];

    // SQL query to search both FilterName and FilterCode
    $sql = "SELECT * FROM filters WHERE FilterName LIKE ? OR FilterCode LIKE ?";
    $stmt = $conn->prepare($sql);
    $searchInput = "%$input%"; // Add wildcard characters for partial match
    $stmt->bind_param("ss", $searchInput, $searchInput); // Bind the input for both columns
    $stmt->execute();
    $result = $stmt->get_result();
}
?>

<table>
    <thead>
        <tr>
            <th>OEM Code</th>
            <th>Part Number</th>
            <th>Filter Name</th>
            <th>Materials</th>
            <th>Quantity</th>
            <th>Max Stock</th>
            <th>Low Stock Signal</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($result && $result->num_rows > 0) {
            // Output data of each row
            while ($row = $result->fetch_assoc()) {
                // Determine the stock status
                $quantityClass = 'quantity-high'; // Default to high
                if ($row['Quantity'] <= $row['LowStockSignal']) {
                    $quantityClass = 'quantity-low';
                } elseif ($row['Quantity'] < $row['MaxStock'] / 2) {
                    $quantityClass = 'quantity-medium';
                }

                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['FilterCode'] ?? 'N/A') . "</td>";
                echo "<td>" . htmlspecialchars($row['PartNumber'] ?? 'N/A') . "</td>";
                echo "<td>" . htmlspecialchars($row['FilterName'] ?? 'N/A') . "</td>";
                echo "<td>" . htmlspecialchars($row['Materials'] ?? 'N/A') . "</td>";
                echo "<td class='$quantityClass'>" . htmlspecialchars($row['Quantity'] ?? 'N/A') . "</td>";
                echo "<td>" . htmlspecialchars($row['MaxStock'] ?? 'N/A') . "</td>";
                echo "<td>" . htmlspecialchars($row['LowStockSignal'] ?? 'N/A') . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='6'>No filters found</td></tr>";
        }
        ?>
    </tbody>
</table>