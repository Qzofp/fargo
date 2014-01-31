/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.4
 *
 * File:    fargo.private.refresh.js
 *
 * Created on Jul 14, 2013
 * Updated on Jan 31, 2014
 *
 * Description: Fargo's jQuery and Javascript functions page for the XBMC update and refresh (import).
 *
 */

//////////////////////////////////////////    Main Functions    ///////////////////////////////////////////

/*
 * Function:	PrepareRefreshHandler
 *
 * Created on Nov 23, 2013
 * Updated on Jan 31, 2014
 *
 * Description: Prepare the refresh handler, show the refresh popup box and start the refresh.
 * 
 * In:	type, $popup
 * Out:	title
 *
 */
function PrepareRefreshHandler(type, $popup) // May be not necessary and can be replaced with StartRefreshOnlineHandler!
{
    switch(type)
    {
        case "titles"   : StartRefreshOnlineHandler("movies", "movies", $popup);
                          //SetStartRefreshHandler("movies", id, xbmcid, -1);
                          break;
                          
        case "sets"     : SetStartRefreshHandler("sets", id, xbmcid, -1);
                          break;                             
                          
        case "movieset" : SetStartRefreshHandler("movies", id, xbmcid, -1);
                          break;
                      
        case "tvtitles" : SetStartRefreshHandler("tvshows", id, xbmcid, -1);
                          break;                      
                      
        case "series"   : var xbmc = xbmcid.split("_")[1];
                          if (xbmc > 0) {
                             xbmc--;
                          }
                          ConvertAndStartSeriesRefresh(xbmcid, xbmc, xbmcid.split("_")[0]);
                          break;  

        case "seasons"  : var xbmc = xbmcid.split("_")[1];
                          if (xbmc > 0) {
                             xbmc--;
                          }
                          SetStartRefreshHandler("seasons", id.split("_")[0], xbmc, xbmcid.split("_")[0]);
                          break;          
        
        case "episodes" : SetStartRefreshHandler("episodes", id, xbmcid, -1);
                          break;           
        
        case "albums"   : SetStartRefreshHandler("music", id, xbmcid, -1);
                          break;                      
    }
}

/*
 * Function:	StartRefreshOnlineHandler
 *
 * Created on Jan 31, 2014
 * Updated on Jan 31, 2014
 *
 * Description:  Check if XBMC is online handler for refresh media.
 * 
 * In:	media, type, $popup
 * Out:	Globals cCONNECT, gTRIGGER.START and gTRIGGER.END
 *
 * Note : Init globals cCONNECT, gTRIGGER.START and gTRIGGER.END.
 *
 */
function StartRefreshOnlineHandler(media, type, $popup)
{   
    InitImportBox();
    
    // Returns cCONNECT, gTRIGGER.START and gTRIGGER.END.
    var start = StartOnlineCheck(type);
    start.done (function() {
    
        $("#action_box .message").html(cSTATUS.ONLINE);
        StartRefreshHandler(media, type, $popup);
        
    }).fail (function() {
        ShowOffline(true); 
    }); // End Start.    
}

/*
 * Function:	StartRefreshHandler
 *
 * Created on Sep 14, 2013
 * Updated on Jan 31, 2014
 *
 * Description: Set the refresh handler and start the refresh.
 * 
 * In:	media, type, $popup
 * Out:	-
 *
 */
function StartRefreshHandler(media, type, $popup)
{
    
    var $prg = $("#action_box .progress");
    var $img = $("#action_thumb img");
    var $tit = $("#action_title");
    var $sub = $("#action_sub");
    var $msg = $("#action_box .message");
    
    var fargoid = $popup.find(".id").text();
    var xbmcid  = $popup.find(".xbmcid").text();
    //var title   = $popup.find("#action_title").text();
    
    console.log("1. Start Refresh Handler " + fargoid + " " + xbmcid); // debug
    //console.log(title);
    //$msg.html(cSTATUS.WAIT);
    
    setTimeout(function() {
    
        //LogEvent("Information", "Refresh " + ConvertMedia(media) + " started.");
            
        var start = StartRefresh(type, fargoid, xbmcid);
        start.progress(function(i, id) {
            //ShowImportProgress($msg, $prg, $img, $tit, $sub, type, i, id, delta);
            console.log("Counter: " + i + " " + id); // debug.
        });
            
        start.done (function() {
            if (!gTRIGGER.CANCEL) 
            {
                setTimeout(function() {
                   ShowRefreshFinished(media);
                }, gCONNECT.TIMEOUT);
            }
        }).fail (function() {
            ShowOffline(true); 
        });
         
    }, gCONNECT.TIMEOUT); // End setTimeout.
}

