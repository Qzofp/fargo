/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.6
 *
 * File:    fargo.private.main.js
 *
 * Created on May 04, 2013
 * Updated on Jul 05, 2014
 *
 * Description: Fargo's jQuery and Javascript functions page when the user is logged in.
 *
 */

//////////////////////////////////////////    Main Functions    ///////////////////////////////////////////

/*
 * Function:	LoadFargoMedia
 *
 * Created on May 04, 2013
 * Updated on May 26, 2014
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
    
    InitStatus();
    
    SetState("mode", "Information");
    $("#screen").text(cBUT.LIST);
    
    ChangeControlBar(media);
    ChangeSubControlBar(media);

    ShowMediaTable(gSTATE.PAGE, gSTATE.SORT);
    
    // On mouse move media event.
    $("#display_content, #info_left").on("mouseenter mouseleave", "td", SetScrollTitleHandler);
    $("#info_box").on("mouseenter mouseleave", ".title", SetScrollTitleHandler);
    
    // The media info or zoom in click events.
    $("#display_thumb").on("click", "td", SetInfoZoomHandlerWithActions);
    $("#display_list").on("click", "tr", SetInfoZoomHandlerWithActions);    
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
    $(".button").on("click", ".yes", {retry:false}, SetActionHandler);
    $(".button").on("click", ".retry", {retry:true}, SetActionHandler);
    
    // No button is pressed, close popup.
    $(".button").on("click", ".no", SetCloseHandler);
    
    // Cancel or finish import. 
    $(".button").on("click", ".cancel", SetCloseHandler);
    
    // Manage (Show, Refresh, Import, Hide and Remove) click events.
    $("#modes").on("click", SetButtonsHandler);
    
    // Media type events (titles, sets, series, albums, songs).
    $("#type").on("click", SetButtonsTypeHandler);
    
    // Screen events (List or thumbnails view).
    $("#screen").on("click", SetButtonsScreenHandler);
    
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
    
    // Pagination (bullet) click event.
    $("#bullets").on("click", ".bullet", SetBulletHandler);
            
    // Keyboard events.
    $(document).on("keydown", SetKeyHandler);
}

/*
 * Function:	InitStatus
 *
 * Created on Dec 15, 2013
 * Updated on Dec 24, 2013
 *
 * Description: Initialize the status table.
 *
 * In:	-
 * Out:	Initialized status table.
 *
 */
function InitStatus()
{
    $.ajax({
        url: 'jsonmanage.php?action=init',
        async: false,
        dataType: 'json',
        success: function(json)
        {    
            if (Number(json.ready) > 0) {
                SetState("title", "latest");
            }
            else {
                SetState("title", "name_asc");
            }
        } // End Success.        
    }); // End Ajax;    
}

/*
 * Function:	ChangeProperty
 *
 * Created on May 27, 2013
 * Updated on Feb 11, 2014
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
        url: 'jsonmanage.php?action=property&option=' + option + '&number=' + number + '&value=' + value,
        dataType: 'json',
        success: function(json) 
        {    
            // Updated porperty value.
            
            // Log event...
            if (json.counter > 0) {
                LogEvent("Information", "Removed " + json.counter + " " + ConvertMedia(json.name) + " items.");
            }
            
        } // End Success.        
    }); // End Ajax;
}

/*
 * Function:	ChangeSubControlBar
 *
 * Created on May 09, 2013
 * Updated on Jun 26, 2014
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
                            $("#modes, #type, #screen, #title, #genres, #years").show();
                            $("#type").text(cBUT.SETS);
                            $control.slideDown("slow");
                         });
                         break;

        case "tvshows" : type = "tvtitles";
                         $control.stop().slideUp("slow", function() {
                            $("#logout").hide();
                            $("#modes, #type, #screen, #title, #genres, #years").show();
                            $("#type").text(cBUT.SERIES);
                            $control.slideDown("slow");
                         });                            
                         break;

        case "music"   : type = "albums";
                         $control.stop().slideUp("slow", function() {
                            $("#logout").hide();
                            $("#modes, #type, #screen, #title, #genres, #years").show();
                            $("#type").text(cBUT.SONGS);
                            $control.slideDown("slow");
                         }); 
                         break;
                           
        case "system" : $control.stop().slideUp("slow", function() {
                            $("#logout").show();
                            $("#modes, #type, #screen, #title, #genres, #years").hide(); 
                            $control.slideDown("slow");
                        });                        
                        break;
    }    
    
    SetState("type", type);    
}

/*
 * Function:	SetActionHandler
 *
 * Created on Sep 08, 2013
 * Updated on May 25, 2014
 *
 * Description: Perform action
 * 
 * In:	retry
 * Out:	Action
 *
 */
