/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.4
 *
 * File:    fargo.private.import.js
 *
 * Created on Jul 14, 2013
 * Updated on Jan 17, 2014
 *
 * Description: Fargo's jQuery and Javascript functions page for the XBMC media import.
 *
 */

//////////////////////////////////////////    Main Functions    ///////////////////////////////////////////

/*
 * Function:	SetStartImportHandler
 *
 * Created on Jul 14, 2013
 * Updated on Jan 13, 2014
 *
 * Description: Set the start import handler.
 * 
 * In:	media, step
 * Out:	-
 *
 */
function SetStartImportHandler(media, step)
{
    switch (media)
    {
        case "movies"  : SetStartMoviesImportHandler(step);
                         break;
        
        case "tvshows" :
                         break;
                         
        case "music"   :
                         break;
    }
}

/////////////////////////////////////////    Import Functions    //////////////////////////////////////////

/*
 * Function:	SetStartMoviesImportHandler
 *
 * Created on Jan 12, 2014
 * Updated on Jan 14, 2014
 *
 * Description:  Start the import handler.
 * 
 * In:	type
 * Out:	-
 *
 */
function SetStartMoviesImportHandler(step)
{
    switch (step)
    {
        case 1 : // Import Movies meta data.
                 StartMetaImportHandler("movies");
                 break;
                 
        case 2 : // Import Movies.
                 StartImportHandler("movies", 3);
                 //console.log("Start movies import."); // Debug.
                 break;
                 
        case 3: // Import Sets meta data.
                console.log("Start sets meta data import."); // Debug.
                break;
    }
}


/*
 * Function:	StartMetaImportHandler
 *
 * Created on Jan 12, 2014
 * Updated on Jan 16, 2014
 *
 * Description:  Start the meta import handler.
 * 
 * In:	type
 * Out:	-
 *
 * Note: Uses globals cCONNECT, gTRIGGER.START and gTRIGGER.END. 
 * 
 */
function StartMetaImportHandler(type)
{
    // Returns cCONNECT, gTRIGGER.START and gTRIGGER.END.
    var start = StartOnlineHandler(type);
    start.done (function() {
        
        var $prg = $("#action_box .progress");
        var end  = Math.round(gTRIGGER.END/cBULKMAX);
        
        if (gTRIGGER.END >= 0)
        {
            $("#action_box .message").html(cSTATUS.ONLINE);
            //LogEvent("Information", "Import movies meta data started.");
            var meta = StartMetaImport(type, end);
            
            meta.progress(function(i) {
                ShowMetaProgress($prg, type, i, end-1);
                //console.log("Meta Counter: " + i);
            }); 
            
            meta.done (function() {
                if (!gTRIGGER.CANCEL) {
                    SetStartImportHandler(type, 2); // Step 2: Import media.
                }
            }).fail (function() {
               ShowOffline(); 
            });
        }         
    }).fail (function() {
        ShowOffline(); 
    }); // End Start.
}

/*
 * Function:	StartOnlineHandler
 *
 * Created on Jul 14, 2013
 * Updated on Jan 14, 2014
 *
 * Description:  Check if XBMC is online.
 * 
 * In:	type
 * Out:	Globals cCONNECT, gTRIGGER.START and gTRIGGER.END
 *
 * Note : Init globals cCONNECT, gTRIGGER.START and gTRIGGER.END.
 *
 */
function StartOnlineHandler(type)
{
    var deferred = $.Deferred();
    InitImportBox();
    
    // Reset media status, get xbmc connection (url), port and fargo media counter.
    $.ajax({
        url: 'jsonmanage.php?action=reset&media=' + type,
        async: false,
        dataType: 'json',
        success: function(json)
        {
            cCONNECT = json;                   
            var url = GetTransferUrl(cCONNECT) + "/fargo/transfer.html?action=counter&media=" + type + "&key=" + cCONNECT.key;
            var ready = ImportData(url, 0);
            ready.done(function() {
                //console.log("Iframe is ready"); // debug 
                
                // Returns gTRIGGER.START and gTRIGGER.END.
                GetXbmcMediaLimits(type);
                
                deferred.resolve();       
            }).fail(function() {
                deferred.reject();
            }); // End Ready.  
        } // End Success.       
    }); // End Ajax; 

    return deferred.promise();
}

