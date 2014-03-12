<?php

/* qrgridpdf.php

   Generates grid of QR codes to PDF. 
   Each page is based on $BACKGROUND_FILE PDF file
   Grid cell is filled with logo from $LOGO_FILE and appropriate QR code
   The list of QR codes is given in "source" parameter that is text file, where every line interprets one QR 
   urlencode() is called for each line

   parameters:
    source - reference to text file containing list of data 
    circle - draw circle around QR code (default=1)

*/

require('fpdf/fpdf.php');
require('fpdf/fpdi.php');

class MPDF extends FPDI
{
   function Circle($x, $y, $r, $style='')
   {
      $this->Ellipse($x, $y, $r, $r, $style);
   }

   function Ellipse($x, $y, $rx, $ry, $style='D')
   {
      if($style=='F')
         $op='f';
      elseif($style=='FD' or $style=='DF')
         $op='B';
      else
         $op='S';
      $lx=4/3*(M_SQRT2-1)*$rx;
      $ly=4/3*(M_SQRT2-1)*$ry;
      $k=$this->k;
      $h=$this->h;
      $this->_out(sprintf('%.2f %.2f m %.2f %.2f %.2f %.2f %.2f %.2f c', 
         ($x+$rx)*$k, ($h-$y)*$k, 
         ($x+$rx)*$k, ($h-($y-$ly))*$k, 
         ($x+$lx)*$k, ($h-($y-$ry))*$k, 
         $x*$k, ($h-($y-$ry))*$k));
      $this->_out(sprintf('%.2f %.2f %.2f %.2f %.2f %.2f c', 
         ($x-$lx)*$k, ($h-($y-$ry))*$k, 
         ($x-$rx)*$k, ($h-($y-$ly))*$k, 
         ($x-$rx)*$k, ($h-$y)*$k));
      $this->_out(sprintf('%.2f %.2f %.2f %.2f %.2f %.2f c', 
         ($x-$rx)*$k, ($h-($y+$ly))*$k, 
         ($x-$lx)*$k, ($h-($y+$ry))*$k, 
         $x*$k, ($h-($y+$ry))*$k));
      $this->_out(sprintf('%.2f %.2f %.2f %.2f %.2f %.2f c %s', 
         ($x+$lx)*$k, ($h-($y+$ry))*$k, 
         ($x+$rx)*$k, ($h-($y+$ly))*$k, 
         ($x+$rx)*$k, ($h-$y)*$k, 
      $op));
   }
}

$PAGE_WIDTH=460;
$PAGE_HEIGHT=305.054;
$BAND_HEIGHT=58.496;                            // horizontal band height
$GRID_SIZE=54;                                  // grid square size
$DIAMETER=30;                                   // cutoff circle
$BORDER=($GRID_SIZE-$DIAMETER)/2;               // distance between cutoff circle and grid square
$OFFSET_LOGO_VERTICAL=241.779-4*$BAND_HEIGHT;   // grid vertical offset to upleft corner of the page
$OFFSET_LOGO_HORIZONTAL=14.655;                 // grid horizontal offset to upleft corner of the page
$OFFSET_QR_VERTICAL=260.2-4*$BAND_HEIGHT;       // QR code offset to upleft corner of the page (grid to page offset + QR to grid offset)
$OFFSET_QR_HORIZONTAL=32.6;                     // QR code offset to upleft corner of the page (grid to page offset + QR to grid offset)
$QR_SIZE=18.1;                                  // size of QR 
$BACKGROUND_FILE="pdf/designfile.pdf";              // Chinese issue
$LOGO_FILE="pdf/qrlogo.pdf";                        // Logo design

$drawCircle=1;                         
if (isset($_REQUEST["circle"]))
   $drawCircle=$_REQUEST["circle"];


isset($_REQUEST["source"]) or die("source parameter missing");
$dataSource=$_REQUEST["source"];
$data = str_getcsv(file_get_contents($dataSource),"\n");
$qrCount=count($data);

$pdf = new MPDF('L','mm',array($PAGE_WIDTH,$PAGE_HEIGHT));

$cntr=0;

while ($cntr < $qrCount)      // one cycle = one page
{
   $pdf->AddPage(); 
   $pdf->setSourceFile($BACKGROUND_FILE);    
   $tplIdx = $pdf->importPage(1); 
   $pdf->useTemplate($tplIdx, 0, 0, 0, 0, true); 

   $pdf->setSourceFile($LOGO_FILE);        
   $tplIdx = $pdf->importPage(1); 

   for ($r=0; $r<5 && $cntr < $qrCount; $r++)         // rows
   {
      for ($i=0; $i < 8 && $cntr < $qrCount; $i++)    // grid cells 
      {
         while ($cntr < $qrCount and !($href=urlencode($data[$cntr++]))) ; // skip empty lines
         if ($href)     // we have valid href that means not EOF 
         {
            $pdf->useTemplate($tplIdx, $OFFSET_LOGO_HORIZONTAL+$BORDER+$i*$GRID_SIZE, $OFFSET_LOGO_VERTICAL+$r*$BAND_HEIGHT+$BORDER); 
            $pdf->Image('http://qr.edocu.sk/?data='.$href.'&level=H&size=10&border=0',$OFFSET_QR_HORIZONTAL+$i*$GRID_SIZE,$OFFSET_QR_VERTICAL+$r*$BAND_HEIGHT,$QR_SIZE,$QR_SIZE,'PNG');
            if ($drawCircle) 
               $pdf->Circle($OFFSET_LOGO_HORIZONTAL+($i+0.5)*$GRID_SIZE, $OFFSET_LOGO_VERTICAL+$r*$BAND_HEIGHT+0.5*$GRID_SIZE,$DIAMETER/2);
         }
      }
   }
}

$pdf->Output('qrgrid.pdf', 'D');

?>
