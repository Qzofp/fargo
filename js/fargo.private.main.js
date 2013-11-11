/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.3
 *
 * File:    fargo.private.main.js
 *
 * Created on May 04, 2013
 * Updated on Nov 10, 2013
 *
 * Description: Fargo's jQuery and Javascript functions page when the user is logged in.
 *
 */

//////////////////////////////////////////    Main Functions    ///////////////////////////////////////////

/*
 * Function:	LoadFargoMedia
 *
 * Created on May 04, 2013
 * Updated on Nov 10, 2013
 *
 * Description: Load the media from Fargo with system.
 *
 * In:	media
 * Out:	Fargo's interactie main page.
 *
 */
function LoadFargoMedia(media)
{      
    var aOptions = ['Statistics', 'Settings', 'Library', 'Event Log', 'Credits', 'About'];
    //gSTATE.MEDIA = media;
    
    //SetState("title", "latest");
    SetState("title", "name_asc");
    SetState("mode", "Information");

    ChangeControlBar(media);
    ChangeSubControlBar(media);

    ShowMediaTable(gSTATE.PAGE, gSTATE.SORT);
    
    // The media info or zoom in click events.
    $("#display_content").on("click", "td", SetInfoZoomHandlerWithActions);
    $(".button").on("click", ".url", SetShowUrlHandler);

    // The media click events.
    $("#movies").on("click", {media:"movies"}, SetMediaHandler);
    $("#tvshows").on("click", {media:"tvshows"}, SetMediaHandler);
    $("#music").on("click", {media:"music"}, SetMediaHandler);
    $("#system").on("click", {media:"system", options:aOptions}, SetSystemHandler);
    
    // Options event.
    $("#display_system_left").on("click", ".option", SetOptionHandler);
    
    // Properties event.
    $("#display_system_right").on("mouseenter mouseleave", ".property", SetPropertyMouseHandler);
    $("#display_system_right").on("click", ".property .on", SetPropertyClickHandler);
    
    // Yes or retry button is pressed. Preform action
    $(".button").on("click", ".yes", SetActionHandler);
    $(".button").on("click", ".retry", SetActionHandler);
    
    // No button is pressed, close popup.
    $(".button").on("click", ".no", SetCloseHandler);
    
    // Cancel or finish import. 
    $(".button").on("click", ".cancel", SetCloseHandler);    
    
    // Manage (Show, Refresh, Import, Hide and Remove) click events.
    $("#modes").on("click", SetButtonsHandler);
    
    // Media type events (titles, sets, series, episodes, albums).
    $("#type").on("click", SetButtonsTypeHandler);
    $(".button").on("click", ".selection", SetShowButtonTypeHandler);    
    
    // Sort (title), Genres or Years click events.   
    $("#title").on("click", SetButtonsHandler);
    $("#genres").on("click", SetButtonsHandler);
    $("#years").on("click", SetButtonsHandler);
    $(".button").on("click", ".choice", SetShowButtonHandler);
            
    // Close popup.
    $("#mask, .close, .close_right").on("click", SetCloseHandler);
    
    // Logout event.
    $("#logout").on("click", SetLogoutHandler);
 
    // The next/prev page click events.
    $("#next").on("click", {action:"n"}, SetPageHandler);
    $("#prev").on("click", {action:"p"}, SetPageHandler);
            
    // Keyboard events.
    $(document).on("keydown", SetKeyHandler);
}

/*
 * Function:	ChangeProperty
 *
 * Created on May 27, 2013
 * Updated on Oct 27, 2013
 *
 * Description: Get option and update property value.
 *
 * In:	number, value
 * Out:	Updated property value
 *
 */
function ChangeProperty(number, value)
{
    var option = $('#display_system_left .dim').text();
    
    $.ajax({
        url: 'jsonfargo.php?action=property&option=' + option + '&number=' + number + '&value=' + value,
        dataType: 'json',
        success: function(json) 
        {    
            // Updated porperty value.
            
            // Log event...
            if (json.counter > 0) {
                LogEvent("Information", "Cleaned " + json.counter + " " + ConvertMedia(json.name) + " items.");
            }
            
        } // End Success.        
    }); // End Ajax;
}