/*
 * Function:	StartMetaImport
 *
 * Created on Jan 12, 2014
 * Updated on Jan 17, 2014
 *
 * Description: Control and Import the media meta data transfered from XBMC.
 *
 * In:	type, end
 * Out:	-
 *
 */
function StartMetaImport(type, end)
{
    var deferred = $.Deferred();   
    var url = GetTransferUrl(cCONNECT) + "/fargo/meta.html?action=" + type + "&counter=" + 0 + "&key=" + cCONNECT.key;
    var currentStep = ImportData(url, 0);
    
    deferred.notify(0);
    
    for(var i = 1; i <= end; i++)
    {       
        //console.log("Meta Counter: " + i);
        currentStep = currentStep.pipe(function(j){
            if (!gTRIGGER.CANCEL) {
                deferred.notify(++j);
                url = GetTransferUrl(cCONNECT) + "/fargo/meta.html?action=" + type + "&counter=" + j + "&key=" + cCONNECT.key;
                return ImportData(url, j);
            }
        });
    }
    $.when(currentStep).done(function(){
        //console.log("All steps done.");
        deferred.resolve();
    }).fail(function(){
        deferred.reject();
    });
    
    return deferred.promise();
}

/*
 * Function:	ShowMetaProgress
 *
 * Created on Jan 13, 2014
 * Updated on Jan 16, 2014
 *
 * Description: Show the meta data import progress.
 * 
 * In:	$prg, type, i, end
 * Out:	-
 *
 */
function ShowMetaProgress($prg, type, i, end)
{
    var percent;
    if (end > 0)
    {
        percent = 1 - (end - i)/end;
        percent = Math.round(percent * 100);
    }
    else {
        i = 1;
        percent = 100; 
    }
    
    $prg.progressbar({
        value : percent       
    });
    
    if (i == 1 ) {
        $("#action_box .message").html(cSTATUS.SEARCH.replace("[dummy]", ConvertMedia(type)));
    }
}

/*
 * Function:	StartImportHandler
 *
 * Created on Jan 14, 2014
 * Updated on Jan 16, 2014
 *
 * Description:  Start the media import handler.
 * 
 * In:	type, next
 * Out:	-
 *
 * Note: Uses globals cCONNECT, gTRIGGER.START and gTRIGGER.END. 
 *
 */
function StartImportHandler(type, next)
{
    var $prg = $("#action_box .progress");
    var $img = $("#action_thumb img");
    var $tit = $("#action_title");
    var $sub = $("#action_sub");
    var $msg = $("#action_box .message");
       
    $msg.html(cSTATUS.WAIT);
    setTimeout(function() {
    
        if (gTRIGGER.START >= gTRIGGER.END) 
        {
           $msg.html(cSTATUS.NOTFOUND.replace("[dummy]", ConvertMedia(type)));
           SetStartImportHandler(type, next); // Continue with the next step.
        }
        else 
        {
            //$msg.html(cSTATUS.ONLINE);
            //LogEvent("Information", "Import movies meta data started.");
            var start = StartImport(type);
            
            start.progress(function(i, status) {
                ShowImportProgress($msg, $prg, $img, $tit, $sub, type, i, status);
                console.log("Counter: " + i); // debug.
            }); 
            
            start.done (function() {
                if (!gTRIGGER.CANCEL) {
                    SetStartImportHandler(type, next);  // Continue with the next step.
                }
            }).fail (function() {
                ShowOffline(); 
            });
        }    
    }, cCONNECT.timeout); // End setTimeout.
}

/*
 * Function:	StartImport
 *
 * Created on Jan 14, 2014
 * Updated on Jan 17, 2014
 *
 * Description: Control and Import the media data transfered from XBMC.
 *
 * In:	type
 * Out:	-
 *
 * Note: Uses globals cCONNECT, gTRIGGER.START and gTRIGGER.END. 
 *
 */

