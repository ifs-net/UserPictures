<?php
include_once("ImageResizeClass.php");

/**
* class ImageResizeFactory
*
* { Description :- 
*	This Class is a factory method class which returns the appropriate object of ImageResizeClass depending on the type of Image 
*	i.e jpg or Png.
* }
*/

class ImageResizeFactory
{
	/**
	* Method ImageResizeFactory::getInstanceOf()
	*
	* { Description :- 
	*	This method resizes the image.
	* }
	*/
	
	function getInstanceOf($imageName, $resizedImageName, $newWidth, $newHeight)
	{
		$extension = explode(".", $imageName);
		if(preg_match("/jpg|JPG|jpeg|JPEG/", end($extension)))
		{
			return new ImageResizeJpeg($imageName, $resizedImageName, $newWidth, $newHeight);
		}
		elseif(preg_match("/png|PNG/", end($extension)))
		{
			return new ImageResizePng($imageName, $resizedImageName, $newWidth, $newHeight);
		}
	}
}
?>