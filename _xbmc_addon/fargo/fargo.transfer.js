/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.3
 *
 * File:    fargo.transfer.js
 *
 * Created on Jul 13, 2013
 * Updated on Oct 27, 2013
 *
 * Description: Fargo Transfer jQuery and Javascript functions page.
 *
 */

//////////////////////////////////////////    Main Functions    ///////////////////////////////////////////

/*
 * Function:	Transfer
 *
 * Created on Jul 13, 2013
 * Updated on Oct 26, 2013
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
        case "counter"  : TransferMediaCounter(aRequest.key, aRequest.media, aRequest.tvshowid);
                          break;

        case "movies"   : TransferMovie(aRequest.key, aRequest.xbmcid, aRequest.fargoid);
                          break;
            
        case "sets"     : TransferMovieSet(aRequest.key, aRequest.xbmcid, aRequest.fargoid);
                          break;  
            
        case "tvshows"  : TransferTVShow(aRequest.key, aRequest.xbmcid, aRequest.fargoid);
                          break;
                         
        case "seasons"  : TransferTVShowSeason(aRequest.key, aRequest.xbmcid, aRequest.fargoid, aRequest.tvshowid);
                          break;
                         
        case "episodes" : TransferTVShowEpisode(aRequest.key, aRequest.xbmcid, aRequest.fargoid);
                          break;                         
        
        case "music"    : TransferAlbum(aRequest.key, aRequest.xbmcid, aRequest.fargoid); 
                          break;            
    }
}

/*
 * Function:	TransferMediaCounter
 *
 * Created on Jul 22, 2013
 * Updated on Oct 26, 2013
 *
 * Description: Transfers media counter (e.g. total number of movies) from XBMC to Fargo.
 * 
 * In:	media, key, id
 * Out:	Transfered media counter.
 *
 */
function TransferMediaCounter(key, media, id)
{
    switch (media)
    {
        case "movies"    : // libMoviesCounter -> library id = 1.
                           RequestCounter("VideoLibrary.GetMovies", 1, key);           
                           break;

        case "sets"      : // libMovieSetsCounter -> library id = 4.
                           RequestCounter("VideoLibrary.GetMovieSets", 4, key);         
                           break;        
            
        case "tvshows"   : // libTVShowsCounter -> library id = 11.
                           RequestCounter("VideoLibrary.GetTVShows", 11, key);
                           break;
                         
        case "tvseasons" : // libTVShowsCounter -> library id = 14. Note TV Seasons uses the same counter as TV Shows.
                           RequestCounter("VideoLibrary.GetTVShows", 14, key);
                           break;                         
                         
        case "seasons"   : // libTVShowSeasonsCounter -> library id = 15.
                           RequestSeasonCounter(id, 15, key);
                           break;
                       
        case "episodes" : // libTVShowEpisodesCounter -> library id = 31.
                           RequestCounter("VideoLibrary.GetEpisodes", 31, key);
                           break;                         
        
        case "music"   :   // libAlbumsCounter -> library id = 21.
                           RequestCounter("AudioLibrary.GetAlbums", 41, key);
                           break;
    }
}

/*
 * Function:	RequestCounter
 *
 * Created on Oct 06, 2013
 * Updated on Oct 26, 2013
 *
 * Description: JSON Request XBMC media counter and the media highest id.
 * 
 * In:	library, id, key
 * Out: json.counter, json.maxid
 *
 */
function RequestCounter(library, id, key)
{
    var counter_req = '{"jsonrpc":"2.0","method":"' + library + '",\n\
                        "params":{"limits":{"start":0,"end":1}},"id":"' + id + '"}';
    
    // Get media total (counter) from XBMC.
    $.getJSON("../jsonrpc?request=" + counter_req, function(json)
    {
        var start = Number(json.result.limits.total) - 1;
        var end   = Number(json.result.limits.total);
        
        var max_req = '{"jsonrpc":"2.0","method":"' + library + '",\n\
                        "params":{"limits":{"start":' + start + ',"end":' + end + '}},"id":"' + id + '"}';
        
        // Get highest id (maxid) from XBMC.
        $.getJSON("../jsonrpc?request=" + max_req, function(data)
        {
            data.key = key;
            
            // Tranfer data to Fargo.
            TransferData(data);          
        }); // End getJSON.         
    }); // End getJSON.         
}

