<?php
require('fpdf/fpdf.php');
include("connect.php");

if (isset($_GET['jobOrderNumber'])) {
    $jobOrderNumber = $_GET['jobOrderNumber'];

    $stmt = $conn->prepare("SELECT * FROM order_form WHERE jobOrderNumber = ?");
    $stmt->bind_param("i", $jobOrderNumber);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();

    class PDF extends FPDF {
        function drawLine() {
            $this->Line(10, $this->GetY(), 200, $this->GetY());
            $this->Ln(5);
        }

        function addLabeledField($label, $value, $width = 90) {
            $this->SetFont('Arial', '', 12);
            $this->Cell($width, 10, $label . ': ' . $value, 0, 0);
        }

        function addUnderlineField($label, $value = '') {
            $this->SetFont('Arial', '', 12);
            $this->Cell(50, 10, $label . ':', 0, 0);
            $this->Cell(0, 10, $value, 0, 1);
        }
    }

    $pdf = new PDF();
    $pdf->AddPage();

    // Job Order Number
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(50, 10, 'Job Order No.: ' . $order['jobOrderNumber'], 0, 1);

    $pdf->SetFont('Arial', '', 12);

    $colWidth = 95;
    $rowHeight = 10;

    // Company & Quantity
    $pdf->Cell($colWidth, $rowHeight, 'Company: ' . $order['company'], 'TB', 0, 'L');
    $pdf->Cell($colWidth, $rowHeight, 'Quantity: ' . $order['quantity'], 'TBL', 1, 'L');

    // Items & Required Date
    $pdf->Cell($colWidth, $rowHeight, 'Items: ' . $order['items'], 'TB', 0, 'L');
    $pdf->Cell($colWidth, $rowHeight, 'Required Date: ' . $order['requiredDate'], 'TBL', 1, 'L');

    $pdf->drawLine();

    // Descriptions
    $pdf->SetFont('Arial', 'B', 13);
    $pdf->Cell(0, 10, 'Descriptions:', 0, 1);
    $pdf->SetFont('Arial', '', 12);

    $pdf->addUnderlineField('Cap', $order['cap']);
    $pdf->addUnderlineField('Size', $order['size']);
    $pdf->addUnderlineField('Gasket', $order['gasket']);
    $pdf->addUnderlineField('O-Ring', $order['oring']);
    $pdf->addUnderlineField('Filter Media', $order['filterMedia']);
    $pdf->addUnderlineField('Inside Support', $order['insideSupport']);
    $pdf->addUnderlineField('Outside Support', $order['outsideSupport']);
    $pdf->addUnderlineField('Brand', $order['brand']);

    $pdf->drawLine();

    // Filter Drawing
    $pdf->SetFont('Arial', 'B', 13);
    $pdf->Cell(0, 10, 'Filter Drawing:', 0, 1);
    $pdf->SetFont('Arial', '', 12);

    if (!empty($order['filterDrawing'])) {
        $imgPath = 'temp_image.jpg';
        file_put_contents($imgPath, $order['filterDrawing']);
        $pdf->Ln(10);
        $pdf->Image($imgPath, null, null, 100);
        unlink($imgPath);
    }

    // Footer
    $pdf->SetY(-40);
    $pdf->SetFont('Arial', '', 12);
    $columnWidth = 95;
    $height = 10;

    $pdf->Cell($columnWidth, $height, 'Sales by:', 'TB', 0, 'L');
    $pdf->Cell($columnWidth, $height, 'Approved by:', 'TBL', 1, 'L');

    $pdf->Output('D', $order['jobOrderNumber'] . '.pdf');

} else {
    echo "No job order number provided.";
    exit;
}
?>
