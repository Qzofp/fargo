<?php
/*
 * Title:   Toolbox
 * Author:  Qzofp Productions
 * Version: 0.1
 *
 * File:    files.php
 *
 * Created on Mar 04, 2013
 * Updated on Jun 21, 2013
 *
 * Description: Files and I/O toolbox functions.
 *
 */

/*
 * Function:	ResizeJpegImage
 *
 * Created on Mar 04, 2013
 * Updated on Jun 21, 2013
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
    
    // Check extension.
    $ext = strtolower(pathinfo($image, PATHINFO_EXTENSION));
    if ($ext == 'jpg')
    {
        $img = @imagecreatefromjpeg($image);
    }
    else // if png.
    {
        $img = @imagecreatefrompng($image);
    }
 
    // Check if the creation of the image failed!
    if (!$img) 
    {
        /* Create a blank image */
        $img = imagecreatetruecolor($new_w, $new_h);
        $bgc = imagecolorallocate($img, 255, 255, 255);
        $tc  = imagecolorallocate($img, 0, 0, 0);

        imagefilledrectangle($img, 0, 0, $new_w, $new_h, $bgc);

        /* Output an error message */
        imagestring($img, 1, 5, 5, 'Error loading ' . $image, $tc);
    }

    // Create a new temporary image.
    $tmp_img = imagecreatetruecolor($new_w, $new_h);
    
    // Copy and resize old image into new image.
    imagecopyresampled($tmp_img, $img, 0, 0, 0, 0, $new_w, $new_h, $old_w, $old_h); // Better result, quality.
    
    // Use output buffering to capture outputted image stream.
    ob_start();
    imagejpeg($tmp_img);
    $i = ob_get_clean();
    
    imagedestroy ($img);
    imagedestroy ($tmp_img);
    
    // Save file
    $fp = fopen ($destination, 'w');
    fwrite ($fp, $i);
    fclose ($fp);
}

/*
 * Function:	DownloadFile
 *
 * Created on Apr 22, 2013
 * Updated on Apr 22, 2013
 *
 * Description: Download a file.
 *
 * In:	$url, $save
 * Out:	Deleted file(s).
 * 
 * From: http://4rapiddev.com/php/download-image-or-file-from-url/
 * 
 */
function DownloadFile($url, $save)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_POST, 0); 
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
    $file_content = curl_exec($ch);
    curl_close($ch);
 
    $downloaded_file = fopen($save, 'w');
    fwrite($downloaded_file, $file_content);
    fclose($downloaded_file);
}

/*
 * Function:	DeleteFile
 *
 * Created on Mar 27, 2013
 * Updated on Jun 21, 2013
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