/*
 * Function:	StartRefresh
 *
 * Created on Sep 14, 2013
 * Updated on Jan 31, 2013
 *
 * Description: Control and Refresh the media transfered from XBMC.
 *
 * In:	type, fargoid, xbmcid
 * Out:	Refreshed media
 *
 */
function StartRefresh(type, fargoid, xbmcid)
{
    var deferred = $.Deferred(); 
    var url, ready;
    var start = 0;
    var retry = 0;
        
    // Get status (returns gMEDIA and gTRIGGER.STATUS)
    GetMediaStatus(type, fargoid, xbmcid);    
    
    // Check media status.
    var status = setInterval(function()
    {     
        if (gTRIGGER.CANCEL || retry > gTRIGGER.RETRY)
        {
            deferred.notify(start, xbmcid);// Show status.
            deferred.resolve(); // End Import.

            if (retry > gTRIGGER.RETRY) 
            {
                gTRIGGER.CANCEL = true;
                ShowOffline(false);
            }
            
            // End status check.
            clearInterval(status); 
        }
        else
        {           
            // Get status (returns gMEDIA and gTRIGGER.STATUS).
            GetMediaStatus(type, fargoid, xbmcid);            
            switch (gTRIGGER.STATUS)
            {
                case -999 : // Error.
                            break;
                            
                case -1   : // Wait
                            break;
                            
                case 1    : // Match, refresh media.
                            break;
                            
                case 2    : // Match title but not fargoid.
                            break;
                            
                case 3    : // Title doesn't match, use id to get media.
                            // Not implemented yet... 
                            break;
            }
                
            if (gMEDIA.TITLE) {            
                deferred.notify(start, xbmcid);// Show status.
            }
            
            retry++;
        }
    }, gCONNECT.TIMEOUT);    
    
    // Refresh media.
    (function setImportTimer() 
    {
        if (gTRIGGER.CANCEL) {              
            return; // End Refresh.
        } 

        
        if (gMEDIA.XBMCID != xbmcid)
        {
            xbmcid = gMEDIA.XBMCID;
               
            url = GetTransferUrl() + "/fargo/transfer.html?action=" + type + "&xbmcid=" + gMEDIA.XBMCID + "&fargoid=-1" + "&key=" + gCONNECT.KEY;
            
            ready = ImportData(url, 0);
            ready.done(function() {
                //start++;
                retry = 0;
            }).fail(function() {
                console.log("Failure..."); //debug
                gTRIGGER.CANCEL = true;
                deferred.reject();
            }); // End Ready.
        }
               
        // Timeout is set in the Fargo system screen.
        setTimeout(setImportTimer, gCONNECT.TIMEOUT);
    }()); // End setImportTimer. 
    
    return deferred.promise();    
}








/*
 * Function:	ConvertAndStartSeriesRefresh
 *
 * Created on Dec 10, 2013
 * Updated on Dec 10, 2013
 *
 * Description: Convert TV show id's to season id's and refresh serie (season 1).
 * 
 * In:	xbmcid, tvshowid
 * Out:	-
 *
 */
function ConvertAndStartSeriesRefresh(id, start, tvshowid) // Obsolete?
{
    $.ajax({
        url: 'jsonmanage.php?action=convert&id=' + id,
        async: false,
        dataType: 'json',
        success: function(json)
        {
            //alert(json.id + " " + start + " " + tvshowid);
            
            SetStartRefreshHandler("seasons", json.id, start, tvshowid);
        } // End Success.        
    }); // End Ajax;      
}

