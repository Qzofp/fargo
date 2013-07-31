/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


function TestCanvas()
{
    var img = "http://localhost:8080/image/image%3A%2F%2Fhttp%253a%252f%252fcf2.imgobject.com%252ft%252fp%252foriginal%252fz7lvVd5JIj3JAHy3mC8eUtzrsTJ.jpg";
    
    //var img = "artist_tmb.jpg";
    
    //Build if image exist check.
    
    
    var canvas = DrawImageOnCanvas("poster", img, 100, 140, 0.25);
    //ExportAndSaveCanvas(canvas);
    
}


/*
 * Function:	DrawImageOnCanvas
 *
 * Created on Jul 29, 2013
 * Updated on Jul 29, 2013
 *
 * Description: Draw and blur an image omn the HTML5 canvas
 * 
 * In:	selector, image, w, h, blur
 * Out:	canvas
 *
 * Note: Uses the Pixastic blurfast effect.
 *
 */
function DrawImageOnCanvas(selector, image, w, h, blur)
{
    var canvas  = document.getElementById(selector);
    var context = canvas.getContext("2d");
    var img = new Image();

    img.onload = function() {        
	var newimg = Pixastic.process(img, "blurfast", {"amount":blur});
        
        canvas.width  = w;
        canvas.height = h;

        context.drawImage(newimg, 0, 0, w, h);
        ExportAndSaveCanvas(canvas);
    };
       
    img.src = image;
    
    return canvas;
}

/*
 * Function:	ExportAndSaveCanvas
 *
 * Created on Jul 29, 2013
 * Updated on Jul 29, 2013
 *
 * Description: Export and save canvas image to server.
 * 
 * In:	canvas
 * Out:	Exported image.
 *
 * Note: Uses the Base64 and Canvas2Image libraries.
 * 
 * Code: http://www.fabiobiondi.com/blog/2012/10/export-and-save-a-screenshot-of-an-html5-canvas-using-php-jquery-and-easeljs/ 
 *
 */
function ExportAndSaveCanvas(canvas) 
{    
    // Get the canvas screenshot as PNG
    var screenshot = Canvas2Image.saveAsPNG(canvas, true);    
 
    // This is a little trick to get the SRC attribute from the generated <img> screenshot
    canvas.parentNode.appendChild(screenshot);
    screenshot.id = "canvasimage";     
    var data = $('#canvasimage').attr('src');
    canvas.parentNode.removeChild(screenshot);
  
    // Send the screenshot to PHP to save it on the server
    var url = 'export.php';
    $.ajax({
        type: "POST",
        url: url,
        dataType: 'text',
        data: {
        base64data : data
        }
    });
}