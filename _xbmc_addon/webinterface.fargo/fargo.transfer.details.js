/*
 * Title:   Fargo Transfer
 * Author:  Qzofp Productions
 * Version: 0.6
 *
 * File:    fargo.transfer.details.js
 *
 * Created on Jul 13, 2013
 * Updated on Jul 12, 2014
 *
 * Description: Fargo Transfer Details jQuery and Javascript functions page.
 *
 */

//////////////////////////////////////////    Main Functions    ///////////////////////////////////////////

/*
 * Function:	Transfer
 *
 * Created on Jul 13, 2013
 * Updated on Jun 27, 2014
 *
 * Description: Transfers data from XBMC to Fargo.
 * 
 * In:	-
 * Out:	-
 *
 */
function Transfer()
{
    var aRequest = GetUrlParameters();
    
    switch(aRequest.action)
    {
        case "counter"  : TransferMediaCounter(aRequest.key, aRequest.media);
                          break;
                          
        case "search"   : SearchAndTransferTitle(aRequest.key, aRequest.media, aRequest.xbmcid, aRequest.title);
                          break;

        case "movies"   : TransferMovie(aRequest.key, aRequest.xbmcid, aRequest.fargoid);
                          break;
            
        case "sets"     : TransferMovieSet(aRequest.key, aRequest.xbmcid, aRequest.fargoid);
                          break;  
            
        case "tvshows"  : TransferTVShow(aRequest.key, aRequest.xbmcid, aRequest.fargoid);
                          break;
                         
        case "seasons"  : TransferTVShowSeason(aRequest.key, aRequest.xbmcid, aRequest.fargoid);
                          break;
                         
        case "episodes" : TransferTVShowEpisode(aRequest.key, aRequest.xbmcid, aRequest.fargoid);
                          break;                         
        
        case "albums"   : TransferAlbum(aRequest.key, aRequest.xbmcid, aRequest.fargoid); 
                          break;
        
        case "songs"    : TransferSong(aRequest.key, aRequest.xbmcid, aRequest.fargoid); 
                          break;                          
    }
}

/*
 * Function:	TransferMediaCounter
 *
 * Created on Jul 22, 2013
 * Updated on Jun 27, 2014
 *
 * Description: Transfers media counter (e.g. total number of movies) from XBMC to Fargo.
 * 
 * In:	media, key
 * Out:	Transfered media counter.
 *
 */
function TransferMediaCounter(key, media)
{
    switch (media)
    {
        case "movies"   : // libMediaCounter -> library id = 1.
                          RequestCounter("VideoLibrary.GetMovies", 1, key);
                          break;

        case "sets"     : // libMediaCounter -> library id = 1.
                          RequestCounter("VideoLibrary.GetMovieSets", 1, key);         
                          break;        
            
        case "tvshows"  : // libMediaCounter -> library id = 1.
                          RequestCounter("VideoLibrary.GetTVShows", 1, key);
                          break;
                                 
        case "episodes" : // libMediaCounter -> library id = 1.
                          RequestCounter("VideoLibrary.GetEpisodes", 1, key);
                          break;                         
        
        case "albums"   : // libMediaCounter -> library id = 1.
                          RequestCounter("AudioLibrary.GetAlbums", 1, key);
                          break;
                          
        case "songs"    : // libMediaCounter -> library id = 1.
                          RequestCounter("AudioLibrary.GetSongs", 1, key);
                          break;                          
    }
}

/*
 * Function:	RequestCounter
 *
 * Created on Oct 06, 2013
 * Updated on Jun 17, 2014
 *
 * Description: JSON Request XBMC media counter and the media highest id.
 * 
 * In:	library, id, key
 * Out: json.counter, json.maxid
 *
 */
function RequestCounter(library, id, key)
{
    var counter_req = '{"jsonrpc":"2.0","method":"' + library + '",' +
                      '"params":{"limits":{"start":0,"end":1}},"id":"' + id + '"}';
    
    // Get media total (counter) from XBMC.
    $.getJSON("../../jsonrpc?request=" + counter_req, function(json)
    {
        json.key = key;
        TransferData(json, cIMPORT);
    }); // End getJSON.         
}