/*
 * Function:	SetStartRefreshHandler
 *
 * Created on Sep 14, 2013
 * Updated on Dec 02, 2013
 *
 * Description: Set the refresh handler, show the refresh popup box and start the refresh.
 * 
 * In:	media, id, xbmcid, tvshowid
 * Out:	-
 *
 */
function SetStartRefreshHandler_old(media, id, xbmcid, tvshowid)  // Obsolete
{
    InitImportBox();
    
    // Reset status, get xbmc connection (url) and port.
    $.ajax({
        url: 'jsonmanage.php?action=reset&media=' + media + '&counter=false',
        async: false,
        dataType: 'json',
        success: function(json)
        {
            var timer, i = 0;
            var online;
            
            // Check if XBMC is online and transfer XBMC media counter (total).
            ImportCounter(json, media, tvshowid);
            
            // Check if XBMC is online and start refresh.
            timer = setInterval(function()
            {
                // Check if iframe from ImportCounter finished loading.
                if ($("#ready").text() == "true")
                {
                    // Returns gTRIGGER.END;
                    GetXbmcMediaLimits(media);
                    online = gTRIGGER.END; // If value > 0 then XBMC is online.
                    
                    if (online > 0 || i > 3)
                    {
                        if (online > 0) {
                            StartRefresh(json, media, id, xbmcid, tvshowid);
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
 * Updated on Dec 24, 2013
 *
 * Description: Control and Refresh the media transfered from XBMC.
 *
 * In:	xbmc, media, id, xmbcid, tvshowid
 * Out:	Refreshed media
 *
 */
function StartRefresh_old(xbmc, media, id, xbmcid, tvshowid) // Obsolete
{
    var retry   = 0;
    var delay   = 0;
    var percent = 15;
    var factor  = 1.5;
    var $ready  = $("#ready");
    
    // Import media process.
    $("#action_box .message").html(cSTATUS.ONLINE);  
    ImportMedia(xbmc, media, id, xbmcid, tvshowid);
    LogEvent("Information", "Refresh " + ConvertMedia(media) + " started.");
            
    // Check status.
    var status = setInterval(function()
    {
        if (gTRIGGER.CANCEL || delay >= gTRIGGER.RETRY || retry > gTRIGGER.RETRY)
        {
            if (gTRIGGER.CANCEL) {
                LogEvent("Warning", "Refresh " + ConvertMedia(media) + " canceled!");
            }
            else if (retry > gTRIGGER.RETRY) {
                ShowOffline();
            }
            else {
                ShowRefreshFinished(media);
            }             
            
            clearInterval(status);
        }
        else
        {
            // Show status and returns gTRIGGER.READY.
            if (tvshowid < 0) {
                ShowRefreshStatus(media, xbmcid, percent);
            }    
            else {
                ShowRefreshStatus(media, id, percent);
            }    
            percent = 100 - 100/factor;
            
            // Wait until thumb is ready.
            if (gTRIGGER.READY) {
                delay += 2;
                factor *= 2;
            }
            else if ($ready.text() == "true") {
                retry++;
            }
        }        
    }, xbmc.timeout); // 800   
}

/*
 * Function:	ShowRefreshStatus
 *
 * Created on Sep 14, 2013
 * Updated on Dec 24, 2013
 *
 * Description: Show the refresh status.
 *
 * In:	media
 * Out:	Status
 *
 */
function ShowRefreshStatus(media, id, percent) // Obsolete
{   
    $.ajax({
        url: 'jsonmanage.php?action=status&media=' + media + '&mode=refresh' + '&id=' + id,
        dataType: 'json',
        success: function(json) 
        {     
            gTRIGGER.READY = Number(json.ready);
            
            $("#action_box .message").html(cSTATUS.REFRESH);   
                      
            // Preload image.
            var img = new Image();
            img.src = json.thumbs + '/'+ json.id +'.jpg?v=' + json.refresh;
            $("#action_thumb img").attr('src', img.src);
                                
            // If images not found then show no poster.
            $("#action_thumb img").error(function(){
                $(this).attr('src', 'images/no_poster.jpg');
            });
                    
            $("#action_title").html(json.title);
            $("#action_sub").html(json.sub);
            
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