function StartImport(type)
{
    var deferred = $.Deferred(); 
    var url, ready;
    var retry = 0;
    var start = gTRIGGER.START;
    gTRIGGER.BUSY = false;
    
    cMETA.NEXTID = 0;
    
    (function setImportTimer() 
    {
        if (gTRIGGER.CANCEL || start > gTRIGGER.END || retry > gTRIGGER.RETRY) 
        {            
            if (start > gTRIGGER.END) {
                deferred.resolve(); // Import is ready
            }      
            return; // End Import.
        }
        
        // Returns cMETA.
        GetXbmcIdAndStatus(type, start, cMETA.NEXTID);
        
        if (gTRIGGER.BUSY == false)
        {
            gTRIGGER.BUSY = true;
               
            url = GetTransferUrl(cCONNECT) + "/fargo/transfer.html?action=" + type + "&xbmcid=" + cMETA.NEXTID + "&fargoid=-1" + "&key=" + cCONNECT.key;
            ready = ImportData(url, 0);
            ready.done(function() {
                deferred.notify(start, cMETA);
            }).fail(function() {
                deferred.reject();
            }); // End Ready.
            
            start += 1;
            retry = 0;
        }
               
        // Timeout is set in the Fargo system screen.
        setTimeout(setImportTimer, cCONNECT.timeout);
    }()); // End setImportTimer.
    
    return deferred.promise();
}


/*
 * Function:	StartImport
 *
 * Created on Jan 14, 2014
 * Updated on Jan 17, 2014
 *
 * Description: Control and Import the media data transfered from XBMC.
 *
 * In:	type, end
 * Out:	-
 *
 * Note: Uses globals cCONNECT, gTRIGGER.START and gTRIGGER.END. 
 *
 */
function StartImportTest(type)
{
    var deferred = $.Deferred(); 
    var url, currentStep;
    var start = gTRIGGER.START;
    
    // Returns XBMCID, cSTATUS 
    GetXbmcIdAndStatus(type, start++, 0);
    //console.log("Get xbmcid ready..."); //debug
    
    url = GetTransferUrl(cCONNECT) + "/fargo/transfer.html?action=" + type + "&xbmcid=" + cMETA.nextid + "&fargoid=-1" + "&key=" + cCONNECT.key;
    currentStep = ImportData(url, start);
    
    deferred.notify(0, cMETA);
    
    for(var i = start; i <= gTRIGGER.END; i++)
    {  
        //console.log("Import Counter: " + i);
        currentStep = currentStep.pipe(function(j){
            if (!gTRIGGER.CANCEL) {
                GetXbmcIdAndStatus(type, j, cMETA.nextid);
                deferred.notify(++j, cMETA);
                url = GetTransferUrl(cCONNECT) + "/fargo/transfer.html?action=" + type + "&xbmcid=" + cMETA.nextid + "&fargoid=-1" + "&key=" + cCONNECT.key;
                return ImportData(url, j);
            }
        });
    }
    $.when(currentStep).done(function(){
        //console.log("All steps done.");
        
        deferred.resolve();
    }).fail(function(){
        deferred.reject();
    });
    
    return deferred.promise();
}

/*
 * Function:	GetXbmcIdAndStatus
 *
 * Created on Jan 14, 2014
 * Updated on Jan 14, 2014
 *
 * Description: Control and Import the media data transfered from XBMC.
 *
 * In:	type, start, xbmcid
 * Out:	Global cMETA (xbmcid, title, subtitle, thumbs)
 *
 */
function GetXbmcIdAndStatus(type, start, xbmcid)
{  
    $.ajax({
        url: 'jsonmanage.php?action=status&media=' + type + '&id=' + start + '&xbmcid=' + xbmcid,
        async: false,
        dataType: 'json',
        success: function(json)
        {     
            cMETA.NEXTID = json.nextid;
            cMETA.TITLE  = json.title;
            cMETA.SUB    = json.sub;
            cMETA.THUMBS = json.thumbs;
            cMETA.XBMCID = json.xbmcid;
            
            //console.log("METa done."); // debug.
            
        } // End succes.    
    }); // End Ajax.    
}

