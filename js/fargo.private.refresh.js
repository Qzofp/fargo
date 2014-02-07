/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.4
 *
 * File:    fargo.private.refresh.js
 *
 * Created on Jul 14, 2013
 * Updated on Feb 07, 2014
 *
 * Description: Fargo's jQuery and Javascript functions page for the XBMC update and refresh (import).
 *
 */

//////////////////////////////////////////    Main Functions    ///////////////////////////////////////////

/*
 * Function:	PrepareRefreshHandler
 *
 * Created on Nov 23, 2013
 * Updated on Feb 07, 2014
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
                          
        case "sets"     : StartRefreshOnlineHandler("sets", "sets", $popup);
                          //SetStartRefreshHandler("sets", id, xbmcid, -1);
                          break;                  
                          
        case "movieset" : StartRefreshOnlineHandler("movies", "movies", $popup);
                          //SetStartRefreshHandler("movies", id, xbmcid, -1);
                          break;
                      
        case "tvtitles" : StartRefreshOnlineHandler("tvshows", "tvshows", $popup);
                          //SetStartRefreshHandler("tvshows", id, xbmcid, -1);
                          break;                      
                      
        case "series"   : StartRefreshOnlineHandler("tvshows", "seasons", $popup);
                          /*var xbmc = xbmcid.split("_")[1];
                          if (xbmc > 0) {
                             xbmc--;
                          }
                          ConvertAndStartSeriesRefresh(xbmcid, xbmc, xbmcid.split("_")[0]);*/
                          break;  

        case "seasons"  : StartRefreshOnlineHandler("tvshows", "seasons", $popup);
                          /*var xbmc = xbmcid.split("_")[1];
                          if (xbmc > 0) {
                             xbmc--;
                          }
                          SetStartRefreshHandler("seasons", id.split("_")[0], xbmc, xbmcid.split("_")[0]);*/
                          break;          
        
        case "episodes" : StartRefreshOnlineHandler("episodes", "episodes", $popup);
                          //SetStartRefreshHandler("episodes", id, xbmcid, -1);
                          break;           
        
        case "albums"   : StartRefreshOnlineHandler("music", "music", $popup);
                          //SetStartRefreshHandler("music", id, xbmcid, -1);
                          break;                      
    }
}

/*
 * Function:	StartRefreshOnlineHandler
 *
 * Created on Jan 31, 2014
 * Updated on Feb 05, 2014
 *
 * Description:  Check if XBMC is online handler for refresh media.
 * 
 * In:	type, $popup
 * Out:	Globals cCONNECT, gTRIGGER.START and gTRIGGER.END
 *
 * Note : Init globals cCONNECT, gTRIGGER.START and gTRIGGER.END.
 *
 */
function StartRefreshOnlineHandler(media, type, $popup)
{   
    InitImportBox();
    
    // Returns cCONNECT, gTRIGGER.START and gTRIGGER.END.
    var start = StartOnlineCheck(media);
    start.done (function() {
    
        $("#action_box .message").html(cSTATUS.ONLINE);
        setTimeout(function() {
            StartRefreshHandler(type, $popup);       
        }, gCONNECT.TIMEOUT);
        
    }).fail (function() {
        ShowOffline(true); 
    }); // End Start.    
}

/*
 * Function:	StartRefreshHandler
 *
 * Created on Sep 14, 2013
 * Updated on Feb 05, 2014
 *
 * Description: Set the refresh handler and start the refresh.
 * 
 * In:	type, $popup
 * Out:	-
 *
 */
function StartRefreshHandler(type, $popup)
{  
    var $prg = $("#action_box .progress");
    var $img = $("#action_thumb img");
    var $tit = $("#action_title");
    var $sub = $("#action_sub");
    var $msg = $("#action_box .message");
    
    var fargoid = $popup.find(".id").text();
    var xbmcid  = $popup.find(".xbmcid").text();
    var title   = $popup.find("#action_title").text();
    
    LogEvent("Information", "Refresh '" + title + "' started.");  
    $msg.html(cSTATUS.WAIT);
    
    setTimeout(function() {

        var start = StartRefresh(type, fargoid, xbmcid);
        start.progress(function(i, id) {
            ShowRefreshProgress($msg, $prg, $img, $tit, $sub, type, i, id);
        });
            
        start.done (function() {
            if (!gTRIGGER.CANCEL) 
            {
                setTimeout(function() {
                   ShowRefreshFinished(type);
                }, gCONNECT.TIMEOUT);
            }
        }).fail (function(s) {
            ShowOffline(s); 
        });
         
    }, gCONNECT.TIMEOUT); // End setTimeout.
}

