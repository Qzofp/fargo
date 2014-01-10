/*
 * Title:   Fargo Transfer
 * Author:  Qzofp Productions
 * Version: 0.4
 *
 * File:    fargo.transfer.bulk.js
 *
 * Created on Jan 10, 2014
 * Updated on Jan 10, 2014
 *
 * Description: Fargo Transfer Bulk jQuery and Javascript functions page.
 *
 */

//////////////////////////////////////////    Main Functions    ///////////////////////////////////////////

/*
 * Function:	BulkTransfer
 *
 * Created on Jan 10, 2014
 * Updated on Jan 10, 2014
 *
 * Description: Transfers bulk data from XBMC to Fargo.
 * 
 * In:	-
 * Out:	-
 *
 */
function BulkTransfer()
{
    var aRequest = GetUrlParameters();
    
    switch(aRequest.action)
    {
        case "movies"   : // libMovies -> library id = 1. 
                          BulkTransferMedia(aRequest.key, aRequest.counter, "GetMovies", 1);
                          break;
            
        case "sets"     : 
                          break;  
            
        case "tvshows"  : 
                          break;
                         
        case "seasons"  : 
                          break;
                         
        case "episodes" : // libTVShows -> library id = 5.
                          BulkTransferMedia(aRequest.key, aRequest.counter, "GetEpisodes", 5);
                          break;                         
        
        case "music"    : 
                          break;            
    }
}

/*
 * Function:	BulkTransferMedia
 *
 * Created on jan 10, 2014
 * Updated on Jan 10, 2014
 *
 * Description: Transfer movies from XBMC to Fargo.
 * 
 * In:	key, counter, media, id
 * Out:	Transfered movies.
 *
 */
function BulkTransferMedia(key, counter, media, id)
{
    var start = counter * Number(cBULKMAX);
    var end   = ++counter * Number(cBULKMAX);
    
    var request = '{"jsonrpc":"2.0","method":"VideoLibrary.'+ media +'","params":{"properties":["lastplayed"],'+
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
            TransferData(json, cBULK);
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

