/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.3
 *
 * File:    fargo.private.import.js
 *
 * Created on Jul 14, 2013
 * Updated on Oct 21, 2013
 *
 * Description: Fargo's jQuery and Javascript functions page for the XBMC media import.
 *
 */

//////////////////////////////////////////    Main Functions    ///////////////////////////////////////////

// Import constants.
var cIMPORT = {
    IMPORT:   "Import",
    REFRESH:  "Refresh",
    FINISH:   "Finish"
};

var cSTATUS = {
    ONLINE:   "XBMC is online.",
    OFFLINE:  "XBMC is offline!",  
    CONNECT:  "Connecting...",
    START:    "Starting with [dummy] import.",
    RETRY:    "Retry import...",
    SEARCH:   "Searching for new [dummy]...",
    PROCESS:  "Processing [dummy]...", 
    IMPORT:   "Importing [dummy]...",
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
 * Updated on Oct 17, 2013
 *
 * Description: Set the refresh handler, show the refresh popup box and start the refresh.
 * 
 * In:	media, id, xbmcid
 * Out:	title
 *
 */
function SetStartRefreshHandler(media, id, xbmcid)
{
    InitImportBox();
    
    // Reset status, get xbmc connection (url) and port.
    $.ajax({
        url: 'jsonfargo.php?action=reset&media=' + media + '&counter=' + false,
        async: false,
        dataType: 'json',
        success: function(json)
        {
            var timer, i = 0;
            var online;
            
            // Check if XBMC is online and transfer XBMC media counter (total).
            ImportCounter(json, media, -1);
            
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
 * Updated on Oct 10, 2013
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
    ImportMedia(xbmc, media, id, xbmcid, -1);
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
            ShowRefreshStatus(media, xbmcid, percent);
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
 * Updated on Oct 19, 2013
 *
 * Description: Set the import handler, show the import popup box and start import.
 * 
 * In:	media, selector, found
 * Out:	-
 *
 */
function SetStartImportHandler(media, selector, found)
{    
    switch (media + "_" + selector)
    {
        case "movies_1"  : // First import the movies.
                           InitImportBox();
                           StartImportHandler(media, selector, "movies");
                           break;
                         
        case "movies_2"  : // Second continue with movie sets.
                           StartImportHandler(media, selector, "sets");
                           break;
                          
        case "movies_3"  : // Third import movies end.
                           ShowFinished(found);
                           break;
                                                 
        case "tvshows_1" : // First import the TV shows.
                           InitImportBox();
                           StartImportHandler(media, selector, "tvshows");
                           break;
                           
        case "tvshows_2" : // Second import the TV show seasons.
                           //alert("Import Seasons.");
                           SetTVSeasonsImportHandler(media, selector, "seasons");
                           break;
                         
        case "music_1"   : // First import the albums.
                           InitImportBox();
                           StartImportHandler(media, selector, "music");                          
                           break;
                          
        case "music_2"   : // Second import albums end.
                           ShowFinished(found);
                           break;                          
    }
}

/*
 * Function:	SetTVSeasonImportHandler
 *
 * Created on Oct 19, 2013
 * Updated on Oct 19, 2013
 *
 * Description: Start the seasons import handler.
 * 
 * In:	media, selector
 * Out:	-
 *
 */
function SetTVSeasonsImportHandler(media, selector, type)
{ 
    // Reset media status, get xbmc connection (url), port and fargo media counter.
    $.ajax({
        url: 'jsonfargo.php?action=reset&media=' + media + type + '&counter=' + true,
        async: false,
        dataType: 'json',
        success: function(json)
        {
            var delta, start, end;
            var timer, i = 0;            
            
            // Check if XBMC and get the XbmcTVShowsSeasonsEnd counter from XMBC. 
            ImportCounter(json, "tvseasons", -1);
            
            // Get XBMC media counters from Fargo.
            timer = setInterval(function()
            {
                // Check if iframe from ImportCounter finished loading.
                if ($("#ready").text() == "true")
                {
                    ResetImportBox(type);
                    
                    // Returns global_start and global_end;
                    GetXbmcMediaLimits(media + type);
                    start = global_xbmc_start;
                    end   = global_xbmc_end;
                    delta = end - start;
                    
                    if (end > 0 || i > 3)
                    {
                        if (end > 0 && end >= start) 
                        {
                            /*if (selector == 1) {
                                $("#action_box .message").html(cSTATUS.ONLINE);
                            }*/
                            LogEvent("Information", "Import " + ConvertMedia(type) + " started.");
                            StartTVSeasonsImportWrapper(start, end);
                        }
                        else if (end >= 0) 
                        {
                            /*if (selector == 1) {
                                $("#action_box .message").html(cSTATUS.ONLINE);
                            }*/
                            LogEvent("Information", "No new " + ConvertMedia(type) + " found.");
                            ShowNoNewMedia(type, function(){
                                SetStartImportHandler(media, ++selector, false); // Start next import (episodes).    
                            });
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
 * Function:	StartTVSeasonsImportWrapper
 *
 * Created on Oct 19, 2013
 * Updated on Oct 21, 2013
 *
 * Description: Control and Import the media transfered from XBMC.
 *
 * In:	start, end
 * Out:	-
 *
 */
function StartTVSeasonsImportWrapper(start, end)
{   
    var next = -1;
    
    StartSeasonsImportHandler(start);
    start += 1;
    
    (function setImportTimer() 
    {
        if (global_cancel || start > end) {
            
            if (start > end) {
                alert("Import Seasons Finished!");
            }
            
            return; // End Import.
        }

        if (start == next) { // Get seasons next TV Show.
            StartSeasonsImportHandler(start);
            start += 1;
        }
        
        setTimeout(setImportTimer, 1600);   
    }()); // End setImportTimer.      
    
    // Check TV Show Seasons status.
    var status = setInterval(function()
    {
        if (global_cancel || start > end) {   
            clearInterval(status);
        }
        else { // Get seasons next TV Show.
            next = ShowTVShowSeasonsStatus(start);
        }        
    }, 1600);  
}

/*
 * Function:	ShowTVShowSeasonsStatus
 *
 * Created on Oct 21, 2013
 * Updated on Oct 21, 2013
 *
 * Description: Show the import TV Show Seasons status.
 *
 * In:	start
 * Out:	Status
 *
 */
function ShowTVShowSeasonsStatus(tvshowid)
{   
    var start = 0;
    
    $.ajax({
        url: 'jsonfargo.php?action=status&media=tvshowsseasons&mode=import' + '&id=' + tvshowid,
        async: false, // async can return (start) value.
        dataType: 'json',
        success: function(json) 
        {     
            start = json.counter;
            
            if (json.id > 0)
            {
                
            }  
            /*else {
                $msg.html(msg2); 
            } */             
        } // End succes.    
    }); // End Ajax.
    
    return start;
}

/*
 * Function:	StartSeasonsImportHandler
 *
 * Created on Oct 19, 2013
 * Updated on Oct 21, 2013
 *
 * Description: Control and Import the media transfered from XBMC.
 *
 * In:	tvshowid
 * Out:	
 *
 */
function StartSeasonsImportHandler(tvshowid)
{
    // Reset media status, get xbmc connection (url), port and fargo media counter.
    $.ajax({
        url: 'jsonfargo.php?action=reset&media=seasons' + '&counter=' + false,
        async: false,
        dataType: 'json',
        success: function(json)
        {
            var start, end;
            var timer, i = 0;
            
            // Check if XBMC is online and transfer XBMC media counter (total).
            ImportCounter(json, "seasons", tvshowid);
            
            // Get XBMC media counter from Fargo.
            timer = setInterval(function()
            {
                // Check if iframe from ImportCounter finished loading.
                if ($("#ready").text() == "true")
                {
                    /*if (selector > 1) {
                        ResetImportBox(type);
                    }*/
                    
                    // Returns global_start and global_end;
                    GetXbmcMediaLimits("seasons");
                    start = global_xbmc_start;
                    end   = global_xbmc_end;
                    //delta = end - start;
                    
                    if (end >= 0 || i > 3)
                    {
                        if (end > 0 && end > start) {
                            StartSeasonsImport(json, start, end, tvshowid);
                        }
                        else if (end == 0) { // TV Show not found, skip TV Show.
                            StartSeasonsImport(json, start, end, tvshowid);
                        }
                        else {
                            ShowOffline();
                        }
                        
                        clearInterval(timer);
                    }
                }
                i++;
                
            }, 500); // End timer // 1000
        } // End Success.  
    }); // End Ajax;
}

/*
 * Function:	StartSeasonsImport
 *
 * Created on Oct 19, 2013
 * Updated on Oct 21, 2013
 *
 * Description: Control and Import the media transfered from XBMC.
 *
 * In:	xbmc, start, end, tvshowid
 *
 */
function StartSeasonsImport(xbmc, start, end, tvshowid)
{
    var timeout  = 0;
    var busy     = true;
    var $ready   = $("#ready");
    
    var $img = $("#action_thumb img");
    var $tit = $("#action_title");
     
    // Import media process.
    ImportMedia(xbmc, "seasons", -1, start, tvshowid);
    start += 1;
    
    (function setImportTimer() 
    {
        if (global_cancel || start >= end   || timeout > xbmc.timeout/1000) {      
            return; // End Import.
        }

        // Check if iframe from ImportMedia finished loading.
        if ($ready.text() == "true")
        {
            if (busy == false)
            {    
                busy = true; // pause import.
                ImportMedia(xbmc, "seasons", -1, start, tvshowid);       
                start += 1;
                timeout = 0; // reset timeout.
            }
        }
        
        setTimeout(setImportTimer, 800);  // 1200
    }()); // End setImportTimer.   
        
    // Check seasons status.
    var status = setInterval(function()
    {
        if (global_cancel || global_xbmc_start >= end  || timeout > xbmc.timeout/1000)
        {
            if (timeout > xbmc.timeout/1000) {
                //SetRetryImportHandler(media, type, start, delta, selector);
                //alert("Retry Season");
                console.log("Retry?!?");
            }    
            clearInterval(status);
        }
        else
        {
            // Show status and returns global_xbmc_start.
            ShowSeasonsStatus(start, $img, $tit);
            if (global_xbmc_start == start) {
                busy = false; // Resume import.
            }
            
            if ($ready.text() == "true") {
                timeout++;
            }
        }        
    }, 800); // 1200
}

/*
 * Function:	ShowSeasonsStatus
 *
 * Created on Oct 20, 2013
 * Updated on Oct 20, 2013
 *
 * Description: Show the import seasons status.
 *
 * In:	start, $img, $tit
 * Out:	Status
 *
 */
function ShowSeasonsStatus(start, $img, $tit)
{   
    $.ajax({
        url: 'jsonfargo.php?action=status&media=seasons&mode=import' + '&id=' + start,
        dataType: 'json',
        success: function(json) 
        {     
            global_xbmc_start = json.counter;
            
            if (json.id > 0)
            {
                /*var percent = start - (end - delta);
                percent = Math.round(percent/delta * 100);
                $prg.progressbar({
                    value : percent       
                });             
                
                $msg.html(msg1);*/
                      
                // Preload image.
                var img = new Image();
                img.src = json.thumbs + '/'+ json.tvshowid + '_'+ json.season +'.jpg';
                $img.attr('src', img.src);
                                
                // If images not found then show no poster.
                $img.error(function(){
                    $(this).attr('src', 'images/no_poster.jpg');
                });
                    
                $tit.html(json.title);
            }  
            /*else {
                $msg.html(msg2); 
            } */             
        } // End succes.    
    }); // End Ajax. 
}

/*
 * Function:	StartImportHandler
 *
 * Created on Jul 14, 2013
 * Updated on Oct 17, 2013
 *
 * Description: Start the import handler.
 * 
 * In:	media, selector, type
 * Out:	-
 *
 */
function StartImportHandler(media, selector, type)
{
    // Reset media status, get xbmc connection (url), port and fargo media counter.
    $.ajax({
        url: 'jsonfargo.php?action=reset&media=' + type  + '&counter=' + true,
        async: false,
        dataType: 'json',
        success: function(json)
        {
            var delta, start, end;
            var timer, i = 0;
            
            // Check if XBMC is online and transfer XBMC media counter (total).
            ImportCounter(json, type, -1);
            
            // Get XBMC media counter from Fargo.
            timer = setInterval(function()
            {
                // Check if iframe from ImportCounter finished loading.
                if ($("#ready").text() == "true")
                {
                    if (selector > 1) {
                        ResetImportBox(type);
                    }
                    
                    // Returns global_start and global_end;
                    GetXbmcMediaLimits(type);
                    start = global_xbmc_start;
                    end   = global_xbmc_end;
                    delta = end - start;
                    
                    if (end > 0 || i > 3)
                    {
                        if (end > 0 && end >= start) 
                        {
                            if (selector == 1) {
                                $("#action_box .message").html(cSTATUS.ONLINE);
                            }
                            LogEvent("Information", "Import " + ConvertMedia(type) + " started.");
                            StartImport(json, media, type, start, end, delta, selector);
                        }
                        else if (end >= 0) 
                        {
                            if (selector == 1) {
                                $("#action_box .message").html(cSTATUS.ONLINE);
                            }
                            LogEvent("Information", "No new " + ConvertMedia(type) + " found.");
                            ShowNoNewMedia(type, function(){
                                SetStartImportHandler(media, ++selector, false); // Start next import (sets or episodes).    
                            });
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
 * Updated on Oct 17, 2013
 *
 * Description: Set the import handler and retry the import.
 * 
 * In:	media, type, start, delta, selector
 * Out:	-
 *
 */
function SetRetryImportHandler(media, type, start, delta, selector)
{   
    // Reset media status, get xbmc connection (url), port and fargo media counter.
    $.ajax({
        url: 'jsonfargo.php?action=reset&media=' + type  + '&counter=' + false,
        async: false,
        dataType: 'json',
        success: function(json)
        {
            var end;
            var timer, i = 0;
            
            // Check if XBMC is online and transfer XBMC media counter (total).
            ImportCounter(json, type, -1);
            
            // Get XBMC media counter from Fargo.
            timer = setInterval(function()
            {
                $("#action_box .message").html(cSTATUS.RETRY);  
                
                // Check if iframe from ImportCounter finished loading.
                if ($("#ready").text() == "true")
                {
                    // Returns global_start and global_end;
                    GetXbmcMediaLimits(type);
                    start = global_xbmc_start;
                    end   = global_xbmc_end;
                    
                    if (end > 0 || i > 3)
                    {
                        if (end > 0 && end > start) 
                        {
                            LogEvent("Information", "Restart import from XBMC (Retry).");
                            StartImport(json, media, type, start, end, delta, selector);                            
                        }
                        else if (end >= 0) 
                        {
                            LogEvent("Information", "No new " + ConvertMedia(type) + " found.");
                            ShowNoNewMedia(type, function(){
                                SetStartImportHandler(media, ++selector, false); // Start next import (sets or episodes).    
                            });
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
 * Function:	InitImportBox
 *
 * Created on Aug 18, 2013
 * Updated on Oct 17, 2013
 *
 * Description: Initialize import box values.
 * 
 * In:	-
 * Out:	-
 *
 */
function InitImportBox()
{
    //var media = GetState("media"); // Get state media.
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
}

/*
 * Function:	ResetImportBox
 *
 * Created on Oct 17, 2013
 * Updated on Oct 17, 2013
 *
 * Description: Reset the import box values.
 * 
 * In:	media
 * Out:	-
 *
 */
function ResetImportBox(media)
{
    var msg = cSTATUS.START.replace("[dummy]", ConvertMedia(media));
    
    $("#action_box .message").html(msg);   
    $("#action_box .progress").progressbar({
        value : 0       
    });
}

/*
 * Function:	ShowNoNewMedia
 *
 * Created on Aug 18, 2013
 * Updated on Oct 17, 2013
 *
 * Description: Show no new media and add to log event.
 * 
 * In:	media, callback
 * Out:	-
 *
 */
function ShowNoNewMedia(media, callback)
{
    var finish = 2 + Math.floor(Math.random() * 3);
    var msg1 = cSTATUS.SEARCH.replace("[dummy]", ConvertMedia(media));
    var msg2 = cSTATUS.NOTFOUND.replace("[dummy]", ConvertMedia(media));
    
    //$("#action_box .message").html(cSTATUS.ONLINE);
    SetState("xbmc", "online");
    DisplayStatusMessage(msg1, msg2, finish, callback);
    //LogEvent("Information", "No new " + ConvertMedia(media) + " found.");
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
 * Updated on Oct 17, 2013
 *
 * Description: Show import finished message and add to log event.
 * 
 * In:	found
 * Out:	-
 *
 */
function ShowFinished(found)
{   
    var msg = cSTATUS.READY.replace("[dummy]", cIMPORT.IMPORT);
    
    if (found) 
    {
        setTimeout(function() 
        {
            $("#action_box .message").html(msg); 
            $(".cancel").html("Finish");
        }, 2000);        
    }
    else {
        $(".cancel").html("Finish");
    } 
}

/*
 * Function:	SetImportCancelHandler
 *
 * Created on May 09, 2013
 * Updated on Oct 18, 2013
 *
 * Description: Set the import handler, cancel or finish the import.
 * 
 * In:	media
 * Out:	title
 *
 */
function SetImportCancelHandler()
{    
    //var $popup = $(".popup:visible");
    var media = $("#control_bar").find(".on").attr('id');
    //SetState("media", media); 
    
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
    
    /*if ($popup.find(".cancel").text() == "Cancel") {
        //LogEvent("Warning", "Import " + ConvertMedia(media) + " canceled!");
        LogImportCounter(media, false);
    }*/
    
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
 * Updated on Oct 21, 2013
 *
 * Description: Control and Import the media transfered from XBMC.
 *
 * In:	xbmc, media, type, start, end, delta, selector
 * Out:	-
 *
 */
function StartImport(xbmc, media, type, start, end, delta, selector)
{
    var timeout  = 0;
    var busy     = true;
    var $ready   = $("#ready");
    
    var $prg = $("#action_box .progress");
    var $img = $("#action_thumb img");
    var $tit = $("#action_title");
    var $msg = $("#action_box .message");
    var msg1 = cSTATUS.IMPORT.replace("[dummy]", ConvertMediaToSingular(type));
    var msg2 = cSTATUS.PROCESS.replace("[dummy]", ConvertMediaToSingular(type));
 
    // Import media process.
    ImportMedia(xbmc, type, -1, start, -1);
    start += 1;
    
    (function setImportTimer() 
    {
        if (global_cancel || start > end   || timeout > xbmc.timeout/1000) 
        {            
            if (start > end) 
            {
                setTimeout(function() 
                {
                    SetStartImportHandler(media, ++selector, true);
                    LogImportCounter(type, true);
                }, 3000); // 2000
            }
            
            if (global_cancel) {
                LogImportCounter(type, false);
            }
            
            return; // End Import.
        }

        // Check if iframe from ImportMedia finished loading.
        if ($ready.text() == "true")
        {
            if (busy == false)
            {    
                busy = true; // pause import.
                ImportMedia(xbmc, type, -1, start, -1);       
                start += 1;
                timeout = 0; // reset timeout.
            }
        }
        
        setTimeout(setImportTimer, 1200); // 500    
    }()); // End setImportTimer.   
        
    // Check status.
    var status = setInterval(function()
    {
        if (global_cancel || global_xbmc_start > end  || timeout > xbmc.timeout/1000)
        {
            /*if (global_cancel) {
                LogEvent("Warning", "Import " + ConvertMedia(media) + " canceled!");
            }
            else*/
            if (timeout > xbmc.timeout/1000) {
                SetRetryImportHandler(media, type, start, delta, selector);
            }
            /*else {
                ShowFinished(type, delta);
            }*/
            
            clearInterval(status);
        }
        else
        {
            // Show status and returns global_xbmc_start.
            ShowStatus(delta, start-1, end, type, $prg, $img, $tit, $msg, msg1, msg2);
            if (global_xbmc_start == start) {
                busy = false; // Resume import.
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
 * Updated on Oct 17, 2013
 *
 * Description: Show the import status.
 *
 * In:	delta, start, end, media, $prg, $img, $tit, $msg, msg1, msg2
 * Out:	Status
 *
 */
function ShowStatus(delta, start, end, media, $prg, $img, $tit, $msg, msg1, msg2)
{   
    $.ajax({
        url: 'jsonfargo.php?action=status&media=' + media + '&mode=import' + '&id=' + start,
        dataType: 'json',
        success: function(json) 
        {     
            global_xbmc_start = json.counter;
            
            if (json.id > 0)
            {
                var percent = start - (end - delta);
                percent = Math.round(percent/delta * 100);
                $prg.progressbar({
                    value : percent       
                });             
                
                $msg.html(msg1);
                      
                // Preload image.
                var img = new Image();
                img.src = json.thumbs + '/'+ json.id +'.jpg';
                $img.attr('src', img.src);
                                
                // If images not found then show no poster.
                $img.error(function(){
                    $(this).attr('src', 'images/no_poster.jpg');
                });
                    
                $tit.html(json.title);
            }  
            else {
                $msg.html(msg2); 
            }              
        } // End succes.    
    }); // End Ajax. 
}

/*
 * Function:	ImportCounter
 *
 * Created on Jul 22, 2013
 * Updated on Oct 19, 2013
 *
 * Description: Import the media counter transfered from XBMC.
 *
 * In:	xbmc, media, tvshowid
 * Out:	Imported media counter
 *
 */
function ImportCounter(xbmc, media, tvshowid)
{
    var $result = $("#transfer");
    var $ready  = $("#ready");
    var url, iframe;
    
    url = "http://" + xbmc.connection;
    if (xbmc.port) {
        url = "http://" + xbmc.connection + ":" + xbmc.port;
    }
    
    url   += "/fargo/transfer.html?action=counter&media=" + media + "&tvshowid=" + tvshowid + "&key=" + xbmc.key;
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
 * Updated on Oct 19, 2013
 *
 * Description: Import the media transfered from XBMC.
 *
 * In:	xbmc, media, fargoid, xbmcid, tvshowid
 * Out:	Imported media
 *
 */
function ImportMedia(xbmc, media, fargoid, xbmcid, tvshowid)
{
    var $result = $("#transfer");
    var $ready  = $("#ready");   
    var url, iframe;
    
    url = "http://" + xbmc.connection;
    if (xbmc.port) {
        url = "http://" + xbmc.connection + ":" + xbmc.port;
    }    
    
    url   += "/fargo/transfer.html?action=" + media + "&xbmcid=" + xbmcid + "&fargoid=" + fargoid + "&tvshowid=" + tvshowid + "&key=" + xbmc.key;
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
 * Updated on Oct 15, 2013
 *
 * Description: Display status message.
 *
 * In:	str1, str2, end, callback
 * Out:	Status
 *
 */
function DisplayStatusMessage(str1, str2, end, callback)
{
    var i = 0;
    var percent;
    var timer = setInterval(function()
    {
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
                //$(".cancel").html(cIMPORT.FINISH);
                
		if (callback && typeof(callback) === "function") {  
                    callback();
		}       
            }
        }    
        else {
            clearInterval(timer);
        }    
        
    }, 1000);
}

/*
 * Function:	LogImportCounter
 *
 * Created on Oct 17, 2013
 * Updated on Oct 18, 2013
 *
 * Description: Get and log import counter.
 *
 * In:	media. finish
 * Out:	-
 *
 */
function LogImportCounter(media, finish)
{
     $.ajax({
        url: 'jsonfargo.php?action=counter&media=' + media,
        async: false,
        dataType: 'json',
        success: function(json) 
        {           
            var counter = Number(json.import);
            var name    = ConvertMedia(media);
            
            if (finish) {
                LogEvent("Information", "Import of " + counter + " " + name + " finished."); 
            }
            else {
                LogEvent("Warning", counter + " " + name + " imported. The import was canceled!");
            }
        } // End Success.
    }); // End Ajax;   
}