/*
 * Function:	SearchAndTransferTitle
 *
 * Created on Jan 28, 2014
 * Updated on Jul 12, 2014
 *
 * Description: Search andtransfer title from XBMC to Fargo.
 * 
 * In:	key, media, xbmcid, title
 * Out:	Transfered search results.
 *
 */
function SearchAndTransferTitle(key, media, xbmcid, title)
{
    switch (media)
    {
        case "movies"   : // libMovies -> library id = 1.
                          TransferTitle(key, "title", "VideoLibrary.GetMovies", xbmcid, title, 1);
                          break;

        case "sets"     : // libMovieSets -> library id = 2.  
                          TransferTitleId(key, "VideoLibrary.GetMovieSetDetails", "setid", xbmcid, 2);
                          break; 
            
        case "tvshows"  : // libTVShows -> library id = 3. 
                          TransferTitle(key, "title", "VideoLibrary.GetTVShows", xbmcid, title, 3);
                          break;
                                                       
        case "seasons"  : // libTVShowSeasons -> library id = 4. 
                          TransferTitleId(key, "VideoLibrary.GetSeasonDetails", "seasonid", xbmcid, 4);
                          break;
                       
        case "episodes" : // libTVShowEpisodes -> library id = 5.  
                          TransferTitle(key, "title", "VideoLibrary.GetEpisodes", xbmcid, title, 5);
                          break;                         
        
        case "albums"   : // libAlbums -> library id = 6.
                          TransferTitle(key, "album", "AudioLibrary.GetAlbums", xbmcid, title, 6);
                          break;
                          
        case "songs"    : // libSongs -> library id = 7.
                          TransferTitle(key, "title", "AudioLibrary.GetSongs", xbmcid, title, 7);
                          break;                          
    }
}

/*
 * Function:	TransferTitle
 *
 * Created on Jan 28, 2014
 * Updated on Jun 17, 2014
 *
 * Description: JSON Request XBMC media title and transfer title to Fargo.
 * 
 * In:	key, library, xbmcid, title
 * Out: json.counter, json.maxid
 *
 */
function TransferTitle(key, field, library, xbmcid, title, id)
{    
    var search_req = '{"jsonrpc": "2.0", "params": {"sort": {"method": "label"}, "filter":' +
                     '{"operator": "is", "field": "' + field + '", "value": "' + title + '"}},' +
                     '"method": "' + library + '", "id": "'+ id +'"}';
    
    // Get media total (counter) from XBMC.
    $.getJSON("../../jsonrpc?request=" + search_req, function(json)
    {
        json.key = key;
        json.xbmcid = xbmcid;
        TransferData(json, cSEARCH);
    }); // End getJSON.         
}

/*
 * Function:	TransferTitleId
 *
 * Created on Feb 03, 2014
 * Updated on Jun 14, 2014
 *
 * Description: JSON Request XBMC media title and transfer the title id to Fargo.
 * 
 * In:	key, library, xbmcid, title
 * Out: json.counter, json.maxid
 * 
 * Note: Sets and Seasons can't be filtered, hence the check if the id exists.
 *
 */
function TransferTitleId(key, library, typeid, xbmcid, id)
{            
    var search_req = '{"jsonrpc":"2.0","method":"' + library + '","params":{"'+ typeid +'":'+ xbmcid +'},"id":'+ id +'}';  
    
    // Get media total (counter) from XBMC.
    $.getJSON("../../jsonrpc?request=" + search_req, function(json)
    {
        json.key = key;
        //json.xbmcid = xbmcid;
        TransferData(json, cSEARCH);
    }); // End getJSON.         
}

/*
 * Function:	TransferMovie
 *
 * Created on Oct 07, 2013
 * Updated on Jun 17, 2014
 *
 * Description: Transfer movie from XBMC to Fargo.
 * 
 * In:	key, xbmcid, fargoid
 * Out:	Transfered movie.
 *
 */
