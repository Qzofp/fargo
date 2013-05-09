/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.1
 *
 * File:    fargo-system.js
 *
 * Created on May 04, 2013
 * Updated on May 08, 2013
 *
 * Description: Fargo's jQuery and Javascript functions page for the user interface with the system option.
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
 * Created on May 04, 2013
 * Updated on May 08, 2013
 *
 * Description: Load the media from Fargo with system.
 *
 * In:	-
 * Out:	Media
 *
 */
function LoadFargoMedia()
{      
    var media = ChangeMedia(global_media);
    
    GetFargoValues(global_media, global_sort);
    ShowMediaTable(media, global_page, global_column, global_sort);

    $("#control_sub").show();
    $("#import").css( "display", "inline").text("Import Movies");

    // The media click events.
    $("#movies").on("click", {media:"movies"}, SetMediaHandler);
    $("#tvshows").on("click", {media:"tvshows"}, SetMediaHandler);
    $("#music").on("click", {media:"music"}, SetMediaHandler);
    $("#system").on("click", {media:"system"}, SetFullSystemHandler);
    
    // Import click event.
    $("#import").on("click", SetImportHandler);
    $("#mask, .close_right").on("click", SetMaskHandler);
    
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
 * Updated on May 08, 2013
 *
 * Description: Set the import handler and show the import popup box.
 * 
 * In:	media
 * Out:	title
 *
 */
function SetImportHandler()
{
    var title = "";
    var media = $("#state_media").text(); // Get state media. 
    switch (media)
    {
        case 'movies' : title = "Import Movies";
                        break;
                        
        case 'tvshows': title = "Import TV Shows";
                        break;
                        
        case 'music'  : title = "Import Music";
                        break;
        
        default       : break;
    }
    
     ShowPopupBox(title)
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
   
   $("#control_sub").slideDown("slow");
   
   GetFargoValues(global_media, global_sort);
   ShowMediaTable(global_media, global_page, global_column, global_sort);
}


/*
 * Function:	SetFullSystemHandler
 *
 * Created on May 04, 2013
 * Updated on May 05, 2013
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
    var txt_media = "";
    
    $("#sort").css("visibility", "hidden");
    
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
    
    switch (media)
    {
        case 'movies' : txt_media = "Movies";
                        break;
                        
        case 'tvshows': txt_media = "TV Shows";
                        break;
                        
        case 'music'  : txt_media = "Music";
                        break;
        
        default       : break;
    }
    
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
    
    return media;
}


/*
 * Function:	SetLogoutHandler
 *
 * Created on May 04, 2013
 * Updated on May 04, 2013
 *
 * Description: Log the user out.
 * 
 * In:	-
 * Out:	logout and return to the main page.
 *
 */
function SetLogoutHandler()
{    
    window.location='logout.php';
}