/*
 * Function:	RequestSeasonCounter
 *
 * Created on Oct 18, 2013
 * Updated on Oct 26, 2013
 *
 * Description: JSON Request XBMC season counter.
 * 
 * In:	library, id, key
 * Out: json
 *
 */
function RequestSeasonCounter(tvshowid, id, key)
{
    var request = '{"jsonrpc":"2.0","method":"VideoLibrary.GetSeasons",\n\
                    "params":{"tvshowid":'+ tvshowid +',"limits":{"start":0,"end":1}},\n\
                    "id":"' + id + '"}';
    
    // Get season total (counter) from XBMC.
    $.getJSON("../jsonrpc?request=" + request, function(json)
    {
        json.key = key;
            
        // Tranfer data to Fargo.
        TransferData(json);                 
    }); // End getJSON.         
}

/*
 * Function:	TransferMovie
 *
 * Created on Oct 07, 2013
 * Updated on Oct 26, 2013
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
    var request = '{"jsonrpc":"2.0","method":"VideoLibrary.GetMovieDetails","params":\n\
                   {"movieid":' + xbmcid + ',\n\
                   "properties":["title","genre","year","rating","director","trailer","tagline","plot",\n\
                   "plotoutline","originaltitle","lastplayed","playcount","writer","studio","mpaa","cast",\n\
                   "country","imdbnumber","runtime","set","showlink","streamdetails","top250","votes","fanart",\n\
                   "thumbnail","file","sorttitle","resume","setid","dateadded","tag","art"]},"id":'+ id +'}';     
    
    $.ajax({
        url: '../jsonrpc?request=' + request,
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
                
                    // Transfer the data to Fargo.
                    TransferData(json);
                }); // End when.         
            } 
            else if (json.error.code == -32602) { // Movie not found.
                TransferData(json);
                //xbmcid = Number(xbmcid) + 1;
                //TransferMovie(key, xbmcid, fargoid);
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
 * Updated on Oct 26, 2013
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
    var request = '{"jsonrpc":"2.0","method":"VideoLibrary.GetMovieSetDetails","params":\n\
                   {"setid":' + xbmcid + ',\n\
                   "properties":["title","playcount","art","thumbnail"]},"id":'+ id +'}';
    
    $.ajax({
        url: '../jsonrpc?request=' + request,
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
                fanart = CreateImageUrl(json.result.setdetails.art.fanart);
        
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
                    json.fanart  = GetImageFromCanvas(b_chk, fanart, "fanart", 0.7); // 0.7
                
                    // Transfer the data to Fargo.
                    TransferData(json);
                }); // End when.         
            } 
            else if (json.error.code == -32602) { // Movie not found.
                TransferData(json);
                //xbmcid = Number(xbmcid) + 1;
                //TransferMovie(key, xbmcid, fargoid);
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
 * Updated on Oct 26, 2013
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
    var request = '{"jsonrpc":"2.0","method":"VideoLibrary.GetTVShowDetails","params":\n\
                    {"tvshowid":' + xbmcid + ',\n\
                    "properties":["title","genre","year","rating","plot","studio","mpaa","cast","playcount",\n\
                    "episode","imdbnumber","premiered","votes","lastplayed","fanart","thumbnail","file",\n\
                    "originaltitle","sorttitle","episodeguide","season","watchedepisodes","dateadded",\n\
                    "tag","art"]},"id":'+ id +'}';
    
    $.ajax({
        url: '../jsonrpc?request=' + request,
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
                
                    // Transfer the data to Fargo.
                    TransferData(json);
      
                }); // End when.         
            }
            else if (json.error.code == -32602) { // TV Show not found.
                TransferData(json);
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
 * Updated on Oct 26, 2013
 *
 * Description: Transfer TV Show Season from XBMC to Fargo.
 * 
 * In:	key, start, fargoid, tvshowid
 * Out:	Transfered TV Show Season.
 *
 */
