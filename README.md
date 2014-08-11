QR Code generator
=================

Usage
-----

http://qr.edocu.sk/?data=http://example.com

### Parameters:

* **data  - content**
* level - error correction level ("L"ow,"M"edium,"Q"uarter,"H"igh) (*default: H*)
* size  - pixels per point (*default: 5*)
* border - border size in points (*default: 2*)
* fileName - PNG file name (*default: qrcode.png*) 
* blackWhite - color schema 0=color 1=gray 2=B&W (*default=0*)
* format - output format - PNG, EPS, TEXT, RAW (*default=PNG*)



QR Code grid to PDF
===================

Usage
-----

http://qr.edocu.sk/qrgridpdf.php?source=list_of_qrcodes_json.php&preset=A4_4x3
http://qr.edocu.sk/qrgridpdf.php?source=list_of_qrcodes_json.php&rowCount=4&cellCount=6&pageUnits=mm&pageWidth=320&pageHeight=280&qrSize=38

### Parameters:

* **source - reference to JSON list of QR codes content**
* circle - draw cutoff circle (*default: 1*)
* rowCount - number of rows/bands (*default=3*)
* cellCount - number of cella per band (*default=4*)
* pageWidth - page width in pageUnits (*default=297*)
* pageHeight - page height in pageUnits (*default=210*)
* pageOrient - page orientation P/L (*default=L*)
* pageUnits - units (*default=mm*)
* bandHeight - height of one band/line (*default=68*)
* gridHeight - height of one cell (*default=66*)
* gridWidth - width of one cell (*default=70*)
* diameter -  cutoff circle diameter (*default=30*)
* gridOffsetVertical - grid vertical offset to upleft corner of the page (*default=4*)
* gridOffsetHorizontal - grid horizontal offset to upleft corner of the page (*default=8*)
* qrOffsetVertical - QR code offset to grid (*default=5*)
* qrOffsetHorizontal - QR code offset to grid (*default=7*)
* qrSize - size of QR (*default=56*)
* backgroundFile - background grid file (*default=""*)
* logoFile - logo file (*designfile=""*)
* outputName - name of output file (*default=qrgrid.pdf*)
* preset - page layout preset (*"60_1x1", "A4_4x3" or "ch_8x5"*) 
* showSerial - show cell serial number (*default=0*)
* blackWhite - QR code color schema 0=color 1=gray 2=B&W (*default=0*)
* showName - show element name (*default=0*)
* showType - show element type (*default=0*)
* vector - print QR code as vector instead of PNG (*defult=0*)
* nameLimit - limit QR name length (*default=15*)
* typeLimit - limit QR type length (*default=15*)
* level - error correction level ("L"ow,"M"edium,"Q"uarter,"H"igh, default=H) 

