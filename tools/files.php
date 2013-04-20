<?php
/*
 * Title:   Toolbox
 * Author:  Qzofp Productions
 * Version: 0.1
 *
 * File:    files.php
 *
 * Created on Mar 04, 2013
 * Updated on Apr 20, 2013
 *
 * Description: Files and I/O toolbox functions.
 *
 */


/*
 * Function:	ResizeJpegImage
 *
 * Created on Mar 04, 2013
 * Updated on Mar 04, 2013
 *
 * Description: Resize an Image and save it to a file.
 *
 * In:	$image, $newWidth, $newHeight, $destination
 * Out:	Saved file.
 *
 * Note: $destination = 'd:/temp/img.jpg' or 'tmp/img.jpeg'
 * 
 * From: http://php.net/manual/en/function.imagejpeg.php
 * 
 */

function ResizeJpegImage($image, $new_w, $new_h, $destination)
{
    list($old_w, $old_h) = getimagesize($image);
    $img = imagecreatefromjpeg($image);
    
    // Create a new temporary image.
    $tmp_img = imagecreatetruecolor($new_w, $new_h);
    
    // Copy and resize old image into new image.
    //imagecopyresized($tmp_img, $img, 0, 0, 0, 0, $new_w, $new_h, $old_w, $old_h);
    imagecopyresampled($tmp_img, $img, 0, 0, 0, 0, $new_w, $new_h, $old_w, $old_h); // Better result, quality.
    
    // Use output buffering to capture outputted image stream.
    ob_start();
    imagejpeg($tmp_img);
    $i = ob_get_clean();
    
    // Save file
    $fp = fopen ($destination, 'w');
    fwrite ($fp, $i);
    fclose ($fp);
}


/*
 * Function:	DeleteFile
 *
 * Created on Mar 27, 2013
 * Updated on Mar 27, 2013
 *
 * Description: Resize an Image and save it to a file.
 *
 * In:	$file(s)
 * Out:	Deleted file(s).
 *
 * Note: $file = 'd:/temp/img.jpg' or 'tmp/img.jpeg' or tmp/*.jpg
 * 
 * From: http://www.php.net/manual/en/function.unlink.php
 * 
 */
function DeleteFile($file)
{
    array_map( "unlink", glob($file ));
}


?>
