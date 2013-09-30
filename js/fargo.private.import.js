/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.2
 *
 * File:    fargo.private.import.js
 *
 * Created on Jul 14, 2013
 * Updated on Sep 30, 2013
 *
 * Description: Fargo's jQuery and Javascript functions page for the XBMC media import.
 *
 */

//////////////////////////////////////////    Main Functions    ///////////////////////////////////////////

// Import constants.
var cIMPORT = {
    IMPORT:   "Import",
    REFRESH:  "Refresh"  
};

var cSTATUS = {
    ONLINE:   "XBMC is online.",
    OFFLINE:  "XBMC is offline!",  
    CONNECT:  "Connecting...",
    SEARCH:   "Searching...",
    PROCESS:  "Processing...", 
    IMPORT:   "Importing...",
    REFRESH:  "Refreshing...",
    READY:    "[dummy] is ready.",
    NOTFOUND: "No new [dummy] found."
};

/*
 * Function:	SetImportHandler
 *
 * Created on Sep 09, 2013
 * Updated on Sep 09, 2013
 *
 * Description: Set the import handler, show the import popup box with yes/no buttons.
 * 
 * In:	-
 * Out:	Show Import Popup
 *
 */
function SetImportPopupHandler(media)
{
    $("#action_box .message").text("Do you want to start importing " + ConvertMedia(media) + "?");
    
    if (media == "music") 
    {
        $("#action_wrapper").height(116);
        $("#action_thumb").height(100);
        $("#action_thumb img").height(100);
    }
    else 
    {
        $("#action_wrapper").height(156);
        $("#action_thumb").height(140);
        $("#action_thumb img").height(140);
    }
    
    // Show popup.
    ShowPopupBox("#action_box", "Import " + ConvertMedia(media));
    SetState("page", "popup"); 
}

/*
 * Function:	SetStartRefreshHandler
 *
 * Created on Sep 14, 2013
 * Updated on Sep 16, 2013
 *
 * Description: Set the refresh handler, show the refresh popup box and start the refresh.
 * 
 * In:	id, xbmcid
 * Out:	title
 *
 */
