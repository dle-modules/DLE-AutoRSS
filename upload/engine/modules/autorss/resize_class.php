<?php
/*
 =============================================================================
 resize_class - класс для ресайза картинок в модулях от ПафНутиЙ. 
 ============================================================================= 

 Информация об оригинальном классе:
 # ========================================================================#
 #
 #  Author:    Rajani .B
 #  Version:	 1.0
 #  Date:      07-July-2010
 #  Purpose:   Resizes and saves image
 #  Requires : Requires PHP5, GD library.
 #  Usage Example:
 #                     include("resize_class.php");
 #                     $resizeObj = new resize('images/cars/large/input.jpg');
 #                     // $resizeObj = new resize('http://yandex.st/www/1.526/yaru/i/logo.png');
 #                     $resizeObj -> resizeImage(150, 100, 0);
 #                     $resizeObj -> saveImage('images/cars/large/output.jpg', 100);
 #
 #
 # ========================================================================# 

 Доработка класса под нужды модулей: 
 =============================================================================
 Автор:   ПафНутиЙ 
 URL:     http://pafnuty.name/
 twitter: https://twitter.com/pafnuty_name
 google+: http://gplus.to/pafnuty
 email:   pafnuty10@gmail.com
 =============================================================================
*/

if (!defined('DATALIFEENGINE')) die("Go fuck yourself!");

