/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.1
 *
 * File:    fargo-import.js
 *
 * Created on Apr 14, 2013
 * Updated on May 13, 2013
 *
 * Description: Fargo's jQuery and Javascript functions page for the XBMC media import.
 *
 */


//////////////////////////////////////////    Main Functions    ///////////////////////////////////////////
 
/*
 * Function:	ImportMedia
 *
 * Created on Apr 14, 2013
 * Updated on May 13, 2013
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
    var retry   = 0;
    var start   = 0;
  
    // Get global_total
    GetFargoCounter(media);
    start = global_total;
    
    if (media == "music") {
        $("#import_wrapper").height(114);
        $("#thumb").height(102);
    }
    else {
        $("#import_wrapper").height(154);
        $("#thumb").height(142);
    }

    ShowStatus(counter, retry, start, media); 
}


/*
 * Function:	ShowStatus
 *
 * Created on Apr 17, 2013
 * Updated on May 13, 2013
 *
 * Description: Show the import status.
 *
 * In:	counter, retry, start, media
 * Out:	Status
 *
 */
function ShowStatus(counter, retry, start, media)
{
    if(typeof global_ajax_request !== 'undefined') {
        global_ajax_request.abort();
    }
    global_ajax_request = $.ajax(
    {
        url: 'jsonxbmc.php?action=status&media=' + media,
        //async: false,
        dataType: 'json',
        timeout:5000,
        success: function(json) 
        {
            var ready = false;
        
            // Check if cancel button is pressed.
            if (global_cancel) 
            {        
                LogEvent("Warning", "Import " + ConvertMedia(media) + " canceled!");
                return;
            }
        
            if (json.online) 
            {               
               if (counter == 0) 
               {
                   LogEvent("Information", "Import " + ConvertMedia(media) + " started.");
                   $(".message").html('XBMC is online.');
               }
               
               if (json.delta == 0 && counter == 0) 
               {  
                   setTimeout(function() {
                       $(".message").html('Searching...');                       
                   }, 1000);
                   
                   setTimeout(function() {
                       $(".message").html('No new ' + ConvertMedia(media) + ' found.');
                       LogEvent("Information", "No new " + ConvertMedia(media) + " found.");
                       $(".cancel").html("Ok");
                   }, 2500);                   
                   
                   return;
               }
              
               if (json.delta > 0 && start < json.total)
               {
                   setTimeout(function() {         
                       $(".message").html('Importing...');                       
                   }, 1500);
           
                   StartImport(start, media);                   
                   $("#progress").html('Movie ID: ' + json.xbmcid);
                   start += 3;
               }   
               else 
               {
                   setTimeout(function() {
                       $(".message").html('Import is ready.');
                       $(".cancel").html("Finish");
                   }, 1000);
                   
                   $("#progress").html('Finished');                   
                     
                   ready = true;
               }  
            }
            else 
            {
               counter = -1;
               retry++;
               $(".message").html('XBMC is offline!');
            }
            
            $("#counter").html('Counter: ' + counter + ' Retry: ' + retry + ' Start ' + start);
            $("#delta").html('Delta: ' + json.delta + ' Total: ' + json.total);
                    
            if (json.id > 0 && json.online)
            {
               $("#thumb").html('<img src= "' + json.thumbs + '/'+ json.xbmcid +'.jpg" />');
               $("#title").html(json.title);
            } 
            
            // If ready exit progress, else retry.
            if (ready) 
            {
                if (json.total > 0) {
                    LogEvent("Information", "Import of " + json.total + " " + ConvertMedia(media) + " finished.");
                }    
                return;
            }
            else 
            {
                setTimeout(function() {
                    ShowStatus(counter, retry, start, media);
                },1000);
            }
            
            counter++;
            
        }, // End Success.
        error: function() // Begin Error.
        { 
            // Log time and counter in text file.
            LogEvent("Warning", "Retry import of " + media + " at counter " + counter + ".");

            // Retry...
            retry++;
            ShowStatus(counter, retry, start, media);
        } // End Error.
    }); // End Ajax.
 }


/*
 * Function:	StartImport
 *
 * Created on Apr 17, 2013
 * Updated on Mat 12, 2013
 *
 * Description: Start the import process.
 *
 * In:	start, media
 * Out:	processed media.
 *
 */
function StartImport(start, media) 
{
    $.ajax({
        url: 'jsonxbmc.php?action=import&media=' + media + '&start=' + start,
        //async: false,
        dataType: 'json',
        success: function(json) {

        } // End Success.
        
    }); // End Ajax;
}