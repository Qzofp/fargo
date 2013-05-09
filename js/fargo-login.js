/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.1
 *
 * File:    fargo-login.js
 *
 * Created on Apr 05, 2013
 * Updated on May 08, 2013
 *
 * Description: Fargo's jQuery and Javascript functions page for the user interface with the login option.
 *
 */

//////////////////////////////////////////    Main Functions    ///////////////////////////////////////////

// Global variables?!? jQuery sucks or I don't get it!!!
var global_media = "movies";
var global_page  = 1;
var global_sort  = "";

var global_lastpage = 1; //last page
var global_column   = 0;
var global_popup    = false;

/*
 * Function:	LoadFargoMedia
 *
 * Created on Apr 06, 2013
 * Updated on May 06, 2013
 *
 * Description: Load the media from Fargo with login.
 *
 * In:	-
 * Out:	Media
 *
 */
function LoadFargoMedia()
{      
    var media = ChangeMedia(global_media);
    var title = "";
    
    GetFargoValues(global_media, global_sort);
    ShowMediaTable(media, global_page, global_column, global_sort);

    // The media click events.
    $("#movies").on("click", {media:"movies"}, SetMediaHandler);
    $("#tvshows").on("click", {media:"tvshows"}, SetMediaHandler);
    $("#music").on("click", {media:"music"}, SetMediaHandler);
    $("#system").on("click", {media:"system"}, SetSystemHandler);
      
    // Login click event.
    $("#login").on("click", {title:title}, SetPopupHandler);
    $("#mask, .close_right").on("click", SetMaskHandler);
    
    // Login validation event.
    $(".button").on("click", SetLoginValidateHandler);
 
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
 * Updated on May 07, 2013
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

   global_page = 1;
   global_sort = "";
   
   global_media = ChangeMedia(media);
   
   $("#display_left").show();
   $("#display_right").show();
   
   // Set state media;
   $("#state_media").text(media);
   
   GetFargoValues(global_media, global_sort);
   ShowMediaTable(global_media, global_page, global_column, global_sort);
}


/*
 * Function:	SetSystemHandler
 *
 * Created on May 04, 2013
 * Updated on May 05, 2013
 *
 * Description: Show the system page with minimum options.
 * 
 * In:	event
 * Out:	Media
 *
 */
function SetSystemHandler(event)
{            
   var media  = event.data.media;  

   global_page = 1;
   global_sort = "";
   
   global_media = ChangeMedia(media);
   
   $("#display_left").hide();
   $("#display_right").hide();  
   $("#control_sub").slideDown("slow");
   
   $('#display_content')[0].innerHTML = "";
      
   //GetFargoValues(global_media, global_sort)
   //ShowMediaTable(global_media, global_page, global_column, global_sort);
}


/*
 * Function:	ChangeMedia
 *
 * Created on Apr 08, 2013
 * Updated on May 05, 2013
 *
 * Description: .
 *
 * In:	media
 * Out:	media
 *
 */
function ChangeMedia(media)
{   
    var aMedia = ['movies','tvshows','music','system'];
    
    $("#sort").css("visibility", "hidden");
    $("#control_sub").slideUp("slow");
    
    id = '#' + media;    
    $(id).addClass("on");   
    
    $.each(aMedia, function(key, value) 
    {
        if (value != media) 
        {
            id = '#' + value;
            $(id).removeClass("on");
        }
    });
   
    return media;
}


/*
 * Function:	SetLoginValidateHandler
 *
 * Created on May 04, 2013
 * Updated on May 04, 2013
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
    var data = 'username='+ username + '&password='+ password;
    
    $.ajax
    ({
        type: "POST",
        url: "login.php",
        data: data,
        cache: false,
        success: function(result)
        {
            
            //alert(result);
            
            if(result){
                window.location='index.php';
            }
            else {
                $("#password").val("");
            }
        } // End succes.
  }); // End Ajax.
}