function TransferTVShowSeason(key, start, fargoid, tvshowid)
{
    var a, b, a_chk, b_chk;
    var id, poster, fanart;
    var error = {code : -32602};
    var end = Number(start) + 1; 
    
    if (fargoid < 0) {
        id = 16; // Import TV Show Season.
    }
    else {
        id = 17; // Refresh TV Show Season.
    }
    
    // libTVShowSeasons -> library id = 16 or 17.
    var request = '{"jsonrpc":"2.0","method":"VideoLibrary.GetSeasons","params":\n\
                   {"tvshowid":'+ tvshowid +',"limits":{"start":'+ start +',"end":' + end + '},\n\
                   "properties":["episode","watchedepisodes","season","tvshowid","showtitle","playcount",\n\
                   "thumbnail"]},"id":'+ id +'}';
    
    $.ajax({
        url: '../jsonrpc?request=' + request,
        type: 'get',
        dataType: 'json',
        timeout: 1000,
        tryCount: 0,
        retryLimit: 3,
        success: function(json) 
        {            
            json.key = key; 
            if (json.result && json.result.seasons && json.result.limits.total > 0)
            {
                poster = CreateImageUrl(json.result.seasons[0].thumbnail);
                fanart = CreateImageUrl(json.result.seasons[0].fanart);
        
                // Show title.
                $("#info").text(json.result.seasons[0].showtitle);
        
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
                
                    // Transfer the data to Fargo.
                    TransferData(json);
      
                }); // End when.         
            }
            else  // TV Show Season not found.
            {   
                json.error = error;
                TransferData(json);
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
 * Updated on Oct 27, 2013
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
    var request = '{"jsonrpc":"2.0","method":"VideoLibrary.GetEpisodeDetails","params":\n\
                    {"episodeid":' + xbmcid + ',\n\
                    "properties":["tvshowid","title","rating","plot","cast","playcount",\n\
                    "episode","firstaired","votes","lastplayed","fanart","thumbnail","file",\n\
                    "originaltitle","showtitle","season","streamdetails","runtime","dateadded",\n\
                    "writer","director","art"]},"id":'+ id +'}';
    
    $.ajax({
        url: '../jsonrpc?request=' + request,
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
                
                    // Transfer the data to Fargo.
                    TransferData(json);
      
                }); // End when.         
            }
            else if (json.error.code == -32602) { // TV Show not found.
                TransferData(json);
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
 * Updated on Oct 26, 2013
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
    var request = '{"jsonrpc":"2.0","method":"AudioLibrary.GetAlbumDetails","params":\n\
                    {"albumid":' + xbmcid + ',\n\
                    "properties":["title","description","artist","genre","theme","mood","style","type",\n\
                    "albumlabel","rating","year","musicbrainzalbumid","musicbrainzalbumartistid","fanart",\n\
                    "thumbnail","playcount","genreid","artistid","displayartist"]},"id":'+ id +'}';
    
    $.ajax({
        url: '../jsonrpc?request=' + request,
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
                
                    // Transfer the data with Ajax.
                    TransferData(json);
                }); // End when.         
            }
            else if (json.error.code == -32602) { // TV Show not found.
                TransferData(json);
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

/*
 * Function:	TransferData
 *
 * Created on Jul 14, 2013
 * Updated on Sep 29, 2013
 *
 * Description: Call ajax and transfers the data from XBMC to Fargo.
 * 
 * In:	data
 * Out:	Transfered data.
 *
 */
function TransferData(data)
{
    var url = "http://" + cFARGOSITE + "/include/" + cIMPORT;
    
    // Send the images to PHP to save it on the server.
    var request = $.ajax({
        type: "POST",
        url: url,
        data: data
    }); // End ajax.
                
    // Callback handler that will be called on success.
    request.done(function (response, textStatus, jqXHR){
        // log a message to the console
        console.log("Json data send successfully!");
    }); // End request.done.
        
    // callback handler that will be called on failure
    request.fail(function (jqXHR, textStatus, errorThrown){
        // log the error to the console
        console.error("The following error occured: " + textStatus, errorThrown);
    }); // End request.fail.  
}

/*
 * Function:	GetUrlParameters
 *
 * Created on Jul 13, 2013
 * Updated on Jul 13, 2013
 *
 * Description: Get URL parameters.
 * 
 * In:	-
 * Out:	-
 *
 * Note: Code from http://stackoverflow.com/questions/979975/how-to-get-the-value-from-url-parameter
 *
 */
function GetUrlParameters()
{
    var params = {};

    if (location.search) {
        var parts = location.search.substring(1).split('&');

        for (var i = 0; i < parts.length; i++) {
            var nv = parts[i].split('=');
            if (!nv[0]) continue;
            params[nv[0]] = nv[1] || true;
        }
    }

    return params;
}