/*
 * Function:	ShowImportProgress
 *
 * Created on Jan 16, 2014
 * Updated on Jan 17, 2014
 *
 * Description: Show the import progress.
 * 
 * In:	$msg, $prg, $img, $tit, $sub, type, i, status
 * Out:	-
 *
 */
function ShowImportProgress($msg, $prg, $img, $tit, $sub, type, i, status)
{
    var delta = gTRIGGER.END - gTRIGGER.START;
    
    var percent = i - (gTRIGGER.END - delta);
    percent = Math.round(percent/delta * 100);
                
    $prg.progressbar({
        value : percent       
    });
    
    if (i == 0) {
        $msg.html(cSTATUS.IMPORT.replace("[dummy]", ConvertMediaToSingular(type)));
    }
    else 
    {    
        // Preload image.
        var img = new Image();      
        img.src = status.THUMBS + '/'+ status.XBMCID +'.jpg';
        
        console.log(i + " " + img.src); // debug.
        
        $img.attr('src', img.src);
                                
        // If images not found then show no poster.
        $img.error(function(){
            $(this).attr('src', 'images/no_poster.jpg');
        });
                    
        $tit.html(status.TITLE);
        $sub.html(status.SUB);
    }
    
    gTRIGGER.BUSY = false;
}




/*
 * Function:	SetStartImportHandler
 *
 * Created on Jul 14, 2013
 * Updated on Oct 31, 2013
 *
 * Description: Set the import handler, show the import popup box and start import.
 * 
 * In:	media, selector, found, delay
 * Out:	-
 *
 */
/*function SetStartImportHandlerOld(media, selector, found, delay)
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
}*/

/////////////////////////////////////////    Import Functions    //////////////////////////////////////////

/*
 * Function:	StartImportHandler
 *
 * Created on Jul 14, 2013
 * Updated on Nov 21, 2013
 *
 * Description: Start the import handler.
 * 
 * In:	media, selector, type
 * Out:	-
 *
 */
