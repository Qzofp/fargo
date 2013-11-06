/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.3
 *
 * File:    fargo.public.main.js
 *
 * Created on Apr 05, 2013
 * Updated on Nov 03, 2013
 *
 * Description: Fargo's jQuery and Javascript functions page when the user is logged out.
 *
 */

//////////////////////////////////////////    Main Functions    ///////////////////////////////////////////

/*
 * Function:	LoadFargoMedia
 *
 * Created on Apr 06, 2013
 * Updated on Nov 03, 2013
 *
 * Description: Load the media from Fargo with login.
 *
 * In:	media
 * Out:	Fargo's interactie main page.
 *
 */
function LoadFargoMedia(media)
{    
    var aOptions = ['Statistics', 'Credits', 'About'];
    gSTATE.MEDIA = media;
    
    SetState("title", "Latest");
    
    ChangeControlBar(gSTATE.MEDIA);
    ChangeSubControlBar(gSTATE.MEDIA);

    //ShowMediaTable(media, gSTATE.PAGE, gSTATE.SORT);
    ShowMediaTable(gSTATE.PAGE, gSTATE.SORT);

    // The media info click events
    $("#display_content").on("click", "td", SetInfoHandler);
    $(".button").on("click", ".url", SetShowUrlHandler);

    // The media click events.
    $("#movies").on("click", {media:"movies"}, SetMediaHandler);
    $("#tvshows").on("click", {media:"tvshows"}, SetMediaHandler);
    $("#music").on("click", {media:"music"}, SetMediaHandler);
    $("#system").on("click", {media:"system", options:aOptions}, SetSystemHandler);
    
    // Option event.
    $("#display_system_left").on("click", ".option", SetOptionHandler);
    
    // Properties event.
    $("#display_system_right").on("mouseenter mouseleave", ".property", SetPropertyMouseHandler);
    
    // Media type events (titles, sets, series, episodes, albums).
    $("#type").on("click", SetButtonsTypeHandler);
    $(".button").on("click", ".selection", SetShowButtonTypeHandler);
    
    // Sort (title), Genres or Years click events.
    $("#title").on("click", SetButtonsHandler);
    $("#genres").on("click", SetButtonsHandler);
    $("#years").on("click", SetButtonsHandler);
    $(".button").on("click", ".choice", SetShowButtonHandler);
    
    // Login click event.
    $("#login").on("click", {type:"#login_box", title:"Login"}, SetPopupHandler);
    
    // Close popup.
    $("#mask, .close, .close_right").on("click", SetCloseHandler);
    
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
 * Updated on Nov 03, 2013
 *
 * Description: Change the sub control bar for Movies, TV Shows, Music or System.
 *
 * In:	media
 * Out:	type
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
                             $("#type, #title, #genres, #years").show();
                             $("#type").text(cBUT.TITLES);
                             break;

            case "tvshows" : $("#login").hide();
                             $("#type, #title, #genres, #years").show();
                             $("#type").text(cBUT.SERIES);
                             break;

            case "music"   : $("#login").hide();
                             $("#type, #title, #genres, #years").show();
                             $("#type").text(cBUT.ALBUMS);
                             break;
                           
            case "system" : $("#login").show();
                            $("#type, #title, #genres, #years").hide();
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