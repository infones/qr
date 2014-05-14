<?php
/*
 * PHP QR Code encoder
 *
 * Image output of code using GD2
 *
 * PHP QR Code is distributed under LGPL 3
 * Copyright (C) 2010 Dominik Dzienia <deltalab at poczta dot fm>
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 */
 
    define('QR_IMAGE', true);

    class QRimage {
    
        //----------------------------------------------------------------------
        public static function eps($frame, $filename = false, $pixelPerPoint = 4, $outerFrame = 4, $blackWhite=0) 
        {
            $h = count($frame);
            $w = strlen($frame[0]);

            $imgW = $w + 2*$outerFrame;
            $imgH = $h + 2*$outerFrame;
            $scaledW = $imgW * $pixelPerPoint;
            $scaledH = $imgH * $pixelPerPoint;

            if ($blackWhite==1)
            {
               $blockColor1 = '000000';
               $blockColor2 = '404040';
            }
            elseif ($blackWhite==2)
            {
               $blockColor1 = '000000';
               $blockColor2 = '000000';
            }
            else
            {
               $blockColor1 = '003AC3';
               $blockColor2 = 'FF0000';
            }

            $blockColorR = round(hexdec(substr($blockColor1, 0, 2)) / 255, 5);
            $blockColorG = round(hexdec(substr($blockColor1, 2, 2)) / 255, 5);
            $blockColorB = round(hexdec(substr($blockColor1, 4, 2)) / 255, 5);
            $blockColor1 = $blockColorR.' '.$blockColorG.' '.$blockColorB;

            $blockColorR = round(hexdec(substr($blockColor2, 0, 2)) / 255, 5);
            $blockColorG = round(hexdec(substr($blockColor2, 2, 2)) / 255, 5);
            $blockColorB = round(hexdec(substr($blockColor2, 4, 2)) / 255, 5);
            $blockColor2 = $blockColorR.' '.$blockColorG.' '.$blockColorB;

            $partone = true;

            date_default_timezone_set('Europe/Prague');

            $output = 
                "%!PS-Adobe EPSF-3.0\n".
                "%%CreationDate: ".date('Y-m-d')."\n".
                "%%Title: QR-code\n".
                "%%Pages: 1\n".
                "%%DocumentData: Clean7Bit\n".
                "%%LanguageLevel: 2\n".
                "%%BoundingBox: 0 0 $scaledW $scaledH\n".
                "$pixelPerPoint $pixelPerPoint scale\n".
                "$outerFrame $outerFrame translate\n".
                "$blockColor1 setrgbcolor\n".
                "/r {rectfill} def\n";

            for($x=0; $x<$w; $x++) {
                for($y=0; $y<$h; $y++) {
                    if ($x + $y < $w) {
                       if ($partone)
                       {
                           $partone=false;
                           $output.="$blockColor1 setrgbcolor\n";
                       }
                    }
                    else
                    {
                       if (!$partone)
                       {
                           $partone=true;
                           $output.="$blockColor2 setrgbcolor\n";
                       }
                    }  
                    if ($frame[$y][$x] == '1') {
                        $_y = $h - 1 - $y;
                        $output .= "$x $_y 1 1 r\n";
                    }
                }
            }

            $output .= '%%EOF';


            if ($filename !== FALSE) {
                file_put_contents($filename, $output);
            }
            else
            {
                Header("Content-type: application/postscript");
                printf("%s\n", $output);
            }

            return $output;
        }
    
        //----------------------------------------------------------------------
        public static function png($frame, $filename = false, $pixelPerPoint = 4, $outerFrame = 4,$saveandprint=FALSE, $blackWhite=0) 
        {
            $image = self::image($frame, $pixelPerPoint, $outerFrame, $blackWhite);
            
            if ($filename === false) {
                Header("Content-type: image/png");
                ImagePng($image);
            } else {
                if($saveandprint===TRUE){
                    ImagePng($image, $filename);
                    header("Content-type: image/png");
                    ImagePng($image);
                }else{
                    ImagePng($image, $filename);
                }
            }
            
            ImageDestroy($image);
        }
    
        //----------------------------------------------------------------------
        public static function jpg($frame, $filename = false, $pixelPerPoint = 8, $outerFrame = 4, $q = 85, $blackWhite=0) 
        {
            $image = self::image($frame, $pixelPerPoint, $outerFrame, $blackWhite);
            
            if ($filename === false) {
                Header("Content-type: image/jpeg");
                ImageJpeg($image, null, $q);
            } else {
                ImageJpeg($image, $filename, $q);            
            }
            
            ImageDestroy($image);
        }
    
        //----------------------------------------------------------------------
        private static function image($frame, $pixelPerPoint = 4, $outerFrame = 4, $blackWhite=0) 
        {
            $h = count($frame);
            $w = strlen($frame[0]);
            
            $imgW = $w + 2*$outerFrame;
            $imgH = $h + 2*$outerFrame;
            
            $base_image =ImageCreate($imgW, $imgH);

            $col[0] = ImageColorAllocate($base_image,255,255,255);
            if ($blackWhite==1)
            {
               $col[1] = ImageColorAllocate($base_image,64,64,64);
               $col[2] = ImageColorAllocate($base_image,0,0,0);
            }
            elseif ($blackWhite==2)
            {
               $col[1] = ImageColorAllocate($base_image,0,0,0);
               $col[2] = ImageColorAllocate($base_image,0,0,0);
            }
            else
            {
               $col[1] = ImageColorAllocate($base_image,0,58,195);
               $col[2] = ImageColorAllocate($base_image,255,0,0);
            }

            imagefill($base_image, 0, 0, $col[0]);
            for($y=0; $y<$h; $y++) {
                for($x=0; $x<$w; $x++) {
                    if ($frame[$y][$x] == '1') {
                        if ($x + $y < $w) {
                            ImageSetPixel($base_image,$x+$outerFrame,$y+$outerFrame,$col[1]); 
                        } else {
                            ImageSetPixel($base_image,$x+$outerFrame,$y+$outerFrame,$col[2]); 
                        }
                        
                    }
                }
            }
            
            $target_image =ImageCreate($imgW * $pixelPerPoint, $imgH * $pixelPerPoint);
            ImageCopyResized($target_image, $base_image, 0, 0, 0, 0, $imgW * $pixelPerPoint, $imgH * $pixelPerPoint, $imgW, $imgH);
            ImageDestroy($base_image);
            
            return $target_image;
        }
    }