function SetStartRefreshHandler(id, xbmcid)
{
    var media;
    media = InitImportAndShowPopup();
    
    // Reset status, get xbmc connection (url) and port.
    $.ajax({
        url: 'jsonfargo.php?action=reset&media=' + media,
        async: false,
        dataType: 'json',
        success: function(json)
        {
            var timer, i = 0;
            var online;
            
            // Check if XBMC is online and transfer XBMC media counter (total).
            ImportCounter(json, media);
            
            // Check if XBMC is online and start refresh.
            timer = setInterval(function()
            {
                // Check if iframe from ImportCounter finished loading.
                if ($("#ready").text() == "true")
                {
                    // Returns global_total_fargo and global_total_xbmc;
                    GetXbmcMediaLimits(media);
                    online = global_xbmc_end; // If value > 0 then XBMC is online.
                    
                    if (online > 0 || i > 3)
                    {
                        if (online > 0) {
                            StartRefresh(json, media, id, xbmcid);
                        }
                        else {
                            ShowOffline();
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
 * Function:	StartRefresh
 *
 * Created on Sep 14, 2013
 * Updated on Sep 22, 2013
 *
 * Description: Control and Refresh the media transfered from XBMC.
 *
 * In:	xbmc, media, id, xmbcid
 * Out:	Refreshed media
 *
 */
function StartRefresh(xbmc, media, id, xbmcid)
{
    var timeout = 0;
    var delay   = 0;
    var percent = 15;
    var factor  = 1.5;
    var $ready  = $("#ready");
    
    // Import media process.
    $("#action_box .message").html(cSTATUS.ONLINE);  
    ImportMedia(xbmc, media, "refresh", id, xbmcid);
    LogEvent("Information", "Refresh " + ConvertMedia(media) + " started.");
            
    // Check status.
    var status = setInterval(function()
    {
        if (global_cancel || delay >= xbmc.timeout/1000 || timeout > xbmc.timeout/1000)
        {
            if (global_cancel) {
                LogEvent("Warning", "Refresh " + ConvertMedia(media) + " canceled!");
            }
            else if (timeout > xbmc.timeout/1000) {
                ShowOffline();
            }
            else {
                ShowRefreshFinished(media);
            }             
            
            clearInterval(status);
        }
        else
        {
            // Show status and returns global_ready.
            ShowRefreshStatus(media, id, percent);
            percent = 100 - 100/factor;
            
            // Wait until thumb is ready.
            if (global_ready) {
                delay += 2;
                factor *= 2;
            }
            else if ($ready.text() == "true") {
                timeout++;
            }
        }        
    }, 800);   
}

/*
 * Function:	ShowRefreshStatus
 *
 * Created on Sep 14, 2013
 * Updated on Sep 21, 2013
 *
 * Description: Show the refresh status.
 *
 * In:	media
 * Out:	Status
 *
 */
function ShowRefreshStatus(media, id, percent)
{   
    $.ajax({
        url: 'jsonfargo.php?action=status&media=' + media + '&mode=refresh' + '&id=' + id,
        dataType: 'json',
        success: function(json) 
        {     
            global_ready = Number(json.ready);
            
            $("#action_box .message").html(cSTATUS.REFRESH);      
                      
            // Preload image.
            var img = new Image();
            img.src = json.thumbs + '/'+ json.xbmcid +'.jpg?v=' + json.refresh;
            $("#action_thumb img").attr('src', img.src);
                                
            // If images not found then show no poster.
            $("#action_thumb img").error(function(){
                $(this).attr('src', 'images/no_poster.jpg');
            });
                    
            $("#action_title").html(json.title);
            
            $("#action_box .progress").progressbar({
                value:percent       
            });
            
        } // End succes.    
    }); // End Ajax. 
}

/*
 * Function:	ShowRefreshFinished
 *
 * Created on Sep 14, 2013
 * Updated on Sep 14, 2013
 *
 * Description: Show refresh finished message and add to log event.
 * 
 * In:	media
 * Out:	-
 *
 */
function ShowRefreshFinished(media)
{   
    var msg = cSTATUS.READY.replace("[dummy]", cIMPORT.REFRESH);
    
    $("#action_box .message").html(msg);             
    $("#action_box .progress").progressbar({
        value:100
    });
    
    $(".cancel").html("Finish");
    LogEvent("Information", "Refresh of " + ConvertMedia(media) + " finished.");    
}

/*
 * Function:	SetStartImportHandler
 *
 * Created on Jul 14, 2013
 * Updated on Sep 30, 2013
 *
 * Description: Set the import handler, show the import popup box and start import.
 * 
 * In:	-
 * Out:	title
 *
 */
function SetStartImportHandler()
{  
    var media = InitImportAndShowPopup();
  
    // Reset media status, get xbmc connection (url), port and fargo media counter.
    $.ajax({
        url: 'jsonfargo.php?action=reset&media=' + media,
        async: false,
        dataType: 'json',
        success: function(json)
        {
            var delta, start, end;
            var timer, i = 0;
            
            // Check if XBMC is online and transfer XBMC media counter (total).
            ImportCounter(json, media);
            
            // Get XBMC media counter from Fargo.
            timer = setInterval(function()
            {
                // Check if iframe from ImportCounter finished loading.
                if ($("#ready").text() == "true")
                {
                    // Returns global_start and global_end;
                    GetXbmcMediaLimits(media);
                    start = global_xbmc_start;
                    end   = global_xbmc_end;
                    delta = end - start;
                    
                    if (end > 0 || i > 3)
                    {
                        if (end > 0 && end > start) 
                        {
                            LogEvent("Information", "Import " + ConvertMedia(media) + " started.");
                            StartImport(json, media, start, end, delta);
                        }
                        else if (end >= 0) {
                            ShowNoNewMedia(media);
                        }
                        else {
                            ShowOffline();
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
 * Function:	SetRetryImportHandler
 *
 * Created on Sep 30, 2013
 * Updated on Sep 30, 2013
 *
 * Description: Set the import handler and retry the import.
 * 
 * In:	media, start, delta
 * Out:	-
 *
 */
function SetRetryImportHandler(media, start, delta)
{   
    // Reset media status, get xbmc connection (url), port and fargo media counter.
    $.ajax({
        url: 'jsonfargo.php?action=reset&media=' + media,
        async: false,
        dataType: 'json',
        success: function(json)
        {
            var end;
            var timer, i = 0;
            
            // Check if XBMC is online and transfer XBMC media counter (total).
            ImportCounter(json, media);
            
            // Get XBMC media counter from Fargo.
            timer = setInterval(function()
            {
                // Check if iframe from ImportCounter finished loading.
                if ($("#ready").text() == "true")
                {
                    // Returns global_start and global_end;
                    GetXbmcMediaLimits(media);
                    start = global_xbmc_start;
                    end   = global_xbmc_end;
                    
                    if (end > 0 || i > 3)
                    {
                        if (end > 0 && end > start) 
                        {
                            LogEvent("Information", "Restart import from XBMC (Retry)."); 
                            StartImport(json, media, start, end, delta);
                        }
                        else if (end >= 0) {
                            ShowNoNewMedia(media);
                        }
                        else {
                            ShowOffline();
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
 * Updated on Sep 14, 2013
 *
 * Description: Initialize import values and show popup.
 * 
 * In:	-
 * Out:	media
 *
 */
function InitImportAndShowPopup()
{
    var media = GetState("media"); // Get state media.
    global_cancel = false;
    global_ready  = false;
    
    // Initialize status popup box.
    $("#action_box .message").html(cSTATUS.CONNECT);
    
    // Turn progress on.
    $(".progress_off").toggleClass("progress_off progress");
    
    $(".yes").hide();
    $(".no").toggleClass("no cancel");
    $(".retry").toggleClass("retry cancel");  
    
    $(".cancel").html("Cancel");    
    
    return media;
}

/*
 * Function:	ShowNoNewMedia
 *
 * Created on Aug 18, 2013
 * Updated on Sep 30, 2013
 *
 * Description: Show no new media and add to log event.
 * 
 * In:	media
 * Out:	-
 *
 */
function ShowNoNewMedia(media)
{
    var finish = 2 + Math.floor(Math.random() * 3);
    var msg = cSTATUS.NOTFOUND.replace("[dummy]", ConvertMedia(media));
    
    $("#action_box .message").html(cSTATUS.ONLINE);
    SetState("xbmc", "online");
    DisplayStatusMessage(cSTATUS.SEARCH, msg, finish);
    LogEvent("Information", "No new " + ConvertMedia(media) + " found.");      
}

/*
 * Function:	ShowOffline
 *
 * Created on Aug 19, 2013
 * Updated on Sep 30, 2013
 *
 * Description: Show offline message and add to log event.
 * 
 * In:	msg
 * Out:	-
 *
 */
function ShowOffline()
{
    $("#action_box .message").html(cSTATUS.OFFLINE);
    SetState("xbmc", "offline");
    
    $(".cancel").toggleClass("cancel retry");    
    $(".retry").html("Retry");
    
    LogEvent("Information", "XBMC is offline or not reachable."); 
}

/*
 * Function:	ShowFinished
 *
 * Created on Aug 19, 2013
 * Updated on Sep 30, 2013
 *
 * Description: Show import finished message and add to log event.
 * 
 * In:	media, counter
 * Out:	-
 *
 */
function ShowFinished(media, counter)
{   
    var msg = cSTATUS.READY.replace("[dummy]", cIMPORT.IMPORT);
    
    $("#action_box .message").html(msg);
    $(".cancel").html("Finish");
    LogEvent("Information", "Import of " + counter + " " + ConvertMedia(media) + " finished.");    
}

/*
 * Function:	SetImportCancelHandler
 *
 * Created on May 09, 2013
 * Updated on Sep 30, 2013
 *
 * Description: Set the import handler, cancel or finish the import.
 * 
 * In:	media
 * Out:	title
 *
 */
function SetImportCancelHandler()
{    
    var $popup = $(".popup:visible");
    var media = $("#control_bar").find(".on").attr('id');
    SetState("media", media); 
    
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
    
    if ($popup.find(".cancel").text() == "Cancel") {
        LogEvent("Warning", "Import " + ConvertMedia(media) + " canceled!");
    }
    
    // Reset import values.
    global_total_fargo = 0;
    global_total_xbmc  = 0;    

    $("#action_box .progress").progressbar({
        value : 0       
    });
    
    if (media != "system") {
        window.location='index.php?media=' + media;
    }
}

/*
 * Function:	StartImport
 *
 * Created on Jul 22, 2013
 * Updated on Sep 30, 2013
 *
 * Description: Control and Import the media transfered from XBMC.
 *
 * In:	xbmc, media, start, end, delta
 * Out:	Imported media
 *
 */
function StartImport(xbmc, media, start, end, delta)
{
    var timeout = 0;
    var busy    = true;
    //var delta   =  end - start;
    var $ready  = $("#ready");
    
    // Import media process.
    $("#action_box .message").html(cSTATUS.ONLINE);  
    ImportMedia(xbmc, media, "import", 0, start);
    start += 1;
    
    (function setImportTimer() {
            
        //alert("timer");
        if (global_cancel || start >= end   || timeout > xbmc.timeout/1000) {
            return; // End Import.
        }

        // Check if iframe from ImportMedia finished loading.
        if ($("#ready").text() == "true")
        {
            if (busy == false)
            {    
                busy = true; // pause import.
                ImportMedia(xbmc, media, "import", 0, start);       
                start += 1;
                timeout = 0; // reset timeout.
            }
        }
        
        // debug
        // console.log("Import Timer, start: " + start);
        
        setTimeout(setImportTimer, 1200); // 500
            
    }()); // End setImportTimer.   
        
    // Check status.
    var status = setInterval(function()
    {
        if (global_cancel || global_xbmc_start >= end  || timeout > xbmc.timeout/1000)
        {
            /*if (global_cancel) {
                LogEvent("Warning", "Import " + ConvertMedia(media) + " canceled!");
            }
            else*/
            if (timeout > xbmc.timeout/1000) {
                SetRetryImportHandler(media, start, delta);
            }
            else {
                ShowFinished(media, delta);
            }             
            
            clearInterval(status);
        }
        else
        {
            // Show status and returns global_total_fargo.
            ShowStatus(delta, start, end, media);
            if (global_xbmc_start == start) {
                busy = false; // resume import.
            }
            
            if ($ready.text() == "true") {
                timeout++;
            }
        }        
    }, 1200); // 800
}

/*
 * Function:	ShowStatus
 *
 * Created on Aug 19, 2013
 * Updated on Sep 30, 2013
 *
 * Description: Show the import status.
 *
 * In:	delta, end, media
 * Out:	Status
 *
 */
function ShowStatus(delta, start, end, media)
{   
    $.ajax({
        url: 'jsonfargo.php?action=status&media=' + media + '&mode=import' + '&id=' + start,
        dataType: 'json',
        success: function(json) 
        {     
            global_xbmc_start = json.counter;
            
            if (json.xbmcid > 0)
            {
                var percent = start - (end - delta);
                percent = Math.round(percent/delta * 100);
                $("#action_box .progress").progressbar({
                    value : percent       
                });                
                
                $("#action_box .message").html(cSTATUS.PROCESS);
                      
                // Preload image.
                var img = new Image();
                img.src = json.thumbs + '/'+ json.xbmcid +'.jpg';
                $("#action_thumb img").attr('src', img.src);
                                
                // If images not found then show no poster.
                $("#action_thumb img").error(function(){
                    $(this).attr('src', 'images/no_poster.jpg');
                });
                    
                $("#action_title").html(json.title);
                
            }  
            else {
                $("#action_box .message").html(cSTATUS.IMPORT);                    
            }              
        } // End succes.    
    }); // End Ajax. 
}

/*
 * Function:	ImportCounter
 *
 * Created on Jul 22, 2013
 * Updated on Sep 28, 2013
 *
 * Description: Import the media counter transfered from XBMC.
 *
 * In:	media
 * Out:	Imported media counter
 *
 */
function ImportCounter(xbmc, media)
{
    var $result = $("#transfer");
    var $ready  = $("#ready");
    var url, iframe;
    
    url = "http://" + xbmc.connection;
    if (xbmc.port) {
        url = "http://" + xbmc.connection + ":" + xbmc.port;
    }
    
    url   += "/fargo/transfer.html?action=counter&media=" + media + "&key=" + xbmc.key;
    iframe = '<iframe src="' + url + '" onload="IframeReady()"></iframe>';
    
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
 * Updated on Sep 28, 2013
 *
 * Description: Import the media transfered from XBMC.
 *
 * In:	sbmc, media, mode, fargo, start
 * Out:	Imported media
 *
 */
function ImportMedia(xbmc, media, mode, fargo, start)
{
    var $result = $("#transfer");
    var $ready  = $("#ready");   
    var url, iframe;
    
    url = "http://" + xbmc.connection;
    if (xbmc.port) {
        url = "http://" + xbmc.connection + ":" + xbmc.port;
    }    
    
    url   += "/fargo/transfer.html?action=" + media + "&mode=" + mode + "&start=" + start + "&fargoid=" + fargo + "&key=" + xbmc.key;
    iframe = '<iframe src="' + url + '" onload="IframeReady()"></iframe>';   
    
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
 * Function:	DisplayStatusMessage
 *
 * Created on May 17, 2013
 * Updated on Sep 09, 2013
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
            $("#action_box .message").html(str1);
            
            percent = Math.round(i/end * 100);
            $("#action_box .progress").progressbar({
                value : percent       
            });
            
            i++; 
	
            // End interval loop.
            if (i > end)
            {
                clearInterval(timer);
                $("#action_box .message").html(str2);
                $(".cancel").html("Ok");           
            }
        }    
        else {
            clearInterval(timer);
        }    
        
    }, 1000);	
}