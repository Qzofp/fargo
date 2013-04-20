/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.1
 *
 * File:    fargo-import.js
 *
 * Created on Apr 14, 2013
 * Updated on Apr 20, 2013
 *
 * Description: Fargo's jQuery and Javascript functions page for the XBMC media import.
 *
 */


//////////////////////////////////////////    Main Functions    ///////////////////////////////////////////

/*
 * Function:	ImportTVShows
 *
 * Created on Apr 14, 2013
 * Updated on Apr 20, 2013
 *
 * Description: Import the TV Shows from XBMC.
 *
 * In:	-
 * Out:	Imported TV Shows
 *
 */
function ImportTVShows()
{
    var counter = 0;
    var media = "tvshows";

    ShowStatus(counter, media);
}


/*
 * Function:	ShowStatus
 *
 * Created on Apr 17, 2013
 * Updated on Apr 20, 2013
 *
 * Description: Show the import status.
 *
 * In:	counter, media
 * Out:	Status
 *
 */
function ShowStatus(counter, media)
{
    $.ajax({
        url: 'jsonxbmc.php?action=status&media=' + media,
        dataType: 'json',
        success: function(json) 
        {
            var ready = false;
            var online = '';
        
            if (json.online) 
            {
               online = 'Online!';                        
               if (json.delta > 0)
               {
                   StartImport(media);
                   $("#progress").html('Movie ID: ' + json.xbmcid);
               }   
               else 
               {
                   $("#progress").html('Gereed!');
                    ready = true;
               }    
            }
            else 
            {
               online = 'Offline!';
            }

            $("#online").html('XBMC is ' + online);
            $("#counter").html(counter);
            $("#delta").html('Delta: ' + json.delta);
                    
            if (json.id > 0 && json.online)
            {
               $("#thumb").html('<img src= "' + json.thumbs + '/'+ json.xbmcid +'.jpg" />');
               $("#title").html(json.title);
            } 
            
            // If ready exit progress, else retry.
            if (ready) 
            {
                return;
            }
            else 
            {
                setTimeout(function() {
                    ShowStatus(counter, media); 
                },1000);
            }
            
            counter++;
            
        } // End Success.
    }); // End Ajax.
 }


/*
 * Function:	StartImport
 *
 * Created on Apr 17, 2013
 * Updated on Apr 20, 2013
 *
 * Description: Start the import process.
 *
 * In:	media
 * Out:	processed media.
 *
 */
function StartImport(media) 
{
    $.ajax({
        url: 'jsonxbmc.php?action=import&media=' + media,
        dataType: 'json',
        success: function(json) {
            //alert(json.counter);
        } // End Success.
        
    }); // End Ajax;
}
