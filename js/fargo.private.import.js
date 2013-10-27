/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.3
 *
 * File:    fargo.private.import.js
 *
 * Created on Jul 14, 2013
 * Updated on Oct 27, 2013
 *
 * Description: Fargo's jQuery and Javascript functions page for the XBMC media import.
 *
 */

//////////////////////////////////////////    Main Functions    ///////////////////////////////////////////

/*
 * Function:	SetStartImportHandler
 *
 * Created on Jul 14, 2013
 * Updated on Oct 27, 2013
 *
 * Description: Set the import handler, show the import popup box and start import.
 * 
 * In:	media, selector, found, delay
 * Out:	-
 *
 */
function SetStartImportHandler(media, selector, found, delay)
{    
    switch (media + "_" + selector)
    {
        case "movies_1"  : // First import the movies.
                           InitImportBox();
                           StartImportHandler(media, selector, "movies");
                           break;
                         
        case "movies_2"  : // Second continue with movie sets.
                           setTimeout(function() {
                               StartImportHandler(media, selector, "sets");
                           }, delay);
                           break;
                          
        case "movies_3"  : // Third import movies end.
                           ShowFinished(found);
                           break;
                                                 
        case "tvshows_1" : // First import the TV shows.
                           InitImportBox();
                           StartImportHandler(media, selector, "tvshows");
                           break;
                           
        case "tvshows_2" : // Second import the TV show seasons.
                           setTimeout(function() {
                               SetTVSeasonsImportHandler(media, selector, "seasons");
                           }, delay); 
                           break;
                           
        case "tvshows_3" : // Third import the TV show episodes.                           
                           setTimeout(function() {
                               $("#action_thumb").css("margin-left", "-125px");
                               $("#action_thumb").width(220);
                               $("#action_thumb img").width(220);
                               StartImportHandler(media, selector, "episodes");
                           }, delay);
                           break;
                       
        case "tvshows_4" : // Fourth import TV Shows end.
                           ShowFinished(found);
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

/////////////////////////////////////////    Import Functions    //////////////////////////////////////////

/*
 * Function:	StartImportHandler
 *
 * Created on Jul 14, 2013
 * Updated on Oct 26, 2013
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
                        ResetImportBox();
                    }
                    
                    // Returns global_start and global_end;
                    GetXbmcMediaLimits(type);
                    start = gTRIGGER.START;
                    end   = gTRIGGER.END;
                    delta = end - start + 1;
                    
                    if (end > 0 || i > 3)
                    {
                        if (end > 0 && end >= start) 
                        {
                            if (selector == 1) {
                                $("#action_box .message").html(cSTATUS.ONLINE);
                            }
                            LogEvent("Information", "Import " + ConvertMedia(type) + " started.");
                            StartImport(json, media, type, delta, start, end, selector);
                        }
                        else if (end >= 0) 
                        {
                            if (selector == 1) {
                                $("#action_box .message").html(cSTATUS.ONLINE);
                            }
                            LogEvent("Information", "No new " + ConvertMedia(type) + " found.");
                            ShowNoNewMedia(type, function(){
                                SetStartImportHandler(media, ++selector, false, 0); // Start next import (sets or episodes).    
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
 * Updated on Oct 26, 2013
 *
 * Description: Set the import handler and retry the import.
 * 
 * In:	media, type, delta, start, selector
 * Out:	-
 *
 */
function SetRetryImportHandler(media, type, delta, start, selector)
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
                    start = gTRIGGER.START;
                    end   = gTRIGGER.END;
                    
                    if (end > 0 || i > 3)
                    {
                        if (end > 0 && end > start) 
                        {
                            LogEvent("Information", "Restart import from XBMC (Retry).");
                            StartImport(json, media, type, delta, start, end, selector);                            
                        }
                        else if (end >= 0) 
                        {
                            LogEvent("Information", "No new " + ConvertMedia(type) + " found.");
                            ShowNoNewMedia(type, function(){
                                SetStartImportHandler(media, ++selector, false, 0); // Start next import (sets or episodes).    
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
 * Function:	StartImport
 *
 * Created on Jul 22, 2013
 * Updated on Oct 26, 2013
 *
 * Description: Control and Import the media transfered from XBMC.
 *
 * In:	xbmc, media, type, delta, start, end, selector
 * Out:	-
 *
 */
function StartImport(xbmc, media, type, delta, start, end, selector)
{
    var timeout  = 0;
    var busy     = false;
    var $ready   = $("#ready");
    
    var $prg = $("#action_box .progress");
    var $img = $("#action_thumb img");
    var $tit = $("#action_title");
    var $msg = $("#action_box .message");
    var msg1 = cSTATUS.IMPORT.replace("[dummy]", ConvertMediaToSingular(type));
    var msg2 = cSTATUS.PROCESS.replace("[dummy]", ConvertMediaToSingular(type));
 
    // Import media process.
    //ImportMedia(xbmc, type, -1, start, -1);
    //start += 1;
    
    (function setImportTimer() 
    {
        if (gTRIGGER.CANCEL || start > end || timeout > xbmc.timeout/1000) 
        {            
            if (start > end) 
            {
                SetStartImportHandler(media, ++selector, true, 3000);
                setTimeout(function() 
                {
                    $prg.progressbar({value : 100});
                    LogImportCounter(type, true);
                }, 1000);
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
        if (gTRIGGER.CANCEL || gTRIGGER.START > end  || timeout > xbmc.timeout/1000)
        {
            if (timeout > xbmc.timeout/1000) {
                SetRetryImportHandler(media, type, start, delta, selector);
            }
            
            clearInterval(status); // End Status check.
        }
        else
        {
            // Show status and returns global_xbmc_start.
            ShowStatus(delta, start-1, end, type, $prg, $img, $tit, $msg, msg1, msg2);
            if (gTRIGGER.START == start) {
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
 * Updated on Oct 26, 2013
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
            gTRIGGER.START = json.counter;
            
            if (json.id > 0)
            {
                var percent = start - (end - delta);
                percent = Math.round(percent/delta * 100);
                
                // Don't show the last step.
                if (start != end) 
                {
                    $prg.progressbar({
                        value : percent       
                    });
                    
                    $msg.html(msg1);
                }
                      
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

////////////////////////////////////    Import Seasons Functions    ///////////////////////////////////////

/*
 * Function:	SetTVSeasonImportHandler
 *
 * Created on Oct 19, 2013
 * Updated on Oct 26, 2013
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
                    ResetImportBox();
                    
                    // Returns global_start and global_end;
                    GetXbmcMediaLimits(media + type);
                    start = gTRIGGER.START;
                    end   = gTRIGGER.END;
                    delta = end - start + 1;
                    
                    if (end > 0 || i > 3)
                    {
                        if (end > 0 && end >= start) 
                        {
                            LogEvent("Information", "Import " + ConvertMedia(type) + " started.");
                            StartTVSeasonsImportWrapper(json, start, end, delta);
                        }
                        else if (end >= 0) 
                        {
                            LogEvent("Information", "No new " + ConvertMedia(type) + " found.");
                            ShowNoNewMedia(type, function(){
                                SetStartImportHandler(media, ++selector, false, 0); // Start next import (episodes).    
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
 * Updated on Oct 26, 2013
 *
 * Description: Control and Import the media transfered from XBMC.
 *
 * In:	xbmc, start, end, delta
 * Out:	-
 *
 */
function StartTVSeasonsImportWrapper(xbmc, start, end, delta)
{   
    var timeout  = 0;
    var $prg = $("#action_box .progress");
    //var $img = $("#action_thumb img");
    //var $tit = $("#action_title");
    var $msg = $("#action_box .message");
    var msg1 = cSTATUS.IMPORT.replace("[dummy]", "Season");
    var msg2 = cSTATUS.PROCESS.replace("[dummy]", "Season");
    
    gTRIGGER.STARTTV = start;

    StartSeasonsImportHandler(start);
    start += 1;
    
    (function setImportTimer() 
    {
        if (gTRIGGER.CANCEL || start > end || timeout > xbmc.timeout/1000) {
            
            if (start > end) 
            {
                //alert("Import Seasons Finished!");
                SetStartImportHandler("tvshows", 3, true, 3000);
                setTimeout(function() 
                {
                    $prg.progressbar({value : 100});
                    LogImportCounter("seasons", true);
                }, 1000);
            }
            
            return; // End Import.
        }

        if (start == gTRIGGER.STARTTV) { // Get seasons next TV Show.
            StartSeasonsImportHandler(start);
            start += 1;
        }
        
        setTimeout(setImportTimer, 1600);   
    }()); // End setImportTimer.      
    
    // Check TV Show Seasons status.
    var status = setInterval(function()
    {
        if (gTRIGGER.CANCEL || start > end || timeout > xbmc.timeout/1000) 
        {   
            if (timeout > xbmc.timeout/1000) {
                //SetRetryImportHandler(media, type, start, delta, selector);
                alert("Retry Season Wrapper!");
            }
            
            clearInterval(status); // End Status check.
        }
        else { // Get seasons next TV Show.
            // Returns gTRIGGER.STARTTV
            ShowTVShowSeasonsStatus(start, end, delta, $prg, $msg, msg1, msg2);
        }        
    }, 1600);  
}

/*
 * Function:	ShowTVShowSeasonsStatus
 *
 * Created on Oct 21, 2013
 * Updated on Oct 26, 2013
 *
 * Description: Show the import TV Show Seasons status.
 *
 * In:	start, end, delta, $prg, $msg1, $msg2
 * Out:	Status
 *
 */
function ShowTVShowSeasonsStatus(start, end, delta, $prg, $msg, msg1, msg2)
{   
    $.ajax({
        url: 'jsonfargo.php?action=status&media=tvshowsseasons&mode=import' + '&id=' + start, // tvshowid
        dataType: 'json',
        success: function(json) 
        {     
            gTRIGGER.STARTTV = json.counter;
            
            if (json.id > 0)
            {
                var percent = start - (end - delta);
                percent = Math.round(percent/delta * 100);
                
                if (start != end)
                {    
                    $prg.progressbar({
                        value : percent      
                    });             
                
                    $msg.html(msg2);
                }    
            }  
            else {
                $msg.html(msg1); 
            }             
        } // End succes.    
    }); // End Ajax.
}

/*
 * Function:	StartSeasonsImportHandler
 *
 * Created on Oct 19, 2013
 * Updated on Oct 25, 2013
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
                        ResetImportBox();
                    }*/
                    
                    // Returns global_start and global_end;
                    GetXbmcMediaLimits("seasons");
                    start = gTRIGGER.START;
                    end   = gTRIGGER.END;
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
 * Updated on Oct 25, 2013
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
        if (gTRIGGER.CANCEL || start >= end || timeout > 2 * xbmc.timeout/1000) 
        {            
            if (gTRIGGER.CANCEL) {
                LogImportCounter("seasons", false);
            }
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
        if (gTRIGGER.CANCEL || gTRIGGER.START >= end || timeout > 2 * xbmc.timeout/1000)
        {
            if (timeout > 2 * xbmc.timeout/1000) {
                //SetRetryImportHandler(media, type, start, delta, selector);
                //alert("Retry Season");
                console.log("Retry?!? " + xbmc.timeout);
            }    
            clearInterval(status);
        }
        else
        {
            // Show status and returns global_xbmc_start.
            ShowSeasonsStatus(start, $img, $tit);
            if (gTRIGGER.START == start) {
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
 * Updated on Oct 25, 2013
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
            gTRIGGER.START = json.counter;
            
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

//////////////////////////////////   Common Import & Refresh Functions    /////////////////////////////////

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
 * Function:	InitImportBox
 *
 * Created on Aug 18, 2013
 * Updated on Oct 23, 2013
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
    gTRIGGER.CANCEL = false;
    global_ready    = false;
    
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
 * Updated on Oct 26, 2013
 *
 * Description: Reset the import box values.
 * 
 * In:	-
 * Out:	-
 *
 */
function ResetImportBox()
{
    var msg = cSTATUS.WAIT; //.replace("[dummy]", ConvertMedia(media));
    
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
 * Updated on Oct 26, 2013
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
       }, 1000);      
    }
    else {
        $(".cancel").html("Finish");
    } 
}

/*
 * Function:	SetImportCancelHandler
 *
 * Created on May 09, 2013
 * Updated on Oct 25, 2013
 *
 * Description: Set the import handler, cancel or finish the import.
 * 
 * In:	media
 * Out:	title
 *
 */
function SetImportCancelHandler()
{    
    var media  = $("#control_bar").find(".on").attr('id');
    var $popup = $(".popup:visible");
    
    // Find media type (Movie, TV Show, Season, Episode, Album) in message and replaces "..." with "s". 
    var type   = $popup.find(".message").text().split(" ")[1].replace(/[.]+/, "s");   
    
    /*
    // Abort pending ajax request.
    if(typeof global_status_request !== 'undefined') {
        global_status_request.abort();
    }
    
    if(typeof global_import_request !== 'undefined') {
        global_import_request.abort();
    }
    */
    
    // Reset import values.
    gTRIGGER.CANCEL = true;
    global_total_fargo = 0;
    global_total_xbmc  = 0;    

    $("#action_box .progress").progressbar({
        value : 0       
    });
    
    if ($popup.find(".cancel").text() == "Cancel") {
        LogImportCounter(type, false);
    }
    
    if (media != "system") {
        window.location='index.php?media=' + media;              
    }
}

/*
 * Function:	DisplayStatusMessage
 *
 * Created on May 17, 2013
 * Updated on Oct 24, 2013
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
        if (!gTRIGGER.CANCEL)
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