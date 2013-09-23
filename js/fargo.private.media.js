/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.2
 *
 * File:    fargo.private.media.js
 *
 * Created on Aug 31, 2013
 * Updated on Sep 23, 2013
 *
 * Description: Fargo's jQuery and Javascript private media functions page.
 *
 */

//////////////////////////////////////////    Main Functions    ///////////////////////////////////////////

/*
 * Function:	ChangeModeMediaInterface
 *
 * Created on Aug 31, 2013
 * Updated on Sep 07, 2013
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
        case "Normal"    : $("#header_mode").text(mode).css({'color':'dodgerblue'});
                           ChangeMediaTableHoverColor("dodgerblue", "dodgerblue");                           
                           break;
                        
        case "Refresh"   : $("#header_mode").text(mode).css({'color':'#33BF38'});
                           ChangeMediaTableHoverColor("#33BF38", "#33BF38");
                           break;
                        
        case "Hide/Show" : $("#header_mode").text(mode).css({'color':'white'});
                           ChangeMediaTableHoverColor("white", "white");
                           break; 

        case "Delete"    : $("#header_mode").text(mode).css({'color':'#D82020'});
                           ChangeMediaTableHoverColor("#D82020", "#D82020");
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
 * Function:	SetMediaHandlerWithActions
 *
 * Created on Aug 31, 2013
 * Updated on Sep 23, 2013
 *
 * Description: Set and show the media info.
 * 
 * In:	-
 * Out:	Media Info
 *
 */
function SetInfoHandlerWithActions()
{   
    var mode  = GetState("mode");
    var media = GetState("media"); 
    var id = $(this).attr("class").match(/[0-9]+/);

    switch(mode)
    {
        case "Refresh"   : //alert ("Refresh");
                           ShowModePopup(mode, media, id);
                           break;
                         
        case "Hide/Show" : //alert ("Hide and Show");
                           HideOrShowMedia(media, id);
                           break;               

        case "Delete"    : alert ("Delete");
                           break;
        
        default          : ShowMediaInfo(media, id); //Mode is Normal.
                           break;
    }    
}

/*
 * Function:	ShowModePopup
 *
 * Created on Sep 07, 2013
 * Updated on Sep 21, 2013
 *
 * Description: Show the action popup with the yes/no buttons.
 * 
 * In:	mode, media, id
 * Out:	Action Popup
 *
 */
function ShowModePopup(mode, media, id)
{
    $.ajax
    ({
        url: 'jsonfargo.php?action=info&media=' + media + '&id=' + id,
        async: false,
        dataType: 'json',
        success: function(json)
        {   
            $("#action_box .id").text(id);
            $("#action_box .xbmcid").text(json.media.xbmcid);
            $("#action_box .message").text("Do you want to refresh this " + ConvertMediaToSingular(media) + "?");
            
            // Show fanart.
            $("#action_thumb img").error(function(){
                $(this).attr('src', 'images/no_poster.jpg');
            })
            .attr('src', json.params.thumbs + '/' + json.media.xbmcid + '.jpg' + '?v=' + json.media.refresh);
    
            $("#action_title").text(json.media.title);

            if (media == "music") 
            {
                $("#action_wrapper").height(116);
                $("#action_thumb").height(100);
                $("#action_thumb img").height(100);
            }
            else 
            {
                $("#action_wrapper").height(156);
                $("#action_thumb").height(140);
                $("#action_thumb img").height(140);
            }
            
            // Show popup.
            ShowPopupBox("#action_box", mode);
            SetState("page", "popup");    
        } // End succes.
    }); // End Ajax.       
}

/*
 * Function:	HideOrShowMedia
 *
 * Created on Sep 23, 2013
 * Updated on Sep 23, 2013
 *
 * Description: Hide or show media.
 * 
 * In:	media, id
 * Out:	-
 *
 */
function HideOrShowMedia(media, id)
{
    var $cell = $("#display_content .i" + id);
    
    $cell.toggleClass("hide");
    
    if ($cell.hasClass('hide')) {
        HideOrShowMediaInFargo(media, id, true);
    }
    else {
        HideOrShowMediaInFargo(media, id, false);
    }
}

/*
 * Function:	HideOrShowMediaInFargo
 *
 * Created on Sep 23, 2013
 * Updated on Sep 23, 2013
 *
 * Description: Hide or show media in Fargo. Update 
 * 
 * In:	media, id, value
 * Out:	-
 *
 */
function HideOrShowMediaInFargo(media, id, value)
{
    $.ajax
    ({
        url: 'jsonfargo.php?action=hide&media=' + media + '&id=' + id + '&value=' + value,
        async: false,
        dataType: 'json',
        success: function(json)
        {   
            
        } // End succes.
    }); // End Ajax.       
}

/*
 * Function:	ClearActionBox
 *
 * Created on Sep 08, 2013
 * Updated on Sep 14, 2013
 *
 * Description: Clear the action box. Set back to initial values.
 *
 * In:	-
 * Out:	-
 *
 */
function ClearActionBox()
{
    var $action = $("#action_box");
    var $thumb  = $("#action_thumb");
    
    setTimeout(function() {
        $action.find(".id").text("");
        $("#action_box .xbmcid").text("");
        $action.find(".title").text("");
        $action.find(".message").html("<br/>");
        
        $("#transfer").html("<br/>");
        $("#ready").html("<br/>");
        
        $("#action_wrapper").removeAttr("style");
        $thumb.removeAttr("style");
        $thumb.children("img").removeAttr("style").attr("src", "");
        
        $("#action_title").html("&nbsp;");
        
        // Remove progressbar.
        if($action.find(".ui-progressbar").length != 0) {   
            $action.find(".progress").progressbar( "destroy" );
        }        
        $(".progress").toggleClass("progress progress_off");
        
        // Reset buttons.
        $(".yes").show();
        $(".retry").toggleClass("retry no");
        $(".cancel").toggleClass("cancel no");
        $(".no").text("No");
   
    }, 300);     
}
