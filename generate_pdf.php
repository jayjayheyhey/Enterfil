<?php
session_start();

require('./fpdf/fpdf.php');
include("connect.php");

// Create PDF instance
$pdf = new FPDF();
$pdf->AddPage();

// Set global font
$pdf->SetFont('Arial', 'B', 12);

// Define styles
$headerBackgroundColor = [125, 125, 235]; // RGB for header background
$headerTextColor = [255, 255, 255];       // White text for header
$borderColor = [221, 221, 221];           // Light gray border
$fontSizeHeader = 12;                     // Header font size
$fontSizeBody = 10;                       // Body font size

// Add Table Headers
$pdf->SetFillColor(...$headerBackgroundColor);
$pdf->SetTextColor(...$headerTextColor);
$pdf->SetDrawColor(...$borderColor);

$headers = ['OEM Code', 'Part Number', 'Filter Name', 'Dimensions', 'Quantity', 'Max Stock', 'Low Stock Signal'];
$columnWidths = [30, 30, 50, 50, 20, 20, 30]; // Adjust column widths to match CSS

foreach ($headers as $index => $col) {
    $pdf->Cell($columnWidths[$index], 10, $col, 1, 0, 'C', true);
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

        // Determine row background color based on stock levels
        if ($row['Quantity'] <= $row['LowStockSignal']) {
            $pdf->SetFillColor(245, 66, 66); // Red for low stock
        } elseif ($row['Quantity'] < $row['MaxStock'] / 2) {
            $pdf->SetFillColor(255, 225, 53); // Yellow for medium stock
        } else {
            $pdf->SetFillColor(128, 255, 128); // Green for high stock
        }

        $data = [
            $row['FilterCode'] ?? 'N/A',
            $row['PartNumber'] ?? 'N/A',
            $row['FilterName'] ?? 'N/A',
            $dimensions,
            $row['Quantity'] ?? 'N/A',
            $row['MaxStock'] ?? 'N/A',
            $row['LowStockSignal'] ?? 'N/A',
        ];

        foreach ($data as $index => $cell) {
            $align = ($index == 4) ? 'C' : 'L'; // Align Quantity to center
            $pdf->Cell($columnWidths[$index], 10, $cell, 1, 0, $align, true);
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