/*
 * Function:	ChangeSubControlBar
 *
 * Created on May 09, 2013
 * Updated on Nov 10, 2013
 *
 * Description: Change the sub control bar for Movies, TV Shows, Music or System.
 *
 * In:	media
 * Out:	-
 *
 */
function ChangeSubControlBar(media)
{   
    var type = "";
    var $control = $("#control_sub");
    
    switch(media)
    {
        case "movies"  : type = "titles";
                         $control.stop().slideUp("slow", function() {
                            $("#logout").hide();
                            $("#modes, #type, #title, #genres, #years").show();
                            $("#type").text(cBUT.TITLES);
                            $control.slideDown("slow");
                         });
                         break;

        case "tvshows" : type = "tvtitles";
                         $control.stop().slideUp("slow", function() {
                            $("#logout").hide();
                            $("#modes, #type, #title, #genres, #years").show();
                            $("#type").text(cBUT.TITLES);
                            $control.slideDown("slow");
                         });                            
                         break;

        case "music"   : type = "albums";
                         $control.stop().slideUp("slow", function() {
                            $("#logout").hide();
                            $("#modes, #type, #title, #genres, #years").show();
                            $("#type").text(cBUT.ALBUMS);
                            $control.slideDown("slow");
                         }); 
                         break;
                           
        case "system" : $control.stop().slideUp("slow", function() {
                            $("#logout").show();
                            $("#modes, #type, #title, #genres, #years").hide(); 
                            $control.slideDown("slow");
                        });                        
                        break;
    }    
    
    SetState("type", type);    
    
    
/*    $control.stop().slideUp("slow", function()
    {
        switch(media)
        {
            case "movies"  : $("#logout").hide();
                             $("#modes, #type, #title, #genres, #years").show();
                             $("#type").text(cBUT.TITLES);
                             break;

            case "tvshows" : $("#logout").hide();
                             $("#modes, #type, #title, #genres, #years").show();
                             $("#type").text(cBUT.SERIES);
                             break;

            case "music"   : $("#logout").hide();
                             $("#modes, #type, #title, #genres, #years").show();
                             $("#type").text(cBUT.ALBUMS);
                             break;
                           
            case "system" : $("#logout").show();
                            $("#modes, #type, #title, #genres, #years").hide();                            
                            break;                      
        }
        
        $control.slideDown("slow");
    }); */
}

/*
 * Function:	SetActionHandler
 *
 * Created on Sep 08, 2013
 * Updated on Oct 25, 2013
 *
 * Description: Perform action
 * 
 * In:	-
 * Out:	Action
 *
 */
function SetActionHandler()
{    
    var $popup = $(".popup:visible");
    var media = GetState("media");
    
    gTRIGGER.CANCEL = false;
    
    switch($popup.find(".title").text().split(" ")[0])
    {
        case "Refresh"   : SetStartRefreshHandler(media, $popup.find(".id").text(), $popup.find(".xbmcid").text());
                           //alert("Refresh Something! " + $popup.find(".id").text());                           
                           break;
                           
        case "Remove"    : SetRemoveHandler($popup.find(".id").text(), $popup.find(".xbmcid").text());
                           //alert("Remove Something! " + $popup.find(".id").text()); 
                           break;
                           
        case "Cleaning"  : SetCleanDatabaseHandler();
                           break;
                           
        case "Import"    : SetStartImportHandler(media, 1, true); //alert("Import Something!"); 
                           break;                                       
    }
}

/*
 * Function:	SetRemoveHandler
 *
 * Created on Oct 05, 2013
 * Updated on Oct 31, 2013
 *
 * Description: Remove media from Frago handler.
 * 
 * In:	id, xbmcid
 * Out:	-
 *
 */
