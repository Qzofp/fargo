/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.1
 *
 * File:    fargo-import.js
 *
 * Created on Apr 14, 2013
 * Updated on May 20, 2013
 *
 * Description: Fargo's jQuery and Javascript functions page for the XBMC media import.
 *
 */


//////////////////////////////////////////    Main Functions    ///////////////////////////////////////////
 
/*
 * Function:	ImportMedia
 *
 * Created on Apr 14, 2013
 * Updated on May 20, 2013
 *
 * Description: Import the media from XBMC.
 *
 * In:	media
 * Out:	Imported media
 *
 */
function ImportMedia(media)
{
    var end   = 0;
    var start = 0;
    var delta = 0;
    
    var percent = 0;
  
    var msg = ["XBMC is online.", 
               "XBMC is offline!", 
               "Searching...", 
               "Processing...",                
               "Importing...", 
               "No new " + ConvertMedia(media) + " found.",
               "Import is ready."];
  
    // Get global_total_fargo
    GetFargoCounter(media);
    start = global_total_fargo;
    
    //Get global_total_xbmc
    GetXbmcCounter(media);
    end = global_total_xbmc;
    
    if (start >= end) 
    {
        if (end == -1) 
        {
            $(".message").html(msg[1]);
            $(".cancel").toggleClass("cancel retry");
            $(".retry").html("Retry");
        }
        else 
        {
            finish = 2 + Math.floor(Math.random() * 3);
            $(".message").html(msg[0]);
            SetState("xbmc", "online");
            DisplayStatusMessage(msg[2], msg[5], 3);
            LogEvent("Information", "No new " + ConvertMedia(media) + " found.");
        }
    }
    else
    {           
        $(".message").html(msg[0]);
        SetState("xbmc", "online");
        LogEvent("Information", "Import " + ConvertMedia(media) + " started.");
        
        // Import status. 
        delta = end - start;
        
        //global_total_fargo++;
        var status = setInterval(function(){
        
            if (GetState("xbmc") == "online")
            {   
                // Calculate percentage.
                percent = global_total_fargo - (end - delta);
                percent = Math.round(percent/delta * 100);
                $("#counter").html('Percentage: ' + percent + '%');
                $("#progress").progressbar({
                    value : percent       
                });
                
                // global_total_fargo++
                ShowStatus(delta, end, status, media, msg);
            }
            else 
            {
                $(".message").html(msg[1]);
                $(".cancel").toggleClass("cancel retry");
                $(".retry").html("Retry"); 
                clearInterval(status);
            }
            
            // debug.
            //proc = (100 * global_total_fargo)/delta;
            //$("#counter").html('Percentage: ' + proc);
        
        }, 800);

        // Import process.
        var process = setInterval(function(){
        
            if (GetState("xbmc") == "online")
            { 
                StartImport(start, end, process, media);                  
                start += 3;
            }
            else {
                clearInterval(process);
            }
        
            //debug
            //$("#start").html('Start: ' + start);
        
        }, 3000);
          
    }
}

/*
 * Function:	ShowStatus
 *
 * Created on Apr 17, 2013
 * Updated on May 18, 2013
 *
 * Description: Show the import status.
 *
 * In:	delta, end, status, media
 * Out:	Status
 *
 */
function ShowStatus(delta, end, status, media, msg)
{        
    if (global_cancel || global_total_fargo >= end)
    {
        if (global_cancel) {
            LogEvent("Warning", "Import " + ConvertMedia(media) + " canceled!");
        }
        else {
            $(".message").html(msg[6]);
            $(".cancel").html("Finish");
            LogEvent("Information", "Import of " + delta + " " + ConvertMedia(media) + " finished.");
        }    
        clearInterval(status);
    }
    else
    {    
        if(typeof global_status_request !== 'undefined') {
            global_status_request.abort();
        }
        global_status_request = $.ajax(
        {
            url: 'jsonfargo.php?action=status&media=' + media + '&id=' + (global_total_fargo + 1),
            dataType: 'json',
            success: function(json) 
            {              
                if (json.xbmcid > 0)
                {
                    $(".message").html(msg[4]);
                    $("#thumb").html('<img src= "' + json.thumbs + '/'+ json.xbmcid +'.jpg" />');
                    $("#title").html(json.title);
                    global_total_fargo++;
                }  
                else {
                    $(".message").html(msg[3]);                    
                }              
            }, // End succes.
            error: function() // Begin Error.
            { 
                // Log time and counter in text file.
                LogEvent("Error", "Status check of " + media + " at counter " + global_total_fargo + " failed!");
            } // End Error.
        }); // End Ajax.            
    }
}

/*
 * Function:	StartImport
 *
 * Created on Apr 17, 2013
 * Updated on May 20, 2013
 *
 * Description: Start the import process.
 *
 * In:	start, end, process, media
 * Out:	processed media.
 *
 */
function StartImport(start, end, process, media) 
{        
    if (global_cancel || start >= end)
    {
        clearInterval(process);
    }
    else
    {    
        if(typeof global_import_request !== 'undefined') {
            global_import_request.abort();
        }
        global_import_request = $.ajax({
            url: 'jsonxbmc.php?action=import&media=' + media + '&start=' + start,
            dataType: 'json',
            success: function(json) 
            { 
                if (json.online == -1) {
                    SetState("xbmc", "offline");
                }
            }, // End Success.  
            error: function() // Begin Error.
            { 
                // Log time and counter in text file.
                LogEvent("Warning", "Server is to busy with import of " + media + "!");
            } // End Error.                        
        }); // End Ajax;
    }    
}

/*
 * Function:	AdjustImageSize
 *
 * Created on May 17, 2013
 * Updated on May 17, 2013
 *
 * Description: Adjust the size of the image.
 *
 * In:	media
 * Out:	Adjusted image size
 *
 */
function AdjustImageSize(media)
{
    if (media == "music") {
        $("#import_wrapper").height(114);
        $("#thumb").height(100);
    }
    else {
        $("#import_wrapper").height(154);
        $("#thumb").height(140);
    }    
}

/*
 * Function:	DisplayStatusMessage
 *
 * Created on May 17, 2013
 * Updated on May 17, 2013
 *
 * Description: Display status message.
 *
 * In:	str1, str2, end
 * Out:	Status
 *
 */
function DisplayStatusMessage(str1, str2, end)
{
    var i = 0; 
    var timer = setInterval(function(){
			
        $(".message").html(str1);
        i++; 
	
        // End interval loop.
        if (i > end) 
        {
            clearInterval(timer);
            $(".message").html(str2);
            $(".cancel").html("Ok");
        }		
    }, 1000);	
}