function TransferMovie(key, xbmcid, fargoid)
{
    var a, b, a_chk, b_chk;
    var id, poster, fanart;
    
    if (fargoid < 0) {
        id = 2; // Import movie.
    }
    else {
        id = 3; // Refresh movie.
    }

    // libMovies -> library id = 2 or 3.
    var request = '{"jsonrpc":"2.0","method":"VideoLibrary.GetMovieDetails","params":' +
                   '{"movieid":' + xbmcid + ',' +
                   '"properties":["title","genre","year","rating","director","trailer","tagline","plot",' +
                   '"plotoutline","originaltitle","lastplayed","playcount","writer","studio","mpaa","cast",' +
                   '"country","imdbnumber","runtime","set","showlink","streamdetails","top250","votes","fanart",' +
                   '"thumbnail","file","sorttitle","resume","setid","dateadded","tag","art"]},"id":'+ id +'}'; 
    
    $.ajax({
        url: '../../jsonrpc?request=' + request,
        type: 'get',
        dataType: 'json',
        timeout: 1000,
        tryCount: 0,
        retryLimit: 3,
        success: function(json) 
        {            
            json.key = key;
            if (json.result && json.result.moviedetails)
            {         
                poster = CreateImageUrl(json.result.moviedetails.art.poster);
                fanart = CreateImageUrl(json.result.moviedetails.art.fanart);
        
                // Show title.
                $("#info").text(json.result.moviedetails.label);
        
                // Draw image on canvas and wait until it's doen.
                a = DrawImageOnCanvas("poster", poster);
                b = DrawImageOnCanvas("fanart", fanart);

                // Check if image loaded successfully.
                a.done( function(a_check) { a_chk = a_check; });
                b.done( function(b_check) { b_chk = b_check; });
        
                // Wait until DrawImageOnCanvas functions are ready.
                $.when(a, b).done(function()
                { 
                    json.fargoid = fargoid;
                    json.poster  = GetImageFromCanvas(a_chk, poster, "poster", 0.7); // 0.7
                    json.fanart  = GetImageFromCanvas(b_chk, fanart, "fanart", 0.7); // 0.7
                  
                    TransferData(json, cIMPORT); // Transfer the data to Fargo.
                }); // End when.         
            } 
            else if (json.error.code == -32602) { // Movie not found.
                TransferData(json, cIMPORT);
            }
        }, // End success.
        error: function(xhr, textStatus, errorThrown ) {
            if (textStatus == 'timeout') 
            {
                this.tryCount++;
                if (this.tryCount <= this.retryLimit) 
                {
                    //try again
                    $.ajax(this);
                    return;
                }
                console.log('We have tried ' + this.retryLimit + ' times and it is still not working. We give in. Sorry.');
                return;
            }
            if (xhr.status == 500) {
                console.log('Oops! There seems to be a server problem, please try again later.');
            } 
            else {
                console.log('Oops! There was a problem, sorry.');
            }
        } // End error.    
    }); // End Ajax.         
}

/*
 * Function:	TransferMovieSet
 *
 * Created on Oct 13, 2013
 * Updated on Jun 17, 2014
 *
 * Description: Transfer movie set from XBMC to Fargo.
 * 
 * In:	key, xbmcid, fargoid
 * Out:	Transfered movie set.
 *
 */
