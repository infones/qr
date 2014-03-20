<?php
// Generation of font definition file for tutorial 7
require('/var/www/localhost/htdocs/eDocu/trash/fpdf17/makefont/makefont.php');

$dir = opendir('/usr/share/fonts/corefonts/');
while (($relativeName = readdir($dir)) !== false) {
   if ($relativeName == '..' || $relativeName == '.')
      continue;
   MakeFont("/usr/share/fonts/corefonts/$relativeName",'ISO-8859-2');
}
?>
