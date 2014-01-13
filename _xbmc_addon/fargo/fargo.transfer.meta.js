/*
 * Title:   Fargo Transfer
 * Author:  Qzofp Productions
 * Version: 0.4
 *
 * File:    fargo.transfer.meta.js
 *
 * Created on Jan 10, 2014
 * Updated on Jan 11, 2014
 *
 * Description: Fargo Transfer Meta Data jQuery and Javascript functions page.
 *
 */

//////////////////////////////////////////    Main Functions    ///////////////////////////////////////////

/*
 * Function:	TransferMeta
 *
 * Created on Jan 10, 2014
 * Updated on Jan 11, 2014
 *
 * Description: Transfers meta data from XBMC to Fargo.
 * 
 * In:	-
 * Out:	-
 *
 */
function TransferMeta()
{
    var aRequest = GetUrlParameters();
    
    switch(aRequest.action)
    {
        case "movies"   : // libMovies -> library id = 1. 
                          TransferMediaMeta(aRequest.key, aRequest.counter, "VideoLibrary.GetMovies", 1);
                          break;
            
        case "sets"     : // libMovieSets -> library id = 2.
                          TransferMediaMeta(aRequest.key, aRequest.counter, "VideoLibrary.GetMovieSets", 2);
                          break;  
            
        case "tvshows"  : // libTVShows -> library id = 3.
                          TransferMediaMeta(aRequest.key, aRequest.counter, "VideoLibrary.GetTVShows", 3);
                          break;
                         
        case "seasons"  : // libTVShowSeasons -> library id = 4.
                          TransferSeasonsMeta(aRequest.key, aRequest.tvshowid, "VideoLibrary.GetSeasons", 4);
                          break;
                         
        case "episodes" : // libTVShowEpisodes -> library id = 5.
                          TransferMediaMeta(aRequest.key, aRequest.counter, "VideoLibrary.GetEpisodes", 5);
                          break;                         
        
        case "music"    : // libAlbums -> library id = 6.
                          TransferMediaMeta(aRequest.key, aRequest.counter, "AudioLibrary.GetAlbums", 6);
                          break;         
    }
}

/*
 * Function:	TransferMediaMeta
 *
 * Created on Jan 10, 2014
 * Updated on Jan 11, 2014
 *
 * Description: Transfer media meta data from XBMC to Fargo.
 * 
 * In:	key, counter, media, id
 * Out:	Transfered media meta data.
 *
 */
function TransferMediaMeta(key, counter, media, id)
{
    var start = counter * Number(cBULKMAX);
    var end   = ++counter * Number(cBULKMAX);
    
    var request = '{"jsonrpc":"2.0","method":"'+ media +'","params":{"properties":["playcount"],'+
                  '"limits":{"end":'+ end +',"start":'+ start +'}},"id":'+ id +'}';
    
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
            TransferData(json, cMETA);
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
 * Function:	TransferSeasonsMeta
 *
 * Created on Jan 11, 2014
 * Updated on Jan 11, 2014
 *
 * Description: Transfer seasons meta data from XBMC to Fargo.
 * 
 * In:	key, counter, media, id
 * Out:	Transfered media meta data.
 *
 * Note: This function works only if there are no more no 250 seasons for each TV Show.
 *
 */
function TransferSeasonsMeta(key, tvshowid, seasons, id)
{    
    var request = '{"jsonrpc":"2.0","method":"'+ seasons +'","params":{"tvshowid":'+ tvshowid +
                  ',"properties":["playcount"]},"id":'+ id +'}';
    
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
            TransferData(json, cMETA);
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