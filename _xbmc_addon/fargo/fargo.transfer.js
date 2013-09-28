/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.2
 *
 * File:    fargo.transfer.js
 *
 * Created on Jul 13, 2013
 * Updated on Sep 28, 2013
 *
 * Description: Fargo Transfer jQuery and Javascript functions page.
 *
 */

//////////////////////////////////////////    Main Functions    ///////////////////////////////////////////

/*
 * Function:	Transfer
 *
 * Created on Jul 13, 2013
 * Updated on Sep 28, 2013
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
        case "counter" : TransferCounter(aRequest.key, aRequest.media);
                         break

        case "movies"  : TransferMovies(aRequest.key, aRequest.mode, aRequest.start, aRequest.fargoid, 1);
                         break;
            
        case "tvshows" : TransferTVShows(aRequest.key, aRequest.mode, aRequest.start, aRequest.fargoid, 1);
                         break;
        
        case "music"   : TransferAlbums(aRequest.key, aRequest.mode, aRequest.start, aRequest.fargoid, 1); 
                         break
    }

}

/*
 * Function:	TransferCounter
 *
 * Created on Jul 22, 2013
 * Updated on Sep 28, 2013
 *
 * Description: Transfers media counter (e.g. total number of movies) from XBMC to Fargo.
 * 
 * In:	media, key
 * Out:	Transfered media counter.
 *
 */
function TransferCounter(key, media)
{
    var request;
    var fargo = "http://" + cFARGOSITE + "/include/" + cIMPORT;
   
    switch (media)
    {
        case "movies"  : request = '{"jsonrpc": "2.0", "method": "VideoLibrary.GetMovies",\n\
                                     "params": { "limits": { "start" : 0, "end": 1 }}, "id": "libMovies"}';
                         break;
            
        case "tvshows" : request = '{"jsonrpc": "2.0", "method": "VideoLibrary.GetTVShows",\n\
                                     "params": { "limits": { "start" : 0, "end": 1 }}, "id": "libTvShows"}';
                         break;
        
        case "music"   : request = '{"jsonrpc": "2.0", "method": "AudioLibrary.GetAlbums",\n\
                                     "params": { "limits": { "start" : 0, "end": 1 }}, "id": "libAlbums"}';
                         break;
    }
    
    $("#debug").text(request);
    
    TransferJSON(key, request, fargo, "Counter");
}


/*
 * Function:	TransferMovies
 *
 * Created on Jul 13, 2013
 * Updated on Sep 28, 2013
 *
 * Description: Transfers movies from XBMC to Fargo.
 * 
 * In:	key, mode, start, fargoid, offset
 * Out:	Transfered movies.
 *
 */
function TransferMovies(key, mode, start, fargoid, offset)
{
    var request;    
    var fargo   = "http://" + cFARGOSITE + "/include/" + cIMPORT;     
    var end     = Number(start) + Number(offset);
            
    if (mode == "import") 
    {        
        request = '{"jsonrpc": "2.0", "method": "VideoLibrary.GetMovies", "params":\n\
                   {"limits": {"start": ' + start + ', "end": ' + end + '},\n\
                   "properties": ["title","genre","year","rating","director","trailer","tagline","plot",\n\
                   "plotoutline","originaltitle","lastplayed","playcount","writer","studio","mpaa","cast",\n\
                   "country","imdbnumber","runtime","set","showlink","streamdetails","top250","votes","fanart",\n\
                   "thumbnail","file","sorttitle","resume","setid","dateadded","tag","art"]}, "id": "libMovies"}';
        
        TransferGetMovies(key, fargo, request);
    }
    else // Refresh movie.
    {   
        request = '{"jsonrpc": "2.0", "method": "VideoLibrary.GetMovieDetails", "params":\n\
                   {"movieid": ' + start + ', \n\
                   "properties": ["title","genre","year","rating","director","trailer","tagline","plot",\n\
                   "plotoutline","originaltitle","lastplayed","playcount","writer","studio","mpaa","cast",\n\
                   "country","imdbnumber","runtime","set","showlink","streamdetails","top250","votes","fanart",\n\
                   "thumbnail","file","sorttitle","resume","setid","dateadded","tag","art"]}, "id": "libMovies"}'; 
        
        TransferGetMovieDetails(key, fargo, request, fargoid);
    }    
}

