<?php
/*
This Code renders the individual PDF (Completed Course Report)
Given a Cert No. Starting year and End year
@ Yining Huang
*/

require('../../FPDF/fpdf.php');
class myPDF extends FPDF {
    function addSubheaders() {
        // Sub headers
        $width=$this -> w; // Width of Current Page
        $height=$this->getY()+5;
        $this->SetLineWidth(0.65);
        $this->SetDrawColor(0,0,0);
        $this->Line(10,$height,$width-10,$height); // Line one Cross

        if ($this->PageNo() != 1) {
            $this->Cell(0,5,"Cert.No: ".$GLOBALS["certid"],0,0,'R');
            $this->ln();
        } else {
            $this->ln(10);
        }

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

        // Draw a line
        $width=$this -> w; // Width of Current Page
        $height=$this -> h; // Height of Current Page
        $this->SetLineWidth(0.7);
        $this->SetDrawColor(162,157,150);
        $this->Line(10, 30,$width-10,30); // Line one Cross

        if ($this->PageNo() != 1) {
            // Sub headers
            $this->addSubheaders();
        }

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
                WHERE [New_Employee].CertNo=".(string)$certid.
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
            $lastName = $row['LastName'];
            $firstName = $row['FirstName'];
            $status = $row['CurrentStatus'];
            $specialty = $row['Auditor'];
            if ($specialty == "True") {
              $specialty = "Audit";
            } else {
              $specialty = "";
            }
            if ($row['PermCertDate'] == NULL) {
              $certDate = "NA"; // if not permanet, data shows "NA"
            } else {
              $certDate = date("m/d/Y",strtotime($row['PermCertDate']));
            }
            $certType = $row['CertType'];
        }
        sqlsrv_free_stmt($stmt);

        // Fill in data for personal information
        $this->SetFont('Arial','B',12);
        $this->SetTextColor(0,0,0);
        $this->Cell(500,5,$status,0,0,'C');

        $this->Ln(10);
        $this->SetFont('Arial','',11);
        $this->SetTextColor(0,0,0);
        $this->Cell(0,0,'Name:  ');
        $this->SetFont('Arial','B',11);
        $this->Ln(0);
        $this->Cell(13);
        $this->Cell(0,0,$lastName.', '.$firstName);
        $this->Ln();
        $this->SetFont('Arial','',11);
        $this->Cell(274,0,'Employee Number:',0,0,'C');
        $this->Ln();
        $this->Cell(255,0,'Certificate Number:',0,0,'R');
        $this->Cell(0,0,$certid,0,0,'R');

        $this->Ln(5);
        $this->Cell(0,0,'Title:');
        $this->Ln();
        $this->Cell(250,0,'Item:',0,0,'C');
        $this->Ln();
        $this->Cell(249.5,0,'Certificate Date:',0,0,'R');
        $this->Cell(0,0,$certDate,0,0,'R');

        $this->Ln(5);
        $this->Cell(0,0,'Specialty: '.$specialty);
        $this->Ln();
        $this->Cell(264.5,0,'Pay Location:',0,0,'C');
        $this->Ln();

        // Comment out Cert Type because they might be different for every year
        // $this->Cell(250,0,'Certificate Type:',0,0,'R');
        // $this->Cell(0,0,$certType,0,0,'R');


// ********************       End of Personal Information          ********************
/////////////////////////////////////////////////////////////////////////////////////////





/////////////////////////////////////////////////////////////////////////////////////////
// ********************       Start of Course Datails          ********************

        // Generate Course Info
        // Add Titles


        $this->SetFont('Arial','B',11);
        $this->SetTextColor(0,0,0);
        $this->Ln(3);

        $this->addSubheaders();
        // $this->Ln(5);
        $year1 = $GLOBALS["fromYearInt"];
        $year2 = $GLOBALS["toYearInt"];

        //$this->Ln(10);

        // Query from [New_CourseDetail] table
        $EndDate = ""; $Course = ""; $HoursEarned = "";
        $TotalHoursEarned = 0;

