<?php
/*
This code renders the individual PDF (Annual Totals Summary)
Given a specific Cert No
@ Yining Huang
*/

require('../../FPDF/fpdf.php');
class myPDF extends FPDF {
    function header() {
        if ($this->PageNo() == 1) { // if on cover page, add logo
             // Add logo
            $this->Image('../../../img/Logo.gif',10,8,-270);

            // Add Titles
            $this->SetFont('Arial','B',12);
            $this->SetTextColor(0,0,128);
            $this->Cell(0,5,'OFFICE OF THE ASSESSOR',0,0,'C');
            $this->Ln();
            $this->Cell(0,5,'Appraiser Annual Totals Calculation',0,0,'C');
            $this->Ln(10);

            // Draw a line
            $width=$this -> w; // Width of Current Page
            $height=$this -> h; // Height of Current Page
            $this->SetLineWidth(0.7);
            $this->SetDrawColor(162,157,150);
            $this->Line(10, 30,$width-10,30); // Line one Cross
        }
        else { // if summary not finish, add columns titles to the next page
            $this->ln(10);
            $this->addSummaryTitles();
        }

    }

    // Helper function to draw out summary's sub titles
    function addSummaryTitles() {
        $width=$this -> w; // Width of Current Page
        $height=$this->getY();
        $this->SetLineWidth(0.5);
        $this->SetDrawColor(0,0,0);
        $this->Line(10,$height-10,$width-10,$height-10); // Line one Cross
        // Add Titles
        $this->SetFont('Arial','',11);
        $this->SetTextColor(0,0,0);

        $this->Cell(0,0,'Status');
        $this->Ln();
        $this->Cell(20);
        $this->Cell(0,0,'Cert. Type');
        $this->Ln();
        $this->Cell(50);
        $this->Cell(0,0,'Fiscal Year');
        $this->ln(-5);
        $this->Cell(82);
        $this->Cell(0,0,'Earned');
        $this->ln(5);
        $this->Cell(84);
        $this->Cell(0,0,'Hours');
        $this->ln(-5);
        $this->Cell(103.5);
        $this->Cell(0,0,'Required');
        $this->ln(5);
        $this->Cell(109);
        $this->Cell(0,0,'Hours');
        $this->ln(-5);
        $this->Cell(127);
        $this->Cell(0,0,'Current Year');
        $this->ln(5);
        $this->Cell(129.5);
        $this->Cell(0,0,'Over/Under');
        $this->ln(-5);
        $this->Cell(161.5);
        $this->Cell(0,0,'Prior Year');
        $this->ln(5);
        $this->Cell(159);
        $this->Cell(0,0,'Over/Under');
        $this->ln(-5);
        $this->Cell(187);
        $this->Cell(0,0,'Carry To');
        $this->ln(5);
        $this->Cell(189);
        $this->Cell(0,0,'Year +1');
        $this->ln(-5);
        $this->Cell(207);
        $this->Cell(0,0,'Carry To');
        $this->ln(5);
        $this->Cell(209);
        $this->Cell(0,0,'Year +2');
        $this->ln(-5);
        $this->Cell(227);
        $this->Cell(0,0,'Carry To');
        $this->ln(5);
        $this->Cell(229);
        $this->Cell(0,0,'Year +3');
        $this->ln(-5);
        $this->Cell(0,0,'Carry',0,0,'R');
        $this->ln(5);
        $this->Cell(0,0,'Forward Total',0,0,'R');

        $width=$this -> w; // Width of Current Page
        $height=$this->getY();
        $this->SetLineWidth(0.5);
        $this->SetDrawColor(0,0,0);
        $this->Line(10,$height+5,$width-10,$height+5); // Line one Cross
        $this->ln(10);
    }