/*
 * Function:	TransferGetMovies
 *
 * Created on Sep 08, 2013
 * Updated on Sep 28, 2013
 *
 * Description: Transfers the GetMovies from XBMC to Fargo.
 * 
 * In:	fargo, request
 * Out:	Transfered movies.
 *
 */
function TransferGetMovies(key, fargo, request)
{
    var a, b, a_chk, b_chk;
    var poster, fanart;
    
    $.getJSON("../jsonrpc?request=" + request, function(json)
    {    
        if (json.result && json.result.movies)
        {
            poster = CreateImageUrl(json.result.movies[0].art.poster);
            fanart = CreateImageUrl(json.result.movies[0].art.fanart);
        
            // Show title.
            $("#info").text(json.result.movies[0].label);
        
            // Draw image on canvas and wait until it's doen.
            a = DrawImageOnCanvas("poster", poster);
            b = DrawImageOnCanvas("fanart", fanart);

            // Check if image loaded successfully.
            a.done( function(a_check) { a_chk = a_check; });
            b.done( function(b_check) { b_chk = b_check; });
        
            // Wait until DrawImageOnCanvas functions are ready.
            $.when(a, b).done(function()
            { 
                json.key    = key;
                json.action = "Movies";
                json.poster = GetImageFromCanvas(a_chk, poster, "poster", 0.7);
                json.fanart = GetImageFromCanvas(b_chk, fanart, "fanart", 0.7);
                
                // Transfer the data with Ajax.
                CallAjax(fargo, json);
      
            }); // End when.         
        } // End if.
    }); // End getJSON.       
}

/*
 * Function:	TransferGetMovieDetails
 *
 * Created on Sep 08, 2013
 * Updated on Sep 28, 2013
 *
 * Description: Transfers the GetMovieDetails from XBMC to Fargo.
 * 
 * In:	key, fargo, request, id
 * Out:	Transfered movie details.
 *
 */
function TransferGetMovieDetails(key, fargo, request, id)
{
    var a, b, a_chk, b_chk;
    var poster, fanart;
    
    $.getJSON("../jsonrpc?request=" + request, function(json)
    {         
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
                json.key     = key;                
                json.action  = "MovieDetails";
                json.fargoid = id;
                json.poster  = GetImageFromCanvas(a_chk, poster, "poster", 0.7);
                json.fanart  = GetImageFromCanvas(b_chk, fanart, "fanart", 0.7);
                
                // Transfer the data with Ajax.
                CallAjax(fargo, json);
      
            }); // End when.         
        } // End if.
    }); // End getJSON.       
}

/*
 * Function:	TransferTVShows
 *
 * Created on Jul 13, 2013
 * Updated on Sep 28, 2013
 *
 * Description: Transfers TV Shows from XBMC to Fargo.
 * 
 * In:	key, mode, start, , fargoid, offset
 * Out:	Transfered TV Shows.
 *
 */
function TransferTVShows(key, mode, start, fargoid, offset)
{
    var request;    
    var fargo   = "http://" + cFARGOSITE + "/include/" + cIMPORT;     
    var end     = Number(start) + Number(offset);
    
    if (mode == "import") 
    {  
        request = '{"jsonrpc": "2.0", "method": "VideoLibrary.GetTVShows", "params":\n\
                    {"limits": {"start": '+ start +', "end": ' + end + '},\n\
                    "properties": ["title", "genre", "year", "rating", "plot", "studio", "mpaa", "cast", "playcount",\n\
                    "episode", "imdbnumber", "premiered", "votes", "lastplayed", "fanart", "thumbnail", "file",\n\
                    "originaltitle", "sorttitle", "episodeguide", "season", "watchedepisodes", "dateadded",\n\
                    "tag", "art"] }, "id": "libTvShows"}';
        
        TransferGetTVShows(key, fargo, request);
    }
    else  // Refresh TV show.
    {
        request = '{"jsonrpc": "2.0", "method": "VideoLibrary.GetTVShowDetails", "params":\n\
                    {"tvshowid": ' + start + ', \n\
                    "properties": ["title", "genre", "year", "rating", "plot", "studio", "mpaa", "cast", "playcount",\n\
                    "episode", "imdbnumber", "premiered", "votes", "lastplayed", "fanart", "thumbnail", "file",\n\
                    "originaltitle", "sorttitle", "episodeguide", "season", "watchedepisodes", "dateadded",\n\
                    "tag", "art"] }, "id": "libTvShows"}';
        
        TransferGetTVShowDetails(key, fargo, request, fargoid);
    }
}

