<?php

/* qrgridpdf.php

   Generates grid of QR codes to PDF. 
   Each page is based on $BACKGROUND_FILE PDF file
   Grid cell is filled with logo from $LOGO_FILE and appropriate QR code
   The list of QR codes is given in "source" parameter that is text file, where every line interprets one QR 
   urlencode() is called for each line

   parameters:
    source - reference to JSON file containing list of data 
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
    showSerial - show cell serial number (default=0)
    blackWhite - QR color mode 0=color 1=gray 2=B&W (default=0)
    showList - print list of QR codes at the end of document (default=0)
    showName - print element name (default=0)
    showType - print element type (default=0)
    nameLimit - max. number of characters of name to print (default=15)
    nameType - max. number of characters of type to print (default=15)
    vector - print QR code as vector instead of PNG (defult=0)

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

   // Parse QR code in string and draw directly to PDF
   function ImageText ($file, $x, $y, $w=0, $h=0, $blackWhite=0)
   {
      $data = file_get_contents($file); 
      if ($data===false) $this->Error('TEXT file not found: '.$file); 
      
      if ($blackWhite==1)
      {
         $blockColor1R = 0x40;
         $blockColor1G = 0x40;
         $blockColor1B = 0x40;
         $blockColor2R = 0x00;
         $blockColor2G = 0x00;
         $blockColor2B = 0x00;
      }
      elseif ($blackWhite==2)
      {
         $blockColor1R = 0x00;
         $blockColor1G = 0x00;
         $blockColor1B = 0x00;
         $blockColor2R = 0x00;
         $blockColor2G = 0x00;
         $blockColor2B = 0x00;
      }
      else
      {
         $blockColor1R = 0x00;
         $blockColor1G = 0x3A;
         $blockColor1B = 0xC3;
         $blockColor2R = 0xFF;
         $blockColor2G = 0x00;
         $blockColor2B = 0x00;
      }

      $row=0;
      $col=0;
      $width=sqrt(strlen($data));


      for ($i=0; $i< strlen($data); $i++)
      {
         if ($data[$i] == "1")
         {
            if ($col + $row < $width - 1)
            {
               $this->SetFillColor($blockColor1R,$blockColor1G,$blockColor1B);
            }
            else   
            {
               $this->SetFillColor($blockColor2R,$blockColor2G,$blockColor2B);
            }

            $this->Rect($x+$w/$width*$col,$y+$h/$width*$row,$w/$width,$h/$width,"F");
         }

         if (++$col >= $width-1)
         {
            $row++;
            $col=0;
         }
      }
      return true;
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
$NAME_OFFSET_VERTICAL=1;
$NAME_OFFSET_HORIZONTAL=1;

$drawCircle=0; 
$rowCount=3;
$cellCount=4;
$preset=0;
$showSerial=0;
$showList=0;
$blackWhite=0;
$showName=0;
$showType=0;
$vector=0;
$nameLimit=15;                                 // limit QR name length
$typeLimit=15;                                 // limit QR type length

if (isset($_REQUEST["preset"]))
   $preset=$_REQUEST["preset"];

switch ($preset) 
{
   case "24_1x1":
      $PAGE_ORIENT="P";
      $PAGE_WIDTH=24;
      $PAGE_HEIGHT=30;
      $BAND_HEIGHT=20;
      $GRID_HEIGHT=20; 
      $GRID_WIDTH=20; 
      $GRID_OFFSET_VERTICAL=2;
      $GRID_OFFSET_HORIZONTAL=2;
      $QR_OFFSET_VERTICAL=0;
      //$QR_OFFSET_VERTICAL=6;
      $QR_OFFSET_HORIZONTAL=0; 
      $QR_SIZE=20;
      $BACKGROUND_FILE="";
      $LOGO_FILE="";
      $OUTPUT_NAME="qr_1x1.pdf";
      $rowCount=1;
      $cellCount=1;
      $showName=1;
      $showType=1;
      $showList=0;
      $showSerial=0;
      $blackWhite=2;
      break;
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
      $showSerial=1;
      $showList=1;
      break;
   default:
      break;
}

if (isset($_REQUEST["blackWhite"]))
   $blackWhite=$_REQUEST["blackWhite"];

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

if (isset($_REQUEST["showSerial"]))
   $showSerial=$_REQUEST["showSerial"];

if (isset($_REQUEST["showList"]))
   $showList=$_REQUEST["showList"];

if (isset($_REQUEST["showName"]))
   $showName=$_REQUEST["showName"];

if (isset($_REQUEST["showType"]))
   $showType=$_REQUEST["showType"];

if (isset($_REQUEST["vector"]))
   $vector=$_REQUEST["vector"];

if (isset($_REQUEST["nameLimit"]))
   $nameLimit=$_REQUEST["nameLimit"];

if (isset($_REQUEST["typeLimit"]))
   $typeLimit=$_REQUEST["typeLimit"];


$LOGO_BORDER_TOP=($GRID_HEIGHT-$DIAMETER)/2;          // distance between cutoff circle and grid square
$LOGO_BORDER_LEFT=($GRID_WIDTH-$DIAMETER)/2;          // distance between cutoff circle and grid square


isset($_REQUEST["source"]) or die("source parameter missing");
$dataSource=$_REQUEST["source"];
$dataSource= str_replace(' ', '%20', $dataSource);
$data = json_decode(file_get_contents($dataSource));
$qrCount=count($data);


$pdf = new MPDF($PAGE_ORIENT, $PAGE_UNITS, array($PAGE_WIDTH,$PAGE_HEIGHT));
$pdf->AddFont('arial','','arial.php');
$pdf->SetFont('arial','',16);
$pdf->SetAutoPageBreak(false);

$cntr=0;

if ($BACKGROUND_FILE <> "")
{
   $backgroundFileName=tempnam(sys_get_temp_dir(),"qrgrid_bck_");
   file_put_contents($backgroundFileName, fopen("$BACKGROUND_FILE", 'r'));
   $finfo = finfo_open(FILEINFO_MIME_TYPE);
   $backgroundFileMime=finfo_file($finfo, $backgroundFileName);
   if ($backgroundFileMime=="application/pdf") 
   {
      $pdf->setSourceFile($backgroundFileName);    
      $tplIdxBck = $pdf->importPage(1); 
   }
   elseif ($backgroundFileMime=="image/png") 
      $backgroundFileType="PNG";
   elseif ($backgroundFileMime=="image/gif") 
      $backgroundFileType="GIF";
   elseif ($backgroundFileMime=="image/jpeg") 
      $backgroundFileType="JPEG";
}

if ($LOGO_FILE <> "")
{
   $logoFileName=tempnam(sys_get_temp_dir(),"qrgrid_logo_");
   file_put_contents($logoFileName, fopen("$LOGO_FILE", 'r'));
   $finfo = finfo_open(FILEINFO_MIME_TYPE);
   $logoFileMime=finfo_file($finfo, $logoFileName);
   if ($logoFileMime=="application/pdf") 
   {
      $pdf->setSourceFile($logoFileName);    
      $tplIdxLogo = $pdf->importPage(1); 
   }
   elseif ($logoFileMime=="image/png") 
      $logoFileType="PNG";
   elseif ($logoFileMime=="image/gif") 
      $logoFileType="GIF";
   elseif ($logoFileMime=="image/jpeg") 
      $logoFileType="JPEG";
}

while ($cntr < $qrCount)      // one cycle = one page
{
   $pdf->AddPage(); 
   if ($BACKGROUND_FILE <> "")
   {
      if ($backgroundFileMime=="application/pdf") 
         $pdf->useTemplate($tplIdxBck, 0, 0, 0, 0, true);
      else
         $pdf->Image($backgroundFileName,0,0,0,0,$backgroundFileType);
   }

   for ($r=0; $r<$rowCount && $cntr < $qrCount; $r++)         // rows
   {
      for ($i=0; $i < $cellCount && $cntr < $qrCount; $i++)    // grid cells 
      {
         if ($LOGO_FILE <> "")
         {
            if ($logoFileMime=="application/pdf") 
               $pdf->useTemplate($tplIdxLogo, $GRID_OFFSET_HORIZONTAL+$LOGO_BORDER_LEFT+$i*$GRID_WIDTH, $GRID_OFFSET_VERTICAL+$r*$BAND_HEIGHT+$LOGO_BORDER_TOP);
            else
               $pdf->Image($logoFileName,$GRID_OFFSET_HORIZONTAL+$LOGO_BORDER_LEFT+$i*$GRID_WIDTH, $GRID_OFFSET_VERTICAL+$r*$BAND_HEIGHT+$LOGO_BORDER_TOP,0,0,$logoFileType);
         }
         $href=urlencode($data[$cntr]->href);
         if ($showSerial) 
         {
            $pdf->SetFont('arial','B',16);
            $pdf->SetXY($GRID_OFFSET_HORIZONTAL+$i*$GRID_WIDTH, $GRID_OFFSET_VERTICAL+$r*$BAND_HEIGHT);                                                                                                                                                                                                         
            $pdf->Cell($GRID_WIDTH,10,$cntr+1);
         }
         if ($showName) 
         {
            $name=substr(iconv('UTF-8', 'ISO-8859-2',$data[$cntr]->name),0,$nameLimit);
            $pdf->SetFont('arial','',8);
            $pdf->SetY($GRID_OFFSET_VERTICAL+($r+1)*$BAND_HEIGHT);
            $pdf->SetX($GRID_OFFSET_HORIZONTAL+$i*$GRID_WIDTH);
            $pdf->Cell($GRID_WIDTH,5,$name,0,0,'C', false);
         }
         if ($showType) 
         {
            $type=substr(iconv('UTF-8', 'ISO-8859-2',$data[$cntr]->type),0,$typeLimit);
            $pdf->SetFont('arial','',8);
            $pdf->SetY($GRID_OFFSET_VERTICAL+3+($r+1)*$BAND_HEIGHT);
            $pdf->SetX($GRID_OFFSET_HORIZONTAL+$i*$GRID_WIDTH);
            $pdf->Cell($GRID_WIDTH,5,$type,0,0,'C', false);
         }
        if ($vector)
            //$pdf->ImageText('http://localhost/qr/index.php?format=TEXT&filename=temp.txt&data='.$href.'&level=H',$QR_OFFSET_HORIZONTAL+$GRID_OFFSET_HORIZONTAL+$i*$GRID_WIDTH,$QR_OFFSET_VERTICAL+$GRID_OFFSET_VERTICAL+$r*$BAND_HEIGHT,$QR_SIZE,$QR_SIZE, $blackWhite);
            $pdf->ImageText('http://qr.edocu.sk/?format=TEXT&filename=temp.txt&data='.$href.'&level=H',$QR_OFFSET_HORIZONTAL+$GRID_OFFSET_HORIZONTAL+$i*$GRID_WIDTH,$QR_OFFSET_VERTICAL+$GRID_OFFSET_VERTICAL+$r*$BAND_HEIGHT,$QR_SIZE,$QR_SIZE, $blackWhite);
         else   
            //$pdf->Image('http://localhost/qr/index.php?data='.$href.'&level=H&size=10&border=0&blackWhite='.$blackWhite,$QR_OFFSET_HORIZONTAL+$GRID_OFFSET_HORIZONTAL+$i*$GRID_WIDTH,$QR_OFFSET_VERTICAL+$GRID_OFFSET_VERTICAL+$r*$BAND_HEIGHT,$QR_SIZE,$QR_SIZE,'PNG');
            $pdf->Image('http://qr.edocu.sk/?data='.$href.'&level=H&size=10&border=0&blackWhite='.$blackWhite,$QR_OFFSET_HORIZONTAL+$GRID_OFFSET_HORIZONTAL+$i*$GRID_WIDTH,$QR_OFFSET_VERTICAL+$GRID_OFFSET_VERTICAL+$r*$BAND_HEIGHT,$QR_SIZE,$QR_SIZE,'PNG');
         if ($drawCircle) 
         {
            $pdf->Circle($GRID_OFFSET_HORIZONTAL+($i+0.5)*$GRID_WIDTH, $GRID_OFFSET_VERTICAL+$r*$BAND_HEIGHT+0.5*$GRID_HEIGHT,$DIAMETER/2);
         }
         $cntr++;
      }
   }
}

if ($BACKGROUND_FILE <> "")
{
   unlink($backgroundFileName);
}

if ($LOGO_FILE <> "")
{
   unlink($logoFileName);
}

// print list of QR codes at the end of document
if ($showList)
{
   $pdf->SetFont('arial','',10);

   $rowSize=6;
   $cntr=0;
   $rowsPerPage=($PAGE_HEIGHT-40)/$rowSize;

   while ($cntr < $qrCount)      // one cycle = one page
   {
      $pdf->AddPage(); 
      for ($r=0; $r<$rowsPerPage && $cntr < $qrCount; $r++)
      {
         $href=$data[$cntr]->href;
         $pdf->SetXY(20,20+$rowSize*$r);
         $pdf->Cell(0,0,$cntr+1);
         $pdf->SetXY(50,20+$rowSize*$r);
         $pdf->Cell(0,0,$href);
         $cntr++;
      }
   }

}

$pdf->Output($OUTPUT_NAME, 'I');

?>
