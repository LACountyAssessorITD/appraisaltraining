<?php
/*
This Code renders the individual PDF (Completed Course Report)
Given a Cert No. Starting year and End year
@ Yining Huang
*/

require('../../FPDF/fpdf.php');
class myPDF extends FPDF {
    function header() {
        // Add logo
        $this->Image('../../../img/Logo.gif',10,8,-270);

        // Add Titles
        $this->SetFont('Arial','B',12);
        $this->SetTextColor(0,0,128);
        $this->Cell(0,5,'OFFICE OF THE ASSESSOR',0,0,'C');
        $this->Ln();
        $this->Cell(0,5,'Record of Completed Courses Summary',0,0,'C');
        $this->Ln();

        // Add year
        $fromYearInt = $GLOBALS["fromYearInt"];
        $toYearInt = $GLOBALS["toYearInt"];
        $this->Cell(0,5,'FY '.(string)$fromYearInt.'-'.(string)($toYearInt+1),0,0,'C');
        $this->Ln();

        // Draw a lin
        $width=$this -> w; // Width of Current Page
        $height=$this -> h; // Height of Current Page
        $this->SetLineWidth(0.7);
        $this->SetDrawColor(162,157,150);
        $this->Line(10, 30,$width-10,30); // Line one Cross
    }

