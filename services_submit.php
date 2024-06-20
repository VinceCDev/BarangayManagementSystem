<?php
include 'connection.php';

// Use statement must be at the top
require('fpdf186/fpdf.php');
require('fpdi2/src/autoload.php');

use setasign\Fpdi\Fpdi;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $certificate_id = $_POST["certificate_id"];
    $fullName = $_POST["fullName"];
    $age = $_POST["age"];
    $purpose = $_POST["purpose"];
    $address = $_POST["address"];
    $dob = $_POST["dob"];
    $civilStatus = $_POST["civilStatus"];
    $placeOfBirth = $_POST["placeOfBirth"];
    $sex = $_POST["sex"];
    $email = $_POST["email"];
    $business = $_POST["business"];

    // Insert the new document request
    $sql = "INSERT INTO document_requests (certificate_id, fullName, age, purpose, address, dob, civilStatus, placeOfBirth, sex, email, business) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('isissssssss', $certificate_id, $fullName, $age, $purpose, $address, $dob, $civilStatus, $placeOfBirth, $sex, $email, $business);

    if ($stmt->execute()) {
        // Get the last inserted ID
        $request_id = $stmt->insert_id;

        // Fetch the certificate template details
        $templateSql = "SELECT file, certificate_name FROM certificates WHERE id = ?";
        $templateStmt = $conn->prepare($templateSql);
        $templateStmt->bind_param('i', $certificate_id);
        $templateStmt->execute();
        $templateResult = $templateStmt->get_result();

        if ($templateResult->num_rows > 0) {
            $templateRow = $templateResult->fetch_assoc();
            $templateFile = $templateRow['file'];
            $certificateName = $templateRow['certificate_name'];

            // Create instance of FPDI
            $pdf = new FPDI();
            $pdf->AddPage();
            $pdf->setSourceFile('uploads/' . $templateFile);
            $tplIdx = $pdf->importPage(1);
            $pdf->useTemplate($tplIdx, 0, 0, 210);
            $pdf->SetFont('Arial', '', 12);

            // Add dynamic content based on the certificate name
            if ($certificateName == 'Certificate of Residency') {
                $pdf->SetFont('times', '', 12);
                $pdf->SetXY(80, 110);
                $pdf->Cell(0, 10, $fullName, 0, 1);
                $pdf->SetXY(115, 110);
                $pdf->Cell(0, 10, $age, 0, 1);
                $requestDate = date('Y-m-d');
                $requestMonth = date('F');
                $requestDay = date('d');
                $pdf->SetXY(58.5, 125);
                $pdf->Cell(0, 10, $requestDay, 0, 1);
                $pdf->SetXY(78.5, 125);
                $pdf->Cell(0, 10, $requestMonth, 0, 1);
            } elseif ($certificateName == 'Certificate of Indigency') {
                $pdf->SetXY(100, 107);
                $pdf->Cell(0, 10, $fullName, 0, 1);
                $pdf->SetXY(84, 115);
                $pdf->Cell(0, 10, $age, 0, 1);
                $pdf->SetXY(135, 115);
                $pdf->Cell(0, 10, $civilStatus, 0, 1);
                $requestDate = date('Y-m-d');
                $pdf->SetXY(143, 56);
                $pdf->Cell(0, 10, $requestDate, 0, 1);
            } elseif ($certificateName == 'Business Clearance') {
                $pdf->SetFont('Arial', 'B', 11);
                $pdf->SetXY(100, 81);
                $pdf->Cell(0, 10, $fullName, 0, 1);
                $pdf->SetXY(110, 86);
                $pdf->Cell(0, 10, $address, 0, 1);
                $pdf->SetXY(160, 92.5);
                $pdf->Cell(0, 10, $business, 0, 1);
            } elseif ($certificateName == 'Barangay Clearance') {
                $pdf->SetXY(50, 102);
                $pdf->Cell(0, 10, $fullName, 0, 1);
                $pdf->SetXY(50, 109);
                $pdf->Cell(0, 10, $address, 0, 1);
                $pdf->SetXY(55, 115);
                $pdf->Cell(0, 10, $dob, 0, 1);
                $pdf->SetXY(55, 121);
                $pdf->Cell(0, 10, $placeOfBirth, 0, 1);
                $pdf->SetXY(153, 115);
                $pdf->Cell(0, 10, $civilStatus, 0, 1);
                $pdf->SetXY(153, 121);
                $pdf->Cell(0, 10, $sex, 0, 1);
                $requestDate = date('Y-m-d');
                $pdf->SetXY(145, 65);
                $pdf->Cell(0, 10, $requestDate, 0, 1);
            }

            // Define the paths to save the PDFs
            $requestPdfFolder = 'request_pdf/';
            $pdfFolder = '../DMS/request_pdf/';
            
            // Generate the PDF filename based on the certificate name and request ID
            $pdfFilename = str_replace(' ', '_', $certificateName) . '_' . $request_id . '.pdf';

            // Save to the first location ($requestPdfFolder)
            $pdf->Output('F', $requestPdfFolder . $pdfFilename);

            // Save to the second location ($pdfFolder)
            $pdf->Output('F', $pdfFolder . $pdfFilename);

            $fileManagementConn = new mysqli('127.0.0.1:3307', 'root', 'Allen_122', 'file_management_system');
            $insertSql = "INSERT INTO request_file (request_id, request_from, person_requested, person_email, created_date) VALUES (?, ?, ?, ?, ?)";
            $insertStmt = $fileManagementConn->prepare($insertSql);
            $request_from = $pdfFilename; // Assuming the PDF file name is the source of the request
            $person_requested = $fullName;
            $person_email = $email;
            $created_date = date('Y-m-d'); // Assuming created_date is today's date
            $insertStmt->bind_param('issss', $request_id, $request_from, $person_requested, $person_email, $created_date);
            $insertStmt->execute();

            // Close the insert statement
            $insertStmt->close();

            // Redirect after saving the PDF
            header("Location: Certificate.php");
            exit();
        } else {
            echo "No certificate template found";
        }
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>
