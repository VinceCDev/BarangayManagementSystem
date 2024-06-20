<?php
require('fpdf186/fpdf.php');
require('fpdi2/src/autoload.php'); // Ensure the correct path to FPDI autoload

use setasign\Fpdi\Fpdi;

// Fetch data from the database
include 'connection.php'; // Your connection.php file

// Suppress warnings
error_reporting(E_ALL & ~E_NOTICE);

// Check if the request ID and action are set
if (isset($_GET['id']) && isset($_GET['action'])) {
    $requestId = $_GET['id'];
    $action = $_GET['action'];

    // Fetch document request details
    $sql = "SELECT dr.certificate_id, dr.fullName, dr.address, dr.dob, dr.placeOfBirth, dr.civilStatus, dr.sex, dr.purpose, dr.business, dr.request_date,
       YEAR(CURDATE()) - YEAR(dr.dob) - (DATE_FORMAT(CURDATE(), '%m%d') < DATE_FORMAT(dr.dob, '%m%d')) AS age
       FROM document_requests dr
       WHERE dr.id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $requestId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $certificateId = $row['certificate_id'];

        // Fetch the template file and name from the certificates table
        $templateSql = "SELECT file, certificate_name FROM certificates WHERE id = ?";
        $templateStmt = $conn->prepare($templateSql);
        $templateStmt->bind_param('i', $certificateId);
        $templateStmt->execute();
        $templateResult = $templateStmt->get_result();

        if ($templateResult->num_rows > 0) {
            $templateRow = $templateResult->fetch_assoc();
            $templateFile = $templateRow['file'];
            $certificateName = $templateRow['certificate_name'];

            // Create instance of FPDI
            $pdf = new FPDI();

            // Add a page
            $pdf->AddPage();

            // Set the source PDF file
            $pdf->setSourceFile('uploads/' . $templateFile); // Use the file column from the database

            // Import page 1
            $tplIdx = $pdf->importPage(1);

            // Use the imported page and adjust the position
            $pdf->useTemplate($tplIdx, 0, 0, 210);

            // Set font
            $pdf->SetFont('Arial', '', 12);

            // Add dynamic content at the correct positions based on certificate name
            if ($certificateName == 'Certificate of Residency') {
                 $pdf->SetFont('times', '', 12);
                
                $pdf->SetXY(80, 110);
                $pdf->Cell(0, 10, $row['fullName'], 0, 1);

                if (isset($row['age'])) {
                    $pdf->SetXY(115, 110);
                    $pdf->Cell(0, 10, $row['age'], 0, 1);
                }
                
                $requestDate = date('Y-m-d', strtotime($row['request_date'])); // Extract date

                $requestMonth = date('F', strtotime($row['request_date']));
                
                $requestDay = date('d', strtotime($row['request_date'])); // Extract day of the month

$pdf->SetXY(58.5, 125);
$pdf->Cell(0, 10, $requestDay, 0, 1); // Display day of the month

                
                $pdf->SetXY(78.5, 125);
                $pdf->Cell(0, 10, $requestMonth, 0, 1); // Display month

                
            } elseif ($certificateName == 'Certificate of Indigency') {
                $pdf->SetXY(100, 107);
                $pdf->Cell(0, 10, $row['fullName'], 0, 1);

                if (isset($row['age'])) {
                    $pdf->SetXY(84, 115);
                    $pdf->Cell(0, 10, $row['age'], 0, 1);
                }

                $pdf->SetXY(135, 115);
                $pdf->Cell(0, 10, $row['civilStatus'], 0, 1);
                
                $requestDate = date('Y-m-d', strtotime($row['request_date']));

                // Add the request date
                $pdf->SetXY(143, 56);
                $pdf->Cell(0, 10, $requestDate, 0, 1);
            } elseif ($certificateName == 'Business Clearance') {
                $pdf->SetFont('Arial', 'B', 11);
                
                $pdf->SetXY(100, 81);
                $pdf->Cell(0, 10, $row['fullName'], 0, 1);

                $pdf->SetXY(110, 86);
                $pdf->Cell(0, 10, $row['address'], 0, 1);

                $pdf->SetXY(160, 92.5);
                $pdf->Cell(0, 10, $row['business'], 0, 1);
            } elseif ($certificateName == 'Barangay Clearance') {
                $pdf->SetXY(50, 102);
                $pdf->Cell(0, 10, $row['fullName'], 0, 1);

                $pdf->SetXY(50, 109);
                $pdf->Cell(0, 10, $row['address'], 0, 1);

                $pdf->SetXY(55, 115);
                $pdf->Cell(0, 10, $row['dob'], 0, 1);

                $pdf->SetXY(55, 121);
                $pdf->Cell(0, 10, $row['placeOfBirth'], 0, 1);

                $pdf->SetXY(153, 115);
                $pdf->Cell(0, 10, $row['civilStatus'], 0, 1);

                $pdf->SetXY(153, 121);
                $pdf->Cell(0, 10, $row['sex'], 0, 1);
                
                $requestDate = date('Y-m-d', strtotime($row['request_date']));

                // Add the request date
                $pdf->SetXY(145, 65);
                $pdf->Cell(0, 10, $requestDate, 0, 1);
            }

            // Generate the PDF filename based on the certificate name
            $pdfFilename = str_replace(' ', '_', $certificateName) . '.pdf';

            // Output to the browser or save to a file
            if ($action == 'view') {
                $pdf->Output('I', $pdfFilename); // Output to browser for viewing
            } else {
                $pdf->Output('D', $pdfFilename); // Output as a download
            }
        } else {
            echo "No certificate template found";
        }
    } else {
        echo "No results found";
    }

    $conn->close();
} else {
    echo "Invalid request";
}
?>