Class resize {
	// *** Class variables
	private $image;
	private $width;
	private $height;
	private $imageResized;
	private $extension;

	function __construct($fileName) {
		// Если $fileName начинается с http:// или https:// - открываем ее с Curl
		if (preg_match('~^http(s)?://~', $fileName)) {
			$this->image = $this->openImageWithCurl($fileName);
		}
		else {
			$this->image = $this->openImage($fileName);
		}

		// *** Get width and height
		$this->width  = @imagesx($this->image);
		$this->height = @imagesy($this->image);
	}

	## --------------------------------------------------------

	private function openImage($file) {
		$extension       = strtolower(strrchr($file, '.'));
		$this->extension = $extension == '.jpg' ? '.jpeg' : $extension;
		$func            = 'imagecreatefrom' . substr($this->extension, 1);
		if (!function_exists($func)) return false;

		return $func($file);
	}

	## --------------------------------------------------------

	private function openImageWithCurl($url) {
		$extension       = strtolower(strrchr($url, '.'));
		$this->extension = $extension == '.jpg' ? '.jpeg' : $extension;

		$file = $this->curlRequest($url);
		if ($file == '') return false;

		return imagecreatefromstring($file);
	}

	## --------------------------------------------------------

	private function curlRequest($url) {
		$ext_headers = array('Expect:', 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9', 'Accept-Language: ru,en-us;q=0.7,en;q=0.7', 'Accept-Charset: utf-8,windows-1251;q=0.7,*;q=0.5',);
		$ch          = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		// curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_MAXREDIRS, 7);
		curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_VERBOSE, 2);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; rv:12.0) Gecko/20120403211507 Firefox/12.0');
		curl_setopt($ch, CURLOPT_HTTPHEADER, $ext_headers);
		$contents = curl_exec($ch);
		curl_close($ch);

		return $contents;
	}

	## --------------------------------------------------------

	public function resizeImage($newWidth, $newHeight, $option = "auto") {
		// *** Get optimal width and height - based on $option
		$optionArray = $this->getDimensions($newWidth, $newHeight, $option);

		$optimalWidth  = $optionArray['optimalWidth'];
		$optimalHeight = $optionArray['optimalHeight'];


		// *** Resample - create image canvas of x, y size
		$this->imageResized = imagecreatetruecolor($optimalWidth, $optimalHeight);

		if ($this->extension == '.png' || $this->extension == '.gif') {
			imagecolortransparent($this->imageResized, imagecolorallocatealpha($this->imageResized, 0, 0, 0, 127));
			imagealphablending($this->imageResized, false);
			imagesavealpha($this->imageResized, true);
		}
		imagecopyresampled($this->imageResized, $this->image, 0, 0, 0, 0, $optimalWidth, $optimalHeight, $this->width, $this->height);
		// *** if option is 'crop', then crop too
		if ($option == 'crop') {
			$this->crop($optimalWidth, $optimalHeight, $newWidth, $newHeight);
		}
	}

	## --------------------------------------------------------

	private function getDimensions($newWidth, $newHeight, $option) {
		$newWidth  = ($newWidth == '0') ? $this->width : $newWidth;
		$newHeight = ($newHeight == '0') ? $this->height : $newHeight;

		switch ($option) {
			case 'exact':
				$optimalWidth  = $newWidth;
				$optimalHeight = $newHeight;
				break;
			case 'portrait':
				$optimalWidth  = $this->getSizeByFixedHeight($newHeight);
				$optimalHeight = $newHeight;
				break;
			case 'landscape':
				$optimalWidth  = $newWidth;
				$optimalHeight = $this->getSizeByFixedWidth($newWidth);
				break;
			case 'auto':
				$optionArray   = $this->getSizeByAuto($newWidth, $newHeight);
				$optimalWidth  = $optionArray['optimalWidth'];
				$optimalHeight = $optionArray['optimalHeight'];
				break;
			case 'crop':
				$optionArray   = $this->getOptimalCrop($newWidth, $newHeight);
				$optimalWidth  = $optionArray['optimalWidth'];
				$optimalHeight = $optionArray['optimalHeight'];
				break;
		}

		return array('optimalWidth' => $optimalWidth, 'optimalHeight' => $optimalHeight);
	}

	## --------------------------------------------------------

	private function getSizeByFixedHeight($newHeight) {
		$ratio    = $this->width / $this->height;
		$newWidth = $newHeight * $ratio;

		return $newWidth;
	}

	private function getSizeByFixedWidth($newWidth) {
		$ratio     = $this->height / $this->width;
		$newHeight = $newWidth * $ratio;

		return $newHeight;
	}

	private function getSizeByAuto($newWidth, $newHeight) {
		if ($this->height < $this->width) // *** Image to be resized is wider (landscape)
		{
			$optimalWidth  = $newWidth;
			$optimalHeight = $this->getSizeByFixedWidth($newWidth);
		}
		elseif ($this->height > $this->width) // *** Image to be resized is taller (portrait)
		{
			$optimalWidth  = $this->getSizeByFixedHeight($newHeight);
			$optimalHeight = $newHeight;
		}
		else // *** Image to be resizerd is a square
		{
			if ($newHeight < $newWidth) {
				$optimalWidth  = $newWidth;
				$optimalHeight = $this->getSizeByFixedWidth($newWidth);
			}
			else if ($newHeight > $newWidth) {
				$optimalWidth  = $this->getSizeByFixedHeight($newHeight);
				$optimalHeight = $newHeight;
			}
			else {
				// *** Sqaure being resized to a square
				$optimalWidth  = $newWidth;
				$optimalHeight = $newHeight;
			}
		}

		return array('optimalWidth' => $optimalWidth, 'optimalHeight' => $optimalHeight);
	}

	## --------------------------------------------------------

	private function getOptimalCrop($newWidth, $newHeight) {

		$heightRatio = $this->height / $newHeight;
		$widthRatio  = $this->width / $newWidth;

		if ($heightRatio < $widthRatio) {
			$optimalRatio = $heightRatio;
		}
		else {
			$optimalRatio = $widthRatio;
		}

		$optimalHeight = $this->height / $optimalRatio;
		$optimalWidth  = $this->width / $optimalRatio;

		return array('optimalWidth' => $optimalWidth, 'optimalHeight' => $optimalHeight);
	}

	## --------------------------------------------------------

	private function crop($optimalWidth, $optimalHeight, $newWidth, $newHeight) {
		// *** Find center - this will be used for the crop
		$cropStartX = ($optimalWidth / 2) - ($newWidth / 2);
		$cropStartY = ($optimalHeight / 2) - ($newHeight / 2);

		$crop = $this->imageResized;
		//imagedestroy($this->imageResized);

		// *** Now crop from center to exact requested size
		$this->imageResized = imagecreatetruecolor($newWidth, $newHeight);

		if ($this->extension == '.png' || $this->extension == '.gif') {
			imagecolortransparent($this->imageResized, imagecolorallocatealpha($this->imageResized, 0, 0, 0, 127));
			imagealphablending($this->imageResized, false);
			imagesavealpha($this->imageResized, true);
		}
		imagecopyresampled($this->imageResized, $crop, 0, 0, $cropStartX, $cropStartY, $newWidth, $newHeight, $newWidth, $newHeight);
	}

	## --------------------------------------------------------

	public function saveImage($savePath, $imageQuality = "100") {
		$save = 'image' . substr($this->extension, 1);
		if (!function_exists($save)) //хотя проверка в принципе не нужна, т.к. уже была при открытии
		return false;
		($this->extension == '.gif') ? $save ($this->imageResized, $savePath) : $save ($this->imageResized, $savePath, ($this->extension == '.png' ? (9 - round(($imageQuality / 100) * 9)) : $imageQuality)// die($savePath);
		);
		imagedestroy($this->imageResized);
	}


	## --------------------------------------------------------

}

?>