function SetActionHandler(event)
{    
    var $popup = $(".popup:visible");
    var media  = GetState("media");
    var type   = GetState("type");

    var step = 1;
    if (event.data.retry) {
        step = gTRIGGER.STEP1;
    }
    
    gTRIGGER.CANCEL = false;
    
    switch($popup.find(".title").text().split(" ")[0])
    {
        case cIMPORT.REFRESH : PrepareRefreshHandler(type, $popup);                        
                               break;
                           
        case cSYSTEM.REMOVE  : if ($popup.find(".title").text().split(" ")[1] == "library") {
                                 SetCleanDatabaseHandler();
                               }
                               else if ($popup.find(".title").text().split(" ")[1] == "event") {
                                 SetCleanDatabaseHandler();  
                               }
                               else {
                                 SetRemoveHandler($popup);  
                               }                       
                               break;
                           
        case cIMPORT.IMPORT  : LockImport(function() {
                                  SetStartImportHandler(media, step, event.data.retry); 
                               });
                               break;           
    }
}

/*
 * Function:	SetRemoveHandler
 *
 * Created on Oct 05, 2013
 * Updated on Jul 05, 2014
 *
 * Description: Remove media from Frago handler.
 * 
 * In:	$popup
 * Out:	-
 *
 */
function SetRemoveHandler($popup)
{
    var $remove, finish;
    var media, type, title;
    var fargoid = $popup.find(".id").text();
    
    finish = 3 + Math.floor(Math.random() * 3);
    media  = GetState("media");
    type   = GetState("type");
    
    // Reset and show progress bar.
    $remove = $("#action_box .progress");
    $remove.progressbar({value : 0});
    $remove.show();  
    
    // Remove media from Fargo.
    RemoveMediaFromFargo(type, fargoid);
    
    media = ConvertMediaToSingular(type);
    DisplayCleaningMessage("Removing " + media + "...", media + " removed!", $remove, ".no", finish);
    
    setTimeout(function(){
        
        if (type == "tracks" || type == "songs") 
        {
           title = $("#action_sub").html(); 
           media = "Track";
        }
        else {
           title = $("#action_title").html().replace("<br>", " ");  
        }        
        
        LogEvent('Information', media + ' "' + title + '" removed!');
    }, 800);    
    
    $(".yes").hide();
    $(".no").html('Cancel');
}

/*
 * Function:	SetCleanDatabaseHandler
 *
 * Created on Jun 10, 2013
 * Updated on Jul 05, 2014
 *
 * Description: Clean a database table (Library or Event Log).
 * 
 * In:	-
 * Out:	Table cleaned (truncate)
 *
 */
function SetCleanDatabaseHandler()
{
    var option, number, finish, art;
    var $clean;
    
    // Get option.
    option = $('#display_system_left .dim').text();
    
    // Get active row number.
    number = $(".property.on").closest("tr").index();  
    
    // Get art radio box. If box is on then remove art files from disc.
    art = $(".property.on").closest(".property").next().find(".xradio.on").length;
    
    $clean = $("#action_box .progress");
    finish = 5 + Math.floor(Math.random() * 5);

    // Reset and show progress bar.
    $clean.progressbar({value : 0});
    $clean.show();
    
    // Truncate table and delete pictures (posters, fanart and thumbs).
    ChangeProperty(number, art);
    
    if (option == "Library")
    {
        DisplayCleaningMessage(cSYSTEM.MESSAGE1 + "...", cSYSTEM.MESSAGE3, $clean, ".no", finish);
    }
    else 
    {
        DisplayCleaningMessage(cSYSTEM.MESSAGE4 + "...", cSYSTEM.MESSAGE5, $clean, ".no", finish);   
        
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