    function generate($conn){
        $certid = $GLOBALS['certid'];
        $year = $GLOBALS["toYearInt"];

/////////////////////////////////////////////////////////////////////////////////////////
// ********************       Start of Personal Information          ********************

        // Query from [New_Employee] table
        $lastName = ""; $firstName = "";
        $certDate = ""; $status = ""; // active or not
        $specialty = "";
        $certType ="";

        // Get Names, Certification Date, cert type, status and specialty
        $tsql = "SELECT * FROM [New_Employee]
                INNER JOIN [New_CertHistory]
                    ON [New_CertHistory].CertNo = [New_Employee].CertNo
                WHERE [New_Employee].CertNo=".(string)certid.
                    " ORDER BY [New_CertHistory].CertYear DESC";
        $conn = $GLOBALS['conn'];
        $stmt = sqlsrv_query( $conn, $tsql);
        if( $stmt === false )
        {
             echo "Error in executing query68.</br>";
             die( print_r( sqlsrv_errors(), true));
        }
        else {
            $row= sqlsrv_fetch_array($stmt);
            $this->lastName = $row['LastName'];
            $this->firstName = $row['FirstName'];
            $this->status = $row['CurrentStatus'];
            $this->specialty = $row['Auditor'];
            if ($this->specialty == "True") {
              $this->specialty = "Audit";
            } else {
              $this->specialty = "";
            }
            if ($row['PermCertDate'] == NULL) {
              $this->certDate = "NA"; // if not permanet, data shows "NA"
            } else {
              $this->certDate = date("m/d/Y",strtotime($row['PermCertDate']));
            }
            $this->certType = $row['CertType'];
        }
        sqlsrv_free_stmt($stmt);

        // Fill in data for personal information
        $this->SetFont('Arial','B',12);
        $this->SetTextColor(0,0,0);
        $this->Cell(500,5,$this->status,0,0,'C');

        $this->Ln(10);
        $this->SetFont('Arial','',11);
        $this->SetTextColor(0,0,0);
        $this->Cell(0,0,'Name:  ');
        $this->SetFont('Arial','B',11);
        $this->Ln(0);
        $this->Cell(13);
        $this->Cell(0,0,$this->lastName.', '.$this->firstName);
        $this->Ln();
        $this->SetFont('Arial','',11);
        $this->Cell(274,0,'Employee Number:',0,0,'C');
        $this->Ln();
        $this->Cell(255,0,'Certificate Number:',0,0,'R');
        $this->Cell(0,0,$this->certid,0,0,'R');

        $this->Ln(5);
        $this->Cell(0,0,'Title:');
        $this->Ln();
        $this->Cell(250,0,'Item:',0,0,'C');
        $this->Ln();
        $this->Cell(249.5,0,'Certificate Date:',0,0,'R');
        $this->Cell(0,0,$this->certDate,0,0,'R');

        $this->Ln(5);
        $this->Cell(0,0,'Specialty: '.$this->specialty);
        $this->Ln();
        //$this->Cell(30,0,$specialty,0,0,'C');
        $this->Cell(264.5,0,'Pay Location:',0,0,'C');
        $this->Ln();

        $this->Cell(250,0,'Certificate Type:',0,0,'R');
        $this->Cell(0,0,$this->certType,0,0,'R');

        // Draw a line
        $width=$this -> w; // Width of Current Page
        $height=$this -> h; // Height of Current Page
        $this->SetLineWidth(0.7);
        $this->SetDrawColor(0,0,0);
        $this->Line(10,53,$width-10,53); // Line one Cross

// ********************       End of Personal Information          ********************
/////////////////////////////////////////////////////////////////////////////////////////





/////////////////////////////////////////////////////////////////////////////////////////
// ********************       Start of Course Datails          ********************

        // Generate Course Info
        // Add Titles
        $this->SetFont('Arial','B',11);
        $this->SetTextColor(0,0,0);
        $this->Ln(22);

        $year1 = $GLOBALS["fromYearInt"];
        $year2 = $GLOBALS["toYearInt"];
        $this->Cell(263,0,(string)$year1."-".(string)($year2),0,0,'R');
        $this->Ln(5);

        $this->Cell(0,0,'DATE');
        $this->Ln();
        $this->Cell(30);
        $this->Cell(0,0,'COURSE TITLE');
        $this->Ln();
        $this->Cell(0,0,'SOURCE',0,0,'C');
        $this->Ln();
        $this->Cell(260,0,'HOURS',0,0,'R');
        $this->Ln(10);

        // Query from [New_CourseDetail] table
        $EndDate = ""; $Course = ""; $HoursEarned = "";
        $TotalHoursEarned = 0;

        $year1 = $GLOBALS["fromYearInt"];
        $year2 = $GLOBALS["toYearInt"]+1;
        $time_start = "'".(string)$year1."/1/1'";
        $time_end = "'".(string)$year2."/12/31'";
        $year_across = "(CourseYear='".(string)$year1."-".(string)($year1+1)."'";
        for ($i = $year1+1; $i < $year2; $i ++) {
            $year_across = $year_across." OR CourseYear='".(string)$i."-".(string)($i+1)."'";
        }
        $year_across = $year_across.")";
        $tsql = "SELECT * FROM [New_CourseDetail] WHERE CertNo=".(string)$certid."
            AND EndDate BETWEEN ".$time_start." AND ".$time_end."
            AND".$year_across;

        $stmt = sqlsrv_query( $conn, $tsql);
        if( $stmt === false )
        {
             echo "Error in executing query220.</br>";
             die( print_r( sqlsrv_errors(), true));
        }
        else {
            while($row = sqlsrv_fetch_array($stmt)){
                $y=$this -> getY(); // Height of Current Page
                if ($y >= 180) {    // Force Page Break if too low on page
                    $this->AddPage();
                    $this->ln(10);
                }
                $EndDate = date("m/d/Y",strtotime($row['EndDate']));;
                $Course = $row['CourseName'];
                //$Course = str_replace((string)$year."-".(string)($year+1)." ANNUAL ","",$Course);
                $HoursEarned= $row['CourseHours'];
                $TotalHoursEarned += $HoursEarned;
                $this->SetFont('Arial','',11);
                $this->Ln(5);
                $this->Cell(0,0,$EndDate);
                $this->Ln();
                $this->Cell(30);

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

                $this->Cell(0,0,'NA',0,0,'C');  // Source
                $this->Ln();
                $this->Cell(260,0,$HoursEarned,0,0,'R');
                $this->SetXY($x_begin,$y_temp);
            }

        }
        sqlsrv_free_stmt($stmt);
        $y=$this -> getY(); // Height of Current Page
        if ($y >= 180) {    // Force Page Break if too low on page
            $this->AddPage();
            $this->ln(10);
        }
        $this->ln(15);
        $this->SetFont('Arial','B',11);
        $this->Cell(240,0,'Total',0,0,'R');
        $this->ln(0);
        $this->Cell(260,0,$TotalHoursEarned,0,0,'R');

// ********************       End of Course Datails          ********************
/////////////////////////////////////////////////////////////////////////////////////////
}

    function footer(){
        $this->SetY(-18);
        $this->SetFont('Arial','B',11);
        $this->SetLineWidth(0.5);
        $this->SetY(-15);
        $this->SetFont('Arial','',8);
        $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'R');
    }
}
?>
