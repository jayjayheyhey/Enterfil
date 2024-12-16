<?php
function renderFiltersTable($conn) {
    ?>
    <div id="filters_table">
        <table>
            <thead style="font-family: Arial, sans-serif";>
                <tr>
                    <th>OEM Code</th>
                    <th>Part Number</th>
                    <th>Filter Name</th>
                    <th>Dimensions</th>
                    <th>Quantity</th>
                    <th>Max Stock</th>
                    <th>Low Stock Signal</th>
                </tr>
            </thead>
            <tbody style="font-family: Arial, sans-serif";>
                <?php
                // Fetch data from filters table
                $sql = "SELECT * FROM filters"; // Updated table name
                $result = $conn->query($sql);

                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        // Determine the stock status
                        $quantityClass = 'quantity-high'; // Default to high
                        if ($row['Quantity'] <= $row['LowStockSignal']) {
                            $quantityClass = 'quantity-low';
                        } elseif ($row['Quantity'] < $row['MaxStock'] / 2) {
                            $quantityClass = 'quantity-medium';
                        }

                        $dimensions = "{$row['Length']}{$row['LengthUnit']} x
                                       {$row['Width']}{$row['WidthUnit']} x
                                       {$row['Height']}{$row['HeightUnit']}";

                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['FilterCode'] ?? 'N/A') . "</td>";
                        echo "<td>" . htmlspecialchars($row['PartNumber'] ?? 'N/A') . "</td>";
                        echo "<td>" . htmlspecialchars($row['FilterName'] ?? 'N/A') . "</td>";
                        echo "<td>" . htmlspecialchars($dimensions) . "</td>";  
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
    </div>
    <?php
}
?>