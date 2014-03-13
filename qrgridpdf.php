<?php

/* qrgridpdf.php

   Generates grid of QR codes to PDF. 
   Each page is based on $BACKGROUND_FILE PDF file
   Grid cell is filled with logo from $LOGO_FILE and appropriate QR code
   The list of QR codes is given in "source" parameter that is text file, where every line interprets one QR 
   urlencode() is called for each line

   parameters:
    source - reference to text file containing list of data 
    circle - draw circle around QR code (default=0)
    rowCount - number of rows/bands (default=3)
    cellCount - number of cella per band (default=4)
    pageWidth - page width in pageUnits (default=297)
    pageHeight - page height in pageUnits (default=210)
    pageOrient - page orientation P/L (default=L)
    pageUnits - units (default=mm)
    bandHeight - height of one band/line (default=68)
    gridHeight - height of one cell (default=66)
    gridWidth - width of one cell (default=70)
    diameter -  cutoff circle diameter (default=30)
    gridOffsetVertical - grid vertical offset to upleft corner of the page (default=4)
    gridOffsetHorizontal - grid horizontal offset to upleft corner of the page (default=8)
    qrOffsetVertical - QR code offset to grid (default=5)
    qrOffsetHorizontal - QR code offset to grid (default=7)
    qrSize - size of QR (default=56)
    backgroundFile - background grid file (default="")
    logoFile - logo file (designfile="")
    outputName - name of output file (default=qrgrid.pdf)
    preset - page layout preset ("60_1x1", "A4_4x3" or "ch_8x5") 

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

$PAGE_WIDTH=297;
$PAGE_HEIGHT=210;
$PAGE_ORIENT="L";
$PAGE_UNITS="mm";
$BAND_HEIGHT=68;                                // horizontal band height
$GRID_HEIGHT=66;                                // grid cell height
$GRID_WIDTH=70;                                 // grid cell width
$DIAMETER=0;                                    // cutoff circle
$GRID_OFFSET_VERTICAL=1;                        // grid vertical offset to upleft corner of the page
$GRID_OFFSET_HORIZONTAL=1;                      // grid horizontal offset to upleft corner of the page
$QR_OFFSET_VERTICAL=5;                          // QR code offset to grid 
$QR_OFFSET_HORIZONTAL=7;                        // QR code offset to grid 
$QR_SIZE=56;                                    // size of QR 
$BACKGROUND_FILE="";                            // background grid
$LOGO_FILE="";                                  // Logo design
$OUTPUT_NAME="qrgrid.pdf";                      // output file default name

$drawCircle=0; 
$rowCount=3;
$cellCount=4;
$preset=0;

if (isset($_REQUEST["preset"]))
   $preset=$_REQUEST["preset"];

switch ($preset) 
{
   case "60_1x1":
      $PAGE_WIDTH=60;
      $PAGE_HEIGHT=60;
      $BAND_HEIGHT=60;
      $GRID_HEIGHT=50; 
      $GRID_WIDTH=50; 
      $GRID_OFFSET_VERTICAL=1;
      $GRID_OFFSET_HORIZONTAL=1;
      $QR_OFFSET_VERTICAL=4;
      $QR_OFFSET_HORIZONTAL=4; 
      $QR_SIZE=50;
      $BACKGROUND_FILE="";
      $LOGO_FILE="";
      $OUTPUT_NAME="qr_1x1.pdf";
      $rowCount=1;
      $cellCount=1;
      break;
   case "A4_4x3":
      $PAGE_WIDTH=297;
      $PAGE_HEIGHT=210;
      $PAGE_ORIENT="L";
      $PAGE_UNITS="mm";
      $BAND_HEIGHT=68;
      $GRID_WIDTH=70;     
      $GRID_HEIGHT=66;     
      $GRID_OFFSET_VERTICAL=4;  
      $GRID_OFFSET_HORIZONTAL=8;
      $QR_OFFSET_VERTICAL=5;   
      $QR_OFFSET_HORIZONTAL=7;
      $QR_SIZE=56;              
      $BACKGROUND_FILE="";
      $LOGO_FILE="";
      $OUTPUT_NAME="qrgrid_A4.pdf";           
      $rowCount=3;
      $cellCount=4;
      break;
    case "ch_8x5":
      $PAGE_WIDTH=460;
      $PAGE_HEIGHT=305.054;
      $PAGE_ORIENT="L";
      $PAGE_UNITS="mm";
      $BAND_HEIGHT=58.496;
      $GRID_WIDTH=54;     
      $GRID_HEIGHT=54;     
      $DIAMETER=30;     
      $GRID_OFFSET_VERTICAL=7.795;  
      $GRID_OFFSET_HORIZONTAL=14.655;
      $QR_OFFSET_VERTICAL=18.421;   
      $QR_OFFSET_HORIZONTAL=17.945;
      $QR_SIZE=18.1;              
      $BACKGROUND_FILE="pdf/designfile.pdf"; 
      $LOGO_FILE="pdf/qrlogo.pdf";          
      $OUTPUT_NAME="qrgrid.pdf";           
      $drawCircle=1; 
      $cellCount=8;
      $rowCount=5;
      break;
   default:
      break;
}

if (isset($_REQUEST["circle"]))
   $drawCircle=$_REQUEST["circle"];

if (isset($_REQUEST["rowCount"]))
   $rowCount=$_REQUEST["rowCount"];

if (isset($_REQUEST["cellCount"]))
   $cellCount=$_REQUEST["cellCount"];

if (isset($_REQUEST["pageWidth"]))
   $PAGE_WIDTH=$_REQUEST["pageWidth"];

if (isset($_REQUEST["pageHeight"]))
   $PAGE_HEIGHT=$_REQUEST["pageHeight"];

if (isset($_REQUEST["pageOrient"]))
   $PAGE_ORIENT=$_REQUEST["pageOrient"];

if (isset($_REQUEST["pageUnits"]))
   $PAGE_UNITS=$_REQUEST["pageUnits"];

if (isset($_REQUEST["bandHeight"]))
   $BAND_HEIGHT=$_REQUEST["bandHeight"];

if (isset($_REQUEST["gridHeight"]))
   $GRID_HEIGHT=$_REQUEST["gridHeight"];

if (isset($_REQUEST["gridWidth"]))
   $GRID_WIDTH=$_REQUEST["gridWidth"];

if (isset($_REQUEST["diameter"]))
   $DIAMETER=$_REQUEST["diameter"];

if (isset($_REQUEST["gridOffsetVertical"]))
   $GRID_OFFSET_VERTICAL=$_REQUEST["gridOffsetVertical"];

if (isset($_REQUEST["gridOffsetHorizontal"]))
   $GRID_OFFSET_HORIZONTAL=$_REQUEST["gridOffsetHorizontal"];

if (isset($_REQUEST["qrOffsetVertical"]))
   $QR_OFFSET_VERTICAL=$_REQUEST["qrOffsetVertical"];

if (isset($_REQUEST["qrOffsetHorizontal"]))
   $QR_OFFSET_HORIZONTAL=$_REQUEST["qrOffsetHorizontal"];

if (isset($_REQUEST["qrSize"]))
   $QR_SIZE=$_REQUEST["qrSize"];

if (isset($_REQUEST["backgroundFile"]))
   $BACKGROUND_FILE=$_REQUEST["backgroundFile"];

if (isset($_REQUEST["logoFile"]))
   $LOGO_FILE=$_REQUEST["logoFile"];

if (isset($_REQUEST["outputName"]))
   $OUTPUT_NAME=$_REQUEST["outputName"];


$LOGO_BORDER_TOP=($GRID_HEIGHT-$DIAMETER)/2;          // distance between cutoff circle and grid square
$LOGO_BORDER_LEFT=($GRID_WIDTH-$DIAMETER)/2;          // distance between cutoff circle and grid square


isset($_REQUEST["source"]) or die("source parameter missing");
$dataSource=$_REQUEST["source"];
$data = str_getcsv(file_get_contents($dataSource),"\n");
$qrCount=count($data);

$pdf = new MPDF($PAGE_ORIENT, $PAGE_UNITS, array($PAGE_WIDTH,$PAGE_HEIGHT));

$cntr=0;

while ($cntr < $qrCount)      // one cycle = one page
{
   $pdf->AddPage(); 
   if ($BACKGROUND_FILE <> "")
   {
      $pdf->setSourceFile($BACKGROUND_FILE);    
      $tplIdx = $pdf->importPage(1); 
      $pdf->useTemplate($tplIdx, 0, 0, 0, 0, true); 
   }

   if ($LOGO_FILE <> "")
   {
      $pdf->setSourceFile($LOGO_FILE);        
      $tplIdx = $pdf->importPage(1); 
   }

   for ($r=0; $r<$rowCount && $cntr < $qrCount; $r++)         // rows
   {
      for ($i=0; $i < $cellCount && $cntr < $qrCount; $i++)    // grid cells 
      {
         while ($cntr < $qrCount and !($href=urlencode($data[$cntr++]))) {}; // skip empty lines
         if ($href)     // we have valid href that means not EOF 
         {
            if ($LOGO_FILE <> "")
               $pdf->useTemplate($tplIdx, $GRID_OFFSET_HORIZONTAL+$LOGO_BORDER_LEFT+$i*$GRID_WIDTH, $GRID_OFFSET_VERTICAL+$r*$BAND_HEIGHT+$LOGO_BORDER_TOP); 
            $pdf->Image('http://qr.edocu.sk/?data='.$href.'&level=H&size=10&border=0',$QR_OFFSET_HORIZONTAL+$GRID_OFFSET_HORIZONTAL+$i*$GRID_WIDTH,$QR_OFFSET_VERTICAL+$GRID_OFFSET_VERTICAL+$r*$BAND_HEIGHT,$QR_SIZE,$QR_SIZE,'PNG');
            if ($drawCircle) 
               $pdf->Circle($GRID_OFFSET_HORIZONTAL+($i+0.5)*$GRID_WIDTH, $GRID_OFFSET_VERTICAL+$r*$BAND_HEIGHT+0.5*$GRID_HEIGHT,$DIAMETER/2);
         }
      }
   }
}

$pdf->Output($OUTPUT_NAME, 'D');

?>