/*
 * Function:	StartRefresh
 *
 * Created on Sep 14, 2013
 * Updated on Feb 07, 2014
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
    var enter   = 1;
    var i       = 0;
    var retry   = 1;
        
    // Get status (returns gMEDIA and gTRIGGER.STATUS)
    GetMediaStatus(type, fargoid, xbmcid); 
    
    // Check media status.
    var status = setInterval(function()
    {     
        if (gTRIGGER.CANCEL || retry > gTRIGGER.RETRY)
        {        
            if (retry > gTRIGGER.RETRY) 
            {
                gTRIGGER.CANCEL = true;
                //console.log("Retry..."); // Debug.
                deferred.reject(false); // Failure.
            }
            
            // End status check.
            clearInterval(status); 
        }
        else
        {           
            // Get status (returns gMEDIA and gTRIGGER.STATUS).
            GetMediaStatus(type, fargoid, xbmcid);
    
            // Show status.
            deferred.notify(retry, xbmcid);

            switch (Number(gTRIGGER.STATUS))
            {
                case -999 : // Error.
                            //console.log("Error!");  // Debug.
                            deferred.reject(false); // Failure.
                            gTRIGGER.CANCEL = true;
                            break;
                            
                case -200 : // Not found.
                            //console.log("Not Found!");  // Debug.
                            deferred.resolve(); // Not found.
                            gTRIGGER.CANCEL = true;                    
                            break;
                            
                case -100 : // Refresh ready
                            //console.log("Refresh ready");  // Debug.
                            deferred.resolve(); // Refresh ready.
                            gTRIGGER.CANCEL = true;
                            break;                              
                            
                case -1   : // Wait
                            i++;
                            //console.log("Waiting... " + i);  // Debug.
                            break;
                
                case 0    : // No match on title, try on id.
                            i++;
                            //console.log("Try on id refresh. " + i);  // Debug.
                            break;
                            
                default   : i++;
                            xbmcid = gTRIGGER.STATUS;
                            //console.log("Match start refresh. " + i + " " + xbmcid);  // Debug.
                            break;
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
        
        if (enter <= i)
        {
            if (enter == 1) 
            {
                enter = 2;
                url = GetTransferUrl() + "/fargo/transfer.html?action=search&media=" + type + "&xbmcid=" + xbmcid 
                                       + "&title=" + gMEDIA.TITLE + "&key=" + gCONNECT.KEY;
            }
            else if (enter == 2)
            {
                enter = 999;    
                url = GetTransferUrl() + "/fargo/transfer.html?action=" + type + "&xbmcid=" + xbmcid + "&fargoid=" + 
                                       + fargoid + "&key=" + gCONNECT.KEY;                
            }
            
            ready = ImportData(url, 0);
            ready.done(function() {
                
            }).fail(function() {
                //console.log("Failure..."); //debug
                gTRIGGER.CANCEL = true;
                deferred.reject(true);  // Failure.
            }); // End Ready.
        }
               
        // Timeout is set in the Fargo system screen.
        setTimeout(setImportTimer, gCONNECT.TIMEOUT);
    }()); // End setImportTimer. 
    
    return deferred.promise();    
}

/*
 * Function:	ShowRefreshProgress
 *
 * Created on Feb 01, 2014
 * Updated on Feb 05, 2014
 *
 * Description: Show the import progress.
 * 
 * In:	$msg, $prg, $img, $tit, $sub, type, i, id
 * Out:	-
 *
 */
function ShowRefreshProgress($msg, $prg, $img, $tit, $sub, type, i, id)
{   

    var percent;
    if (i < 4) {
        percent = 20 * i;     
    }
    if (i == 4) {
        percent = 60 + 10 * (i - 3);
    }    
    if (i >= 5 && i <= 6) {
        percent = 70 + 5 * (i - 4);
    }
    if (i > 6) {
        percent = 80 + 2 * (i - 6);
    }
    
    $prg.progressbar({
        value : percent     
    });
    
    if ($msg.text() == cSTATUS.WAIT) {    
        $msg.html(cSTATUS.SEARCH.replace("[dummy]", ConvertMediaToSingular(type)));
    }
    else 
    {    
        if (Number(gTRIGGER.STATUS) >= 0)
        {
            $msg.html(cSTATUS.REFRESH.replace("[dummy]", ConvertMediaToSingular(type)));
            $img.removeAttr("src").attr("src", "");        
        }
        else 
        {    
            // Preload image.
            var img = new Image();      
            img.src = gMEDIA.THUMBS + '/'+ id +'.jpg';
            $img.attr('src', img.src);
                                
            // If images not found then show no poster.
            $img.error(function(){
                $(this).attr('src', 'images/no_poster.jpg');
            });
                    
            $tit.html(gMEDIA.TITLE);
            $sub.html(gMEDIA.SUB);
        }
    }
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
/*function ConvertAndStartSeriesRefresh(id, start, tvshowid) // Obsolete?
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
}*/

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
/*function SetStartRefreshHandler_old(media, id, xbmcid, tvshowid)  // Obsolete
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
}*/

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
/*function StartRefresh_old(xbmc, media, id, xbmcid, tvshowid) // Obsolete
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
}*/

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
/*function ShowRefreshStatus(media, id, percent) // Obsolete
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
}*/

/*
 * Function:	ShowRefreshFinished
 *
 * Created on Sep 14, 2013
 * Updated on Feb 03, 2014
 *
 * Description: Show refresh finished message and add to log event.
 * 
 * In:	-
 * Out:	-
 *
 * Note: Uses globals gMEDIA and gTRIGGER.STATUS 
 *
 */
function ShowRefreshFinished(type)
{   
    var msg;
    
    if (Number(gTRIGGER.STATUS) != -200) {
        msg = cSTATUS.READY.replace("[dummy]", cIMPORT.REFRESH);
        LogEvent("Information", "Refresh '" + gMEDIA.TITLE + "' finished.");  
    }
    else 
    {
        msg = cSTATUS.NOMATCH.replace("[dummy]", ConvertMediaToSingular(type));
        LogEvent("Warning", "Refresh '" + gMEDIA.TITLE + "' failed!");
    }
    
    $("#action_box .message").html(msg);             
    $("#action_box .progress").progressbar({
        value:100
    });
    
    $(".cancel").html("Finish");
}