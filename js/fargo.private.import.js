/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.6
 *
 * File:    fargo.private.import.js
 *
 * Created on Jul 14, 2013
 * Updated on Jul 07, 2014
 *
 * Description: Fargo's jQuery and Javascript functions page for the XBMC media import.
 *
 */

//////////////////////////////////////////    Main Functions    ///////////////////////////////////////////

/*
 * Function:	SetStartImportHandler
 *
 * Created on Jul 14, 2013
 * Updated on Jan 19, 2014
 *
 * Description: Set the start import handler.
 * 
 * In:	media, type, step, retry
 * Out:	-
 *
 */
function SetStartImportHandler(media, step, retry)
{
    switch (media)
    {
        case "movies"  : SetStartMoviesImportHandler(step, retry);
                         break;                      
        
        case "tvshows" : SetStartTVShowsImportHandler(step, retry);
                         break;
                         
        case "music"   : SetStartMusicImportHandler(step, retry);
                         break;
    }
}

/////////////////////////////////////////    Import Functions    //////////////////////////////////////////

/*
 * Function:	SetStartMoviesImportHandler
 *
 * Created on Jan 12, 2014
 * Updated on May 12, 2014
 *
 * Description:  Start the movies import handler.
 * 
 * In:	type, retry
 * Out:	-
 *
 */
function SetStartMoviesImportHandler(step, retry)
{    
    switch (step)
    {
        case 1 : StartOnlineHandler("movies", "movies", 2, true, retry);
                 break;        
        
        case 2 : // Import Movies meta data.
                 StartMetaImportHandler("movies", "movies", 3);
                 break;
                 
        case 3 : // Import Movies.
                 StartImportHandler("movies", "movies", 4, 1);
                 break;
                           
        case 4 : StartOnlineHandler("movies", "sets", 5, false, retry);
                 break;
                
        case 5 : // Import Sets meta data.
                 StartMetaImportHandler("movies", "sets", 6);
                 break;
                
        case 6 : // Import Movie Sets.
                 StartImportHandler("movies", "sets", 7, 0.6);
                 break;  
        
        case 7 : ShowFinished(true);
                 break;               
    }
}

/*
 * Function:	SetStartTVShowsImportHandler
 *
 * Created on Jan 18, 2014
 * Updated on May 17, 2014
 *
 * Description:  Start the TV Shows import handler.
 * 
 * In:	type, retry
 * Out:	-
 *
 */
function SetStartTVShowsImportHandler(step, retry)
{     
    switch (step)
    {
        case 1 : StartOnlineHandler("tvshows", "tvshows", 2, true, retry);
                 break;
        
        case 2 : // Import TV Shows meta data.
                 StartMetaImportHandler("tvshows", "tvshows", 3);
                 break;
                 
        case 3 : // Import TV Shows.
                 StartImportHandler("tvshows", "tvshows", 4, 1);
                 break;
               
        case 4 : StartOnlineHandler("tvshows", "tvseasons", 5, false, retry);
                 break;
        
        case 5 : // Import Seasons meta data.
                 StartSeasonsMetaImportHandler("tvshows", "seasons", 6);
                 break;
              
        case 6 : // Import Seasons.  
                 StartImportHandler("tvshows", "seasons", 7, 0.6);
                 break;
                
        case 7 : StartOnlineHandler("tvshows", "episodes", 8, false, retry);
                 break;
            
        case 8 : // Import Episodes meta data.
                 StartMetaImportHandler("tvshows", "episodes", 9);
                 break;
                 
        case 9 : // Import Episodes data.
                 StartImportHandler("tvshows", "episodes", 10, 0.6);
                 break;                     
                 
        case 10: ShowFinished(true);
                 break;  
    }
}

/*
 * Function:	SetStartMusicImportHandler
 *
 * Created on Jan 18, 2014
 * Updated on Jun 28, 2014
 *
 * Description:  Start the music import handler.
 * 
 * In:	type, retry
 * Out:	-
 *
 */
function SetStartMusicImportHandler(step, retry)
{
    switch (step)
    {
        case 1 : StartOnlineHandler("music", "albums", 2, true, retry);
                 break;        
        
        case 2 : // Import Music meta data.
                 StartMetaImportHandler("music", "albums", 3);
                 break;
                 
        case 3 : // Import Music.
                 StartImportHandler("music", "albums", 4, 0.6);
                 break;
                 
        case 4 : StartOnlineHandler("music", "songs", 5, false, retry);
                 break;
                
        case 5 : // Import Sets meta data.
                 StartMetaImportHandler("music", "songs", 6);
                 break;
                
        case 6 : // Import Movie Sets.
                 StartImportHandler("music", "songs", 7, 0.6);
                 break;                  
                 
        case 7 : ShowFinished(true);
                 break;
    }
}