function TransferMovieSet(key, xbmcid, fargoid)
{
    var a, b, a_chk, b_chk;
    var id, poster, fanart;
    
    if (fargoid < 0) {
        id = 5; // Import Movie Set.
    }
    else {
        id = 6; // Refresh Movie Set.
    }

    // libMovieSets -> library id = 5 or 6.
    var request = '{"jsonrpc":"2.0","method":"VideoLibrary.GetMovieSetDetails","params":' +
                   '{"setid":' + xbmcid + ',' +
                   '"properties":["title","playcount","art","thumbnail"]},"id":'+ id +'}';
    
    $.ajax({
        url: '../../jsonrpc?request=' + request,
        type: 'get',
        dataType: 'json',
        timeout: 1000,
        tryCount: 0,
        retryLimit: 3,
        success: function(json) 
        {            
            json.key = key;
            if (json.result && json.result.setdetails)
            {         
                poster = CreateImageUrl(json.result.setdetails.art.poster);
                fanart = ""; //CreateImageUrl(json.result.setdetails.art.fanart);
        
                // Show title.
                $("#info").text(json.result.setdetails.label);
        
                // Draw image on canvas and wait until it's doen.
                a = DrawImageOnCanvas("poster", poster);
                b = DrawImageOnCanvas("fanart", fanart);

                // Check if image loaded successfully.
                a.done( function(a_check) { a_chk = a_check; });
                b.done( function(b_check) { b_chk = b_check; });
        
                // Wait until DrawImageOnCanvas functions are ready.
                $.when(a, b).done(function()
                { 
                    json.fargoid = fargoid;
                    json.poster  = GetImageFromCanvas(a_chk, poster, "poster", 0.7); // 0.7
                    //json.fanart  = GetImageFromCanvas(b_chk, fanart, "fanart", 0.7); // 0.7
                   
                    TransferData(json, cIMPORT); // Transfer the data to Fargo.
                }); // End when.         
            } 
            else if (json.error.code == -32602) { // Movie not found.
                TransferData(json, cIMPORT);
            }
        }, // End success.
        error: function(xhr, textStatus, errorThrown ) {
            if (textStatus == 'timeout') 
            {
                this.tryCount++;
                if (this.tryCount <= this.retryLimit) 
                {
                    //try again
                    $.ajax(this);
                    return;
                }
                console.log('We have tried ' + this.retryLimit + ' times and it is still not working. We give in. Sorry.');
                return;
            }
            if (xhr.status == 500) {
                console.log('Oops! There seems to be a server problem, please try again later.');
            } 
            else {
                console.log('Oops! There was a problem, sorry.');
            }
        } // End error.    
    }); // End Ajax.         
}

/*
 * Function:	TransferTVShow
 *
 * Created on Oct 07, 2013
 * Updated on Jun 17, 2014
 *
 * Description: Transfer TV Show from XBMC to Fargo.
 * 
 * In:	key, xbmcid, fargoid
 * Out:	Transfered TV Show.
 *
 */
function TransferTVShow(key, xbmcid, fargoid)
{
    var a, b, a_chk, b_chk;
    var id, poster, fanart;
    
    if (fargoid < 0) {
        id = 12; // Import TV Show.
    }
    else {
        id = 13; // Refresh TV Show.
    }
    
    // libTVShows -> library id = 12 or 13.
    var request = '{"jsonrpc":"2.0","method":"VideoLibrary.GetTVShowDetails","params":' +
                   '{"tvshowid":' + xbmcid + ',' +
                   '"properties":["title","genre","year","rating","plot","studio","mpaa","cast","playcount",' +
                   '"episode","imdbnumber","premiered","votes","lastplayed","fanart","thumbnail","file",' +
                   '"originaltitle","sorttitle","episodeguide","season","watchedepisodes","dateadded",' +
                   '"tag","art"]},"id":'+ id +'}';
    
    $.ajax({
        url: '../../jsonrpc?request=' + request,
        type: 'get',
        dataType: 'json',
        timeout: 1000,
        tryCount: 0,
        retryLimit: 3,
        success: function(json) 
        {            
            json.key = key; 
            if (json.result && json.result.tvshowdetails)
            {
                poster = CreateImageUrl(json.result.tvshowdetails.art.poster);
                fanart = CreateImageUrl(json.result.tvshowdetails.art.fanart);
        
                // Show title.
                $("#info").text(json.result.tvshowdetails.label);
        
                // Draw image on canvas and wait until it's doen.
                a = DrawImageOnCanvas("poster", poster);
                b = DrawImageOnCanvas("fanart", fanart);

                // Check if image loaded successfully.
                a.done( function(a_check) { a_chk = a_check; });
                b.done( function(b_check) { b_chk = b_check; });
        
                // Wait until DrawImageOnCanvas functions are ready.
                $.when(a, b).done(function()
                {    
                    json.fargoid = fargoid;
                    json.poster  = GetImageFromCanvas(a_chk, poster, "poster", 0.7);
                    json.fanart  = GetImageFromCanvas(b_chk, fanart, "fanart", 0.7);
                
                    TransferData(json, cIMPORT); // Transfer the data to Fargo.
      
                }); // End when.         
            }
            else if (json.error.code == -32602) { // TV Show not found.
                TransferData(json, cIMPORT);
            }
        }, // End success.
        error: function(xhr, textStatus, errorThrown ) {
            if (textStatus == 'timeout') 
            {
                this.tryCount++;
                if (this.tryCount <= this.retryLimit) 
                {
                    //try again
                    $.ajax(this);
                    return;
                }
                console.log('We have tried ' + this.retryLimit + ' times and it is still not working. We give in. Sorry.');
                return;
            }
            if (xhr.status == 500) {
                console.log('Oops! There seems to be a server problem, please try again later.');
            } 
            else {
                console.log('Oops! There was a problem, sorry.');
            }
        } // End error.
    }); // End Ajax.    
}

