/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.1
 *
 * File:    fargo-import.js
 *
 * Created on Apr 14, 2013
 * Updated on Apr 14, 2013
 *
 * Description: Fargo's jQuery and Javascript functions page for the XBMC media import.
 *
 */


//////////////////////////////////////////    Main Functions    ///////////////////////////////////////////

// Global variables?!? jQuery sucks or I don't get it!!!
var global_online  = false;
var global_total   = 0;
var global_counter = 0;


/*
 * Function:	ImportTVShows
 *
 * Created on Apr 14, 2013
 * Updated on Apr 14, 2013
 *
 * Description: Import the TV Shows from XBMC.
 *
 * In:	-
 * Out:	Imported TV Shows
 *
 */
function ImportTVShows()
{
    var delta;
    
    GetXbmcValuesTVShows();
    
    delta = global_total - global_counter;
    
    // Test Values
    $("#online").html('XBMC is ' + global_online);
    $("#counter").html('Counter: ' + global_counter);
    $("#delta").html('Delta: ' + delta);
}


/*
 * Function:	GetFargoValues
 *
 * Created on Apr 14, 2013
 * Updated on Apr 14, 2013
 *
 * Description: Get the initial values from XBMC.
 *
 * In:	-
 * Out:	Media
 *
 */
function GetXbmcValuesTVShows()
{    
    $.ajax
    ({
        url: 'jsonxbmc.php?action=init&media=tvshows',
        async: false,
        dataType: 'json',
        success: function(json)
        {  
            global_online  = json.online;
            global_total   = json.total;
            global_counter = json.counter;
            
            //alert(global_total);
            
        }  // End Succes.
    }); // End Ajax.       
}