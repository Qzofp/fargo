/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.1
 *
 * File:    fargo-import.js
 *
 * Created on Apr 14, 2013
 * Updated on May 10, 2013
 *
 * Description: Fargo's jQuery and Javascript functions page for the XBMC media import.
 *
 */


//////////////////////////////////////////    Main Functions    ///////////////////////////////////////////

/*
 * Function:	ImportMedia
 *
 * Created on Apr 14, 2013
 * Updated on May 10, 2013
 *
 * Description: Import the media from XBMC.
 *
 * In:	media
 * Out:	Imported media
 *
 */
function ImportMedia(media)
{
    var counter = 0;
    
    if (media == "music") {
        $("#import_wrapper").height(114);
        $("#thumb").height(102);
    }
    else {
        $("#import_wrapper").height(154);
        $("#thumb").height(142);
    }
    
    ShowStatus(counter, media);
}


/*
 * Function:	ShowStatus
 *
 * Created on Apr 17, 2013
 * Updated on May 10, 2013
 *
 * Description: Show the import status.
 *
 * In:	counter, media
 * Out:	Status
 *
 */
function ShowStatus(counter, media)
{
    $.ajax({
        url: 'jsonxbmc.php?action=status&media=' + media,
        async: false,
        dataType: 'json',
        success: function(json) 
        {
            var ready = false;
            var online = '';
        
            // Check if cancel button is pressed.
            if (global_cancel) {
                return;
            }
        
            if (json.online) 
            {
               online = 'online.';
               
               if (json.delta == 0 && counter == 0) 
               {
                   $(".message").html('XBMC is ' + online);
                   setTimeout(function() {
                       $(".message").html('Searching...');                       
                   }, 1000);
                   
                   setTimeout(function() {
                       $(".message").html('No new ' + ConvertMedia(media) + ' found.');                       
                   }, 2500);                   
                   
                   $(".cancel").html("Ok");
                   return;
               }
               
               if (json.delta > 0)
               {
                   StartImport(media);
                   $("#progress").html('Movie ID: ' + json.xbmcid);
               }   
               else 
               {
                   $("#progress").html('Finished');
                   $(".cancel").html("Finished");  
                   ready = true;
               }  
            }
            else 
            {
               online = 'offline!';
            }

            $(".message").html('XBMC is ' + online);
            $("#counter").html(counter);
            $("#delta").html('Delta: ' + json.delta);
                    
            if (json.id > 0 && json.online)
            {
               $("#thumb").html('<img src= "' + json.thumbs + '/'+ json.xbmcid +'.jpg" />');
               $("#title").html(json.title);
            } 
            
            // If ready exit progress, else retry.
            if (ready) 
            {
                return;
            }
            else 
            {
                setTimeout(function() {
                    ShowStatus(counter, media);
                },1000);
            }
            
            counter++;
            
        } // End Success.
    }); // End Ajax.
 }


/*
 * Function:	StartImport
 *
 * Created on Apr 17, 2013
 * Updated on Apr 20, 2013
 *
 * Description: Start the import process.
 *
 * In:	media
 * Out:	processed media.
 *
 */
function StartImport(media) 
{
    $.ajax({
        url: 'jsonxbmc.php?action=import&media=' + media,
        //async: false,
        dataType: 'json',
        success: function(json) {
            //alert(json.counter);
        } // End Success.
        
    }); // End Ajax;
}
