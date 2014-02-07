/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.4
 *
 * File:    fargo.private.media.js
 *
 * Created on Aug 31, 2013
 * Updated on Feb 07, 2014
 *
 * Description: Fargo's jQuery and Javascript private media functions page.
 *
 */

//////////////////////////////////////////    Main Functions    ///////////////////////////////////////////

/*
 * Function:	ChangeModeMediaInterface
 *
 * Created on Aug 31, 2013
 * Updated on Sep 28, 2013
 *
 * Description: Change the interface (hover color and mode notifiction).
 * 
 * In:	mode, media
 * Out:	Changed Interface
 *
 */
function ChangeModeMediaInterface(mode, media)
{  
    media = ConvertMediaToSingular(media);
  
    switch (mode)
    {
        case "Information" : media = media.substr(0,1).toUpperCase() + media.substr(1);
                             mode = mode.substr(0,1).toLowerCase() + mode.substr(1);
                             $("#header_mode").text(media + " " + mode).css({'color':'dodgerblue'});
                             ChangeMediaTableHoverColor("dodgerblue", "dodgerblue");                           
                             break;
                        
        case "Refresh"     : $("#header_mode").text(mode + " " + media).css({'color':'#33BF38'});
                             ChangeMediaTableHoverColor("#33BF38", "#33BF38");
                             break;
                        
        case "Hide/Show"   : $("#header_mode").text(mode + " " + media).css({'color':'white'});
                             ChangeMediaTableHoverColor("white", "white");
                             break; 

        case "Remove"      : $("#header_mode").text(mode + " " + media).css({'color':'#D82020'});
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
 * Function:	SetInfoZoomHandlerWithActions
 *
 * Created on Aug 31, 2013
 * Updated on Feb 07, 2014
 *
 * Description: Set and show the media info.
 * 
 * In:	-
 * Out:	Media Info
 *
 */
function SetInfoZoomHandlerWithActions()
{   
    var mode  = GetState("mode");
    var media = GetState("media");
    var type  = GetState("type");
    var id    = $(this).attr("class");
    
    //var id = $(this).attr("class").match(/^[^\d]*(\d+)/g);
    //var id = $(this).attr("class").match(/\d+(_\d+)?/g);
        
    switch(mode)
    {
        case "Refresh"   : id = id.replace(/i/g, '').match(/^[^\d]*(\d+)/g);
                           ShowModePopup(mode, type, id);
                           break;
                         
        case "Hide/Show" : //id = id.replace(/i/g, '').match(/^[^\d]*(\d+)/g);
                           id = id.match(/\d+(_\d+)?/g);
                           HideOrShowMedia(type, id);
                           break;               

        case "Remove"    : id = id.replace(/i/g, '').match(/^[^\d]*(\d+)/g);
                           ShowModePopup(mode, type, id);
                           break;
        
        default          : id = id.match(/\d+(_\d+)?/g);
                           ShowInfoZoomMedia(media, type, id); //Mode is Normal.
                           break;
    }    
}

/*
 * Function:	ShowModePopup
 *
 * Created on Sep 07, 2013
 * Updated on Dec 23, 2013
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
        url: 'jsonfargo.php?action=popup&media=' + media + '&id=' + id,
        async: false,
        dataType: 'json',
        success: function(json)
        {   
            $("#action_box .id").text(id);
            $("#action_box .xbmcid").text(json.media.xbmcid);
            $("#action_box .message").text("Do you want to " +  mode.toLowerCase() + " this " + ConvertMediaToSingular(media) + "?");
            
            // Show fanart.
            $("#action_thumb img").error(function(){
                $(this).attr('src', 'images/no_poster.jpg');
            })
            .attr('src', json.params.thumbs + '/' + json.media.xbmcid + '.jpg' + '?v=' + json.media.refresh);
    
            $("#action_title").html(json.media.title);
            if (json.media.sub) {
                $("#action_sub").html(json.media.sub);
            }
            /*else {
                $("#action_sub").html("&nbsp;");
            }*/

            if (media == "albums") 
            {
                $("#action_wrapper").height(116);
                $("#action_thumb").height(100);
                $("#action_thumb img").height(100);
            }
            else if (media == "episodes") 
            {
                $("#action_wrapper").height(156);
                $("#action_thumb").css("margin-left", "-125px");
                $("#action_thumb").height(140).width(220);
                $("#action_thumb img").height(140).width(220);
            }
            else
            {
                $("#action_wrapper").height(156);
                $("#action_thumb").height(140);
                $("#action_thumb img").height(140);
            }
            
            // Show popup.
            media = ConvertMediaToSingular(media);
            ShowPopupBox("#action_box", mode + " " + media);
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
 * Updated on Nov 24, 2013
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
        url: 'jsonmanage.php?action=hide&media=' + media + '&id=' + id + '&value=' + value,
        async: false,
        dataType: 'json',
        success: function(json) {
        } // End succes.
    }); // End Ajax.       
}

/*
 * Function:	RemoveMediaFromFargo
 *
 * Created on Oct 05, 2013
 * Updated on Nov 24, 2013
 *
 * Description: Remove media from Frago.
 * 
 * In:	media, id, xbmcid
 * Out:	Removed media
 *
 */
function RemoveMediaFromFargo(media, id, xbmcid)
{    
    $.ajax
    ({
        url: 'jsonmanage.php?action=delete&media=' + media + '&id=' + id + '&xbmcid=' + xbmcid,
        async: false,
        dataType: 'json',
        success: function(json) {
        } // End succes.
    });     
}

/*
 * Function:	ClearActionBox
 *
 * Created on Sep 08, 2013
 * Updated on Dec 23, 2013
 *
 * Description: Clear the action box. Set back to initial values.
 *
 * In:	-
 * Out:	-
 *
 */
function ClearActionBox()
{
    var $action  = $("#action_box");
    var $thumb   = $("#action_thumb");
    var $wrapper = $("#action_wrapper");
    
    setTimeout(function() {        
        $action.find(".id").text("");
        $("#action_box .xbmcid").text("");
        $action.find(".title").text("");
        $action.find(".message").html("<br/>");
        
        $("#transfer").html("<br/>");
        $("#ready").html("<br/>");
        
        $wrapper.show();
        $wrapper.removeAttr("style");
        $thumb.removeAttr("style");
        $thumb.children("img").removeAttr("style").attr("src", "");
        $("#action_title").html("&nbsp;");
        $("#action_sub").html("&nbsp;");
           
        // Remove progressbar.
        $("#action_box .progress").show();
        if($action.find(".ui-progressbar").length != 0) {   
            $action.find(".progress").progressbar( "destroy" );
        }        
        
        // Reset buttons.
        $(".yes").show();
        $(".retry").toggleClass("retry no");
        $(".cancel").toggleClass("cancel no");
        $(".no").text("No");
   
    }, 300);     
}
