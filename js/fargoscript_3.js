/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.1
 *
 * File:    fargoscript.js
 *
 * Created on Apr 05, 2013
 * Updated on Apr 13, 2013
 *
 * Description: Fargo's jQuery and Javascript functions page.
 *
 */

// Global variables.
var global_media = "movies";
var global_page  = 1;
var global_sort  = "";
//var global_last  = 1; //last page



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
    $.ajax
    ({
        url: 'jsonfargo.php?action=init',
        dataType: 'json',
        success: function(json)
        {  
            var media = ChangeMedia(global_media);
            
            ShowMediaTable(media, global_page, json.column, global_sort);

            // Show Prev and Next buttons if there is more than 1 page.
            if (json.lastpage > 1)
            {
                $("#prev").css("visibility", "visible");
                $("#next").css("visibility", "visible");
            }            

            // The media click events.
            $("#movies").on("click", {media:"movies", column:json.column}, SetMediaHandler);
            $("#tvshows").on("click", {media:"tvshows", column:json.column}, SetMediaHandler);
            $("#music").on("click", {media:"music", column:json.column}, SetMediaHandler);
            $("#system").on("click", {media:"system", column:json.column}, SetMediaHandler);
 
            // The next/prev page click events.
            $("#next").on("click", {action:"n", column:json.column, offset:json.lastpage}, SetPageHandler);
            $("#prev").on("click", {action:"p", column:json.column, offset:json.lastpage}, SetPageHandler);
            
            // Keyboard events.
            $(document).on("keypress", {column:json.column}, SetKeyHandler);
        
        }  // End Succes.
    }); // End Ajax.   
}


/*
 * Function:	SetMediaHandler
 *
 * Created on Apr 13, 2013
 * Updated on Apr 13, 2013
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
   var column = event.data.column;
   global_page = 1;
   global_sort = "";
   
   global_media = ChangeMedia(media);   
   ShowMediaTable(global_media, global_page, column, global_sort);
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
    var column = event.data.column;
    var offset = event.data.offset;
    
    global_page = (action == "n") ? global_page + 1 : global_page - 1;
    
    //Check for min.
    if (global_page == 0) {
        global_page = offset;
    }  
                
    //Check for max.
    else if (global_page==offset+1) {
        global_page = 1;
    } 
        
    ShowMediaTable(global_media, global_page, column, global_sort);
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
    var column = event.data.column;
    global_page = 1;
    
    $("#sort").css("visibility", "visible");
    global_sort = String.fromCharCode(event.which).toUpperCase();
               
    ShowMediaTable(global_media, global_page, column, global_sort);
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
    
            if (json[0].id > 0)
            {
                html[i++] = '<table>';
     
                $.each(json, function(key, value)
                {                
                    if (j == 0) {
                        html[i++] = '<tr>';
                    }
                    else if ( j == column) {
                        html[i++] = '</tr>';
                        j = 0;
                    }
                
                    html[i++] = '<td><img src=\"images/thumbs/' + value.xbmcid + '.jpg\"/></br>' + value.title + '</td>';
                    j++;
                });
     
                html[i++] = '</table>';

            }
            
            $('#display_content')[0].innerHTML = html.join('');
            $('#sort').html(sort);            
        }
    });
}