<?php
require('fpdf/fpdf.php');
include("connect.php");

if (isset($_POST['jobOrderNumber'])) {
    $jobOrderNumber = $_POST['jobOrderNumber'];

    $stmt = $conn->prepare("SELECT * FROM order_form WHERE jobOrderNumber = ?");
    $stmt->bind_param("i", $jobOrderNumber);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();

    class PDF extends FPDF {
        function drawLine() {
            $this->Line(10, $this->GetY(), 200, $this->GetY()); // from left to right margin
            $this->Ln(5);
        }

        function addLabeledField($label, $value, $width = 90) {
            $this->SetFont('sans-serif', '', 12);
            $this->Cell($width, 10, $label . ': ' . $value, 0, 0);
        }

        function addUnderlineField($label, $value = '') {
            $this->SetFont('sans-serif', '', 12);
            $this->Cell(50, 10, $label . ':', 0, 0);
            $this->Cell(0, 10, '_______________________________________________________', 0, 1);
        }
    }

    $pdf = new PDF();
    $pdf->AddPage();

    // Job Order Number with line
    $pdf->SetFont('sans-serif', 'B', 16);
    $pdf->Cell(50, 10, 'Job Order No.: ' . $order['jobOrderNumber'], 0, 1);

    $pdf->SetFont('sans-serif', '', 12);

    // Set widths
    $colWidth = 95;
    $rowHeight = 10;
    
    // Row 1: Company & Quantity
    $pdf->Cell($colWidth, $rowHeight, 'Company: ' . $order['company'], 'TB', 0, 'L');
    $pdf->Cell($colWidth, $rowHeight, 'Quantity: ' . $order['quantity'], 'TBL', 1, 'L');
    
    // Row 2: Items & Required Date
    $pdf->Cell($colWidth, $rowHeight, 'Items: ' . $order['items'], 'TB', 0, 'L');
    $pdf->Cell($colWidth, $rowHeight, 'Required Date: ' . $order['requiredDate'], 'TBL', 1, 'L');
    

    $pdf->drawLine();

    // Descriptions section
    $pdf->SetFont('sans-serif', 'B', 13);
    $pdf->Cell(0, 10, 'Descriptions:', 0, 1);
    $pdf->SetFont('sans-serif', '', 12);

    $pdf->addUnderlineField('Cap');
    $pdf->addUnderlineField('Size');
    $pdf->addUnderlineField('Gasket');
    $pdf->addUnderlineField('O-Ring');
    $pdf->addUnderlineField('Filter Media');
    $pdf->addUnderlineField('Inside Support');
    $pdf->addUnderlineField('Outside Support');
    $pdf->addUnderlineField('Brand');

    $pdf->drawLine();

    $pdf->SetFont('sans-serif', 'B', 13);
    $pdf->Cell(0, 10, 'Filter Drawing:', 0, 1);
    $pdf->SetFont('sans-serif', '', 12);

    // Drawing section
    if (!empty($order['filterDrawing'])) {
        $imgPath = 'temp_image.jpg';
        file_put_contents($imgPath, $order['filterDrawing']);
        $pdf->Ln(10);
        $pdf->SetFont('sans-serif', 'B', 13);
        $pdf->Cell(0, 10, 'Filter Drawing', 0, 1, 'C');
        $pdf->Image($imgPath, null, null, 100);
        unlink($imgPath);
    }

    // Move to near the bottom of the page
    $pdf->SetY(-40); // 40mm from bottom
    $pdf->SetFont('sans-serif', '', 12);

    // Width of each column (page width - margins = ~190mm)
    $columnWidth = 95;
    $height = 10; // Height of each box

    // Sales by
    $pdf->Cell($columnWidth, $height, 'Sales by:', 'TB', 0, 'L');

    // Approved by
    $pdf->Cell($columnWidth, $height, 'Approved by:', 'TBL', 1, 'L');


    $pdf->Output('D', $order['jobOrderNumber'] . '.pdf');

} else {
    echo "No job order number provided.";
    exit;
}
?>
