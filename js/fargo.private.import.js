/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.2
 *
 * File:    fargo.private.import.js
 *
 * Created on Jul 14, 2013
 * Updated on Aug 19, 2013
 *
 * Description: Fargo's jQuery and Javascript functions page for the XBMC media import.
 *
 */

//////////////////////////////////////////    Main Functions    ///////////////////////////////////////////

// Global variables?!? jQuery sucks or I don't get it!!!
var global_total_fargo = 0;
var global_total_xbmc  = 0;
var global_cancel      = false;

/*
 * Function:	SetImportHandler
 *
 * Created on Jul 14, 2013
 * Updated on Aug 19, 2013
 *
 * Description: Set the import handler, show the import popup box and start import.
 * 
 * In:	-
 * Out:	title
 *
 */
function SetImportHandler()
{  
    var media, msg;
    
    media = InitImportAndShowPopup();
    msg = ["XBMC is online.", 
           "XBMC is offline!", 
           "Searching...", 
           "Processing...",                
           "Importing...", 
           "No new " + ConvertMedia(media) + " found.",
           "Import is ready."];
  
    // Reset media status and get fargo media counter.
    $.ajax({
        url: 'jsonfargo.php?action=reset&media=' + media,
        async: false,
        dataType: 'json',
        success: function(json)
        {
            var start, end;
            var timer, i = 0;
            
            // Check if XBMC is online and transfer XMBC media counter (total).
            ImportCounter(media);
            
            // Get XBMC media counter from Fargo.
            timer = setInterval(function()
            {
                // Check if iframe from ImportCounter finished loading.
                if ($("#ready").text() == "true")
                {
                    // Returns global_total_fargo and global_total_xbmc;
                    GetXbmcAndFargoCounters(media);
                    start = global_total_fargo;
                    end   = global_total_xbmc;
                    
                    if (end > 0 || i > 3)
                    {
                        if (end > 0 && end > start) {
                            StartImport(media, start, end, msg);
                        }
                        else if (end >= 0) {
                            ShowNoNewMedia(media, msg);
                        }
                        else {
                            ShowOffline(msg);
                        }
                        
                        clearInterval(timer);
                    }
                }
                i++;
                
            }, 1000); // End timer  
        } // End Success.        
    }); // End Ajax;
}

/*
 * Function:	InitImportAndShowPopup
 *
 * Created on Aug 18, 2013
 * Updated on Aug 18, 2013
 *
 * Description: Initialize import values and show popup.
 * 
 * In:	-
 * Out:	media
 *
 */
function InitImportAndShowPopup()
{
    var title = "";
    var media = GetState("media"); // Get state media. 
    
    global_cancel = false;
  
    title = "Import " + ConvertMedia(media); 
    ShowPopupBox("#import_box", title);
    SetState("page", "popup");
     
    $(".retry").toggleClass("retry cancel");
    
    // Initialize status popup box.
    $(".message").html("Connecting...");
    AdjustImageSize(media);
    $(".cancel").html("Cancel");    
    
    return media;
}

/*
 * Function:	ShowNoNewMedia
 *
 * Created on Aug 18, 2013
 * Updated on Aug 18, 2013
 *
 * Description: Show no new media and add to log event.
 * 
 * In:	media, msg
 * Out:	-
 *
 */
function ShowNoNewMedia(media, msg)
{
    var finish = 2 + Math.floor(Math.random() * 3);
    
    $(".message").html(msg[0]);
    SetState("xbmc", "online");
    DisplayStatusMessage(msg[2], msg[5], finish);
    LogEvent("Information", "No new " + ConvertMedia(media) + " found.");      
}

/*
 * Function:	ShowOffline
 *
 * Created on Aug 19, 2013
 * Updated on Aug 19, 2013
 *
 * Description: Show offline message and add to log event.
 * 
 * In:	msg
 * Out:	-
 *
 */
function ShowOffline(msg)
{
    $(".message").html(msg[1]);
    //SetState("xbmc", "offline");
    $(".cancel").toggleClass("cancel retry");
    $(".retry").html("Retry");
    LogEvent("Information", "XBMC is offline or not reachable."); 
}

/*
 * Function:	ShowFinished
 *
 * Created on Aug 19, 2013
 * Updated on Aug 19, 2013
 *
 * Description: Show import finished message and add to log event.
 * 
 * In:	media, delta, msg
 * Out:	-
 *
 */
function ShowFinished(media, delta, msg)
{
    $(".message").html(msg[6]);
    $(".cancel").html("Finish");
    LogEvent("Information", "Import of " + delta + " " + ConvertMedia(media) + " finished.");    
}

/*
 * Function:	SetImportCancelHandler
 *
 * Created on May 09, 2013
 * Updated on Aug 18, 2013
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

    /*
    // Abort pending ajax request.
    if(typeof global_status_request !== 'undefined') {
        global_status_request.abort();
    }
    
    if(typeof global_import_request !== 'undefined') {
        global_import_request.abort();
    }
    */
   
    global_cancel = true;
    
    // Reset import values.
    global_total_fargo = 0;
    global_total_xbmc  = 0;    
    $("#thumb img").attr('src', 'images/no_poster.jpg');
    $("#import_box .progress").progressbar({
        value : 0       
    });
    $("#media_title").html("&nbsp;");
    
    if (button == "Finish" && media != "system") {
        window.location='index.php?media=' + media;
    }
}

/*
 * Function:	StartImport
 *
 * Created on Jul 22, 2013
 * Updated on Aug 19, 2013
 *
 * Description: Control and Import the media transfered from XBMC.
 *
 * In:	media, start, end
 * Out:	Imported media
 *
 */
