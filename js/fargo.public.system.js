/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.1
 *
 * File:    fargo.public.system.js
 *
 * Created on Apr 05, 2013
 * Updated on Jun 08, 2013
 *
 * Description: Fargo's jQuery and Javascript functions page for the user interface with the login option.
 *
 */

//////////////////////////////////////////    Main Functions    ///////////////////////////////////////////

// Global variables?!? jQuery sucks or I don't get it!!!
var global_media = "";
var global_page  = 1;
var global_sort  = "";

var global_lastpage = 1; //last page
var global_column   = 0;

// Fargo setting 
var global_setting_fargo;

/*
 * Function:	LoadFargoMedia
 *
 * Created on Apr 06, 2013
 * Updated on May 12, 2013
 *
 * Description: Load the media from Fargo with login.
 *
 * In:	media
 * Out:	Fargo's interactie main page.
 *
 */
function LoadFargoMedia(media)
{      
    global_media = media;
    
    ChangeControlBar(global_media);
    var title = "";
    
    GetFargoValues(media, global_sort);
    ShowMediaTable(media, global_page, global_column, global_sort);

    // The media click events.
    $("#movies").on("click", {media:"movies"}, SetMediaHandler);
    $("#tvshows").on("click", {media:"tvshows"}, SetMediaHandler);
    $("#music").on("click", {media:"music"}, SetMediaHandler);
    $("#system").on("click", {media:"system"}, SetSystemHandler);
    
    // Option event.
    $("#display_system_left").on("click", ".option", SetOptionHandler);
    
    // Properties event.
    $("#display_system_right").on("mouseenter mouseleave", ".property", SetPropertyMouseHandler);    
      
    // Login click event.
    $("#login").on("click", {title:title}, SetPopupHandler);
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
   $("#control_sub").stop().slideUp("slow");
    
   $("#display_left").show();
   $("#display_right").show();
   
   GetFargoValues(global_media, global_sort);
   ShowMediaTable(global_media, global_page, global_column, global_sort);
}


/*
 * Function:	SetSystemHandler
 *
 * Created on May 04, 2013
 * Updated on Jun 08, 2013
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
   var aOptions = ['Statistics', 'Credits', 'About'];
   var last = $('#display_system_left .option').last().text();
   SetState("page", media);
   
   global_page = 1;
   global_sort = "";
   
   global_media = ChangeControlBar(media);
   
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
   
   $("#control_sub").stop().slideUp("slow");
   $("#control_sub").slideDown("slow");
}


/*
 * Function:	SetLoginValidateHandler
 *
 * Created on May 04, 2013
 * Updated on Jun 08, 2013
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
    GetFargoSetting("Hash"); //Returns global_setting_fargo
    password = CryptoJS.MD5(password + global_setting_fargo);
    
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