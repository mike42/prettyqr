<?php
/*	PrettyQR PHP library - http://bitrevision.com
	A class to make QR codes aesthetically pleasing.

	Copyright (C) 2012 Michael Billington <michael.billington@gmail.com>

	Permission is hereby granted, free of charge, to any person obtaining a
	copy of this software and associated documentation files
	(the "Software"), to deal in the Software without restriction, including
	without limitation the rights to use, copy, modify, merge, publish,
	distribute, sublicense, and/or sell copies of the Software, and to
	permit persons to whom the Software is furnished to do so, subject to
	the following conditions:

	The above copyright notice and this permission notice shall be included
	in all copies or substantial portions of the Software.

	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
	OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
	MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
	IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY
	CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,
	TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
	SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/

class PrettyQR {
	function generate($filename, $text, $logo_file = false, $tile_filename = "square") {
		/* Generates a code with phpqrcode, then prettifies it */
		require_once(dirname(__FILE__)."/../phpqrcode/qrlib.php");
		QRcode::png($text, $filename, 'H', 1, 0);
		PrettyQR::prettify($filename, $logo_file, '', $tile_filename);
		return true;
	}

	function prettify($file, $logo_file, $output = '', $tile_filename = "square") {
		/* Takes a minimal QR code and scales it up, optionally adding a logo */
		/* These are the objects we work with: */
		$template_base = dirname(__FILE__)."/template/";
		$template = new Imagick();
		$qr = new Imagick();
		$tile = new Imagick();

		/* Load all the right images */
		$qr -> readImage($file);
		$tile_filename = $template_base . "tile-". $tile_filename . ".png";
		$tile -> readImage($tile_filename);
		$geometry = $qr -> getImageGeometry();
		$code_version = ($geometry['width'] - 17) / 4;

		/* Scale up evenly */
		$tile_geometry	= $tile -> getImageGeometry();
		$oldsize	= $geometry['width'];
		$tilesize	= $tile_geometry['width'];
		$size		= $oldsize * $tilesize;
		$qr -> scaleImage($size, $size);


		/* Tile up the QR code */
		for($y = 0; $y < $oldsize; $y++) {
			for($x = 0; $x < $oldsize; $x++) {
				$qr -> compositeImage($tile, imagick::COMPOSITE_OVER, $x * $tilesize, $y * $tilesize);
			}
		}

		/* Copy template back over (preserves tracking boxes */
		$template_filename = $template_base . "template-$code_version.png";
		if(file_exists($template_filename)) {
			$template -> readImage($template_filename);
			$template -> scaleImage($size, $size); /* Same as image */
			$qr -> compositeImage($template, imagick::COMPOSITE_OVER, 0, 0 );
		}

		/* Add the logo in the middle */
		if($logo_file && file_exists($logo_file)) {
			$logo = new Imagick();
			$logo -> readImage($logo_file);
			$logo_size = $logo -> getImageGeometry();
			$x = ($size - $logo_size['width']) / 2;
			$y = ($size - $logo_size['height']) / 2;
			$qr -> compositeImage($logo, imagick::COMPOSITE_OVER, $x, $y);
		}

		/* Output image */
		if($output != '') {
			$qr -> setImageFileName($output);
		}
		$qr -> writeImage();
		return true;
	}
}
?>