function StartImport(media, start, end, msg)
{
    var busy = true;
    var delta =  end - start;
    
    // Import media process.
    ImportMedia(media, start);
    start += 1;
    
    LogEvent("Information", "Import " + ConvertMedia(media) + " started.");
    
    (function setImportTimer() {
            
        //alert("timer");
        if (global_cancel || start >= end)  {
            return; // End Import.
        }

        // Check if iframe from ImportMedia finished loading.
        if ($("#ready").text() == "true")
        {
            if (busy == false)
            {    
                busy = true; // pause import.
                ImportMedia(media, start);        
                start += 1;
            }
        }
        
        setTimeout(setImportTimer, 500);
            
    }()); // End setImportTimer.   
        
    // Check status.
    var status = setInterval(function()
    {
        if (global_cancel || global_total_fargo >= end)
        {
            if (global_cancel) {
                LogEvent("Warning", "Import " + ConvertMedia(media) + " canceled!");
            }
            else {
                ShowFinished(media, delta, msg);
            }             
            
            clearInterval(status);
        }
        else 
        {
            // Show status and returns global_total_fargo.
            ShowStatus(delta, start, end, media, msg);
            if (global_total_fargo == start) {
                busy = false; // resume import.
            }
        }
        
    }, 1000);    
}

/*
 * Function:	ShowStatus
 *
 * Created on Aug 19, 2013
 * Updated on Aug 19, 2013
 *
 * Description: Show the import status.
 *
 * In:	delta, end, media, msg
 * Out:	Status
 *
 */
function ShowStatus(delta, start, end, media, msg)
{   
    $.ajax({
        url: 'jsonfargo.php?action=status&media=' + media + '&id=' + start,
        dataType: 'json',
        success: function(json) 
        {     
            global_total_fargo = json.counter;
            
            if (json.xbmcid > 0)
            {
                var percent = start - (end - delta);
                percent = Math.round(percent/delta * 100);
                $("#import_box .progress").progressbar({
                    value : percent       
                });                
                
                $(".message").html(msg[4]);
                      
                // Preload image.
                var img = new Image();
                img.src = json.thumbs + '/'+ json.xbmcid +'.jpg';
                $("#thumb img").attr('src', img.src);
                                
                // If images not found then show no poster.
                $("#thumb img").error(function(){
                    $(this).attr('src', 'images/no_poster.jpg');
                });
                    
                $("#media_title").html(json.title);
                
            }  
            else {
                $(".message").html(msg[3]);                    
            }              
        } // End succes.    
    }); // End Ajax. 
}

/*
 * Function:	ImportCounter
 *
 * Created on Jul 22, 2013
 * Updated on Aug 18, 2013
 *
 * Description: Import the media counter transfered from XBMC.
 *
 * In:	media
 * Out:	Imported media counter
 *
 */
function ImportCounter(media)
{
    var $result = $("#transfer");
    var $ready  = $("#ready");
    
    var url    = "http://192.168.114.128:8080/fargo/transfer.html?action=counter&media=" + media;
    var iframe = '<iframe src="' + url + '" onload="IframeReady()"></iframe>';
    
    // Reset values.
    $ready.text("false");    
    $result.text("");
    
    // Run transfer data in iframe.
    $result.append(iframe); 
    
    // Generates time-out and runs IframeReady function if onload in the iframe succeeds or fails.
    CheckIframeReady($ready, 3);
}

/*
 * Function:	ImportMedia
 *
 * Created on Jul 20, 2013
 * Updated on Aug 19, 2013
 *
 * Description: Import the media transfered from XBMC.
 *
 * In:	media, start
 * Out:	Imported media
 *
 */
function ImportMedia(media, start)
{
    var $result = $("#transfer");
    var $ready  = $("#ready");    
    var url    = "http://192.168.114.128:8080/fargo/transfer.html?action=" + media + "&start=" + start;
    var iframe = '<iframe src="' + url + '" onload="IframeReady()"></iframe>';   
    
    // Reset values.
    $ready.text("false");    
    $result.text("");
    
    // Run transfer data in iframe.
    $result.append(iframe); 
    
    // Generates time-out and runs ImportReady function if onload in the iframe fails.
    CheckIframeReady($ready, 3);
    
}

/*
 * Function:	CheckIframeReady
 *
 * Created on Jul 15, 2013
 * Updated on Jul 15, 2013
 *
 * Description: Check if import is ready. In Firefox IframeReady doesn't trigger if page not found.
 * 
 * In:	$ready, count
 * Out:	-
 *
 */
function CheckIframeReady($ready, count)
{
    var i = 0;
    var _timer = setInterval(function()
    {
        if ($ready.text() == "true" || i > count)
        {
            clearInterval(_timer);
            
            if ($ready.text() == "false") {
                IframeReady();
            }
        }
        i++;
        
    }, 1000);  
}

/*
 * Function:	IframeReady
 *
 * Created on Jul 14, 2013
 * Updated on Jul 22, 2013
 *
 * Description: Signals that the iframe is ready loading.
 * 
 * In:	-
 * Out:	-
 *
 */
function IframeReady()
{  
    if ($("#ready").text() == "false") {
        $("#ready").text("true");        
    }
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
 * Updated on Jul 04, 2013
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
    var percent;
    var timer = setInterval(function(){

        if (!global_cancel)
        {
            $(".message").html(str1);
            
            percent = Math.round(i/end * 100);
            $("#import_box .progress").progressbar({
                value : percent       
            });
            
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