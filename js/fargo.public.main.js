/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.1
 *
 * File:    fargo.public.main.js
 *
 * Created on Apr 05, 2013
 * Updated on Jun 27, 2013
 *
 * Description: Fargo's jQuery and Javascript functions page when the user is logged out.
 *
 */

//////////////////////////////////////////    Main Functions    ///////////////////////////////////////////

// Global variables?!? jQuery sucks or I don't get it!!!
var global_media = "";
var global_page  = 1;
var global_sort  = "";

var global_lastpage = 1; //last page
var global_column   = 0;

// Fargo globals.
var global_setting_fargo;
var global_list_fargo;

/*
 * Function:	LoadFargoMedia
 *
 * Created on Apr 06, 2013
 * Updated on Jun 27, 2013
 *
 * Description: Load the media from Fargo with login.
 *
 * In:	media
 * Out:	Fargo's interactie main page.
 *
 */
function LoadFargoMedia(media)
{    
    var system_options = ['Statistics', 'Credits', 'About'];
    global_media = media;
    
    ChangeControlBar(global_media);
    ChangeSubControlBar(global_media);
 
    GetFargoValues(media, global_sort);
    ShowMediaTable(media, global_page, global_column, global_sort);

    // The media click events.
    $("#movies").on("click", {media:"movies"}, SetMediaHandler);
    $("#tvshows").on("click", {media:"tvshows"}, SetMediaHandler);
    $("#music").on("click", {media:"music"}, SetMediaHandler);
    $("#system").on("click", {media:"system", options:system_options}, SetSystemHandler);
    
    // Option event.
    $("#display_system_left").on("click", ".option", SetOptionHandler);
    
    // Properties event.
    $("#display_system_right").on("mouseenter mouseleave", ".property", SetPropertyMouseHandler);    
    
    // Genres click events.
    $("#genres").on("click", SetGenresHandler);
    $(".button").on("click", ".genre", SetShowGenreHandler);
    
    // Login click event.
    $("#login").on("click", {type:"#login_box", title:"Login"}, SetPopupHandler);
    
    // Close popup.
    $("#mask, .close_right").on("click", SetMaskHandler);
    
    // Login validation event.
    $(".login").on("click", SetLoginValidateHandler);
 
    // The next/prev page click events.
    $("#next").on("click", {action:"n"}, SetPageHandler);
    $("#prev").on("click", {action:"p"}, SetPageHandler);
            
    // Keyboard events.
    $(document).on("keydown", SetKeyHandler);
}

/*
 * Function:	ChangeSubControlBar
 *
 * Created on May 09, 2013
 * Updated on Jun 26, 2013
 *
 * Description: Change the sub control bar for Movies, TV Shows, Music or System.
 *
 * In:	media
 * Out:	-
 *
 */
function ChangeSubControlBar(media)
{   
    var $control = $("#control_sub");
    
    $control.stop().slideUp("slow", function()
    {
        switch(media)
        {
            case "movies"  : $("#login").hide();
                             $("#genres").show();
                             break;

            case "tvshows" : $("#login").hide();
                             $("#genres").show();
                             break;

            case "music"   : $("#login").hide();
                             $("#genres").show();
                             break;
                           
            case "system" : $("#login").show();
                            $("#genres").hide();
                            break;               
        }
        
        $control.slideDown("slow");
    });
}

/*
 * Function:	SetPopupKeyHandler
 *
 * Created on Apr 28, 2013
 * Updated on Jun 27, 2013
 *
 * Description: Disable popup window.
 * 
 * In:	key
 * Out:	disable popup
 *
 */
function SetPopupKeyHandler(key)
{ 
    //var popup = $(".popup#login_box");
    
    if (key == 27) { // ESC key
        SetMaskHandler();
    }    
}

/*
 * Function:	SetLoginValidateHandler
 *
 * Created on May 04, 2013
 * Updated on Jun 09, 2013
 *
 * Description: Validate login user and password.
 * 
 * In:	-
 * Out:	.
 *
 */
function SetLoginValidateHandler()
{
    var username=$('#username').val();
    var password=$('#password').val();
    
    // Hash password.
    password = HashPassword(password);
    
    var data = 'username='+ username + '&password='+ password;
    
    $.ajax
    ({
        type: "POST",
        url: "login.php",
        data: data,
        cache: false,
        success: function(result)
        {
            if(result)
            {
                LogEvent("Information", "User " + username + " succesfully logged in.");                
                window.location='index.php';
            }
            else 
            {
                LogEvent("Warning", "User " + username + " failed to logged in!");
                $("#password").val("");
            }
        } // End succes.
  }); // End Ajax.
}