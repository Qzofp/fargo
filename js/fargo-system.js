/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.1
 *
 * File:    fargo-system.js
 *
 * Created on May 04, 2013
 * Updated on Jun 01, 2013
 *
 * Description: Fargo's jQuery and Javascript functions page for the user interface with the system option.
 *
 */

//////////////////////////////////////////    Main Functions    ///////////////////////////////////////////

// Global variables?!? jQuery sucks or I don't get it!!!
var global_media = "";
var global_page  = 1;
var global_sort  = "";

var global_lastpage = 1; //last page
var global_column   = 0;
//var global_popup    = false;

// Media total.
var global_total_fargo = 0;
var global_total_xbmc  = 0;

var global_cancel = false;

// Ajax requests.
var global_status_request;
var global_import_request;

/*
 * Function:	LoadFargoMedia
 *
 * Created on May 04, 2013
 * Updated on May 26, 2013
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
    $("#display_system_right").on("mouseenter mouseleave", ".set", SetPropertyMouseHandler);
    
    // Import click event.
    $("#import").on("click", SetImportHandler);
    $(".button").on("click", ".retry", SetImportHandler);
    
    // Cancel or finish import.
    $(".button").on("click", ".cancel", SetImportCancelHandler);
    $("#mask, .close_right").on("click", SetImportCancelHandler);
    
    // Logout event.
    $("#logout").on("click", SetLogoutHandler);
 
    // The next/prev page click events.
    $("#next").on("click", {action:"n"}, SetPageHandler);
    $("#prev").on("click", {action:"p"}, SetPageHandler);
            
    // Keyboard events.
    $(document).on("keydown", SetKeyHandler);
}

/*
 * Function:	SetImportHandler
 *
 * Created on May 08, 2013
 * Updated on May 20, 2013
 *
 * Description: Set the import handler, show the import popup box and start import.
 * 
 * In:	media
 * Out:	title
 *
 */
function SetImportHandler()
{
    var title = "";
    var media = GetState("media"); // Get state media. 
  
    title = "Import " + ConvertMedia(media);    
    
    //alert($(this).html());
    ShowPopupBox(title);
    SetState("page", "popup");
    //global_popup = true;
     
    $(".retry").toggleClass("retry cancel");
    
    // Initialize status popup box.
    $(".message").html("Connecting...");
    AdjustImageSize(media);
    $(".cancel").html("Cancel");
     
    // Start Import
    global_cancel = false;  
    setTimeout(function(){
        ImportMedia(media);
    }, 1000);

}

/*
 * Function:	SetImportCancelHandler
 *
 * Created on May 09, 2013
 * Updated on May 19, 2013
 *
 * Description: Set the import handler, Cancel or finish the import.
 * 
 * In:	media
 * Out:	title
 *
 */
function SetImportCancelHandler()
{
    var button = $(".cancel").text();
    var media = GetState("media");
    
    // Abort pending ajax request.
    if(typeof global_status_request !== 'undefined') {
        global_status_request.abort();
    }
    
    global_cancel = true;
    SetMaskHandler();
    
    if (button == "Finish") {
        window.location='index.php?media=' + media;
    }
}

/*
 * Function:	SetMediaHandler
 *
 * Created on Apr 13, 2013
 * Updated on May 20, 2013
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
 * Updated on May 26, 2013
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
   var aOptions = ['Statistics', 'Settings', 'Library', 'Event Log', 'Run Tests', 'Credits', 'About'];
   var last = $('#display_system_left .option').last().text();
   SetState("page", media);

   global_page = 1;
   global_sort = "";
   
   global_media = ChangeControlBar(media);
   ChangeSubControlBar(media)
   
   $("#display_left").hide();
   $("#display_right").hide();
   
   $('#display_content').hide();
   $('#display_content').html("");
   $('#display_system').show();
   
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
 * Function:	SetPropertyMouseHandler
 *
 * Created on May 26, 2013
 * Updated on Jun 01, 2013
 *
 * Description: Show property on hover and update value when changed.
 *
 * In:	event
 * Out:	-
 *
 */
function SetPropertyMouseHandler(event)
{
    var row = $(this);
    var rows = $("#display_system_right .set");
    var current = GetState("property");
    var number, value;
    
    // Dim option.
    $(".option.on").toggleClass("on dim");
    
    // Remove "on" from all rows.
    rows.removeClass("on");
    rows.prev().children().removeClass("on");
    rows.children().removeClass("on");
    
    // Show input text field.
    if (event.type == "mouseenter") 
    {
        // Turn active row "on".
        row.addClass("on");
        row.prev().children().addClass("on");
        row.children().addClass("on");
        
        value = row.children().last().text();
        
        if (row.children().first().text() == "Password") {
            row.children().last().html('<input type="password" value="' + value +'">');
            $("input").focus().setCursorPosition(value.length);
        }
        else {
            row.children().last().html('<input type="text" value="' + value +'">');
            $("input").focus().setCursorPosition(value.length);
        }
        
        // Set state.
        SetState("property", value);
    }
    else 
    {
        value = $("input").val();
        if (row.children().first().text() == "Password") {
            row.children().last().html("******");
        }
        else {
            row.children().last().html(value);
        }
        
        // Property has change, update value.
        if (current != value && current != "") 
        {
           number = row.closest("tr").index();
           ChangeProperty(number, value);
        }
        
        // Reset state.
        SetState("property", "");
    }  
}

/*
 * Function:	ChangeProperty
 *
 * Created on May 27, 2013
 * Updated on Jun 01, 2013
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
    
    //alert(option + " " + number + " " + value);
    
    $.ajax({
        url: 'jsonfargo.php?action=property&option=' + option + '&number=' + number + '&value=' + value,
        //async: false,
        dataType: 'json',
        success: function(json) 
        {    
            // Updated value.
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