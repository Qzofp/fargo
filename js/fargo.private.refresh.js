/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.4
 *
 * File:    fargo.private.refresh.js
 *
 * Created on Jul 14, 2013
 * Updated on Dec 24, 2013
 *
 * Description: Fargo's jQuery and Javascript functions page for the XBMC update and refresh (import).
 *
 */

//////////////////////////////////////////    Main Functions    ///////////////////////////////////////////

/*
 * Function:	PrepareRefreshHandler
 *
 * Created on Nov 23, 2013
 * Updated on Dec 10, 2013
 *
 * Description: Prepare the refresh handler, show the refresh popup box and start the refresh.
 * 
 * In:	type, id, xbmcid
 * Out:	title
 *
 */
function PrepareRefreshHandler(type, id, xbmcid)
{
    switch(type)
    {
        case "titles"   : SetStartRefreshHandler("movies", id, xbmcid, -1);
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
function ConvertAndStartSeriesRefresh(id, start, tvshowid)
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
function SetStartRefreshHandler(media, id, xbmcid, tvshowid)
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
function StartRefresh(xbmc, media, id, xbmcid, tvshowid)
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
function ShowRefreshStatus(media, id, percent)
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
