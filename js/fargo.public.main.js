/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.6
 *
 * File:    fargo.public.main.js
 *
 * Created on Apr 05, 2013
 * Updated on Jun 26, 2014
 *
 * Description: Fargo's jQuery and Javascript functions page when the user is logged out.
 *
 */

//////////////////////////////////////////    Main Functions    ///////////////////////////////////////////

/*
 * Function:	LoadFargoMedia
 *
 * Created on Apr 06, 2013
 * Updated on May 24, 2014
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
    
    SetState("title", "name_asc");
    $("#screen").text(cBUT.LIST);
    
    ChangeControlBar(media);
    ChangeSubControlBar(media);

    ShowMediaTable(gSTATE.PAGE, gSTATE.SORT);
    
    // On mouse move media event.
    $("#display_content, #info_left").on("mouseenter mouseleave", "td", SetScrollTitleHandler);
    $("#info_box").on("mouseenter mouseleave", ".title", SetScrollTitleHandler);    

    // The media info or zoom in click events.
    $("#display_thumb").on("click", "td", SetInfoZoomHandler);
    $("#display_list").on("click", "tr", SetInfoZoomHandler);
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
    
    // Media type events (titles, sets, series, albums).
    $("#type").on("click", SetButtonsTypeHandler);

    // Screen events (List or thumbnails view).
    $("#screen").on("click", SetButtonsScreenHandler);
    
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
    
    // Pagination (bullet) click event.
    $("#bullets").on("click", ".bullet", SetBulletHandler);    
            
    // Keyboard events.
    $(document).on("keydown", SetKeyHandler);
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
                            $("#login").hide();
                            $("#type, #screen, #title, #genres, #years").show();
                            $("#type").text(cBUT.SETS);
                            $control.slideDown("slow");
                         });
                         break;

        case "tvshows" : type = "tvtitles";
                         $control.stop().slideUp("slow", function() {
                            $("#login").hide();
                            $("#type, #screen, #title, #genres, #years").show();
                            $("#type").text(cBUT.SERIES);
                            $control.slideDown("slow");
                         });                            
                         break;

        case "music"   : type = "albums";
                         $control.stop().slideUp("slow", function() {
                            $("#login").hide();
                            $("#type, #screen, #title, #genres, #years").show();
                            $("#type").text(cBUT.SONGS);
                            $control.slideDown("slow");
                         }); 
                         break;
                           
        case "system" : $control.stop().slideUp("slow", function() {
                            $("#login").show();
                            $("#type, #screen, #title, #genres, #years").hide();
                            $control.slideDown("slow");
                        });                        
                        break;
    }    
    
    SetState("type", type);
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