/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.1
 *
 * File:    fargo.private.main.js
 *
 * Created on May 04, 2013
 * Updated on Jun 15, 2013
 *
 * Description: Fargo's jQuery and Javascript functions page when the user is logged in.
 *
 */

//////////////////////////////////////////    Main Functions    ///////////////////////////////////////////

// Global variables?!? jQuery sucks or I don't get it!!!
var global_media = "";
var global_page  = 1;
var global_sort  = "";

var global_lastpage = 1; //last page
var global_column   = 0;

var global_cancel = false;

// Media total.
var global_total_fargo = 0;
var global_total_xbmc  = 0;

// Fargo setting 
var global_setting_fargo;

// Ajax requests.
var global_status_request;
var global_import_request;

/*
 * Function:	LoadFargoMedia
 *
 * Created on May 04, 2013
 * Updated on Jun 15, 2013
 *
 * Description: Load the media from Fargo with system.
 *
 * In:	media
 * Out:	Fargo's interactie main page.
 *
 */
function LoadFargoMedia(media)
{      
    global_media = media;
    
    ChangeControlBar(global_media);
    ChangeSubControlBar(global_media);
    $("#control_sub").show();
        
    GetFargoValues(media, global_sort);
    ShowMediaTable(media, global_page, global_column, global_sort);

    // The media click events.
    $("#movies").on("click", {media:"movies"}, SetMediaHandler);
    $("#tvshows").on("click", {media:"tvshows"}, SetMediaHandler);
    $("#music").on("click", {media:"music"}, SetMediaHandler);
    $("#system").on("click", {media:"system"}, SetFullSystemHandler);
    
    // Options event.
    $("#display_system_left").on("click", ".option", SetOptionHandler);
    
    // Properties event.
    $("#display_system_right").on("mouseenter mouseleave", ".property", SetPropertyMouseHandler);
    $("#display_system_right").on("click", ".property .on", SetPropertyClickHandler);
    
    // Clean database (library or event log) event.
    $(".button").on("click", ".yes", CleanDatabaseHandler);
    
    // Import click event.
    $("#import").on("click", SetImportHandler);
    $(".button").on("click", ".retry", SetImportHandler);
    
    // Cancel or finish import. 
    $(".button").on("click", ".cancel", SetCloseHandler);
    
    // No button is pressed, close popup.
    $(".button").on("click", ".no", SetCloseHandler);
            
    // Close popup.
    $("#mask, .close_right").on("click", SetCloseHandler);
    
    // Logout event.
    $("#logout").on("click", SetLogoutHandler);
 
    // The next/prev page click events.
    $("#next").on("click", {action:"n"}, SetPageHandler);
    $("#prev").on("click", {action:"p"}, SetPageHandler);
            
    // Keyboard events.
    $(document).on("keydown", SetKeyHandler);
}

/*
 * Function:	SetMediaHandler
 *
 * Created on Apr 13, 2013
 * Updated on Jun 02, 2013
 *
 * Description: Set the media and show the media table.
 * 
 * In:	event
 * Out:	Media
 *
 */
function SetMediaHandler(event)
{            
   var media  = event.data.media;  
   SetState("page", media);

   global_page = 1;
   global_sort = "";
   
   $('#display_system').hide();
   $('#display_system_left').html("");  
   $('#display_system_right').html("");  
   $('#display_content').show();
   
   global_media = ChangeControlBar(media);
   ChangeSubControlBar(media);
   
   $("#display_left").show();
   $("#display_right").show();
   
   $("#control_sub").slideDown("slow");
   
   GetFargoValues(global_media, global_sort);
   ShowMediaTable(global_media, global_page, global_column, global_sort);
}

/*
 * Function:	SetFullSystemHandler
 *
 * Created on May 04, 2013
 * Updated on Jun 10, 2013
 *
 * Description: Show the full system page with all the options.
 * 
 * In:	event
 * Out:	Media
 *
 */
function SetFullSystemHandler(event)
{            
   var media  = event.data.media;  
   var aOptions = ['Statistics', 'Settings', 'Library', 'Event Log', 'Credits', 'About'];
   var last = $('#display_system_left .option').last().text();
   SetState("page", media);

   global_page = 1;
   global_sort = "";
   
   global_media = ChangeControlBar(media);
   ChangeSubControlBar(media);
   
   $("#display_left").hide();
   $("#display_right").hide();
   
   $('#display_content').hide();
   $('#display_content').html("");
   $('#display_system').show();

   if ($('#display_system_left #fargo').length == false) {
       $('#display_system_left').append('<div id=\"fargo\">Qzofp\'s Fargo</div>');
   }
   if(last != aOptions[aOptions.length-1])
   {
        $.each(aOptions, function(key, option) 
        {
            $('#display_system_left').append('<div class="option">' + option + '</div>');
        });  
   }
   $('#display_system_left .option').removeClass('on');
   $('#display_system_left .option').first().addClass('on'); 
   
   ShowProperty("Statistics");
   
   $("#control_sub").slideDown("slow");
}

/*
 * Function:	ChangeProperty
 *
 * Created on May 27, 2013
 * Updated on Jun 15, 2013
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
        //async: false,
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
 * Updated on May 10, 2013
 *
 * Description: Change the sub control bar for Movies, TV Shows, Music or System.
 *
 * In:	media
 * Out:	-
 *
 */
function ChangeSubControlBar(media)
{
    var txt_media = ConvertMedia(media);
    
    $("#control_sub").stop().slideUp("slow", function(){
        if (media != 'system') 
        {
            $("#import").css( "display", "inline").text("Import " + txt_media);
            $("#logout").hide();
        }
        else
        {
            $("#import").hide();
            $("#logout").show();        
        }
    });    
}

/*
 * Function:	CleanDatabaseHandler
 *
 * Created on Jun 10, 2013
 * Updated on Jun 15, 2013
 *
 * Description: Clean a database table (Library or Event Log).
 * 
 * In:	-
 * Out:	Table cleaned (truncate)
 *
 */
function CleanDatabaseHandler()
{
    // Get option.
    var option = $('#display_system_left .dim').text();
    // Get active row number.
    var number = $(".property .on").closest("tr").index();  
    
    // Truncate table
    ChangeProperty(number, "");
    
    if (option == "Library") 
    {
        DisplayMessage("Cleaning library...", "Library cleaned!", ".no", 1);
    }
    else 
    {
        DisplayMessage("Cleaning event log...", "Event log cleaned!", ".no", 1);   
        
        setTimeout(function(){
            $(".option.dim").addClass('on');  
            ShowProperty("Event Log");
        }, 500);
        
    }
    
    $(".yes").hide();
    $(".no").html('Ok');

}

/*
 * Function:	SetCloseHandler
 *
 * Created on Jun 09, 2013
 * Updated on Jun 15, 2013
 *
 * Description: Close import or other popup window.
 * 
 * In:	-
 * Out:	disable mask and popup
 *
 */
function SetCloseHandler()
{
    var popup = $("#import_box");
        
    // Abort import.
    if (popup.is(":visible")) {
        SetImportCancelHandler();
    }
    
    // Close popup.
    SetMaskHandler();
}

/*
 * Function:	SetPopupKeyHandler
 *
 * Created on Apr 28, 2013
 * Updated on Jun 15, 2013
 *
 * Description: Disable popup window.
 * 
 * In:	key
 * Out:	disable popup
 *
 */
function SetPopupKeyHandler(key)
{ 
    var popup = $("#import_box");
    
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
    // get username.
    var user = $("#header_txt span").text();
    
    LogEvent("Information", "User " + user + " has succesfully logged out.");   
    window.location='logout.php';
}