/*
 * Function:	StartOnlineHandler
 *
 * Created on Jan 19, 2014
 * Updated on Jun  27, 2014
 *
 * Description:  Check if XBMC is online handler.
 * 
 * In:	media, type, next, online, retry
 * Out:	Globals cCONNECT, gTRIGGER.START and gTRIGGER.END
 *
 * Note : Init globals cCONNECT, gTRIGGER.START and gTRIGGER.END
 *
 */
function StartOnlineHandler(media, type, next, online, retry)
{   
    if (online || retry) {
        InitImportBox();
    }
    
    gTRIGGER.STEP1 = next - 1;
    if (retry) {
        next = gTRIGGER.STEP2;
    }
    
    // Returns gCONNECT, gTRIGGER.START and gTRIGGER.END.
    var start = StartOnlineCheck(type);
    start.done (function() {
        
        if (online || retry) {
            $("#action_box .message").html(cSTATUS.ONLINE);
        }
        
        SetStartImportHandler(media, next, retry);
    }).fail (function() {
        ShowOffline(cSTATUS.OFFLINE);
    }); // End Start.    
}

/*
 * Function:	StartOnlineCheck
 *
 * Created on Jul 14, 2013
 * Updated on Jun 17, 2014
 *
 * Description:  Check if XBMC is online.
 * 
 * In:	type
 * Out:	Globals gCONNECT, gTRIGGER.START and gTRIGGER.END
 *
 * Note : Init globals gCONNECT, gTRIGGER.START and gTRIGGER.END.
 *
 */
