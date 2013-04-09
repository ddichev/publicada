<?php
 
class Publicada_Image_SimpleImage {
   
   protected $image;
   protected $image_type;
 
   function load($filename)
   {
      $image_info = getimagesize($filename);
      $this->image_type = $image_info[2];
      if ( $this->image_type == IMAGETYPE_JPEG ) {
         $this->image = imagecreatefromjpeg($filename);
      }
	  elseif( $this->image_type == IMAGETYPE_GIF ) {
         $this->image = imagecreatefromgif($filename);
      }
	  elseif( $this->image_type == IMAGETYPE_PNG ) {
         $this->image = imagecreatefrompng($filename);
      }
   }
   
   function save($filename, $image_type=IMAGETYPE_JPEG, $compression=75, $permissions=null)
   {
      if ( $image_type == IMAGETYPE_JPEG ) {
         imagejpeg($this->image,$filename,$compression);
      }
	  elseif ( $image_type == IMAGETYPE_GIF ) {
         imagegif($this->image,$filename);         
      }
	  elseif ( $image_type == IMAGETYPE_PNG ) {
         imagepng($this->image,$filename);
      }   
      if ( $permissions != null) {
         chmod($filename,$permissions);
      }
   }
   
   function output($image_type = IMAGETYPE_JPEG)
   {
      if ( $image_type == IMAGETYPE_JPEG ) {
         imagejpeg($this->image);
      }
	  elseif ( $image_type == IMAGETYPE_GIF ) {
         imagegif($this->image);         
      }
	  elseif ( $image_type == IMAGETYPE_PNG ) {
         imagepng($this->image);
      }   
   }
   
   function getWidth()
   {
      return imagesx($this->image);
   }
   
   function getHeight()
   {
      return imagesy($this->image);
   }
   
   function resizeToHeight($height)
   {
      $ratio = $height / $this->getHeight();
      $width = $this->getWidth() * $ratio;
      $this->resize($width,$height);
   }
   
   function resizeToWidth($width)
   {
      $ratio = $width / $this->getWidth();
      $height = $this->getheight() * $ratio;
      $this->resize($width,$height);
   }
   
   function scale($scale)
   {
      $width = $this->getWidth() * $scale/100;
      $height = $this->getheight() * $scale/100; 
      $this->resize($width,$height);
   }
   
   function resize($width, $height)
   {
   		if ($this->getWidth() < $width) { // don't allow wider than original
   			$width = $this->getWidth();
   		}
   		if ($this->getHeight() < $height) { // don't allow higher than original
   			$height = $this->getHeight();
   		}

		$desiredRatio = $width / $height; // ratio of output image

		$sourceRatio = $this->getWidth() / $this->getHeight(); // ratio of input image
		
		if ($this->getWidth() > $this->getHeight()) { // horizontal
			if ($desiredRatio > 1) { // horizontal from horizontal
				if ($desiredRatio > $sourceRatio) { // wider than original
					$src_width = $this->getWidth();
					$src_height = $this->getWidth() / $desiredRatio;

					$src_x = 0;
					$src_y = $this->getHeight() - ($src_height + (($this->getHeight() - $src_height) / 2));
				}
				elseif ($desiredRatio < $sourceRatio) { // narrower than original
					$src_width = $this->getHeight() * $desiredRatio;
					$src_height = $this->getHeight();

					$src_x = $this->getWidth() - ($src_width + (($this->getWidth() - $src_width) / 2));
					$src_y = 0;
				}
				else { // same as original
					$src_width = $this->getWidth();
					$src_height = $this->getHeight();

					$src_x = 0;
					$src_y = 0;
				}
			}
			else { // vertical or square from horizontal
				$src_width = $this->getHeight() * $desiredRatio;
				$src_height = $this->getHeight();

				$src_x = $this->getWidth() - ($src_width + (($this->getWidth() - $src_width) / 2));
				$src_y = 0;
			}
		}

		elseif ($this->getHeight() > $this->getWidth()) { // vertical
			if ($desiredRatio < 1) { // vertical from vertical
				if ($desiredRatio > $sourceRatio) { // wider than original
					$src_width = $this->getWidth();
					$src_height = $this->getWidth() / $desiredRatio;

					$src_x = 0;
					$src_y = $this->getHeight() - ($src_height + (($this->getHeight() - $src_height) / 2));
				}
				elseif ($desiredRatio < $sourceRatio) { // narrower than original
					$src_width = $this->getHeight() * $desiredRatio;
					$src_height = $this->getHeight();

					$src_x = $this->getWidth() - ($src_width + (($this->getWidth() - $src_width) / 2));
					$src_y = 0;
				}
				else {
					$src_width = $this->getWidth();
					$src_height = $this->getWidth() / $desiredRatio;
	
					$src_x = 0;
					$src_y = $this->getHeight() - ($src_height + (($this->getHeight() - $src_height) / 2));
				}
			}
			else { // horizontal or square from vertical
				$src_width = $this->getWidth();
				$src_height = $this->getWidth() / $desiredRatio;

				$src_x = 0;
				$src_y = $this->getHeight() - ($src_height + (($this->getHeight() - $src_height) / 2));
			}
		}
		
		else { // square
			if ($desiredRatio < $sourceRatio) { // vertical from square
				$src_width = $this->getHeight() * $desiredRatio;
				$src_height = $this->getHeight();

				$src_x = $this->getWidth() - ($src_width + (($this->getWidth() - $src_width) / 2));
				$src_y = 0;
			}
			elseif ($desiredRatio > $sourceRatio) { // horizontal from square
				$src_width = $this->getWidth();
				$src_height = $this->getWidth() / $desiredRatio;

				$src_x = 0;
				$src_y = $this->getHeight() - ($src_height + (($this->getHeight() - $src_height) / 2));
			}
			else { // square from square
				$src_width = $this->getWidth();
				$src_height = $this->getWidth() / $desiredRatio;

				$src_x = 0;
				$src_y = 0;
			}
		}
		
		$new_image = imagecreatetruecolor($width, $height);
		// Allocate white color
		$bg = imagecolorallocate($new_image, 255, 255, 255);
		// Fill with white (otherwise filled with black)
		imagefill($new_image, 0, 0, $bg);
		imagecopyresampled($new_image, $this->image, 0, 0, $src_x, $src_y, $width, $height, $src_width, $src_height);
		$this->image = $new_image;
   }      

	
}