/*
 * Function:	TransferTVShowSeason
 *
 * Created on Oct 18, 2013
 * Updated on Jun 17, 2013
 *
 * Description: Transfer TV Show Season from XBMC to Fargo.
 * 
 * In:	key, start, fargoid
 * Out:	Transfered TV Show Season.
 *
 */
function TransferTVShowSeason(key, xbmcid, fargoid)
{
    var a, b, a_chk, b_chk;
    var id, poster, fanart;
    var error = {code : -32602};
    
    if (fargoid < 0) {
        id = 16; // Import TV Show Season.
    }
    else {
        id = 17; // Refresh TV Show Season.
    }
    
    // libTVShowSeasons -> library id = 15 or 16.
    var request = '{"jsonrpc":"2.0","method":"VideoLibrary.GetSeasonDetails","params":' + 
                  '{"seasonid":' + xbmcid + 
                  ',"properties":["episode","watchedepisodes","season","tvshowid","showtitle",' +
                  '"playcount","thumbnail"]},"id":' + id + '}';
    
    $.ajax({
        url: '../../jsonrpc?request=' + request,
        type: 'get',
        dataType: 'json',
        timeout: 1000,
        tryCount: 0,
        retryLimit: 3,
        success: function(json) 
        {            
            json.key = key; 
            if (json.result && json.result.seasondetails)
            {
                poster = CreateImageUrl(json.result.seasondetails.thumbnail);
                fanart = ""; //CreateImageUrl(json.result.seasons[0].fanart);
        
                // Show title.
                $("#info").text(json.result.seasondetails.label);
        
                // Draw image on canvas and wait until it's doen.
                a = DrawImageOnCanvas("poster", poster);
                b = DrawImageOnCanvas("fanart", fanart);

                // Check if image loaded successfully.
                a.done( function(a_check) { a_chk = a_check; });
                b.done( function(b_check) { b_chk = b_check; });
        
                // Wait until DrawImageOnCanvas functions are ready.
                $.when(a, b).done(function()
                {    
                    json.fargoid = fargoid;
                    json.poster  = GetImageFromCanvas(a_chk, poster, "poster", 0.7);
                    //json.fanart  = GetImageFromCanvas(b_chk, fanart, "fanart", 0.7);
                     
                    TransferData(json, cIMPORT); // Transfer the data to Fargo.
      
                }); // End when.         
            }
            else  // TV Show Season not found.
            {   
                json.error = error;
                TransferData(json, cIMPORT);
            }
        }, // End success.
        error: function(xhr, textStatus, errorThrown ) {
            if (textStatus == 'timeout') 
            {
                this.tryCount++;
                if (this.tryCount <= this.retryLimit) 
                {
                    //try again
                    $.ajax(this);
                    return;
                }
                console.log('We have tried ' + this.retryLimit + ' times and it is still not working. We give in. Sorry.');
                return;
            }
            if (xhr.status == 500) {
                console.log('Oops! There seems to be a server problem, please try again later.');
            } 
            else {
                console.log('Oops! There was a problem, sorry.');
            }
        } // End error.
    }); // End Ajax.    
}

