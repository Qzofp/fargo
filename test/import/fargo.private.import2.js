/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.1
 *
 * File:    fargo.private.import.js
 *
 * Created on Jul 14, 2013
 * Updated on Jul 22, 2013
 *
 * Description: Fargo's jQuery and Javascript functions page for the XBMC media import.
 *
 */

//////////////////////////////////////////    Main Functions    ///////////////////////////////////////////

// Global variables?!? jQuery sucks or I don't get it!!!
var global_total_fargo = 0;
var global_total_xbmc  = 0;
var global_cancel      = false;
//var global_setting_fargo = "busy";

/*
 * Function:	StartImport
 *
 * Created on Jul 14, 2013
 * Updated on Jul 14, 2013
 *
 * Description: Start the media from XBMC.
 *
 * In:	-
 * Out:	Imported media
 *
 */
function TestImport()
{
    $("#import").on("click", {media:"movies"}, SetImportHandler);
}

/*
 * Function:	SetImportHandler
 *
 * Created on Jul 14, 2013
 * Updated on Jul 22, 2013
 *
 * Description: Set the import handler, show the import popup box and start import.
 * 
 * In:	media
 * Out:	title
 *
 */
function SetImportHandler(event)
{  
    // Reset media status and get fargo media counter.
    $.ajax({
        url: 'jsonfargo.php?action=reset&media=' + event.data.media,
        async: false,
        dataType: 'json',
        success: function(json)
        {    
            var start, end;
            var offset = 10;
            var timer, i = 0;
            
            // Check if XBMC is online and transfer XMBC media counter (total).
            ImportCounter(event.data.media);
            
            // Get XBMC media counter from Fargo.
            timer = setInterval(function()
            {
                // Check if iframe from ImportCounter finished loading.
                if ($("#ready").text() == "true")
                {
                    // Returns global_total_fargo and global_total_xbmc;
                    GetXbmcAndFargoCounters(event.data.media);
                    start = global_total_fargo;
                    end   = global_total_xbmc;
                    
                    if (end > 0 || i > 3)
                    {
                        if (end > 0 && end > start) {
                            StartImport(event.data.media, start, end, offset);
                            //alert("Online, start import.");  
                        }
                        else if (end >= 0) {
                            // Import is ready...
                            alert("Ready... no new media!");                            
                        }
                        else {
                            // XBMC is offline...
                            alert("Offline!!");   
                        }
                        
                        clearInterval(timer);
                    }
                }
                i++;
                
            }, 1000); // End timer
            
        } // End Success.        
    }); // End Ajax;
}

/*
 * Function:	StartImport
 *
 * Created on Jul 22, 2013
 * Updated on Jul 22, 2013
 *
 * Description: Control and Import the media transfered from XBMC.
 *
 * In:	media, start, end, offset
 * Out:	Imported media
 *
 */
function StartImport(media, start, end, offset)
{
    var busy = true;
        
    // Import media process.
    ImportMedia(media, start, offset);
    start += offset;
    (function setImportTimer() {
            
        //alert("timer");
        if (global_cancel || start >= end)  {
            alert("Import ended!");
            return; // End Import.
        }

        // Check if iframe from ImportMedia finished loading.
        if ($("#ready").text() == "true")
        {
            if (busy == false)
            {    
                busy = true; // stop import.
                ImportMedia(media, start, offset);        
                start += offset;
            }
        }
        
        setTimeout(setImportTimer, 1000);
            
    }()); // End setImportTimer.   
        
    // Check status.
    var status = setInterval(function()
    {
        if (global_cancel || start >= end)  {
            clearInterval(status);
            alert("Status check ended!");
        }        
        
        // Returns global_total_fargo.
        GetFargoCounter(media);
        if (global_total_fargo == start) {
            busy = false; // resume import.
        }
        
    }, 900);    
    
    
}

/*
 * Function:	ImportCounter
 *
 * Created on Jul 22, 2013
 * Updated on Jul 22, 2013
 *
 * Description: Import the media counter transfered from XBMC.
 *
 * In:	media
 * Out:	Imported media counter
 *
 */
function ImportCounter(media)
{
    var $result = $("#result");
    var $ready  = $("#ready");  
    
    var url    = "http://192.168.219.129:8080/fargo/transfer.html?action=counter&media=" + media;
    var iframe = '<iframe src="' + url + '" onload="IframeReady()"></iframe>';
    
    // Reset values.
    $ready.text("false");    
    $result.text("");
    
    // Run transfer data in iframe.
    $result.append(iframe); 
    
    // Generates time-out and runs IframeReady function if onload in the iframe succeeds or fails.
    CheckIframeReady($ready, 3);
}

/*
 * Function:	ImportMedia
 *
 * Created on Jul 20, 2013
 * Updated on Jul 22, 2013
 *
 * Description: Import the media transfered from XBMC.
 *
 * In:	media, start, offset
 * Out:	Imported media
 *
 */
function ImportMedia(media, start, offset)
{
    var $result = $("#result");
    var $ready  = $("#ready");    
    var url    = "http://192.168.219.129:8080/fargo/transfer.html?action=" + media + "&start=" + start + 
                 "&offset=" + offset;
    var iframe = '<iframe src="' + url + '" onload="IframeReady()"></iframe>';   
    
    // Reset values.
    $ready.text("false");    
    $result.text("");
    // Run transfer data in iframe.
    $result.append(iframe); 
    
    // Generates time-out and runs ImportReady function if onload in the iframe fails.
    CheckIframeReady($ready, 3);
    
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
