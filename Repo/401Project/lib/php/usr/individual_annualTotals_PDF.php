<?php
require('../FPDF/fpdf.php');
class myPDF extends FPDF {
    public $isFirstPage = TRUE;
    function header() {
        if ($this->isFirstPage == TRUE) { // if on summary page
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
        else { // if on yearly detailed page

        }

    }

    function personInfo($conn){

        // Get all related personal information
        $certid = $GLOBALS['certid'];
        $lastName = ""; $firstName = ""; $middleName = "";
        $county = "";
        $temp_certDate = "";
        $status = ""; // active or not
        $certDate = ""; $certType =""; $allowedcarryover="";

        $tsql = "SELECT * FROM [AnnualReq] WHERE CertNo=".(string)$certid;

        $stmt = sqlsrv_query( $conn, $tsql);
        $annualreq;
        if( $stmt === false )
        {
             echo "Error in executing query.</br>";
             die( print_r( sqlsrv_errors(), true));
        }
        else {
            $row= sqlsrv_fetch_array($stmt);
            $annualreq = $row;
            $lastName = $row['LastName'];
            $firstName = $row['FirstName'];
            $status = $row['Status'];
            if ($row['PermCertDate'] == NULL) {
              $certDate = "NA";
            }
            else {
              $certDate = date("m/d/Y",strtotime($row['PermCertDate']));
              $certType = $row['CertType'];
            }

            $allowedcarryover=$row['CarryForwardTotal'];
        }
        sqlsrv_free_stmt($stmt);

        // Personal Information: to get specialty
        $specialty = "";
        $tsql = "SELECT * FROM [Summary] WHERE CertNo=".(string)$certid;
        $stmt = sqlsrv_query($conn, $tsql);
        if( $stmt === false )
        {
             echo "Error in executing query.</br>";
             die( print_r( sqlsrv_errors(), true));
        }
        else {
            $row= sqlsrv_fetch_array($stmt);
            $specialty = $row['Auditor'];
            if ($specialty == "True") {
              $specialty = "Audit";
            }
            else {
              $specialty = "";
            }
        }
        sqlsrv_free_stmt($stmt);


        $this->SetFont('Arial','B',12);
        $this->SetTextColor(0,0,0);
        $this->Cell(500,5,$status,0,0,'C');

        $this->Ln(10);
        $this->SetFont('Arial','B',11);
        $this->SetTextColor(0,0,0);
        $this->Cell(0,0,'Last Name:');
        $this->Ln(0);
        $this->Cell(23);
        $this->Cell(0,0,$lastName);
        $this->Ln();
        $this->SetFont('Arial','',11);
        $this->Cell(60);
        $this->Cell(0,0,'First Name:');
        $this->Ln();
        $this->Cell(83);
        $this->Cell(0,0,$firstName);
        $this->Ln();
        $this->Cell(255,0,'Certificate Number:',0,0,'R');
        $this->Cell(0,0,$certid,0,0,'R');

        $this->Ln(5);
        $this->Cell(0,0,'County:');

        $this->Ln(5);
        $this->Cell(0,0,'Temporary Cert. Date:');
        $this->Ln();
        $this->Cell(60);
        $this->Cell(0,0,'Permanent Cert. Date::');
        $this->ln();
        $this->Cell(100);
        $this->Cell(0,0,$certDate);
        $this->Ln();
        $this->Cell(130);
        $this->Cell(0,0,'Advanced Cert. Date:');

        $this->Ln();
        $this->Cell(250,0,'Specialty:  '.$specialty,0,0,'R');

        // Draw a line
        $width=$this -> w; // Width of Current Page
        $height=$this -> h; // Height of Current Page
        $this->SetLineWidth(0.7);
        $this->SetDrawColor(0,0,0);
        $this->Line(10,53,$width-10,53); // Line one Cross


        // Generate Course Info
        // Add Titles
        $this->SetFont('Arial','B',11);
        $this->SetTextColor(0,0,0);
        $this->Ln(18);
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

        // Draw a line
        $width=$this -> w; // Width of Current Page
        $this->SetLineWidth(0.7);
        $this->SetDrawColor(0,0,0);
        $this->Line(10,67,$width-10,67); // Line one Cross

        $certid = $GLOBALS['certid'];
        $EndDate = ""; $Course = ""; $HoursEarned = "";
        $TotalHoursEarned = 0;

        $tsql;
        $year = 2016;
        $time_start = "'".(string)$year."/1/1'";
        $time_end = "'".(string)$year."/12/31'";
        $tsql = "SELECT * FROM [Details] WHERE CertNo=".(string)$certid."
            AND EndDate BETWEEN ".$time_start." AND ".$time_end."
            AND FiscalYear='".(string)$year."-".(string)($year+1)."'";


        $stmt = sqlsrv_query( $conn, $tsql);
        if( $stmt === false )
        {
             echo "Error in executing query.</br>";
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
                $Course = $row['Course'];
                //$Course = str_replace((string)$year."-".(string)($year+1)." ANNUAL ","",$Course);
                $HoursEarned= $row['HoursEarned'];
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
                $this->Cell(0,0,'NA',0,0,'C');
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


        // Add Annual Training Hours Summary
        $y=$this -> getY(); // Height of Current Page
        if ($y >= 132) {    // Force Page Break if too low on page
            $this->AddPage();
            $this->ln(20);
        }
        $this->SetFont('Arial','BU',11);
        $this->Cell(241,0,'Annual Training Hours Summary',0,0,'R');

        //draw out the rectangular border
        $y=$this -> getY(); // Height of Current Page
        $this->SetLineWidth(0.5);
        $this->Rect(163,$y-5,110,52);

        $this->Ln(8);
        $this->SetFont('Arial','',11);
        $this->SetFont('','');
        $this->Cell(237,0,'Carry Over Hours from Prior Years*:',0,0,'R');
        $this->ln(0);
        $this->Cell(260,0,$annualreq['PriorYearBalance'],0,0,'R');

        $this->Ln(6);
        $this->Cell(236.8,0,'Less Req. Hours for FY '.(string)$year."-".(string)($year+1),0,0,'R');
        $this->ln(0);
        $this->Cell(260,0,$annualreq['RequiredHours'],0,0,'R');

        //draw out the first line
        $x=$this -> getX();
        $y=$this -> getY(); // Height of Current Page
        $this->SetLineWidth(0.7);
        $this->Line($x-89,$y+3.5,$x-2,$y+3.5); // Line  Cross

        $this->Ln(8);
        $this->Cell(236.8,0,'Sub-Total:',0,0,'R');
        $this->ln(0);
        $this->Cell(260,0,$annualreq['PriorYearBalance']-$annualreq['RequiredHours'],0,0,'R');

        $this->Ln(6);
        $this->Cell(236.8,0,'Plus FY '.(string)$year."-".(string)($year+1).' Hours Completed:',0,0,'R');
        $this->ln(0);
        $this->Cell(260,0,$TotalHoursEarned,0,0,'R');

        //draw out the second line
        $x=$this -> getX();
        $y=$this -> getY(); // Height of Current Page
        $this->SetLineWidth(0.7);
        $this->Line($x-89,$y+3.5,$x-2,$y+3.5); // Line  Cross

        $this->Ln(8);
        $this->Cell(236.8,0,'Total Carry Over Hours:',0,0,'R');
        $this->ln(0);
        $totalcarryover = $TotalHoursEarned+$annualreq['PriorYearBalance']-$annualreq['RequiredHours'];
        $GLOBALS['totalcarryover'] = $totalcarryover;
        $this->Cell(260,0,$totalcarryover,0,0,'R');

        $this->Ln(6);
        $this->SetFont('Arial','B',11);
        $this->Cell(236.8,0,'Allowed Carry Over Hours for Next FY*:',0,0,'R');
        $this->ln(0);
        $this->Cell(260,0,$allowedcarryover,0,0,'R');

        $this->Ln(10);
        $this->SetFont('Arial','I',10);
        $this->Cell(260,0,'*Please refer to enclosed pamphlet for computation of excess hours',0,0,'R');
    }

    function footer(){


        $this->SetY(-15);
        $this->SetFont('Arial','',8);
        $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'R');
    }
}
?>