function SetRemoveHandler(id, xbmcid)
{
    var $remove, finish;
    var media, name, title;
    
    finish  = 3 + Math.floor(Math.random() * 3);
    media   = GetState("media");
    
    // Turn progress on.
    //$(".progress_off").toggleClass("progress_off progress");

    // Reset and show progress bar.
    $remove = $("#action_box .progress");
    $remove.progressbar({value : 0});
    $remove.show();  
    
    // Remove media from Fargo.
    RemoveMediaFromFargo(media, id, xbmcid);
    
    media = ConvertMediaToSingular(media);
    name  = media.substr(0,1).toUpperCase() + media.substr(1); 
    DisplayCleaningMessage("Removing " + media + "...", name + " removed!", $remove, ".no", finish);
    
    setTimeout(function(){
        title = $("#action_title").text();
        LogEvent("Information", name + " " + title + " removed!");
    }, 800);    
    
    $(".yes").hide();
    $(".no").html('Cancel');
}

/*
 * Function:	SetCleanDatabaseHandler
 *
 * Created on Jun 10, 2013
 * Updated on Oct 31, 2013
 *
 * Description: Clean a database table (Library or Event Log).
 * 
 * In:	-
 * Out:	Table cleaned (truncate)
 *
 */
function SetCleanDatabaseHandler()
{
    var option, number,finish;
    var $clean;
    
    // Turn progress on.
    //$(".progress_off").toggleClass("progress_off progress");
    
    // Get option.
    option = $('#display_system_left .dim').text();
    // Get active row number.
    number = $(".property .on").closest("tr").index();
    
    $clean = $("#action_box .progress");
    finish = 5 + Math.floor(Math.random() * 5);

    // Reset and show progress bar.
    $clean.progressbar({value : 0});
    $clean.show();
    
    //$("#clean_box .message").css({"margin-bottom":"20px"});
    
    // Truncate table and delete pictures (posters, fanart and thumbs).
    ChangeProperty(number, "");
    
    if (option == "Library") 
    {
        DisplayCleaningMessage("Cleaning library...", "Library cleaned!", $clean, ".no", finish);
    }
    else 
    {
        DisplayCleaningMessage("Cleaning event log...", "Event log cleaned!", $clean, ".no", finish);   
        
        setTimeout(function(){
            $(".option.dim").addClass('on');  
            ShowProperty("Event Log");
        }, 500);
        
    }
    
    $(".yes").hide();
    $(".no").html('Cancel');
}

/*
 * Function:	DisplayCleaningMessage
 *
 * Created on Jun 15, 2013
 * Updated on Oct 25, 2013
 *
 * Description: Display cleaning message.
 *
 * In:	str1, str2, prg, btn, end
 * Out:	message
 *
 */
function DisplayCleaningMessage(str1, str2, prg, btn, end)
{   
    var i = 0; 
    var percent;
    
    $(".message").html(str1);
    var timer = setInterval(function()
    {
        if (!gTRIGGER.CANCEL)
        {
            percent = Math.round(i/end * 100);
            prg.progressbar({
                value : percent       
            });
            i++; 
	
            // End interval loop.
            if (i > end)
            {
                clearInterval(timer);
                $(".message").html(str2);
                $(btn).html("Finish");        
            }            
        }
        else {
           clearInterval(timer); 
        }
    }, 500);	
}

/*
 * Function:	SetPopupKeyHandler
 *
 * Created on Apr 28, 2013
 * Updated on Sep 09, 2013
 *
 * Description: Disable popup window.
 * 
 * In:	key
 * Out:	disable popup
 *
 */
function SetPopupKeyHandler(key)
{ 
    var popup = $("#action_box");
    
    if (key == 27) // ESC key
    {   
        // Close import popup.
        if (popup.is(":visible")) {
            SetImportCancelHandler();
        }
        
        SetMaskHandler();
    }  
}

/*
 * Function:	SetLogoutHandler
 *
 * Created on May 04, 2013
 * Updated on May 11, 2013
 *
 * Description: Log the user out.
 * 
 * In:	-
 * Out:	logout and return to the main page.
 *
 */
function SetLogoutHandler()
{    
    // Get username.
    var user = $("#header_txt span").text();
    
    LogEvent("Information", "User " + user + " has succesfully logged out.");   
    window.location='logout.php';
}