function StartImportHandlerOld(media, selector, type)
{
    // Reset media status, get xbmc connection (url), port and fargo media counter.
    $.ajax({
        url: 'jsonmanage.php?action=reset&media=' + type  + '&counter=' + true,
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
                    
                    // Returns gTRIGGER.START and gTRIGGER.END.
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
 * Updated on Nov 21, 2013
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
        url: 'jsonmanage.php?action=reset&media=' + type  + '&counter=' + false,
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
                    // Returns gTRIGGER.START and gTRIGGER.END.
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
 * Updated on Jan 09, 2014
 *
 * Description: Control and Import the media transfered from XBMC.
 *
 * In:	xbmc, media, type, delta, start, end, selector
 * Out:	-
 *
 */
function StartImportOld(xbmc, media, type, delta, start, end, selector)
{
    var retry   = 0;
    var busy    = false;
    var $ready  = $("#ready");
    
    var $prg = $("#action_box .progress");
    var $img = $("#action_thumb img");
    var $tit = $("#action_title");
    var $sub = $("#action_sub");
    var $msg = $("#action_box .message");
    var msg  = cSTATUS.IMPORT.replace("[dummy]", ConvertMediaToSingular(type));
    
    (function setImportTimer() 
    {
        if (gTRIGGER.CANCEL || start > end || retry > gTRIGGER.RETRY) 
        {            
            if (start > end) 
            {
                SetStartImportHandler(media, ++selector, true, 3500);
                setTimeout(function() 
                {
                    $prg.progressbar({value : 100});
                    LogImportCounter(type, true);
                }, 2000);
            }      
            return; // End Import.
        }

        // Check if iframe from ImportMedia finished loading.
        if ($ready.text() == "true")
        {
            if (busy == false)
            {    
                busy = true; // Pause import.
                ImportMedia(xbmc, type, -1, start, -1);
                start += 1;
                retry = 0; // Reset retries.
            }
        }
               
        // Timeout is set in the Fargo system screen.
        setTimeout(setImportTimer, xbmc.timeout);
    }()); // End setImportTimer.   
        
    // Check status.
    var status = setInterval(function()
    {     
        if (gTRIGGER.CANCEL || gTRIGGER.START > end  || retry > gTRIGGER.RETRY)
        {
            if (retry > gTRIGGER.RETRY) {
                SetRetryImportHandler(media, type, delta, start, selector);
            }
            
            // End Status check.
            clearInterval(status); 
        }
        else
        {
            // Show status and returns gTRIGGER.START.
            ShowStatus(delta, start-1, end, type, $prg, $img, $tit, $sub, $msg, msg);
            if (gTRIGGER.START == start) {
                busy = false; // Resume import.
            }
            
            if ($ready.text() == "true") {
                retry++;
            }
        }        
    }, xbmc.timeout); // 1000
}

/*
 * Function:	ShowStatus
 *
 * Created on Aug 19, 2013
 * Updated on Dec 23, 2013
 *
 * Description: Show the import status.
 *
 * In:	delta, start, end, media, $prg, $img, $tit, $sub, $msg, msg
 * Out:	Status
 *
 */
function ShowStatus(delta, start, end, media, $prg, $img, $tit, $sub, $msg, msg)
{   
    $.ajax({
        url: 'jsonmanage.php?action=status&media=' + media + '&mode=import',
        dataType: 'json',
        success: function(json)
        {     
            gTRIGGER.START = Number(json.start);
            gTRIGGER.SLACK = Number(json.slack);
            
            if (json.id > 0 && !gTRIGGER.SLACK)
            {
                var percent = start - (end - delta);
                percent = Math.round(percent/delta * 100);
                
                // Don't show the last step.
                if (start != end) 
                {
                    $prg.progressbar({
                        value : percent       
                    });
                    
                    $msg.html(msg);
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
                $sub.html(json.sub);
            }  
            else if (gTRIGGER.SLACK) 
            {
                $msg.html(cSTATUS.SLACK);
                $img.removeAttr("src").attr("src", "");
                $tit.html(cSTATUS.SKIP + --json.start);
                $sub.html("&nbsp;");
            }
            
        } // End succes.    
    }); // End Ajax. 
}

////////////////////////////////////    Import Seasons Functions    ///////////////////////////////////////

/*
 * Function:	SetTVSeasonImportHandler
 *
 * Created on Oct 19, 2013
 * Updated on Dec 12, 2013
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
        url: 'jsonmanage.php?action=reset&media=' + media + type + '&counter=' + true,
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
                    
                    // Returns gTRIGGER.START and gTRIGGER.END.
                    GetXbmcMediaLimits(media + type);
                    start = gTRIGGER.START;
                    end   = gTRIGGER.END;
                    delta = end - start + 1;
                    
                    if (end > 0 || i > 3)
                    {
                        if (end > 0 && end >= start) 
                        {
                            LogEvent("Information", "Import " + ConvertMedia(type) + " started.");
                            StartTVSeasonsImportWrapper(start, end, delta);
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
 * Updated on Dec 12, 2013
 *
 * Description: Control and Import the media transfered from XBMC.
 *
 * In:	start, end, delta
 * Out:	-
 *
 */
function StartTVSeasonsImportWrapper(start, end, delta)
{   
    var retry = 0;
    var $prg  = $("#action_box .progress");
    var $msg  = $("#action_box .message");
    var msg1  = cSTATUS.IMPORT.replace("[dummy]", "Season");
    var msg2  = cSTATUS.PROCESS.replace("[dummy]", "Season");
    
    gTRIGGER.STARTTV = start;

    StartSeasonsImportHandler(start);
    start += 1;
    
    (function setImportTimer() 
    {
        if (gTRIGGER.CANCEL || start > end) // || retry > gTRIGGER.RETRY) 
        {    
            if (start > end) 
            {
                SetStartImportHandler("tvshows", 3, true, 3000);
                setTimeout(function() 
                {
                    $prg.progressbar({value : 100});
                    LogImportCounter("seasons", true);
                }, 2000);
            }
            
            return; // End Import.
        }

        if (start == gTRIGGER.STARTTV) { // Get seasons next TV Show.
            StartSeasonsImportHandler(start);
            start += 1;
        }
        
        setTimeout(setImportTimer, 1500);   
    }()); // End setImportTimer.      
    
    // Check TV Show Seasons status.
    var status = setInterval(function()
    {
        if (gTRIGGER.CANCEL || start > end) // || retry > gTRIGGER.RETRY) 
        {   
            //if (retry > gTRIGGER.RETRY) {
                //SetRetryImportHandler(media, type, start, delta, selector);
            //    alert("Retry Season Wrapper!");
            //}
            
            clearInterval(status); // End Status check.
        }
        else { // Get seasons next TV Show.
            ShowTVShowSeasonsStatus(start, end, delta, $prg, $msg, msg1, msg2);
        }        
    }, 1500);
}

/*
 * Function:	ShowTVShowSeasonsStatus
 *
 * Created on Oct 21, 2013
 * Updated on Nov 21, 2013
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
        url: 'jsonmanage.php?action=status&media=tvshowsseasons&mode=import',
        dataType: 'json',
        success: function(json) 
        {     
            gTRIGGER.STARTTV = json.start;
            
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
 * Updated on Nov 21, 2013
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
        url: 'jsonmanage.php?action=reset&media=seasons' + '&counter=' + false,
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
                    // Returns gTRIGGER.START and gTRIGGER.END.
                    GetXbmcMediaLimits("seasons");
                    start = gTRIGGER.START;
                    end   = gTRIGGER.END;
                    
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
                
            }, 500); // End timer
        } // End Success.  
    }); // End Ajax;
}

/*
 * Function:	StartSeasonsImport
 *
 * Created on Oct 19, 2013
 * Updated on Dec 23, 2013
 *
 * Description: Control and Import the media transfered from XBMC.
 *
 * In:	xbmc, start, end, tvshowid
 *
 */
function StartSeasonsImport(xbmc, start, end, tvshowid)
{
    var retry  = 0;
    var busy   = true;
    var $ready = $("#ready");
    
    var $img = $("#action_thumb img");
    var $tit = $("#action_title");
    var $sub = $("#action_sub");
     
    // Import media process.
    ImportMedia(xbmc, "seasons", -1, start, tvshowid);
    start += 1;
    
    (function setImportTimer() 
    {
        if (gTRIGGER.CANCEL || start >= end || retry > 2 * gTRIGGER.RETRY) 
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
                busy = true; // Pause import.
                ImportMedia(xbmc, "seasons", -1, start, tvshowid);       
                start += 1;
                retry  = 0; // Reset timeout.
            }
        }
        
        setTimeout(setImportTimer, 800);
    }()); // End setImportTimer.   
        
    // Check seasons status.
    var status = setInterval(function()
    {
        if (gTRIGGER.CANCEL || gTRIGGER.START >= end || retry > 2 * gTRIGGER.RETRY)
        {
            if (retry > 2 * gTRIGGER.RETRY) {
                //SetRetryImportHandler(media, type, start, delta, selector);
                //alert("Retry Season");
                console.log("Retry Season Counter: " + retry);
            }    
            clearInterval(status);
        }
        else
        {
            // Show status and returns gTRIGGER.START.
            ShowSeasonsStatus($img, $tit, $sub);
            if (gTRIGGER.START == start) {
                busy = false; // Resume import.
            }
            
            if ($ready.text() == "true") {
                retry++;
            }
        }        
    }, 800);
}

/*
 * Function:	ShowSeasonsStatus
 *
 * Created on Oct 20, 2013
 * Updated on Dec 23, 2013
 *
 * Description: Show the import seasons status.
 *
 * In:	$img, $tit, $sub
 * Out:	Status
 *
 */
function ShowSeasonsStatus($img, $tit, $sub)
{   
    $.ajax({
        url: 'jsonmanage.php?action=status&media=seasons&mode=import',
        dataType: 'json',
        success: function(json) 
        {     
            gTRIGGER.START = json.start;
            
            if (json.id > 0)
            {                    
                // Preload image.
                var img = new Image();
                img.src = json.thumbs + '/'+ json.tvshowid + '_'+ json.season +'.jpg';
                $img.attr('src', img.src);
                                
                // If images not found then show no poster.
                $img.error(function(){
                    $(this).attr('src', 'images/no_poster.jpg');
                });
                    
                $tit.html(json.title);
                $sub.html(json.sub);
            }           
        } // End succes.    
    }); // End Ajax. 
}

//////////////////////////////////   Common Import & Refresh Functions    /////////////////////////////////

/*
 * Function:	LockImport
 *
 * Created on Dec 13, 2013
 * Updated on Dec 13, 2013
 *
 * Description: New imports cannot be started during the lock.
 * 
 * In:	-
 * Out:	Set lock (ImportReady = 0 (false))
 *
 */
function LockImport(callback)
{
    $.ajax({
        url: 'jsonmanage.php?action=import&mode=lock',
        dataType: 'json',
        success: function(json) 
        { 
            if (callback && typeof(callback) === "function") {  
                callback();
            }             
        } // End succes.    
    }); // End Ajax.        
}

/*
 * Function:	LockImport
 *
 * Created on Dec 13, 2013
 * Updated on Dec 13, 2013
 *
 * Description: New imports cannot be started during the lock.
 * 
 * In:	-
 * Out:	Remove lock (ImportReady = 1(true))
 *
 */
function UnlockImport(callback)
{
    $.ajax({
        url: 'jsonmanage.php?action=import&mode=unlock',
        dataType: 'json',
        success: function(json) 
        { 
            if (callback && typeof(callback) === "function") {  
                callback();
            }             
        } // End succes.    
    }); // End Ajax.        
}

/*
 * Function:	SetImportHandler
 *
 * Created on Sep 09, 2013
 * Updated on Dec 24, 2013
 *
 * Description: Set the import handler, show the import popup box with yes/no buttons.
 * 
 * In:	-
 * Out:	Show Import Popup
 *
 */
function SetImportPopupHandler(media)
{
    // Check if there is alread an import running.
    $.ajax({
        url: 'jsonmanage.php?action=import&mode=check',
        dataType: 'json',
        success: function(json) 
        { 
           var title;
           
           if (Number(json.check))
           {
               $("#action_box .message").text(cIMPORT.START.replace("[dummy]", ConvertMedia(media)));
               title = cIMPORT.IMPORT + " " + ConvertMedia(media); 
               
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
           }
           else 
           {
                $("#action_box .message").text(cIMPORT.RUNNING);
                title = cIMPORT.WARNING;
                $("#action_wrapper").hide();
                $("#action_box .progress").hide();

                //$("#action_title").text("");
                $("#action_sub").text("");
                
                $(".yes").hide();
                $(".no").text("Okay");        
           }    
    
           // Show popup.
           ShowPopupBox("#action_box", title);
           SetState("page", "popup"); 
    
        } // End succes.    
    }); // End Ajax.      
}

/*
 * Function:	ImportData
 *
 * Created on Jan 13, 2013
 * Updated on Jan 14, 2014
 *
 * Description: Import data transfered from XBMC.
 *
 * In:	url, counter
 * Out:	Imported media counter
 *
 * Note: Uses global cCONNECT.
 *
 */
function ImportData(url, counter)
{
    var deferred = $.Deferred();
    var $result = $("#transfer");
    var $ready  = $("#ready");
    var iframe  = '<iframe src="' + url + '" onload="IframeReady()"></iframe>';
    var i = 0;
    
    // Reset values.
    $ready.text("false");    
    $result.text("");
    
    // Run transfer data in iframe.
    $result.append(iframe); 
    
    // Generates time-out and runs IframeReady function if onload in the iframe succeeds or fails.
    var _timer = setInterval(function()
    {
        if ($ready.text() == "true" || i > 3)
        {
            if ($ready.text() == "false") 
            {
                IframeReady();
                deferred.reject(); // XBMC is offline.
            }
            clearInterval(_timer);
            deferred.resolve(counter);
        }
        i++;    
        
    }, cCONNECT.timeout); // End _timer.
     
    return deferred.promise();
}

/*
 * Function:	ImportCounter
 *
 * Created on Jul 22, 2013
 * Updated on Jan 12, 2014
 *
 * Description: Import the media counter transfered from XBMC.
 *
 * In:	xbmc, media, tvshowid
 * Out:	Imported media counter
 *
 */
function ImportCounter(xbmc, media, tvshowid)
{
    var deferred = $.Deferred();
    var $result = $("#transfer");
    var $ready  = $("#ready");
    var url, iframe;
    var i = 0;
    
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
    var _timer = setInterval(function()
    {
        if ($ready.text() == "true" || i > 3)
        {
            if ($ready.text() == "false") {
                IframeReady();
            }
            clearInterval(_timer);
            deferred.resolve();
        }
        i++;    
        
    }, 1000); // End _timer. 
     
    return deferred.promise();
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
 * Function:	GetTransferUrl
 *
 * Created on Jan 13, 2014
 * Updated on Jan 13, 2014
 *
 * Description: Get first of the part transfer url.
 * 
 * In:	xbmc
 * Out:	url
 *
 */
function GetTransferUrl(xbmc)
{
    var url = "http://" + xbmc.connection;
    if (xbmc.port) {
        url = "http://" + xbmc.connection + ":" + xbmc.port;
    }
    
    return url;
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
 * Updated on Jan 12, 2014
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
    gTRIGGER.READY  = false;
    
    // Initialize status popup box.
    $("#action_box .message").html(cSTATUS.CONNECT);
    
    $(".yes").hide();
    $(".no").toggleClass("no cancel");
    $(".retry").toggleClass("retry cancel");  
    
    $(".cancel").html("Cancel");    
}

/*
 * Function:	ResetImportBox
 *
 * Created on Oct 17, 2013
 * Updated on Oct 31, 2013
 *
 * Description: Reset the import box values.
 * 
 * In:	type
 * Out:	-
 *
 */
function ResetImportBox(type)
{
    var msg = cSTATUS.WAIT;
    
    if (type == "episodes") 
    {
        $("#action_thumb").css("margin-left", "-125px");
        $("#action_thumb").width(220);
        $("#action_thumb img").width(220);
    }
    /*else {
        $("#action_title").html("&nbsp;");
    }*/
    
    $("#action_title").html("&nbsp;");
    $("#action_thumb img").removeAttr("src").attr("src", "");
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
 * Updated on Oct 31, 2013
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
       }, 3000); // 1000      
    }
    else {
        $(".cancel").html("Finish");
    } 
}