/*
 * Function:	TransferGetTVShows
 *
 * Created on Sep 09, 2013
 * Updated on Sep 28, 2013
 *
 * Description: Transfers the GetTVShows from XBMC to Fargo.
 * 
 * In:	key, fargo, request
 * Out:	Transfered TV Shows.
 *
 */
function TransferGetTVShows(key, fargo, request)
{
    var a, b, a_chk, b_chk;
    var poster, fanart;
    
    $.getJSON("../jsonrpc?request=" + request, function(json)
    {    
        if (json.result &&  json.result.tvshows)
        {
            poster = CreateImageUrl(json.result.tvshows[0].art.poster);
            fanart = CreateImageUrl(json.result.tvshows[0].art.fanart);
        
            // Show title.
            $("#info").text(json.result.tvshows[0].label);
        
            // Draw image on canvas and wait until it's doen.
            a = DrawImageOnCanvas("poster", poster);
            b = DrawImageOnCanvas("fanart", fanart);

            // Check if image loaded successfully.
            a.done( function(a_check) { a_chk = a_check; });
            b.done( function(b_check) { b_chk = b_check; });
        
            // Wait until DrawImageOnCanvas functions are ready.
            $.when(a, b).done(function()
            { 
                json.key    = key;                
                json.action = "TVShows";
                json.poster = GetImageFromCanvas(a_chk, poster, "poster", 0.7);
                json.fanart = GetImageFromCanvas(b_chk, fanart, "fanart", 0.7);
                
                // Transfer the data with Ajax.
                CallAjax(fargo, json);
      
            }); // End when.         
        } // End if.
    }); // End getJSON.     
}

/*
 * Function:	TransferGetTVShowDetails
 *
 * Created on Sep 09, 2013
 * Updated on Sep 28, 2013
 *
 * Description: Transfers the GetTVShowDetails from XBMC to Fargo.
 * 
 * In:	key, fargo, request, id
 * Out:	Transfered TV Show details.
 *
 */
function TransferGetTVShowDetails(key, fargo, request, id)
{
    var a, b, a_chk, b_chk;
    var poster, fanart;
    
    $.getJSON("../jsonrpc?request=" + request, function(json)
    {    
        if (json.result &&  json.result.tvshowdetails)
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
                json.key     = key;                
                json.action  = "TVShowDetails";
                json.fargoid = id;
                json.poster  = GetImageFromCanvas(a_chk, poster, "poster", 0.7);
                json.fanart  = GetImageFromCanvas(b_chk, fanart, "fanart", 0.7);
                
                // Transfer the data with Ajax.
                CallAjax(fargo, json);
      
            }); // End when.         
        } // End if.
    }); // End getJSON.     
}

/*
 * Function:	TransferAlbums
 *
 * Created on Jul 13, 2013
 * Updated on Sep 28, 2013
 *
 * Description: Transfers music albums from XBMC to Fargo.
 * 
 * In:	key, mode, start, fargoid, offset
 * Out:	Transfered music albums.
 *
 */
function TransferAlbums(key, mode, start, fargoid, offset)
{
    var request;    
    var fargo   = "http://" + cFARGOSITE + "/include/" + cIMPORT;     
    var end     = Number(start) + Number(offset);
    
    if (mode == "import")
    {    
        request = '{"jsonrpc": "2.0", "method": "AudioLibrary.GetAlbums", "params":\n\
                   {"limits": {"start": ' + start + ', "end": ' + end + '},\n\
                    "properties": ["title", "description", "artist", "genre", "theme", "mood", "style","type",\n\
                    "albumlabel", "rating", "year", "musicbrainzalbumid", "musicbrainzalbumartistid", "fanart",\n\
                    "thumbnail","playcount", "genreid", "artistid", "displayartist"] }, "id": "libAlbums"}';

        TransferGetAlbums(key, fargo, request);
    }
    else  // Refresh album.
    {
        request = '{"jsonrpc": "2.0", "method": "AudioLibrary.GetAlbumDetails", "params":\n\
                    {"albumid": ' + start + ', \n\
                    "properties": ["title", "description", "artist", "genre", "theme", "mood", "style","type",\n\
                    "albumlabel", "rating", "year", "musicbrainzalbumid", "musicbrainzalbumartistid", "fanart",\n\
                    "thumbnail","playcount", "genreid", "artistid", "displayartist"] }, "id": "libAlbums"}';   
        
        TransferGetAlbumDetails(key, fargo, request, fargoid);
    } 
}

