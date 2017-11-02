<?php
/*
This Code draw out individual PDF (Annual Totals Summary)
@ Yining Huang
*/

require('../FPDF/fpdf.php');
class myPDF extends FPDF {
    private $isSummaryPage = TRUE;
    private $lastName = "";
    private $firstname = "";
    private $middleName = "";
    private $certid = 0;

    function header() {
        if ($this->isSummaryPage == TRUE) { // if on summary page
            if ($this->PageNo() == 1) { // if on cover page, add logo
                 // Add logo
                $this->Image('../../img/Logo.gif',10,8,-270);

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
        else { // if on yearly detailed pages
            $width=$this -> w; // Width of Current Page
            $height=$this->getY()+5;
            $this->SetLineWidth(0.5);
            $this->SetDrawColor(0,0,0);
            $this->Line(10,$height,$width-10,$height); // Line one Cross

            $this->SetFont('Arial','B',10.5);
            $this->SetTextColor(0,0,0);
            $this->Cell(0,5,"Last Name: ".$this->lastName,0,0,'L');
            $this->ln(0);
            $this->Cell(70);
            $this->Cell(0,5,"First Name: ".$this->firstName,0,0,'L');
            $this->ln(0);
            $this->Cell(140);
            $this->Cell(0,5,"Middle Name: ".$this->middleName,0,0,'L');
            $this->ln(0);
            $this->Cell(0,5,"Cert.No: ".$this->certid,0,0,'R');
             $this->ln();
            // Add Titles
            $this->SetFont('Arial','',11);
            $this->SetTextColor(0,0,0);
            $this->ln();
            $this->Cell(0,0,'Completion Date');
            $this->Ln();
            $this->Cell(38);
            $this->Cell(0,0,'Courses Taken');
            $this->Ln();
            $this->Cell(186);
            $this->Cell(0,0,'Location');
            $this->ln();
            $this->Cell(232);
            $this->Cell(0,0,'Grade');
            $this->ln();
            $this->Cell(0,0,'Hours Earned',0,0,'R');

            $width=$this -> w; // Width of Current Page
            $height=$this->getY();
            $this->SetLineWidth(0.5);
            $this->SetDrawColor(0,0,0);
            $this->Line(10,$height+5,$width-10,$height+5); // Line one Cross
            $this->ln(10);
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
        $this->certid = $certid;

/////////////////////////////////////////////////////////////////////////////////////////
// ********************       Start of Personal Information          ********************

        // Query from [New_Employee] table
        $lastName = ""; $firstName = ""; $middleName = ""; //$county = "";
        $temp_certDate = ""; $perm_certDate = ""; $adv_certDate = "";
        $status = ""; // active or not
        $specialty = "";

        // Query from [New_CertHistory] table
        $certType ="";
        $allowedcarryover="";


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
            $this->lastName = $lastName;
            $this->firstName = $firstName;
            $this->middleName = $middleName;
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
        $this->Cell(0,0,'Temporary Cert. Date: '."11/22/1029");
        //$this->Cell(0,0,'Temporary Cert. Date: '.$temp_certDate);
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

        for ($i = 0; $i < 100; $i ++) {
            $this->Cell(0,0,'Active');
            $this->Ln();
            $this->Cell(20);
            $this->Cell(0,0,'Temporary');
            $this->Ln();
            $this->Cell(50);
            $this->Cell(0,0,'2017-2019');
            $this->Ln();
            $this->Cell(96,0,$i,0,0,'R');
            $this->Ln();
            $this->Cell(121,0,$i,0,0,'R');
            $this->Ln();
            $this->Cell(151,0,'2',0,0,'R');
            $this->Ln();
            $this->Cell(181,0,'0',0,0,'R');
            $this->Ln();
            $this->Cell(204,0,'1',0,0,'R');
            $this->Ln();
            $this->Cell(225,0,'87',0,0,'R');
            $this->Ln();
            $this->Cell(245,0,'12',0,0,'R');
            $this->Ln();
            $this->Cell(0,0,'15',0,0,'R');
            $this->Ln(5);
        }

        /*
        // Get Certification Type and Allowed carry over
        // from [New_CertHistory] table
        $year1 = $GLOBALS["fromYearInt"];
        $year2 = $GLOBALS["toYearInt"]+1;
        $year_across = "(FiscalYear='".(string)$year1."-".(string)($year1+1)."'";
        for ($i = $year1+1; $i < $year2; $i ++) {
            $year_across = $year_across." OR CourseYear='".(string)$i."-".(string)($i+1)."'";
        }
        $year_across = $year_across.")";
        $tsql = "SELECT * FROM [New_CertHistory] WHERE CertNo=".(string)$certid."AND".$year_across.
                "INNER JOIN [New_CarryoverLimits] ON New_CertHistory.Status=New_CarryoverLimits.Status";
        $stmt = sqlsrv_query( $conn, $tsql);
        if( $stmt === false )
        {
             echo "Error in executing query98.</br>";
             die( print_r( sqlsrv_errors(), true));
        }
        else {

        }
        sqlsrv_free_stmt($stmt);
        */
        // // Fill in data for personal information
        // $this->SetFont('Arial','B',12);
        // $this->SetTextColor(0,0,0);
        // $this->Cell(500,5,$status,0,0,'C');

        // $this->Ln(10);
        // $this->SetFont('Arial','',11);
        // $this->SetTextColor(0,0,0);
        // $this->Cell(0,0,'Name:  ');
        // $this->SetFont('Arial','B',11);
        // $this->Ln(0);
        // $this->Cell(13);
        // $this->Cell(0,0,$lastName.', '.$firstName);
        // $this->Ln();
        // $this->SetFont('Arial','',11);
        // $this->Cell(274,0,'Employee Number:',0,0,'C');
        // $this->Ln();
        // $this->Cell(255,0,'Certificate Number:',0,0,'R');
        // $this->Cell(0,0,$certid,0,0,'R');

        // $this->Ln(5);
        // $this->Cell(0,0,'Title:');
        // $this->Ln();
        // $this->Cell(250,0,'Item:',0,0,'C');
        // $this->Ln();
        // $this->Cell(338.5,0,'PayLocation:',0,0,'C');
        // $this->Ln();
        // $this->Cell(249.5,0,'Certificate Date:',0,0,'R');
        // $this->Cell(0,0,$certDate,0,0,'R');

        // $this->Ln(5);
        // $this->Cell(332.5,0,'Specialty:',0,0,'C');
        // $this->Ln(0);
        // $this->Cell(375,0,$specialty,0,0,'C');

        // $this->Ln();
        // $this->Cell(250,0,'Certificate Type:',0,0,'R');
        // $this->Cell(0,0,$certType,0,0,'R');

        $this->isSummaryPage = FALSE;

// ********************       End of Summary Page          ********************        //
/////////////////////////////////////////////////////////////////////////////////////////


/////////////////////////////////////////////////////////////////////////////////////////
// ********************       Start of Yearly Details          ********************
        $this->AddPage();

        $this->SetFont('Arial','',11);
        $this->SetTextColor(0,0,0);
        for ($i = 0; $i < 100; $i ++) {
            $this->Cell(0,0,'11/10/2016');
            $this->Ln();
            $this->Cell(38);
            $this->Cell(0,0,'VALUATION AND ASSESSMENT CONCEPTS FOR SERVICE STATIONS');
            $this->Ln();
            $this->Cell(186);
            $this->Cell(0,0,'VENTURA');
            $this->ln();
            $this->Cell(232);
            $this->Cell(0,0,'Passed');
            $this->ln();
            $this->Cell(0,0,'14',0,0,'R');
            $this->ln(5);
        }


        // Get Course Name, End Date, Grade and Hours Earned from
        // from [New_CourseDetail]  (Location NA)
        $tsql = "SELECT * FROM [New_CourseDetail] WHERE CertNo=".(string)$certid;
        $stmt = sqlsrv_query($conn, $tsql);
        if( $stmt === false )
        {
             echo "Error in executing query68.</br>";
             die( print_r( sqlsrv_errors(), true));
        }
        else {
            $associativeArray = array(array());
            while($row = sqlsrv_fetch_array($stmt)){
                $temp_fiscalYear = (int)substr($row['FiscalYear'], 0, 4);
                if ($temp_fiscalYear >= $fromYearInt)
                    if ($temp_fiscalYear <= $toYearInt)
                        $associativeArray[$row['FiscalYear']][] = $row;
            }
            for ($i = 0; $i < sizeof($associativeArray); $i ++) {
                $total_hours = 0;
                $temp_array = $associativeArray[$i];
                // Draw the Italic Fical Year title
                //$year_title = $temp_array['FiscalYear'];
                //$this->Cell...

                $this->ln(5);

                for ($j = 0; $j < sizeof($temp_array); $j ++) { // Details for this year
                    $row = $temp_array[$j];
                    // End Date
                    // $row['EndDate']

                    // Course Name
                    // $row['CourseName']

                    // Location
                    // $row['CourseLocation']

                    // Grade
                    // $row['CourseGrade']

                    // Hours Earned
                    // $row['HoursEarned']
                    //$total_hours += $row['HoursEarned'];
                }

                // Total Hours

                // Draw a Line


            }
        }
        sqlsrv_free_stmt($stmt);




// ********************       End of Yearly Details          ********************
/////////////////////////////////////////////////////////////////////////////////////////



/*
            // Swith to next line and add an "-" if the course name is too long
            $x_begin=$this -> getX();
            $y_begin=$this -> getY();
            $x_temp = $x_begin;
            $y_temp = $y_begin;
            if ((strlen($Course) <= 37)) {
                $this->Cell(0,0,$Course);
                $this->Ln();
            }
            else {
                while (strlen($Course) > 37) {
                    $x_temp = $this -> getX();
                    $y_temp = $this -> getY();
                    if (strlen($Course) >= 37)
                        $temp_coursename = substr($Course, 0,37);
                    else
                        $temp_coursename = substr($Course, 0,strlen($Course) );
                    $Course = str_replace($temp_coursename,"",$Course);
                    if (strlen($Course) >0) $Course = "-".$Course;
                    $this->Cell(0,0,$temp_coursename);
                    $this->Ln();
                    $this->SetXY($x_temp, $y_temp+5);
                }
                $y_temp += 5;
                $this->SetXY($x_temp,$y_temp);
                $this->Cell(0,0,$Course);
                $this->Ln();
            }
            $this->SetXY($x_begin, $y_begin);
            $this->Ln();
            $this->Cell(0,0,'NA',0,0,'C');
            $this->Ln();
            $this->Cell(260,0,$HoursEarned,0,0,'R');
            $this->SetXY($x_begin,$y_temp);
        }

        }

        $y=$this -> getY(); // Height of Current Page
        if ($y >= 180) {    // Force Page Break if too low on page
            $this->AddPage();
            $this->ln(10);
        }

*/

    }

    function footer(){
        $this->SetY(-15);
        $this->SetFont('Arial','',8);
        $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'R');
    }
}
?>