/*
 * Function:	SetImportCancelHandler
 *
 * Created on May 09, 2013
 * Updated on Jan 11, 2014
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
    var type = $popup.find(".message").text().split(" ")[1].replace(/[.]+/, "s");
    if (type != "Movies" && type != "Sets" && type != "TV Shows" && type != "Seasons" && type != "Episodes" && type != "Albums") {
        type = GetState("media");
    }
        
    // Reset import values.
    gTRIGGER.CANCEL = true;

    $("#action_box .progress").progressbar({
        value : 0       
    });
    
    if ($popup.find(".cancel").text() == "Cancel") {
        LogImportCounter(type, false);
    }
    
    if ($popup.find(".no").text() != "No")
    {    
        // Unlock import.
        UnlockImport(function() {
            if (media != "system") {
                window.location='index.php?media=' + media;              
            }                           
        });        
    }
}

/*
 * Function:	DisplayStatusMessage
 *
 * Created on May 17, 2013
 * Updated on Oct 31, 2013
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
 * Updated on Nov 21, 2013
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
        url: 'jsonmanage.php?action=counter&media=' + media,
        async: false,
        dataType: 'json',
        success: function(json) 
        {           
            var counter = Number(json.import);
            var name    = ConvertMedia(media);
            var msg;
            
            if (finish) 
            {
                msg = cSTATUS.FINISH.replace("[dummy]", counter + " " + name); 
                $("#action_box .message").html(msg);
                LogEvent("Information", msg); 
            }
            else if (counter > 0 ){
                LogEvent("Warning", counter + " " + name + " imported. The import was canceled!");
            }
            else {
                LogEvent("Warning", "The " + name + " import was canceled!");
            }
        } // End Success.
    }); // End Ajax;   
}