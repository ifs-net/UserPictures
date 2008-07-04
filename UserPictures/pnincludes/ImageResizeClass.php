<?php
/**
* class ImageResizeClass
*
* { Description :- 
*	Class to Resize jpg and png image files using gd 2.0. 
* }
*/
class ImageResizeClass
{
	var $imageName;
	var $resizedImageName;
	var $newWidth;
	var $newHeight;
	var $src_image;
	var $dest_image;

	/**
	* Method ImageResizeClass::resizeImage()
	*
	* { Description :- 
	*	This method resizes the image.
	* }
	*/
	
	function resizeImage()
	{
		$old_x = imagesx($this->src_image);
		$old_y = imagesy($this->src_image);
		
		if($old_x > $old_y)
		{
			$thumb_w = $this->newWidth;
			$thumb_h = $old_y*($this->newHeight/$old_x);
		}
		
		if($old_x < $old_y)
		{
			$thumb_w = $old_x*($this->newWidth/$old_y);
			$thumb_h = $this->newHeight;
		}
		
		if($old_x == $old_y)
		{
			$thumb_w = $this->newWidth;
			$thumb_h = $this->newHeight;
		}
		
		$this->dest_image = imagecreatetruecolor($thumb_w, $thumb_h);
		imagecopyresized($this->dest_image, $this->src_image, 0, 0, 0, 0, $thumb_w, $thumb_h, $old_x, $old_y);
		
	}
}

class ImageResizeJpeg extends ImageResizeClass
{
	/**
	* Method ImageResizeJpeg::ImageResizeJpeg()
	*
	* { Description :- 
	*	This method is a constructor for the ImageResizeJpeg (Subclass for JPEG image resizing).
	* }
	*/
	
	function ImageResizeJpeg($imageName, $resizedImageName, $newWidth, $newHeight)
	{
		$this->imageName = $imageName;
		$this->resizedImageName = $resizedImageName;
		$this->newWidth = $newWidth;
		$this->newHeight = $newHeight;
	}
	
	/**
	* Method ImageResizeJpeg::getResizedImage()
	*
	* { Description :- 
	*	This method puts the resized image in the specified destination.
	* }
	*/
	
	function getResizedImage()
	{
		$this->src_image = imagecreatefromjpeg($this->imageName);
		$this->resizeImage();
		imagejpeg($this->dest_image, $this->resizedImageName);
	}
}	

class ImageResizePng extends ImageResizeClass
{
	/**
	* Method ImageResizePng::ImageResizePng()
	*
	* { Description :- 
	*	This method is a constructor for the ImageResizePng (Subclass for Png image resizing).
	* }
	*/
	
	function ImageResizePng($imageName, $resizedImageName, $newWidth, $newHeight)
	{
		$this->imageName = $imageName;
		$this->resizedImageName = $resizedImageName;
		$this->newWidth = $newWidth;
		$this->newHeight = $newHeight;
	}
	
	
	/**
	* Method ImageResizePng::getResizedImage()
	*
	* { Description :- 
	*	This method puts the resized image in the specified destination.
	* }
	*/
	
	function getResizedImage()
	{
		$this->src_image = imagecreatefrompng($this->imageName);
		$this->resizeImage();
		imagepng($this->dest_image, $this->resizedImageName);
	}
}
?>