        $year1 = $GLOBALS["fromYearInt"];
        $year2 = $GLOBALS["toYearInt"]+1;
        $year_across = "(CourseYear='".(string)$year1."-".(string)($year1+1)."'";
        for ($i = $year1+1; $i < $year2; $i ++) {
            $year_across = $year_across." OR CourseYear='".(string)$i."-".(string)($i+1)."'";
        }
        $year_across = $year_across.")";
        $tsql = "SELECT * FROM [New_CourseDetail] WHERE CertNo=".(string)$certid." AND".$year_across.
                "ORDER BY CourseYear DESC,EndDate";

        $stmt = sqlsrv_query( $conn, $tsql);
        if( $stmt === false )
        {
             echo "Error in executing query220.</br>";
             die( print_r( sqlsrv_errors(), true));
        }
        else {
            $this->SetFont('Arial','BI',12);
            $current_year_header = (string)($year2-1)."-".(string)($year2);
            $TotalHoursEarned = 0;
            $this->Cell(0,0,$current_year_header);
            $this->SetFont('Arial','',11);
            while($row = sqlsrv_fetch_array($stmt)){
                $y=$this -> getY(); // Height of Current Page
                $FiscalYear = $row['CourseYear'];
                if ($FiscalYear != $current_year_header) {
                    $this->Ln(7);
                    $this->SetFont('Arial','B',11);
                    $this->Cell(0,0,'Total Hours: '.$TotalHoursEarned,0,0,'R');
                    $this->Ln(5);

                    $width=$this -> w; // Width of Current Page
                    $height=$this->getY();
                    $this->SetLineWidth(0.3);
                    $this->SetDrawColor(162,157,150);
                    $this->Line(10,$height,$width-10,$height); // Line one Cross
                    $this->ln(5);

                    $this->SetFont('Arial','BI',12);
                    // $this->Ln(5);
                    $current_year_header = $FiscalYear;
                    $this->Cell(0,0,$current_year_header);
                    $this->SetFont('Arial','',11);
                    $TotalHoursEarned = 0;

                }
                $EndDate = date("m/d/Y",strtotime($row['EndDate']));;
                $Course = $row['CourseName'];
                //$Course = str_replace((string)$year."-".(string)($year+1)." ANNUAL ","",$Course);
                $Location = $row['CourseLocation'];
                $Grade = $row['CourseGrade'];
                $HoursEarned= $row['CourseHours'];
                $TotalHoursEarned += $HoursEarned;

                if ($Course == "No training was completed during this fiscal year") {
                    $EndDate = "N/A";
                }
                $this->SetFont('Arial','',11);
                $this->Ln(5);
                $this->Cell(0,0,$EndDate);
                $this->Ln();
                $this->Cell(35);

                // Swith to next line and add an "-" if the course name is too long
                $x_begin=$this -> getX();
                $y_begin=$this -> getY();
                $x_temp = $x_begin;
                $y_temp = $y_begin;
                $max_length_of_string = 65;
                if ((strlen($Course) <= $max_length_of_string)) {
                    $this->Cell(0,0,$Course);
                    $this->Ln();
                }
                else {
                    while (strlen($Course) > $max_length_of_string) {
                        $x_temp = $this -> getX();
                        $y_temp = $this -> getY();
                        if (strlen($Course) >= $max_length_of_string)
                            $temp_coursename = substr($Course, 0,$max_length_of_string);
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

                $this->Cell(186);
                $this->Cell(0,0,$Location);
                $this->Ln();
                $this->Cell(232);
                $this->Cell(0,0,$Grade);
                $this->Cell(0,0,$HoursEarned,0,0,'R');
                $this->SetXY($x_begin,$y_temp);


            }

        }
        sqlsrv_free_stmt($stmt);
        $this->Ln(7);
        $this->SetFont('Arial','B',11);
        $this->Cell(0,0,'Total Hours: '.$TotalHoursEarned,0,0,'R');
        $this->Ln(5);

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