function StartOnlineCheck(type)
{
    var deferred = $.Deferred();
    //InitImportBox();
    
    // Reset media status, get xbmc connection (url), port and fargo media counter.
    $.ajax({
        url: 'jsonmanage.php?action=reset&media=' + type,
        async: false,
        dataType: 'json',
        success: function(json)
        {
            gCONNECT.CONNECTION = json.connection;
            gCONNECT.PORT = json.port;
            gCONNECT.TIMEOUT = json.timeout;
            gCONNECT.KEY = json.key;
            gCONNECT.STATUS = json.status;
            
            var url = GetTransferUrl() + "transfer.html?action=counter&media=" + type + "&key=" + gCONNECT.KEY;        
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
 * Function:	StartMetaImportHandler
 *
 * Created on Jan 12, 2014
 * Updated on Feb 24, 2014
 *
 * Description:  Start the meta import handler.
 * 
 * In:	media, type, next
 * Out:	-
 *
 * Note: Uses globals cCONNECT, gTRIGGER.START and gTRIGGER.END
 * 
 */
function StartMetaImportHandler(media, type, next)
{
    var end  = Math.ceil(gTRIGGER.END/gTRIGGER.BULK);
    
    var $prg = $("#action_box .progress");
    var $img = $("#action_thumb img");
    var $tit = $("#action_title");
    var $sub = $("#action_sub");
    
    $prg.progressbar({value : 0 });
    //$img.removeAttr("src").attr("src", "");
    $img.hide();
    $tit.html("&nbsp;");
    $sub.html("&nbsp;");    
    
    gTRIGGER.STEP2 = next - 1;
        
    if (gTRIGGER.END >= 0)
    {
        var meta = StartMetaImport(type, end);            
        meta.progress(function(i) {
            ShowMetaProgress($prg, type, i, end-1);
        }); 
            
        meta.done (function() {
            if (!gTRIGGER.CANCEL) 
            {        
                // Returns gTRIGGER.START and gTRIGGER.END.
                GetXbmcMediaLimits(type);                
                
                SetStartImportHandler(media, next, false); // Continue with the next step.
            }
        }).fail (function(msg) {
            ShowOffline(msg);
        });
    }         
}

/*
 * Function:	StartMetaImport
 *
 * Created on Jan 12, 2014
 * Updated on Jun 17, 2014
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
    
    $.ajax({ // Begin Ajax 1.
        url: 'jsonmanage.php?action=initmeta&type=' + type,
        async: false,
        dataType: 'json',
        success: function(json)
        {      
            var url = GetTransferUrl() + "meta.html?action=" + type + "&counter=" + 0 + "&key=" + gCONNECT.KEY;
            var currentStep = ImportData(url, 0);  
    
            deferred.notify(0);
    
            for(var i = 1; i < end; i++)
            {       
                //console.log("Meta Counter: " + i);
                currentStep = currentStep.pipe(function(j){
                    if (!gTRIGGER.CANCEL) 
                    {
                        deferred.notify(++j);
                        url = GetTransferUrl() + "meta.html?action=" + type + "&counter=" + j + "&key=" + gCONNECT.KEY;
                        return ImportData(url, j);
                    }
                });
            }
            $.when(currentStep).done(function(){
                //console.log("All steps done.");     
                $.ajax({ // Begin Ajax 2.
                    url: 'jsonmanage.php?action=chkmeta&type=' + type,
                    async: false,
                    dataType: 'json',
                    success: function(json)
                    {                  
                        if (json.check) {
                            deferred.resolve();
                        }
                        else {
                            deferred.reject(cSTATUS.METAERR);
                        }     
                    } // End succes.    
                }); // End Ajax 2.              
                
                //deferred.resolve();
            }).fail(function(){
                deferred.reject(cSTATUS.OFFLINE);
            });
 
        } // End succes.    
    }); // End Ajax 1.       
    
    return deferred.promise();
}

/*
 * Function:	ShowMetaProgress
 *
 * Created on Jan 13, 2014
 * Updated on Jan 23, 2014
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
    
    if (i == 1) 
    {
        $("#action_box .message").html(cSTATUS.SEARCH.replace("[dummy]", ConvertMedia(type)));
        
        if (type == "episodes") 
        {
            $("#action_thumb").css("margin-left", "-125px");
            $("#action_thumb").width(220);
            $("#action_thumb img").width(220);
        }
    }
}

/*
 * Function:	StartImportHandler
 *
 * Created on Jan 14, 2014
 * Updated on Jul 02, 2014
 *
 * Description:  Start the media import handler.
 * 
 * In:	media, type, next, factor
 * Out:	-
 *
 * Note: Uses globals cCONNECT, gTRIGGER.START and gTRIGGER.END
 *
 */
function StartImportHandler(media, type, next, factor)
{
    var $prg = $("#action_box .progress");
    var $img = $("#action_thumb img");
    var $tit = $("#action_title");
    var $sub = $("#action_sub");
    var $msg = $("#action_box .message");
    
    var delta = gTRIGGER.END - gTRIGGER.START;   
    
    gTRIGGER.STEP2 = next - 2;
    $prg.progressbar({value : 0 });
    $msg.html(cSTATUS.WAIT);
    
    setTimeout(function() {
    
        if (gTRIGGER.START > gTRIGGER.END) 
        {
           $msg.html(cSTATUS.NOTFOUND.replace("[dummy]", ConvertMedia(type)));
           SetStartImportHandler(media, next, false); // Continue with the next step.
        }
        else 
        {
            LogEvent("Information", "Import " + ConvertMedia(type) + " started.");
            
            var start = StartImport(type, factor);     
            start.progress(function(i) {
                ShowImportProgress($msg, $prg, $img, $tit, $sub, type, i, delta);
            });
            
            start.done (function() {
                if (!gTRIGGER.CANCEL) 
                {
                    setTimeout(function() {
                        LogImportCounter(type, true);
                        SetStartImportHandler(media, next, false);  // Continue with the next step.
                    }, gCONNECT.TIMEOUT);
                }
            }).fail (function(msg) {
                ShowOffline(msg);
            });
        }    
    }, gCONNECT.TIMEOUT); // End setTimeout.
}

/*
 * Function:	StartImport
 *
 * Created on Jan 14, 2014
 * Updated on Jun 27, 2014
 *
 * Description: Control and Import the media data transfered from XBMC.
 *
 * In:	type, factor
 * Out:	-
 *
 * Note: Uses globals cCONNECT, gTRIGGER.START and gTRIGGER.END
 *
 */
function StartImport(type, factor)
{
    var deferred = $.Deferred(); 
    var url, ready;
    var retry = 0;
    var xbmcid = 0;
    var start = gTRIGGER.START;
    var busy = false;
        
    // Get status (returns gMEDIA and gTRIGGER.COUNTER)
    GetMediaStatus(type, start, xbmcid);
    
    // Check media status.
    var status = setInterval(function()
    {     
        if (gTRIGGER.CANCEL || start > gTRIGGER.END || retry > gTRIGGER.RETRY)
        {
            if (start > gTRIGGER.END) 
            {
                GetMediaStatus(type, start, xbmcid);
                deferred.notify(start);// Show status.                
                deferred.resolve(); // End Import.
            }
            
            if (retry > gTRIGGER.RETRY) 
            {
                gTRIGGER.CANCEL = true;
                ShowOffline(cSTATUS.LOST);
            }         
            // End status check.
            clearInterval(status); 
        }
        else
        {
            // Counter confirms that the import succeeded.
            start = gTRIGGER.COUNTER;
            
            // Get status (returns gMEDIA and gTRIGGER.COUNTER).
            GetMediaStatus(type, start, xbmcid);
            deferred.notify(start);// Show status.
            
            switch (Number(gTRIGGER.STATUS))
            {
                case cTRANSFER.ERROR      
                        : deferred.reject(cSTATUS.LOST); // Failure.
                          gTRIGGER.CANCEL = true;
                          break;
                          
                case cTRANSFER.DUPLICATE 
                        : busy = false;
                          break;                          
                            
                case cTRANSFER.NOTFOUND 
                        : console.log("Not found...");
                          deferred.resolve(); // Not found.
                          gTRIGGER.CANCEL = true;                    
                          break;
                            
                case cTRANSFER.READY
                        : busy = false;
                          break;                              
                            
                case cTRANSFER.WAIT   
                        : break;
            }            
            
            retry++;
        }
    }, gCONNECT.TIMEOUT * factor);
    
    // Import media.
    (function setImportTimer() 
    {
        if (gTRIGGER.CANCEL || start > gTRIGGER.END) {               
            return; // End Import.
        }
        
        if (!busy && gMEDIA.XBMCID != xbmcid)
        {
            xbmcid = gMEDIA.XBMCID;
            busy = true;
               
            url = GetTransferUrl() + "transfer.html?action=" + type + "&xbmcid=" + gMEDIA.XBMCID + "&fargoid=-1" + "&key=" + gCONNECT.KEY;
            ready = ImportData(url, 0);
            ready.done(function() {
                retry = 0;
            }).fail(function() {
                gTRIGGER.CANCEL = true;
                deferred.reject(cSTATUS.OFFLINE);
            }); // End Ready.
        }
               
        // Timeout is set in the Fargo system screen.
        setTimeout(setImportTimer, gCONNECT.TIMEOUT * factor);
    }()); // End setImportTimer.
    
    return deferred.promise();
}

/*
 * Function:	GetMediaStatus
 *
 * Created on Jan 14, 2014
 * Updated on Jul 02, 2014
 *
 * Description: Control and Import the media data transfered from XBMC.
 *
 * In:	type, start, xbmcid
 * Out:	Global gMEDIA (xbmcid, title, subtitle, thumbs, poster)
 *
 */
function GetMediaStatus(type, start, xbmcid)
{  
    $.ajax({
        url: 'jsonmanage.php?action=status&media=' + type + '&id=' + start + '&xbmcid=' + xbmcid,
        async: false,
        dataType: 'json',
        success: function(json)
        {     
            gMEDIA.TITLE  = json.title;
            gMEDIA.SUB    = json.sub;
            gMEDIA.THUMBS = json.thumbs;
            gMEDIA.POSTER = json.poster;
            gMEDIA.XBMCID = json.xbmcid;
            
            gTRIGGER.STATUS  = json.status;
            gTRIGGER.COUNTER = json.counter;
        } // End succes.    
    }); // End Ajax.    
}

/*
 * Function:	ShowImportProgress
 *
 * Created on Jan 16, 2014
 * Updated on Jul 07, 2014
 *
 * Description: Show the import progress.
 * 
 * In:	$msg, $prg, $img, $tit, $sub, type, i, delta
 * Out:	-
 *
 */
function ShowImportProgress($msg, $prg, $img, $tit, $sub, type, i, delta)
{   
    var percent = i - (gTRIGGER.END+1 - delta);
    percent = Math.ceil(percent/delta * 100);
                
    $prg.progressbar({
        value : percent     
    });
    
    switch (Number(gTRIGGER.STATUS))
    {                          
        case cTRANSFER.DUPLICATE 
                :   $msg.html(cSTATUS.EXISTS.replace("[dummy]", ConvertMediaToSingular(type)));
                    break;
                                                       
        case cTRANSFER.READY
                :   $msg.html(cSTATUS.PROCESS.replace("[dummy]", ConvertMediaToSingular(type)));
                    break;                              
                            
        case cTRANSFER.WAIT   
                :   $msg.html(cSTATUS.IMPORT.replace("[dummy]", ConvertMediaToSingular(type)));
                    break;
    } 
      
    if (gMEDIA.TITLE) 
    {
        // Preload image.
        var img = new Image();
        if (gMEDIA.POSTER) {
            img.src = gMEDIA.THUMBS + '/' + gMEDIA.POSTER + '.jpg';
        }
        else {
            img.src = 'images/no_poster.jpg';
        }
        $img.attr('src', img.src);
                                
        // If images not found then show no poster.
        $img.error(function(){
            $(this).attr('src', 'images/no_poster.jpg');
        });
         
        $img.show();
        $tit.html(gMEDIA.TITLE);
        $sub.html(gMEDIA.SUB);    
    }
}

////////////////////////////////////    Import Seasons Functions    ///////////////////////////////////////

/*
 * Function:	StartSeasonsMetaImportHandler
 *
 * Created on Jan 20, 2014
 * Updated on Feb 24, 2014
 *
 * Description:  Start the seasons meta import handler.
 * 
 * In:	media, type, next
 * Out:	-
 *
 * Note: Uses globals cCONNECT, gTRIGGER.START and gTRIGGER.END. 
 * 
 */
function StartSeasonsMetaImportHandler(media, type, next)
{       
    var $prg = $("#action_box .progress");
    var $img = $("#action_thumb img");
    var $tit = $("#action_title");
    var $sub = $("#action_sub");
    
    $prg.progressbar({value : 0 });
    //$img.removeAttr("src").attr("src", "");
    $img.hide();
    $tit.html("&nbsp;");
    $sub.html("&nbsp;");     
    
    gTRIGGER.STEP2 = next - 1;
        
    if (gTRIGGER.END >= 0)
    {
        var meta = StartSeasonsMetaImport(type);            
        meta.progress(function(i) {
            ShowMetaProgress($prg, type, i, gTRIGGER.END);
            //console.log("Meta Counter: " + i);
        }); 
            
        meta.done (function() {
            if (!gTRIGGER.CANCEL) {
                
                // Returns gTRIGGER.START and gTRIGGER.END.
                GetXbmcMediaLimits(type);
                //console.log(type);
                
                SetStartImportHandler(media, next, false); // Continue with the next step.
            }
        }).fail (function() {
            ShowOffline(cSTATUS.OFFLINE); 
        });
    }         
}

/*
 * Function:	StartSeasonsMetaImport
 *
 * Created on Jan 20, 2014
 * Updated on Jun 17, 2014
 *
 * Description: Control and Import the seasons media meta data transfered from XBMC.
 *
 * In:	type
 * Out:	-
 *
 * Note: Gets all TV show id's. Max is 5000 id's. 
 *
 */
function StartSeasonsMetaImport(type)
{
    var deferred = $.Deferred();
    
    $.ajax({  // Begin Ajax 1.
        url: 'jsonmanage.php?action=tvshowids&start=0&offset=5000',
        async: false,
        dataType: 'json',
        success: function(json)
        {                 
            if (json.tvshowids) 
            {
                var url = GetTransferUrl() + "meta.html?action=" + type + "&tvshowid=" + json.tvshowids[0] + "&key=" + gCONNECT.KEY;
                var currentStep = ImportData(url, 0);
    
                deferred.notify(0);
    
                for(var i = 1; i < gTRIGGER.END; i++)
                {
                    currentStep = currentStep.pipe(function(j){
                        if (!gTRIGGER.CANCEL) {
                            deferred.notify(++j);
                            url = GetTransferUrl() + "meta.html?action=" + type + "&tvshowid=" + json.tvshowids[j] + "&key=" + gCONNECT.KEY;
                            return ImportData(url, j);
                        }
                    });
                }
                $.when(currentStep).done(function(){
                    //console.log("All steps done.");   
                    $.ajax({ // Begin Ajax 2.
                        url: 'jsonmanage.php?action=chkmeta&type=' + type,
                        async: false,
                        dataType: 'json',
                        success: function(json)
                        {                  
                            if (json.check) {
                                deferred.resolve();
                            }
                            else {
                                deferred.reject(cSTATUS.METAERR);
                            }     
                        } // End succes.    
                    }); // End Ajax 2.                     
                    //deferred.resolve();
                }).fail(function(){               
                    deferred.reject();
                });  
            }
            else {
                deferred.resolve();
            }

        } // End succes.    
    }); // End Ajax 1.
    
    return deferred.promise();    
}

//////////////////////////////////   Common Import & Refresh Functions    /////////////////////////////////

/*
 * Function:	LockImport
 *
 * Created on Dec 13, 2013
 * Updated on May 12, 2014
 *
 * Description: New imports cannot be started during the lock.
 * 
 * In:	-
 * Out:	Set lock (ImportLock = 0 (false))
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
 * Updated on May 12, 2014
 *
 * Description: New imports cannot be started during the lock.
 * 
 * In:	-
 * Out:	Remove lock (ImportLock = 1(true))
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
 * Updated on Jul 04, 2014
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
               
               $("#action_wrapper").show();
               //$("#action_title").html("&nbsp;");
               $("#action_sub").html("&nbsp;");
               
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
    
           $("#action_thumb img").hide();
    
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
 * Updated on Jan 20, 2014
 *
 * Description: Import data transfered from XBMC.
 *
 * In:	url, counter
 * Out:	Imported media counter
 *
 * Note: Uses global gCONNECT.
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
        
    }, gCONNECT.TIMEOUT); // End _timer.
     
    return deferred.promise();
}

/*
 * Function:	GetTransferUrl
 *
 * Created on Jan 13, 2014
 * Updated on Jun 17, 2014
 *
 * Description: Get first of the part transfer url.
 * 
 * In:	global gCONNECT
 * Out:	url
 *
 */
function GetTransferUrl()
{
    var url = "http://" + gCONNECT.CONNECTION;
    if (gCONNECT.PORT) {
        url = "http://" + gCONNECT.CONNECTION + ":" + gCONNECT.PORT;
    }
    
    url += "/" + gCONNECT.PATH;
    
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
function ShowNoNewMedia(media, callback) // Obsolete
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
 * Updated on Feb 19, 2014
 *
 * Description: Show offline message and add to log event.
 * 
 * In:	msg
 * Out:	-
 *
 */
function ShowOffline(msg)
{
    /*if (offline) {
        $("#action_box .message").html(cSTATUS.OFFLINE);
    }
    else {
        $("#action_box .message").html(cSTATUS.LOST);
    }*/
    
    $("#action_box .message").html(msg);
    SetState("xbmc", "offline");
    
    $(".cancel").toggleClass("cancel retry");    
    $(".retry").html("Retry");
    
    LogEvent("Information", "XBMC is offline or not reachable."); 
}

/*
 * Function:	ShowFinished
 *
 * Created on Aug 19, 2013
 * Updated on Jan 20, 2014
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
            $("#action_box .progress").progressbar({value : 100 });
            $(".cancel").html("Finish");
       }, 1500); // 1000      
    }
    else {
        $(".cancel").html("Finish");
    } 
}

/*
 * Function:	SetImportCancelHandler
 *
 * Created on May 09, 2013
 * Updated on Jul 04, 2014
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
       
    // Find media type (Movie, TV Show, Season, Episode, Album, Song) in message and replaces "..." with "s". 
    var type = $popup.find(".message").text().split(" ")[1].replace(/[.]+/, "s").toLowerCase();
    if (type != "movies" && type != "sets" && type != "tv shows" && type != "seasons" && type != "episodes" && type != "albums" && type != "songs") {
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
function DisplayStatusMessage(str1, str2, end, callback) // Obsolete
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
 * Updated on May 12, 2014
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
            var counter = Number(json.import) - 1;
            var name    = ConvertMedia(media);
            var msg;
            
            if (finish && counter > 0) 
            {
                msg = cSTATUS.FINISH.replace("[dummy]", counter + " " + name); 
                $("#action_box .message").html(msg);
                LogEvent("Information", msg); 
            }
            else if (counter == 0)
            {
                msg = (cSTATUS.NOTFOUND.replace("[dummy]", name));
                $("#action_box .message").html(msg);
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

/*
 * Function:	GetXbmcMediaLimits
 *
 * Created on Jul 22, 2013
 * Updated on Feb 14, 2014
 *
 * Description: Get the XBMC media limits (start and end values).
 *
 * In:	media
 * Out:	counter
 *
 */
function GetXbmcMediaLimits(media) 
{
    $.ajax({
        url: 'jsonmanage.php?action=counter&media=' + media,
        async: false,
        dataType: 'json',
        success: function(json) 
        {           
            gTRIGGER.START = Number(json.xbmc.start);
            gTRIGGER.END   = Number(json.xbmc.end);
        } // End Success.        
    }); // End Ajax;
}