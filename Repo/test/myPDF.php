<?php 
require('lib/FPDF/fpdf.php');
class myPDF extends FPDF {
    function header() {
        $this->Image('logo.gif',10,8,-270);

        $this->SetFont('Arial','B',12);
        $this->SetTextColor(0,0,128);

        $this->Cell(0,5,'OFFICE OF THE ASSESSOR',0,0,'C');
        $this->Ln();
        $this->Cell(0,5,'Record of Appraisal Training Hours',0,0,'C');
        $this->Ln();
        
        //year calculation according to input/////////////////////////////////////
        $year = $GLOBALS['year'];
        $this->Cell(0,5,'FY '.(string)$year.'-'.(string)($year+1),0,0,'C');
        $this->Ln();

        $width=$this -> w; // Width of Current Page
        $height=$this -> h; // Height of Current Page
        $this->SetLineWidth(0.7);
        $this->SetDrawColor(162,157,150);
        $this->Line(10, 30,$width-10,30); // Line one Cross
    }

    function personInfo($conn){
        $certid = $GLOBALS['certid'];
        $year = $GLOBALS['year'];
        $lastName = ""; $firstName = ""; 
        $status = ""; // active or not
        $certDate = ""; $certType =""; $allowedcarryover="";

        $tsql = "SELECT * FROM [AnnualReq] WHERE CertNo=".(string)$certid."AND FiscalYear='".
            (string)$year."-".(string)($year+1)."'";  

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
  
  // one more query to get specialty
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
        $this->Cell(338.5,0,'PayLocation:',0,0,'C');
        $this->Ln();
        $this->Cell(249.5,0,'Certificate Date:',0,0,'R');
        $this->Cell(0,0,$certDate,0,0,'R');
        
        $this->Ln(5);
        $this->Cell(332.5,0,'Specialty:',0,0,'C');
  $this->Ln(0);
        $this->Cell(375,0,$specialty,0,0,'C');
  
        $this->Ln();
        $this->Cell(250,0,'Certificate Type:',0,0,'R');
        $this->Cell(0,0,$certType,0,0,'R');


        $width=$this -> w; // Width of Current Page
        $height=$this -> h; // Height of Current Page
        $this->SetLineWidth(0.7);
        $this->SetDrawColor(0,0,0);
        $this->Line(10,53,$width-10,53); // Line one Cross

 
        $this->SetFont('Arial','B',11);
        $this->SetTextColor(0,0,0);
        $this->Ln(22);
        $this->Cell(263,0,(string)$year."-".(string)($year+1),0,0,'R');
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

        
  // Generate Course Info
        //Assume going to report for 10217/////////////////////////
        $certid = $GLOBALS['certid'];
        $EndDate = ""; $Course = ""; $HoursEarned = ""; 
        $TotalHoursEarned = 0;
        
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
                $EndDate = date("m/d/Y",strtotime($row['EndDate']));; 
                $Course = $row['Course']; 
                $Course = str_replace((string)$year."-".(string)($year+1)." ANNUAL ","",$Course);
                $HoursEarned= $row['HoursEarned'];
                $TotalHoursEarned += $HoursEarned;
                $this->SetFont('Arial','',11);
                $this->Ln(5);
                $this->Cell(0,0,$EndDate);
                $this->Ln();
                $this->Cell(30);

    //              $x_temp=$this -> getX(); 
    //              $y_temp=$this -> getY(); 
    //              $this->Cell(30);
    // $this->SetXY( $x_temp, $y_temp-2.5);
    //              $this->MultiCell(75,5,"123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890",0);
    //              $this->SetXY( $x_temp+70, $y_temp);

                $this->Cell(0,0,$Course);
                $this->Ln();
                $this->Cell(0,0,'NA',0,0,'C');
                $this->Ln();
                $this->Cell(260,0,$HoursEarned,0,0,'R');


            }
 
        }
        sqlsrv_free_stmt($stmt);
        
        $this->ln(15);
        $this->SetFont('Arial','B',11);
        $this->Cell(240,0,'Total',0,0,'R');
        $this->ln(0);
        $this->Cell(260,0,$TotalHoursEarned,0,0,'R');

        $y=$this -> getY(); // Height of Current Page
        if ($y >= 132) {$this->AddPage(); $this->ln(20);} // Force Page Break
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

        //draw out the line
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

        //draw out the line
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
        $year = $GLOBALS['year'];
        $this->SetY(-18);
        $this->SetFont('Arial','B',11);
        $this->SetLineWidth(0.5);
        if ($GLOBALS['totalcarryover']>=0) {
          $this->Cell(0,5,'TRAINING HOURS REQUIREMENT HAS BEEN MET FOR FY '.(string)$year.'-'.(string)($year+1),1,0,'C');
        }
        else {
          $this->Cell(0,5,'TRAINING HOURS REQUIREMENT HAS NOT BEEN MET FOR FY '.(string)$year.'-'.(string)($year+1),1,0,'C');
        }


        $this->SetY(-15);
        $this->SetFont('Arial','',8);
        $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'R');
    }
}
?>