/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.2
 *
 * File:    fargo.private.media.js
 *
 * Created on Aug 31, 2013
 * Updated on Aug 31, 2013
 *
 * Description: Fargo's jQuery and Javascript private media functions page.
 *
 */

//////////////////////////////////////////    Main Functions    ///////////////////////////////////////////

/*
 * Function:	ChangeModeMediaInterface
 *
 * Created on Aug 31, 2013
 * Updated on Aug 31, 2013
 *
 * Description: Change the interface (hover color and mode notifiction).
 * 
 * In:	mode
 * Out:	Changed Interface
 *
 */
function ChangeModeMediaInterface(mode)
{
    switch (mode)
    {
        case "Normal"    : ChangeMediaTableHoverColor("dodgerblue", "dodgerblue");
                           break;
                        
        case "Refresh"   : ChangeMediaTableHoverColor("#33BF38", "#33BF38");
                           break;
                        
        case "Hide/Show" : ChangeMediaTableHoverColor("white", "white");
                           break; 

        case "Delete"    : ChangeMediaTableHoverColor("#D82020", "#D82020");
                           break;                   
    }
}

/*
 * Function:	ChangeMediaTableHoverColor
 *
 * Created on Aug 31, 2013
 * Updated on Aug 31, 2013
 *
 * Description: Change the interface hover color.
 * 
 * In:	color_text, color_border
 * Out:	Changed hover color
 *
 */
function ChangeMediaTableHoverColor(color_text, color_border)
{
    $("#display_content td").hover(function() {
            $(this).css({'color':color_text, 'border-color':color_border});
            $(this).children("img").css({'border-color':color_border});
        }, 
        function() {
            $(this).css({'color':'', 'border-color': ''});
            $(this).children("img").css({'border-color':''});
    });
}

/*
 * Function:	SetMediaHandlerWithMode
 *
 * Created on Aug 31, 2013
 * Updated on Aug 31, 2013
 *
 * Description: Set and show the media info.
 * 
 * In:	-
 * Out:	Media Info
 *
 */
function SetInfoHandlerWithMode()
{   
    var mode  = GetState("mode");
    var media = GetState("media");
    var id    = $(this).children(":first-child").text();

    switch(mode)
    {
        case "Refresh"   : alert ("Refresh");
                           break;
                         
        case "Hide/Show" : alert ("Hide and Seek");
                           break;               

        case "Delete"    : alert ("Delete");
                           break;
        
        default          : ShowMediaInfo(media, id); //Mode is Normal.
                           break;
    }    
}