    function generate($conn){

        // Get all related personal information
        $certid = $GLOBALS['certid'];

/////////////////////////////////////////////////////////////////////////////////////////
// ********************       Start of Personal Information          ********************

        // Query from [New_Employee] table
        $lastName = ""; $firstName = ""; $middleName = ""; //$county = "";
        $temp_certDate = ""; $perm_certDate = ""; $adv_certDate = "";
        $specialty = "";
        $status = ""; // active or not

        // Get Names, Temp/Perm Certification Date, status and specialty
        // from [New_Employee]
        $tsql = "SELECT * FROM [New_Employee] WHERE CertNo=".(string)$certid;
        $stmt = sqlsrv_query( $conn, $tsql);
        if( $stmt === false )
        {
             echo "Error in executing query68.</br>";
             die( print_r( sqlsrv_errors(), true));
        }
        else {
            $row= sqlsrv_fetch_array($stmt);
            $lastName = $row['LastName'];
            $firstName = $row['FirstName'];
            $middleName = $row['MiddleName'];
            $status = $row['CurrentStatus'];
            $specialty = $row['Auditor'];
            if ($specialty == "True") {
              $specialty = "Audit";
            } else {
              $specialty = "";
            }
            if ($row['TempCertDate'] == NULL) {
              $temp_certDate = "NA";
            } else {
              $temp_certDate = date("m/d/Y",strtotime($row['TempCertDate']));
            }
            if ($row['PermCertDate'] == NULL) {
              $perm_certDate = "NA";
            } else {
              $perm_certDate = date("m/d/Y",strtotime($row['PermCertDate']));
            }
            if ($row['AdvCertDate'] == NULL) {
              $adv_certDate = "NA";
            } else {
              $adv_certDate = date("m/d/Y",strtotime($row['AdvCertDate']));
            }
        }
        sqlsrv_free_stmt($stmt);

        $this->SetFont('Arial','B',10.5);
        $this->SetTextColor(0,0,0);
        $this->Cell(500,5,$status,0,0,'C');

        $this->Ln(10);
        $this->SetTextColor(0,0,0);
        $this->Cell(0,0,'Last Name: '.$lastName);
        $this->Ln();

        $this->Cell(65);
        $this->Cell(0,0,'First Name: '.$firstName);
        $this->Ln();
        $this->Cell(135);
        $this->Cell(0,0,'Middle Name: '.$middleName);
        $this->Ln();
        $this->Cell(255,0,'Certificate Number: '.$certid,0,0,'R');

        $this->Ln(5);
        $this->Cell(0,0,'County:    19 LOS ANGELES');

        $this->Ln(5);
        $this->Cell(0,0,'Temporary Cert. Date: '.$temp_certDate);
        $this->Ln();
        $this->Cell(65);
        $this->Cell(0,0,'Permanent Cert. Date: '.$perm_certDate);
        $this->Ln();
        $this->Cell(135);
        $this->Cell(0,0,'Advanced Cert. Date: '.$adv_certDate);

        $this->Ln();
        $this->Cell(257,0,'Specialty: '.$specialty,0,0,'R');

// ********************       End of Personal Information          ********************//
/////////////////////////////////////////////////////////////////////////////////////////


/////////////////////////////////////////////////////////////////////////////////////////
// ********************       Start of Summary Page          ********************      //

        // Add Summary Titles
        $this->Ln(14);
        $this->addSummaryTitles();

        $this->SetFont('Arial','',11);
        $this->SetTextColor(0,0,0);
        
        // Get Certification Type and Allowed carry over
        // from [New_CertHistory] table
        $tsql = "SELECT * FROM [New_CertHistory] WHERE CertNo=".(string)$certid.
                "ORDER BY CertYear";
        $stmt = sqlsrv_query( $conn, $tsql);
        if( $stmt === false )
        {
             echo "Error in executing query98.</br>";
             die( print_r( sqlsrv_errors(), true));
        }
        else {
            while($row = sqlsrv_fetch_array($stmt)){
                $this->SetFont('Arial','',11);
                $this->SetTextColor(0,0,0);
                $this->Cell(0,0,$row['Status']);
                $this->Ln();
                $this->Cell(20);
                $this->Cell(0,0,$row['CertType']);
                $this->Ln();
                $this->Cell(50);
                $this->Cell(0,0,$row['CertYear']);
                $this->Ln();
                $this->Cell(96,0,$row['HoursEarned'],0,0,'R');
                $this->Ln();
                $this->Cell(121,0,$row['RequiredHours'],0,0,'R');
                $this->Ln();
                $this->Cell(151,0,$row['CurrentYearBalance'],0,0,'R');
                $this->Ln();
                $this->Cell(181,0,$row['PriorYearBalance'],0,0,'R');
                $this->Ln();
                $this->Cell(204,0,$row['CarryToYear1'],0,0,'R');
                $this->Ln();
                $this->Cell(225,0,$row['CarryToYear2'],0,0,'R');
                $this->Ln();
                $this->Cell(245,0,$row['CarryToYear3'],0,0,'R');
                $this->Ln();
                $this->Cell(0,0,$row['CarryForwardTotal'],0,0,'R');
                $this->Ln(5);
            }
        }
        sqlsrv_free_stmt($stmt);
        
      


// ********************       End of Summary Page          ********************        //
/////////////////////////////////////////////////////////////////////////////////////////



    }

    function footer(){
        $this->SetY(-15);
        $this->SetFont('Arial','',8);
        $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'R');
    }
}
?>
