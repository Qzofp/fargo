/*
 * Title:   Fargo Transfer
 * Author:  Qzofp Productions
 * Version: 0.4
 *
 * File:    fargo.common.js
 *
 * Created on Jan 10, 2014
 * Updated on Jan 17, 2014
 *
 * Description: Fargo Transfer jQuery and Javascript common functions page.
 *
 */

/////////////////////////////////////////    Common Functions    //////////////////////////////////////////

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

/*
 * Function:	TransferData
 *
 * Created on Jul 14, 2013
 * Updated on Jan 17, 2014
 *
 * Description: Call ajax and transfers the data from XBMC to Fargo.
 * 
 * In:	data, file
 * Out:	Transfered data.
 *
 */
function TransferData(data, file)
{
   var url = "http://" + cFARGOSITE + "/include/" + file + "/?"+Math.random();
    
    // Send the images to PHP to save it on the server.
   var request = $.ajax({
        type: "POST",
        url: url,
        data: data,
        cache: false
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