/*
 * Function:	TransferTVShowEpisode
 *
 * Created on Oct 26, 2013
 * Updated on Jun 17, 2014
 *
 * Description: Transfer TV Show Episode from XBMC to Fargo.
 * 
 * In:	key, xbmcid, fargoid
 * Out:	Transfered TV Show Episode.
 *
 */
function TransferTVShowEpisode(key, xbmcid, fargoid)
{
    var a, b, a_chk, b_chk;
    var id, poster, fanart;
    
    if (fargoid < 0) {
        id = 32; // Import TV Show Episode.
    }
    else {
        id = 33; // Refresh TV Show Episode.
    }
    
    // libTVShows -> library id = 32 or 33.
    var request = '{"jsonrpc":"2.0","method":"VideoLibrary.GetEpisodeDetails","params":' +
                   '{"episodeid":' + xbmcid + ',' +
                   '"properties":["tvshowid","title","rating","plot","cast","playcount",' +
                   '"episode","firstaired","votes","lastplayed","fanart","thumbnail","file",' +
                   '"originaltitle","showtitle","season","streamdetails","runtime","dateadded",' +
                   '"writer","director","art"]},"id":'+ id +'}';
    
    $.ajax({
        url: '../../jsonrpc?request=' + request,
        type: 'get',
        dataType: 'json',
        timeout: 1000,
        tryCount: 0,
        retryLimit: 3,
        success: function(json) 
        {            
            json.key = key; 
            if (json.result && json.result.episodedetails)
            {
                poster = CreateImageUrl(json.result.episodedetails.thumbnail);
                fanart = ""; //CreateImageUrl(json.result.episodedetails.fanart);
        
                // Show title.
                $("#info").text(json.result.episodedetails.label);
        
                // Draw image on canvas and wait until it's doen.
                a = DrawImageOnCanvas("poster", poster);
                b = DrawImageOnCanvas("fanart", fanart);

                // Check if image loaded successfully.
                a.done( function(a_check) { a_chk = a_check; });
                b.done( function(b_check) { b_chk = b_check; });
        
                // Wait until DrawImageOnCanvas functions are ready.
                $.when(a, b).done(function()
                {    
                    json.fargoid = fargoid;
                    json.poster  = GetImageFromCanvas(a_chk, poster, "poster", 0.7); 
                    //json.fanart  = GetImageFromCanvas(b_chk, fanart, "fanart", 0.7);
                
                    TransferData(json, cIMPORT); // Transfer the data to Fargo.
      
                }); // End when.         
            }
            else if (json.error.code == -32602) { // TV Show not found.
                TransferData(json, cIMPORT);
            }
        }, // End success.
        error: function(xhr, textStatus, errorThrown ) {
            if (textStatus == 'timeout') 
            {
                this.tryCount++;
                if (this.tryCount <= this.retryLimit) 
                {
                    //try again
                    $.ajax(this);
                    return;
                }
                console.log('We have tried ' + this.retryLimit + ' times and it is still not working. We give in. Sorry.');
                return;
            }
            if (xhr.status == 500) {
                console.log('Oops! There seems to be a server problem, please try again later.');
            } 
            else {
                console.log('Oops! There was a problem, sorry.');
            }
        } // End error.
    }); // End Ajax.    
}

/*
 * Function:	TransferAlbum
 *
 * Created on Jul 13, 2013
 * Updated on Jun 17, 2014
 *
 * Description: Transfer music album from XBMC to Fargo.
 * 
 * In:	key, xbmcid, fargoid
 * Out:	Transfered music album.
 *
 */
