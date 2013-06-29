/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.1
 *
 * File:    fargo.media.js
 *
 * Created on Jun 08, 2013
 * Updated on Jun 28, 2013
 *
 * Description: Fargo's jQuery and Javascript common media functions page.
 *
 */

//////////////////////////////////////////    Main Functions    ///////////////////////////////////////////

/*
 * Function:	SetMediaHandler
 *
 * Created on Apr 13, 2013
 * Updated on Jun 28, 2013
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
   SetState("genre", "");
   
   $('#display_system').hide();
   $('#display_system_left').html(""); 
   $('#display_system_right').html("");   
   $('#display_content').show();
   
   global_media = ChangeControlBar(media);
   ChangeSubControlBar(media);
    
   $("#display_left").show();
   $("#display_right").show();
   
   GetFargoValues(global_media, global_sort);
   ShowMediaTable(global_media, global_page, global_column, global_sort);
}

/*
 * Function:	SetGenresHandler
 *
 * Created on Jun 27, 2013
 * Updated on Jun 28, 2013
 *
 * Description: Show the genres popup.
 * 
 * In:	-
 * Out:	Genres popup.
 *
 */
function SetGenresHandler()
{ 
    var buttons;
    var aGenres;
    var height_box;
    var $btns = $("#genres_box .button");
    var $scroll = $("#genres_box .slimScrollDiv");
    var media = GetState("media");

    // Returns global_list_fargo.
    GetFargoSortList("genres", media);
    aGenres = global_list_fargo;
    
    // Check if genres exits (not empty)
    if (aGenres) 
    {    
        // SlimScroll fix.
        if ($scroll.length) 
        {
            $scroll.css('height', '');
            $(".ui-draggable").css({'width':'0px', 'top':'0px'});
            $btns.css('height', '');
        }
    
        // Reset old buttons.
        $($btns).text("");

        // Show buttons
        buttons = "";
        $.each(aGenres, function(i, value) 
        {
            if (value == "") {
            value = "&nbsp;";
            }        
            buttons += '<button type=\"button\" class=\"genre\">' + value + '</button>';
       
        });
        $($btns).append(buttons);
    
        height_box = $("#genres_box").css('height');
        if (parseInt(height_box) >= 500)
        {    
            $($btns).slimScroll({
                height:478,
                color:'gray',
                alwaysVisible:true
            });
            
            // SlimScroll height fix.
            $scroll.css('height', '478px');
            $(".ui-draggable").css('width','7px');
            $btns.css('height', '478px');
            
            $($btns).children().last().css({"margin-bottom":"20px"});
        }
   
        ShowPopupBox("#genres_box", "Genres");
        SetState("page", "popup");
    }
}

/*
 * Function:	SetShowGenreHandler
 *
 * Created on Jun 27, 2013
 * Updated on Jun 28, 2013
 *
 * Description: Show the genre.
 * 
 * In:	-
 * Out:	Genre.
 *
 */
function SetShowGenreHandler()
{
    var $this = $(this);
    SetState("genre", $this.text());
    
    // Reset page and sort globals;
    global_page = 1;
    global_sort = "";
    $("#sort").css("visibility", "hidden");
    
    // Remove popup.
    SetMaskHandler();
    
    // Show media table.
    GetFargoValues(global_media, global_sort);
    ShowMediaTable(global_media, global_page, global_column, global_sort);    
}

/*
 * Function:	ChangeControlBar
 *
 * Created on Apr 08, 2013
 * Updated on May 09, 2013
 *
 * Description: Change the media on the control bar from Movies, TV Shows, Music or to System.
 *
 * In:	media
 * Out:	media
 *
 */
function ChangeControlBar(media)
{   
    var aMedia = ['movies','tvshows','music','system'];
 
    $("#sort").css("visibility", "hidden");
    
    // Set state media;
    SetState("media", media);
    
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
 * Function:	SetArrowHandler
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
    var offset = global_lastpage;
    
    global_page = (action == "n") ? global_page + 1 : global_page - 1;
    
    //Check for min.
    if (global_page == 0) {
        global_page = offset;
    }  
                
    //Check for max.
    else if (global_page==offset+1) {
        global_page = 1;
    } 
}

/*
 * Function:	SetKeyHandler
 *
 * Created on Apr 13, 2013
 * Updated on May 20, 2013
 *
 * Description: Determine on which page the key is pressed and then continue.
 * 
 * In:	event
 * Out:	Media
 *
 */
function SetKeyHandler(event)
{
    var key = event.charCode || event.keyCode || 0;
    var page = GetState("page");
    
    switch(page)
    {
        case "movies" : SetMainKeyHandler(key, event);
                        break;
                        
        case "tvshows": SetMainKeyHandler(key, event);
                        break;
                        
        case "music"  : SetMainKeyHandler(key, event);
                        break;                        
        
        case "system" : SetSystemKeyHandler(key, event);
                        break;  
        
        case "popup"  : SetPopupKeyHandler(key);
                        break;
    }
}

/*
 * Function:	SetMainKeyHandler
 *
 * Created on Apr 28, 2013
 * Updated on Apr 28, 2013
 *
 * Description: Set the key from the keyboard and show the media table.
 * 
 * In:	key, event
 * Out:	Media
 *
 */
function SetMainKeyHandler(key, event)
{  
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
 * Function:	ConvertMedia
 *
 * Created on May 10, 2013
 * Updated on May 20, 2013
 *
 * Description: Convert the media string to a more readable string.
 * 
 * In:	media
 * Out:	media

 *
 */
function ConvertMedia(media)
{
    switch (media)
    {
        case 'movies' : media = "Movies";
                        break;
                        
        case 'tvshows': media = "TV Shows";
                        break;
                        
        case 'music'  : media = "Music";
                        break;
                        
        case 'system' : media = "System";
                        break;
        
        default       : break;
    }
    
    return media;
}

/*
 * Function:	ShowMediaTable
 *
 * Created on Apr 05, 2013
 * Updated on Jun 28, 2013
 *
 * Description: Shows the media table.
 *
 * In:	media, page, column, sort
 * Out:	Media Table
 *
 */
function ShowMediaTable(media, page, column, sort)
{   
    var genre = GetState("genre");
    
    $.ajax
    ({
        url: 'jsonfargo.php?action=' + media + '&page=' + page + '&genre=' + genre + '&sort=' + sort,
        dataType: 'json',
        success: function(json)
        {  
            var i = 0, j = 0;
            var img, html = [];
            
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
                    
                    img = json.params.thumbs + '/' + value.xbmcid + '.jpg';    
                    html[i++] = '<td><img src="' + img + '"/></br>' + value.title + '</td>';
                    j++;
                });

                html[i++] = '</table>';
            }
            
            $('#display_content')[0].innerHTML = html.join('');
            
            // Show sort character.
            $('#sort').html(sort);            
        } // End success.
    }); // End Ajax. 
}