/*
 * Function:	TransferGetAlbums
 *
 * Created on Sep 09, 2013
 * Updated on Sep 28, 2013
 *
 * Description: Transfers the GetAlbums from XBMC to Fargo.
 * 
 * In:	key, fargo, request
 * Out:	Transfered albums.
 *
 */
function TransferGetAlbums(key, fargo, request)
{
    var a, b, a_chk, b_chk;
    var poster, fanart;
    
    $.getJSON("../jsonrpc?request=" + request, function(json)
    {    
        if (json.result &&  json.result.albums)
        {
            poster = CreateImageUrl(json.result.albums[0].thumbnail);
            fanart = CreateImageUrl(json.result.albums[0].fanart);
        
            // Show title.
            $("#info").text(json.result.albums[0].label);
        
            // Draw image on canvas and wait until it's doen.
            a = DrawImageOnCanvas("poster", poster);
            b = DrawImageOnCanvas("fanart", fanart);

            // Check if image loaded successfully.
            a.done( function(a_check) { a_chk = a_check; });
            b.done( function(b_check) { b_chk = b_check; });
        
            // Wait until DrawImageOnCanvas functions are ready.
            $.when(a, b).done(function()
            { 
                json.key    = key;                
                json.action = "Music";
                json.poster = GetImageFromCanvas(a_chk, poster, "poster", 0.7);
                json.fanart = GetImageFromCanvas(b_chk, fanart, "fanart", 0.7);
                
                // Transfer the data with Ajax.
                CallAjax(fargo, json);
      
            }); // End when.         
        } // End if.
    }); // End getJSON.   
}

/*
 * Function:	TransferGetAlbumDetails
 *
 * Created on Sep 09, 2013
 * Updated on Sep 28, 2013
 *
 * Description: Transfers the GetAlbumDetails from XBMC to Fargo.
 * 
 * In:	key, fargo, request, id
 * Out:	Transfered album details.
 *
 */
function TransferGetAlbumDetails(key, fargo, request, id)
{
    var a, b, a_chk, b_chk;
    var poster, fanart;
    
    $.getJSON("../jsonrpc?request=" + request, function(json)
    {    
        if (json.result &&  json.result.albumdetails)
        {
            poster = CreateImageUrl(json.result.albumdetails.thumbnail);
            fanart = CreateImageUrl(json.result.albumdetails.fanart);
        
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
                json.key     = key;                
                json.action  = "MusicDetails";
                json.fargoid = id;
                json.poster  = GetImageFromCanvas(a_chk, poster, "poster", 0.7);
                json.fanart  = GetImageFromCanvas(b_chk, fanart, "fanart", 0.7);
                
                // Transfer the data with Ajax.
                CallAjax(fargo, json);
      
            }); // End when.         
        } // End if.
    }); // End getJSON.   
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
 * Function:	TransferJSON
 *
 * Created on Jul 14, 2013
 * Updated on Sep 28, 2013
 *
 * Description: Transfers the JSON data from XBMC to Fargo.
 * 
 * In:	key, input, url, action
 * Out:	Transfered data.
 *
 */
function TransferJSON(key, input, url, action)
{  
    $.getJSON("../jsonrpc?request=" + input, function(json){
        
        // Send kind of action to Fargo.
        json.key    = key;
        json.action = action;
        
        // Transfer the data with Ajax.
        CallAjax(url, json);
        
    }); // End getJSON.    
}

/*
 * Function:	CallAjax
 *
 * Created on Jul 14, 2013
 * Updated on Jul 22, 2013
 *
 * Description: Call ajax and transfers the data from XBMC to Fargo.
 * 
 * In:	url, data
 * Out:	Transfered data.
 *
 */
function CallAjax(url, data)
{
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