function TransferAlbum(key, xbmcid, fargoid)
{
    var a, b, a_chk, b_chk;
    var id, poster, fanart;
    
    if (fargoid < 0) {
        id = 42; // Import album.
    }
    else {
        id = 43; // Refresh album.
    }    
    
    // libAlbums -> library id = 42 or 43.
    var request = '{"jsonrpc":"2.0","method":"AudioLibrary.GetAlbumDetails","params":' +
                   '{"albumid":' + xbmcid + ',' +
                   '"properties":["title","description","artist","genre","theme","mood","style","type",' +
                   '"albumlabel","rating","year","musicbrainzalbumid","musicbrainzalbumartistid","fanart",' +
                   '"thumbnail","playcount","genreid","artistid","displayartist"]},"id":'+ id +'}';
    
    $.ajax({
        url: '../../jsonrpc?request=' + request,
        type: 'get',
        dataType: 'json',
        timeout: 1000,
        tryCount: 0,
        retryLimit: 3,
        success: function(json) 
        {            
            json.key = key; 
            if (json.result && json.result.albumdetails)
            {
                poster = CreateImageUrl(json.result.albumdetails.thumbnail);
                fanart = ""; //CreateImageUrl(json.result.albumdetails.fanart);
        
                // Show title.
                $("#info").text(json.result.albumdetails.label);
        
                // Draw image on canvas and wait until it's doen.
                a = DrawImageOnCanvas("poster", poster);
                b = DrawImageOnCanvas("fanart", fanart);

                // Check if image loaded successfully.
                a.done( function(a_check) { a_chk = a_check; });
                b.done( function(b_check) { b_chk = b_check; });
        
                // Wait until DrawImageOnCanvas functions are ready.
                $.when(a, b).done(function()
                { 
                    json.fargoid = fargoid;
                    json.poster  = GetImageFromCanvas(a_chk, poster, "poster", 0.7);
                    //json.fanart  = GetImageFromCanvas(b_chk, fanart, "fanart", 0.7);
                    
                    TransferData(json, cIMPORT); // Transfer the data with Ajax.
                }); // End when.         
            }
            else if (json.error.code == -32602) { // TV Show not found.
                TransferData(json, cIMPORT);
            }
        }, // End success.
        error: function(xhr, textStatus, errorThrown ) {
            if (textStatus == 'timeout') 
            {
                this.tryCount++;
                if (this.tryCount <= this.retryLimit) 
                {
                    //try again
                    $.ajax(this);
                    return;
                }
                console.log('We have tried ' + this.retryLimit + ' times and it is still not working. We give in. Sorry.');
                return;
            }
            if (xhr.status == 500) {
                console.log('Oops! There seems to be a server problem, please try again later.');
            } 
            else {
                console.log('Oops! There was a problem, sorry.');
            }
        } // End error.
    }); // End Ajax.    
}

/*
 * Function:	TransferSong
 *
 * Created on Jun 27, 2014
 * Updated on Jun 28, 2014
 *
 * Description: Transfer music song from XBMC to Fargo.
 * 
 * In:	key, xbmcid, fargoid
 * Out:	Transfered music song.
 *
 */
