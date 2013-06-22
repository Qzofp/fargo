/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.1
 *
 * File:    fargo.private.import.js
 *
 * Created on Apr 14, 2013
 * Updated on Jun 22, 2013
 *
 * Description: Fargo's jQuery and Javascript functions page for the XBMC media import.
 *
 */


//////////////////////////////////////////    Main Functions    ///////////////////////////////////////////
 
/*
 * Function:	SetImportHandler
 *
 * Created on May 08, 2013
 * Updated on Jun 15, 2013
 *
 * Description: Set the import handler, show the import popup box and start import.
 * 
 * In:	media
 * Out:	title
 *
 */
function SetImportHandler()
{
    var title = "";
    var media = GetState("media"); // Get state media. 
  
    title = "Import " + ConvertMedia(media); 
    ShowPopupBox("#import_box", title);
    SetState("page", "popup");
     
    $(".retry").toggleClass("retry cancel");
    
    // Initialize status popup box.
    $(".message").html("Connecting...");
    AdjustImageSize(media);
    $(".cancel").html("Cancel");
     
    // Start Import
    global_cancel = false;  
    setTimeout(function(){
        ImportMedia(media);
    }, 1000);
}

/*
 * Function:	SetImportCancelHandler
 *
 * Created on May 09, 2013
 * Updated on Jun 21, 2013
 *
 * Description: Set the import handler, cancel or finish the import.
 * 
 * In:	media
 * Out:	title
 *
 */
function SetImportCancelHandler()
{
    var button = $(".cancel").text();
    var media = GetState("media");
    
    // Abort pending ajax request.
    if(typeof global_status_request !== 'undefined') {
        global_status_request.abort();
    }
    
    global_cancel = true;
    
    // Reset import values.
    global_total_fargo = 0;
    global_total_xbmc  = 0;    
    $("#thumb img").attr('src', 'images/no_poster.jpg');
    $("#progress").progressbar({
        value : 0       
    });
    $("#title").html("&nbsp;");
    
    if (button == "Finish" && media != "system") {
        window.location='index.php?media=' + media;
    }
}
 
/*
 * Function:	ImportMedia
 *
 * Created on Apr 14, 2013
 * Updated on Jun 21, 2013
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
    var timer;
    
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
    
    // Get global_total_xbmc
    GetXbmcCounter(media);
    end = global_total_xbmc;
    
    // Get global_setting_fargo
    GetFargoSetting("Timer");
    timer = global_setting_fargo;
    if (media == "music") {
        timer = timer/2;
    }
    
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
        
        }, 900);

        // Import process.
        (function setImportTimer() {
            
            if (global_cancel || start >= end)
            {
                return; // End Import.
            }
            
            StartImport(start, media);              
            start += 3;
            
            setTimeout(setImportTimer, timer);
            
        }());  
    }
}

/*
 * Function:	ShowStatus
 *
 * Created on Apr 17, 2013
 * Updated on Jun 22, 2013
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
                       
                    // Preload image.
                    var img = new Image();
                    img.src = json.thumbs + '/'+ json.xbmcid +'.jpg';
                    $("#thumb img").attr('src', img.src);
                    
                    $("#title").html(json.title);
                    global_total_fargo++;
                }  
                else {
                    $(".message").html(msg[3]);                    
                }              
            }, // End succes.
            error: function(xhr, status, error) // Begin Error.
            { 
                // Log error or warning.
                if (status !== "abort") {
                    LogEvent("Error", "Status check of " + ConvertMedia(media) + " at counter " + global_total_fargo + " failed!");
                    //LogEvent("Error", xhr.responseText);
                    //LogEvent("Error", error);
                }
                else {
                    LogEvent("Warning", "Status check of " + ConvertMedia(media) + " aborted!");
                }                
                
            } // End Error.
        }); // End Ajax.            
    }
}

/*
 * Function:	StartImport
 *
 * Created on Apr 17, 2013
 * Updated on Jun 22, 2013
 *
 * Description: Start the import process.
 *
 * In:	media
 * Out:	processed media.
 *
 */
function StartImport(start, media) 
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
        error: function(xhr, status, error) // Begin Error.
        { 
            // Log error or warning.
            if (status !== "abort") {
                LogEvent("Error", "During the import a " + status + " occured!");
                //LogEvent("Error", xhr.responseText);
                //LogEvent("Error", error);
            }
            else {
                LogEvent("Warning", "Import is to busy processing " + ConvertMedia(media) + "!");
            }
        } // End Error.                        
    }); // End Ajax;
}

/*
 * Function:	AdjustImageSize
 *
 * Created on May 17, 2013
 * Updated on May 25, 2013
 *
 * Description: Adjust the size of the image.
 *
 * In:	media
 * Out:	Adjusted image size
 *
 */
function AdjustImageSize(media)
{
    var img = new Image();
    if (media == "music") 
    {   
        $("#import_wrapper").height(114);
        $("#thumb").height(100);
        img.src = 'images/no_cover.jpg';
        $("#thumb img").attr('src', img.src).height(100).width(100);
    }
    else 
    {
        $("#import_wrapper").height(154);
        $("#thumb").height(140);  
        img.src = 'images/no_poster.jpg';
        $("#thumb img").attr('src', img.src).height(140).width(100);
    }    
}

/*
 * Function:	DisplayStatusMessage
 *
 * Created on May 17, 2013
 * Updated on Jun 10, 2013
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

        if (!global_cancel)
        {
            $(".message").html(str1);
            i++; 
	
            // End interval loop.
            if (i > end)
            {
                clearInterval(timer);
                $(".message").html(str2);
                $(".cancel").html("Ok");           
            }
        }    
        else {
            clearInterval(timer);
        }    
        
    }, 1000);	
}