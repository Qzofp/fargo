/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.3
 *
 * File:    fargo.private.refresh.js
 *
 * Created on Jul 14, 2013
 * Updated on Dec 02, 2013
 *
 * Description: Fargo's jQuery and Javascript functions page for the XBMC update and refresh (import).
 *
 */

//////////////////////////////////////////    Main Functions    ///////////////////////////////////////////

/*
 * Function:	PrepareRefreshHandler
 *
 * Created on Nov 23, 2013
 * Updated on Dec 02, 2013
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
                      
        case "series"   : //SetStartRefreshHandler("tvshows", id, xbmcid);
                          break;  

        case "seasons"  : //var start    = --xbmcid.split("_")[1];
                          //var fargoid  = id.split("_")[0];
                          //var tvshowid = xbmcid.split("_")[0];
                          //alert(start + " " + fargoid + " " + tvshowid);
                          
                          SetStartRefreshHandler("seasons", id.split("_")[0], --xbmcid.split("_")[1], xbmcid.split("_")[0]);
                          break;          
        
        case "episodes" : SetStartRefreshHandler("episodes", id, xbmcid, -1);
                          break;           
        
        case "albums"   : SetStartRefreshHandler("music", id, xbmcid, -1);
                          break;                      
    }
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
 * Out:	title
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
 * Updated on Dec 02, 2013
 *
 * Description: Control and Refresh the media transfered from XBMC.
 *
 * In:	xbmc, media, id, xmbcid, tvshowid
 * Out:	Refreshed media
 *
 */
function StartRefresh(xbmc, media, id, xbmcid, tvshowid)
{
    var timeout = 0;
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
        if (gTRIGGER.CANCEL || delay >= xbmc.timeout/1000 || timeout > xbmc.timeout/1000)
        {
            if (gTRIGGER.CANCEL) {
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
                timeout++;
            }
        }        
    }, 800);   
}

/*
 * Function:	ShowRefreshStatus
 *
 * Created on Sep 14, 2013
 * Updated on Nov 21, 2013
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