function TransferSong(key, xbmcid, fargoid)
{
    var a, b, a_chk, b_chk;
    var id, poster, fanart;
    
    if (fargoid < 0) {
        id = 52; // Import song.
    }
    else {
        id = 53; // Refresh song.
    }    
    
    // libSongs -> library id = 52 or 53.
    var request = '{"jsonrpc":"2.0","method":"AudioLibrary.GetSongDetails","params":' +
                   '{"songid":' + xbmcid + ',' +
                   '"properties":["title","artist","artistid","album","albumid","albumartist","albumartistid","genre",' + 
                   '"year","rating","track","duration","comment","lyrics","musicbrainztrackid","musicbrainzartistid",' + 
                   '"musicbrainzalbumid","musicbrainzalbumartistid","fanart","thumbnail","playcount","file",' + 
                   '"lastplayed","disc","genreid","displayartist"]},"id":'+ id +'}';  
    
    $.ajax({
        url: '../../jsonrpc?request=' + request,
        type: 'get',
        dataType: 'json',
        timeout: 1000,
        tryCount: 0,
        retryLimit: 3,
        success: function(json) 
        {            
            json.key = key; 
            if (json.result && json.result.songdetails)
            {
                poster = CreateImageUrl(json.result.songdetails.thumbnail);
                fanart = ""; //CreateImageUrl(json.result.songdetails.fanart);
        
                // Show title.
                $("#info").text(json.result.songdetails.label);
        
                // Draw image on canvas and wait until it's doen.
                a = DrawImageOnCanvas("poster", poster);
                b = DrawImageOnCanvas("fanart", fanart);

                // Check if image loaded successfully.
                a.done( function(a_check) { a_chk = a_check; });
                b.done( function(b_check) { b_chk = b_check; });
        
                // Wait until DrawImageOnCanvas functions are ready.
                $.when(a, b).done(function()
                { 
                    json.fargoid = fargoid;
                    json.poster  = GetImageFromCanvas(a_chk, poster, "poster", 0.7);
                    //json.fanart  = GetImageFromCanvas(b_chk, fanart, "fanart", 0.7);
                    
                    TransferData(json, cIMPORT); // Transfer the data with Ajax.
                }); // End when.         
            }
            else if (json.error.code == -32602) { // TV Show not found.
                TransferData(json, cIMPORT);
            }
        }, // End success.
        error: function(xhr, textStatus, errorThrown ) {
            if (textStatus == 'timeout') 
            {
                this.tryCount++;
                if (this.tryCount <= this.retryLimit) 
                {
                    //try again
                    $.ajax(this);
                    return;
                }
                console.log('We have tried ' + this.retryLimit + ' times and it is still not working. We give in. Sorry.');
                return;
            }
            if (xhr.status == 500) {
                console.log('Oops! There seems to be a server problem, please try again later.');
            } 
            else {
                console.log('Oops! There was a problem, sorry.');
            }
        } // End error.
    }); // End Ajax.    
}

/*
 * Function:	CreateImageUrl
 *
 * Created on Aug 10, 2013
 * Updated on Aug 12, 2013
 *
 * Description: Create image url.
 * 
 * In:	source
 * Out:	url
 *
 */
function CreateImageUrl(source) 
{
    var url = null; 
    
    if (source) {
        url = "/image/" + encodeURIComponent(source.replace(/.$/,''));  
    }
    
    return url;
}

/*
 * Function:	GetImageFromCanvas
 *
 * Created on Aug 10, 2013
 * Updated on Aug 12, 2013
 *
 * Description: Get image from canvas
 * 
 * In:	check, source, selector, quality
 * Out:	image
 *
 */
function GetImageFromCanvas(check, source, selector, quality)
{
    var image = null;
    
    if (source && check) {
        image = document.getElementById(selector).toDataURL("image/jpeg", quality);
    }
    
    return image;
}

/*
 * Function:	DrawImageOnCanvas
 *
 * Created on Jul 29, 2013
 * Updated on Aug 24, 2013
 *
 * Description: Draw and blur an image omn the HTML5 canvas
 * 
 * In:	selector, image
 * Out:	deferred, Image on canvas.
 *
 */
function DrawImageOnCanvas(selector, image)
{
    var deferred = $.Deferred();    
    var canvas, context, img;
    
    if (image) 
    {
        canvas  = document.getElementById(selector);
        context = canvas.getContext("2d");
        img     = new Image();
    
        img.src = image;
        img.onload = function() 
        {
            if (img.complete) 
            { 
                canvas.width  = img.width;
                canvas.height = img.height;

                context.drawImage(img, 0, 0);

                deferred.resolve(true);
            }
        };
        
        img.onerror = function() {
            deferred.resolve(false);
        };  
        
    }
    else {
        deferred.resolve(false);
    }
    
    return deferred.promise();
}