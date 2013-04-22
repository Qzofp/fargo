/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.1
 *
 * File:    fargo-interface.js
 *
 * Created on Apr 05, 2013
 * Updated on Apr 22, 2013
 *
 * Description: Fargo's jQuery and Javascript functions page for the user interface.
 *
 */


//////////////////////////////////////////    Main Functions    ///////////////////////////////////////////

// Global variables?!? jQuery sucks or I don't get it!!!
var global_media = "movies";
var global_page  = 1;
var global_sort  = "";

var global_lastpage = 1; //last page
var global_column   = 0;

/*
 * Function:	LoadFargoMedia
 *
 * Created on Apr 06, 2013
 * Updated on Apr 13, 2013
 *
 * Description: Load the media from Fargo.
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

    // The media click events.
    $("#movies").on("click", {media:"movies"}, SetMediaHandler);
    $("#tvshows").on("click", {media:"tvshows"}, SetMediaHandler);
    $("#music").on("click", {media:"music"}, SetMediaHandler);
    $("#system").on("click", {media:"system"}, SetMediaHandler);
 
    // The next/prev page click events.
    $("#next").on("click", {action:"n"}, SetPageHandler);
    $("#prev").on("click", {action:"p"}, SetPageHandler);
            
    // Keyboard events.
    $(document).on("keydown", SetKeyHandler);
}

/*
 * Function:	GetFargoValues
 *
 * Created on Apr 13, 2013
 * Updated on Apr 13, 2013
 *
 * Description: Get the  initial values from Fargo.
 *
 * In:	media, sort
 * Out:	Media
 *
 */
function GetFargoValues(media, sort)
{    
    $.ajax
    ({
        url: 'jsonfargo.php?action=init&media=' + media + '&sort=' + sort,
        async: false,
        dataType: 'json',
        success: function(json)
        {  
            global_lastpage = json.lastpage;
            global_column   = json.column;
            
            // Show Prev and Next buttons if there is more than 1 page.
            if (global_lastpage > 1)
            {
                $("#prev").css("visibility", "visible");
                $("#next").css("visibility", "visible");
            }
            else 
            {
                $("#prev").css("visibility", "hidden");
                $("#next").css("visibility", "hidden");            
            }
            
        }  // End Succes.
    }); // End Ajax.       
}

/*
 * Function:	SetMediaHandler
 *
 * Created on Apr 13, 2013
 * Updated on Apr 20, 2013
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
   
   GetFargoValues(global_media, global_sort);
   ShowMediaTable(global_media, global_page, global_column, global_sort);
}


/*
 * Function:	SetPageHandler
 *
 * Created on Apr 13, 2013
 * Updated on Apr 13, 2013
 *
 * Description: Set the page and show the media table.
 * 
 * In:	event
 * Out:	Media
 *
 */
function SetPageHandler(event)
{
    var action = event.data.action;
    var offset = global_lastpage;
    
    //alert(action);
    
    global_page = (action == "n") ? global_page + 1 : global_page - 1;
    
    //Check for min.
    if (global_page == 0) {
        global_page = offset;
    }  
                
    //Check for max.
    else if (global_page==offset+1) {
        global_page = 1;
    } 
        
    ShowMediaTable(global_media, global_page, global_column, global_sort);
} 


/*
 * Function:	SetPageHandler2
 *
 * Created on Apr 13, 2013
 * Updated on Apr 13, 2013
 *
 * Description: Set the page and show the media table.
 * 
 * In:	event
 * Out:	Media
 *
 */
function SetArrowHandler(action)
{
    //var action = event.data.action;
    var offset = global_lastpage;
    
    //alert(global_page);
    
    global_page = (action == "n") ? global_page + 1 : global_page - 1;
    
    //Check for min.
    if (global_page == 0) {
        global_page = offset;
    }  
                
    //Check for max.
    else if (global_page==offset+1) {
        global_page = 1;
    } 
        
    //ShowMediaTable(global_media, global_page, global_column, global_sort);
} 


/*
 * Function:	SetKeyHandler
 *
 * Created on Apr 13, 2013
 * Updated on Apr 13, 2013
 *
 * Description: Set the key from the keyboard and show the media table.
 * 
 * In:	event
 * Out:	Media
 *
 */
function SetKeyHandler(event)
{
    var key = event.charCode || event.keyCode || 0;
       
    $("#sort").css("visibility", "visible");
    
    // key between aA and zZ.
    if (key >= 65 && key <= 90) 
    {
        global_page = 1; 
        global_sort = String.fromCharCode(event.which).toUpperCase();
    }
    // key between 0 and 9.
    else if (key >= 48 && key <= 57)
    {
        global_page = 1; 
        global_sort = String.fromCharCode(event.which);
    }   
    else if (key != 37 && key != 39)
    {
        global_page = 1; 
        global_sort = "";       
    }
    
    // The previous and next with the left and right arrow keys.
    if (key == 37)
    {
        SetArrowHandler("p");
    }
    else if (key == 39)
    {
        SetArrowHandler("n");
    }        
    
    if (global_sort == "") {
        $("#sort").css("visibility", "hidden");
    }
    
    GetFargoValues(global_media, global_sort);
    ShowMediaTable(global_media, global_page, global_column, global_sort);
}

/*
 * Function:	ChangeMedia
 *
 * Created on Apr 08, 2013
 * Updated on Apr 08, 2013
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
 * Function:	ShowMediaTable
 *
 * Created on Apr 05, 2013
 * Updated on Apr 13, 2013
 *
 * Description: Shows the media table.
 *
 * In:	media, page, column, sort
 * Out:	Media Table
 *
 */
function ShowMediaTable(media, page, column, sort)
{   
    $.ajax
    ({
        url: 'jsonfargo.php?action=' + media + '&page=' + page + '&sort=' + sort,
        dataType: 'json',
        success: function(json)
        {  
            var i = 0, j = 0, html = [];
            
            if (json.media[0].id > 0)
            {
                html[i++] = '<table>';
     
                $.each(json.media, function(key, value)
                {                
                    if (j == 0) {
                        html[i++] = '<tr>';
                    }
                    else if ( j == column) {
                        html[i++] = '</tr>';
                        j = 0;
                    }
                
                    html[i++] = '<td><img src=\"' + json.params.thumbs + '/' + value.xbmcid + '.jpg\"/></br>' + value.title + '</td>';
                    j++;
                });

                html[i++] = '</table>';
            }
            
            $('#display_content')[0].innerHTML = html.join('');
            $('#sort').html(sort);            
        } // End success.
    }); // End Ajax. 
}