<?php
session_start();

require('./fpdf/fpdf.php');
include("connect.php");

// Create PDF instance
$pdf = new FPDF('L', 'mm', 'Legal');
$pdf->AddPage();

// Set global font for the header
$pdf->SetFont('Arial', 'B', 16);

// Add the main header: "Enterfil Industrial Products"
$pdf->Cell(0, 10, 'Enterfil Industrial Products', 0, 1, 'C');

// Add the title: "Inventory Report"
$pdf->SetFont('Arial', 'B', 14);  // Smaller font for the title
$pdf->Cell(0, 10, 'Inventory Report', 0, 1, 'C');

// Add the current date
$pdf->SetFont('Arial', '', 12);   // Regular font for the date
$currentDate = date('F j, Y');    // Format: Month day, Year (e.g., December 10, 2024)
$pdf->Cell(0, 10, 'As of ' . $currentDate, 0, 1, 'C');

// Add a line break after the header
$pdf->Ln(10); // 10mm space after the header

// Set global font for table
$pdf->SetFont('Arial', 'B', 12);

// Define styles
$headerBackgroundColor = [125, 125, 235]; // RGB for header background
$headerTextColor = [255, 255, 255];       // White text for header
$borderColor = [221, 221, 221];           // Light gray border
$fontSizeHeader = 11;                     // Header font size
$fontSizeBody = 9;                        // Body font size

// Add Table Headers
$pdf->SetFillColor(...$headerBackgroundColor);
$pdf->SetTextColor(...$headerTextColor);
$pdf->SetDrawColor(...$borderColor);

$headers = ['OEM Code', 'Part Number', 'Filter Name', 'Dimensions', 'Quantity', 'MAX Stock', 'LOW Stock Signal'];
$columnWidths = [30, 50, 70, 50, 20, 40, 50]; // Adjust column widths to match CSS

// Get the page width
$pageWidth = $pdf->GetPageWidth();

// Calculate the total width of the table
$tableWidth = array_sum($columnWidths);

// Calculate the X position to center the table
$xPosition = ($pageWidth - $tableWidth) / 2; 

$pdf->SetX($xPosition); // Set the X position to center the table

// Add header row
foreach ($headers as $index => $col) {
    $pdf->Cell($columnWidths[$index], 10, $col, 1, 0, 'C', true); // Center aligned
}
$pdf->Ln();

// Reset text color for body
$pdf->SetTextColor(0, 0, 0);

// Fetch and display table data
$sql = "SELECT * FROM filters";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $pdf->SetFont('Arial', '', $fontSizeBody);
    while ($row = $result->fetch_assoc()) {
        $dimensions = "{$row['Length']}{$row['LengthUnit']} x {$row['Width']}{$row['WidthUnit']} x {$row['Height']}{$row['HeightUnit']}";

        $data = [
            $row['FilterCode'] ?? 'N/A',
            $row['PartNumber'] ?? 'N/A',
            $row['FilterName'] ?? 'N/A',
            $dimensions,
            $row['Quantity'] ?? 'N/A',
            $row['MaxStock'] ?? 'N/A',
            $row['LowStockSignal'] ?? 'N/A',
        ];

        // Set X position to continue from the previous column positions
        $pdf->SetX($xPosition);

        // Center align all columns
        foreach ($data as $index => $cell) {
            $align = 'C'; // Center alignment for all columns

            // Check if the column is "Quantity" and apply specific colors
            if ($index == 4) { // Quantity column
                if ($row['Quantity'] <= $row['LowStockSignal']) {
                    $pdf->SetFillColor(245, 66, 66); // Red for low stock
                } elseif ($row['Quantity'] < $row['MaxStock'] / 2) {
                    $pdf->SetFillColor(255, 225, 53); // Yellow for medium stock
                } else {
                    $pdf->SetFillColor(128, 255, 128); // Green for high stock
                }
            } else {
                $pdf->SetFillColor(255, 255, 255); // White for all other cells
            }

            $pdf->Cell($columnWidths[$index], 10, $cell, 1, 0, $align, true); // Center aligned
        }
        $pdf->Ln();
    }
} else {
    $pdf->Cell(array_sum($columnWidths), 10, 'No filters found', 1, 1, 'C');
}

// Close connection
$conn->close();

// Output the PDF
$pdf->Output('D', 'filters_table.pdf'); // Download the file
?>
