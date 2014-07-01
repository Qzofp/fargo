/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.6
 *
 * File:    fargo.private.refresh.js
 *
 * Created on Jul 14, 2013
 * Updated on Jun 30, 2014
 *
 * Description: Fargo's jQuery and Javascript functions page for the XBMC update and refresh (import).
 *
 */

//////////////////////////////////////////    Main Functions    ///////////////////////////////////////////

/*
 * Function:	PrepareRefreshHandler
 *
 * Created on Nov 23, 2013
 * Updated on Jun 30, 2014
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
                          break;
                          
        case "sets"     : StartRefreshOnlineHandler("sets", "sets", $popup);
                          break;                  
                          
        case "movieset" : StartRefreshOnlineHandler("movies", "movies", $popup);
                          break;
                      
        case "tvtitles" : StartRefreshOnlineHandler("tvshows", "tvshows", $popup);
                          break;                      
                      
        case "series"   : StartRefreshOnlineHandler("tvshows", "seasons", $popup);
                          break;  

        case "seasons"  : StartRefreshOnlineHandler("tvshows", "seasons", $popup);
                          break;          
        
        case "episodes" : StartRefreshOnlineHandler("episodes", "episodes", $popup);
                          break;           
        
        case "albums"   : StartRefreshOnlineHandler("albums", "albums", $popup);
                          break;
                      
        case "songs"    : StartRefreshOnlineHandler("songs", "songs", $popup);
                          break;
                          
        case "tracks"   : StartRefreshOnlineHandler("songs", "songs", $popup);
                          break;        
    }
}

/*
 * Function:	StartRefreshOnlineHandler
 *
 * Created on Jan 31, 2014
 * Updated on Feb 19, 2014
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
        ShowOffline(ShowOffline); 
    }); // End Start.    
}

/*
 * Function:	StartRefreshHandler
 *
 * Created on Sep 14, 2013
 * Updated on Feb 19, 2014
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
    
    LogEvent('Information', 'Refresh "' + title + '" started.');
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
        }).fail (function(msg) {
            ShowOffline(msg);
        });
         
    }, gCONNECT.TIMEOUT); // End setTimeout.
}

/*
 * Function:	StartRefresh
 *
 * Created on Sep 14, 2013
 * Updated on Feb 18, 2014
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
                deferred.reject(cSTATUS.LOST); // Failure.
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
                case cTRANSFER.ERROR      
                        : deferred.reject(cSTATUS.LOST); // Failure.
                          gTRIGGER.CANCEL = true;
                          break;
                            
                case cTRANSFER.NOTFOUND 
                        : deferred.resolve(); // Not found.
                          gTRIGGER.CANCEL = true;                    
                          break;
                            
                case cTRANSFER.READY
                        : deferred.resolve(); // Refresh ready.
                          gTRIGGER.CANCEL = true;
                          break;                              
                            
                case cTRANSFER.WAIT   
                        : i++;
                          break;
                
                case cTRANSFER.NOMATCH
                        : i++;
                          break;
                            
                default : i++;
                          xbmcid = gTRIGGER.STATUS;
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
                url = GetTransferUrl() + "transfer.html?action=search&media=" + type + "&xbmcid=" + xbmcid 
                                       + "&title=" + gMEDIA.TITLE + "&key=" + gCONNECT.KEY;
            }
            else if (enter == 2)
            {
                enter = 999;    
                url = GetTransferUrl() + "transfer.html?action=" + type + "&xbmcid=" + xbmcid + "&fargoid=" + 
                                       + fargoid + "&key=" + gCONNECT.KEY;                
            }
            
            ready = ImportData(url, 0);
            ready.done(function() {
                
            }).fail(function() {
                //console.log("Failure..."); //debug
                gTRIGGER.CANCEL = true;
                deferred.reject(cSTATUS.OFFLINE);  // Failure.
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
 * Updated on Jun 02, 2014
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
            var version = 10000 + Math.floor(Math.random() * 9999);
            img.src = gMEDIA.THUMBS + '/'+ id +'.jpg?v=' + version;
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
 * Function:	ShowRefreshFinished
 *
 * Created on Sep 14, 2013
 * Updated on Feb 09, 2014
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
        LogEvent('Information', 'Refresh "' + gMEDIA.TITLE + '" finished.');  
    }
    else 
    {
        msg = cSTATUS.NOMATCH.replace("[dummy]", ConvertMediaToSingular(type));
        LogEvent('Warning', 'Refresh "' + gMEDIA.TITLE + '" failed!');
    }
    
    $("#action_box .message").html(msg);             
    $("#action_box .progress").progressbar({
        value:100
    });
    
    $(".cancel").html("Finish");
}