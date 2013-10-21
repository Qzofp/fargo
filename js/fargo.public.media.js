/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.3
 *
 * File:    fargo.public.media.js
 *
 * Created on Jun 08, 2013
 * Updated on Oct 19, 2013
 *
 * Description: Fargo's jQuery and Javascript common media functions page.
 *
 */

//////////////////////////////////////////    Main Functions    ///////////////////////////////////////////

/*
 * Function:	SetInfoHandler
 *
 * Created on Jul 05, 2013
 * Updated on Sep 22, 2013
 *
 * Description: Set and show the media info.
 * 
 * In:	-
 * Out:	Media Info
 *
 */
function SetInfoHandler()
{   
    var media = GetState("media");
    //var id = $(this).children(":first-child").text();
    var id = $(this).attr("class").match(/[0-9]+/);
    
    ShowMediaInfo(media, id);
}

/*
 * Function:	ShowMediaInfo
 *
 * Created on Aug 31, 2013
 * Updated on Aug 31, 2013
 *
 * Description: Show the media info.
 * 
 * In:	media, id
 * Out:	Media Info
 *
 */
function ShowMediaInfo(media, id)
{   
    switch(media)
    {
        case "movies"  : ShowMovieInfo(id);
                         break;

        case "tvshows" : ShowTVShowInfo(id);
                         break;
                    
        case "music"   : ShowAlbumInfo(id);
                         break;        
    } 
}

/*
 * Function:	ShowMovieInfo
 *
 * Created on Jul 05, 2013
 * Updated on Sep 14, 2013
 *
 * Description: Show the movie info.
 * 
 * In:	id
 * Out:	Movie Info
 *
 */
function ShowMovieInfo(id)
{
    $.ajax
    ({
        url: 'jsonfargo.php?action=info&media=movies' + '&id=' + id,
        async: false,
        dataType: 'json',
        success: function(json)
        {   
            var buttons = "";            
            var aInfo = [{left:"Director:", right:json.media.director},
                         {left:"Writer:",   right:json.media.writer},
                         {left:"Studio:",   right:json.media.studio},
                         {left:"Genre:",    right:json.media.genre},
                         {left:"Year:",     right:json.media.year},
                         {left:"Runtime:",  right:json.media.runtime},
                         {left:"Rating:",   right:json.media.rating},
                         {left:"Tagline:",  right:json.media.tagline},
                         {left:"Country:",  right:json.media.country}];
            
            // Show info.
            ShowInfoTable(aInfo);
            
            // Change space size for movie info and fanart.
            $("#info_left").css("margin-right", 460);
            $("#info_right").toggleClass("fanart_space", true).toggleClass("cover_space", false);            
            
            // Show fanart.
            $("#info_fanart img").error(function(){
                $(this).attr('src', 'images/no_fanart.jpg');
            })
            .attr('src', json.params.fanart + '/' + json.media.xbmcid + '.jpg' + '?v=' + json.media.refresh)
            .css("width", 450); 
            
            // Show media flags. Path and filename is generated by PHP (jsonfargo.php).
            $("#info_video").html(json.media.video);
            $("#info_audio").html(json.media.audio);
            $("#info_aspect").html(json.media.aspect);
            $("#info_mpaa").html(json.media.mpaa);           
            
            // Show plot.
            $("#info_plot").text("Plot");
            $("#info_plot_text").text(json.media.plot).slimScroll({
                height:'120px',
                color:'gray',
                alwaysVisible:true
            });
            
            // Show buttons (imdb, refresh, trailer).
            if (json.media.imdbnr){
               buttons += '<button type="button" class="url" value="' + json.media.imdbnr + '">IMDb</button>';
            }
            
            if (json.media.trailer){
               buttons += '<button type="button" class="url" value="' + json.media.trailer + '">Trailer</button>';
            }
            
            $("#info_box .button").append(buttons);
            
            // Show popup.
            ShowPopupBox("#info_box", json.media.title);
            SetState("page", "popup");    
        } // End succes.
    }); // End Ajax.       
}

/*
 * Function:	ShowTVShowInfo
 *
 * Created on Jul 09, 2013
 * Updated on Sep 07, 2013
 *
 * Description: Show the TV show info.
 * 
 * In:	id
 * Out:	TV Show Info
 *
 */
function ShowTVShowInfo(id)
{
    $.ajax
    ({
        url: 'jsonfargo.php?action=info&media=tvshows' + '&id=' + id,
        async: false,
        dataType: 'json',
        success: function(json)
        {   
            var btnname, pattern;
            var buttons = "";            
            var aInfo = [{left:"Episodes:", right:json.media.episode},
                         {left:"Aired:",    right:json.media.premiered},
                         {left:"Genre:",    right:json.media.genre},
                         {left:"Studio:",   right:json.media.studio},
                         {left:"Year:",     right:json.media.year},
                         {left:"Rating:",   right:json.media.rating}];
            
            // Show info.
            ShowInfoTable(aInfo);
            
            // Change space size for tv show info and fanart.
            $("#info_left").css("margin-right", 460);
            $("#info_right").toggleClass("fanart_space", true).toggleClass("cover_space", false);
            
            // Show fanart.
            $("#info_fanart img").error(function(){
                $(this).attr('src', 'images/no_fanart.jpg');
            })
            .attr('src', json.params.fanart + '/' + json.media.xbmcid + '.jpg')
            .css("width", 450);
            
            // Show plot.
            $("#info_plot").text("Plot");
            $("#info_plot_text").text(json.media.plot).slimScroll({
                height:'120px',
                color:'gray',
                alwaysVisible:true
            });
            
            // Show buttons (imdb, refresh, trailer).
            if (json.media.imdbnr)
            {
               pattern = /thetvdb/;            
               if(pattern.exec(json.media.imdbnr)) {
                   btnname = "TheTVDB";
               }
               else {
                   btnname = "AniDB";
               }
                
               buttons += '<button type="button" class="url" value="' + json.media.imdbnr + '">' + btnname + '</button>';
            }
            
            $("#info_box .button").append(buttons);
            
            // Show popup.
            ShowPopupBox("#info_box", json.media.title);
            SetState("page", "popup");    
        } // End succes.
    }); // End Ajax.       
}

/*
 * Function:	ShowAlbumInfo
 *
 * Created on Jul 10, 2013
 * Updated on Sep 07, 2013
 *
 * Description: Show the album info.
 * 
 * In:	id
 * Out:	Album Info
 *
 */
function ShowAlbumInfo(id)
{
    $.ajax
    ({
        url: 'jsonfargo.php?action=info&media=music' + '&id=' + id,
        async: false,
        dataType: 'json',
        success: function(json)
        {              
            var aInfo = [{left:"Artist:", right:json.media.artist},
                         {left:"Genre:",  right:json.media.genre},
                         {left:"Rating:", right:json.media.rating},
                         {left:"Moods:",  right:json.media.mood},
                         {left:"Style:",  right:json.media.style},
                         {left:"Themes:", right:json.media.theme},
                         {left:"Label:",  right:json.media.albumlabel},
                         {left:"Year:",   right:json.media.year}];
            
            // Show info.
            ShowInfoTable(aInfo);
            
            // Change space size for album info and cover.
            $("#info_left").css("margin-right", 290);
            $("#info_right").toggleClass("fanart_space", false).toggleClass("cover_space", true);
            
            // Show fanart.
            $("#info_fanart img").error(function(){
                $(this).attr('src', 'images/no_fanart.jpg');
            })
            .attr('src', json.params.covers + '/' + json.media.xbmcid + '.jpg')
            .css("width", 280);
  
            // Show plot.
            $("#info_plot").text("Description");
            $("#info_plot_text").text(json.media.description).slimScroll({
                height:'120px',
                color:'gray',
                alwaysVisible:true
            });
            
            // Show popup.
            ShowPopupBox("#info_box", json.media.title);
            SetState("page", "popup");    
        } // End succes.
    }); // End Ajax.        
}

/*
 * Function:	ShowInfoTable
 *
 * Created on Jul 06, 2013
 * Updated on Jul 06, 2013
 *
 * Description: Show the info.
 * 
 * In:	aInfo
 * Out:	Info table
 *
 */
function ShowInfoTable(aInfo)
{
    var table;
    var $info = $("#info_left");
    
    // Reset info
    $info.html("");
    
    table = '<table>';    
    $.each(aInfo, function(){
        table += '<tr>';
        table += '<td class="left">' + this.left + '</td>';
        table += '<td>' + this.right + '</td>';
        table += '</tr>';
    });   
    table += '</table>';
 
    $info.append(table);
}

/*
 * Function:	ClearInofBox
 *
 * Created on Sep 01, 2013
 * Updated on Sep 01, 2013
 *
 * Description: Clear the info box. Set back to initial values.
 *
 * In:	-
 * Out:	-
 *
 */
function ClearInfoBox()
{
    var $info      = $("#info_box");
    var $plot_text = $("#info_plot_text");
    
    setTimeout(function() {
        $info.find(".title").text("");
        
        $("#info_left").removeAttr("style").text("");
        $("#info_fanart img").removeAttr("style").attr('src', 'images/no_fanart.jpg');
        
        // Reset media flags. Aren't used for TV Shows.
        $("#info_video").html("");
        $("#info_audio").html("");
        $("#info_aspect").html("");
        $("#info_mpaa").html("");           
             
        $plot_text.text("");
    
        //Remove slimScroll.
        if($info.find(".slimScrollDiv").length != 0)
        {   
            $plot_text.unwrap();
            $plot_text.removeAttr("style");
            $info.find(".slimScrollBar").remove();
            $info.find(".slimScrollRail").remove();
        }
        
        // Reset buttons (imdb, refresh, trailer).
        $info.find(".url").remove();
        
    }, 300); 
}

/*
 * Function:	SetShowUrlHandler
 *
 * Created on Jul 09, 2013
 * Updated on Jul 09, 2013
 *
 * Description: Show URL in a new web page.
 * 
 * In:	-
 * Out:	New web page
 *
 */
function SetShowUrlHandler()
{
    var url = $(this).attr("value");
    
    window.open(url, "_blank"); 
}

/*
 * Function:	SetMediaHandler
 *
 * Created on Apr 13, 2013
 * Updated on Jun 30, 2013
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

   // Initialize parameters.
   global_page = 1;
   global_sort = "";
   SetState("title", "Latest");
   SetState("genre", "");
   SetState("year", "");
   
   $('#display_system').hide();
   $('#display_system_left').html(""); 
   $('#display_system_right').html("");   
   $('#display_content').show();
   
   global_media = ChangeControlBar(media);
   ChangeSubControlBar(media);
    
   $("#display_left").show();
   $("#display_right").show();
   
   //GetFargoValues(global_media, global_sort);
   ShowMediaTable(global_media, global_page, global_sort);
}

/*
 * Function:	SetTitleHandler
 *
 * Created on Jun 30, 2013
 * Updated on Jun 30, 2013
 *
 * Description: Show the title popup.
 * 
 * In:	-
 * Out:	Title popup.
 *
 */
function SetTitleHandler()
{
    var buttons;
    var aList = ["Latest", "Oldest", "Ascending", "Descending"];
    var $btns = $("#buttons_box .button");
    
    // Reset old buttons.
    $($btns).text("");

    // Show buttons
    buttons = "";
    $.each(aList, function(i, value) {
        buttons += '<button type=\"button\" class=\"test\">' + value + '</button>';
    });
    $($btns).append(buttons);    
    
    ShowPopupBox("#buttons_box", "Title");
    SetState("page", "popup");    
}

/*
 * Function:	SetButtonsHandler
 *
 * Created on Jun 27, 2013
 * Updated on Sep 28, 2013
 *
 * Description: Show the buttons (Title, Genres or Years) popup.
 * 
 * In:	-
 * Out:	Buttons popup.
 *
 */
function SetButtonsHandler()
{ 
    var $this = $(this);
    var buttons = "";
    var aList;
    var height_box;
    var $btns = $("#buttons_box .button");
    var $scroll = $("#buttons_box .slimScrollDiv");
    var media = GetState("media");
    
    SetState("choice", $this.text());

    if ($this.text() == "Title"){
        aList = ["Latest", "Oldest", "Ascending", "Descending"];  
    }
    else if ($this.text() == "Manage") {
        aList = ["Information", "Hide/Show", "Import", "Refresh", "Remove"]; 
    }
    else
    {
        // Returns global_list_fargo.
        GetFargoSortList($this.text(), media);
        aList = global_list_fargo;
        buttons = '<button type=\"button\" class=\"choice\">- Show All -</button>';
    }
    
    // Check if list exits (not empty)
    if (aList) 
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
        $.each(aList, function(i, value) 
        {
            if (value != "") {
                buttons += '<button type=\"button\" class=\"choice\">' + value + '</button>';
            }       
        });
        $($btns).append(buttons);
    
        height_box = $("#buttons_box").css('height');
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
   
        ShowPopupBox("#buttons_box", $this.text());
        SetState("page", "popup");
    }
}

/*
 * Function:	SetShowButtonHandler
 *
 * Created on Jun 27, 2013
 * Updated on Sep 28, 2013
 *
 * Description: Show the genre.
 * 
 * In:	-
 * Out:	Genre.
 *
 */
function SetShowButtonHandler()
{
    var state, media; 
    var $this = $(this);
    var choice = GetState("choice");
    
    switch (choice)
    {
        case "Title"  : state = "title";
                        break;
                       
        case "Genres" : state = "genre";
                        break;
                       
        case "Years"  : state = "year";
                        break;   
                    
        case "Manage" : state = "mode";
                        break;
    }
    
    ClearButtonsBox();
    
    // Remove popup.
    SetMaskHandler();
    
    // Show all genres or years.
    if ($this.text() == "- Show All -") {
        SetState(state, "");
    }
    else {
        SetState(state, $this.text());
    }
    
    if ($this.text() == "Import") 
    {
        media = GetState("media");  
        setTimeout(function(){
            SetImportPopupHandler(media);
        }, 500);
    }
   
    if (choice != "Manage") 
    {    
        // Reset page and sort globals;
        global_page = 1;
        global_sort = "";
        $("#sort").css("visibility", "hidden");   
    }
    
    // Show media table.
    ShowMediaTable(global_media, global_page, global_sort);  
}

/*
 * Function:	ClearButtonBox
 *
 * Created on Sep 01, 2013
 * Updated on Sep 01, 2013
 *
 * Description: Clear the button box. Set back to initial values.
 *
 * In:	-
 * Out:	-
 *
 */
function ClearButtonsBox()
{
    var $buttons = $("#buttons_box");
    
    setTimeout(function() {
        $buttons.find(".title").text("");
        $buttons.find(".button").text("");
    
        //Remove slimScroll.
        if($buttons.find(".slimScrollDiv").length != 0) 
        {   
            $buttons.find(".button").unwrap();
            $buttons.find(".button").removeAttr("style");
            $buttons.find(".slimScrollBar").remove();
            $buttons.find(".slimScrollRail").remove();
        }    
    }, 300); 
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
        
    ShowMediaTable(global_media, global_page, global_sort);
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
    
    //GetFargoValues(global_media, global_sort);
    ShowMediaTable(global_media, global_page, global_sort);    
}

/*
 * Function:	ConvertMedia
 *
 * Created on May 10, 2013
 * Updated on Oct 19, 2013
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
                        
        case 'sets'   : media = "Movie Sets";
                        break;                        
                        
        case 'tvshows': media = "TV Shows";
                        break;
                        
        case 'seasons': media = "Seasons";
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
 * Function:	ConvertMediaToSingular
 *
 * Created on Sep 07, 2013
 * Updated on Oct 19, 2013
 *
 * Description: Convert the media string to a singular string.
 * 
 * In:	media
 * Out:	media

 *
 */
function ConvertMediaToSingular(media)
{
    switch (media)
    {
        case 'movies' : media = "Movie";
                        break;
                        
        case 'sets'   : media = "Movie Set";
                        break;                        
                        
        case 'tvshows': media = "TV Show";
                        break;
                        
        case 'seasons': media = "Season";
                        break;                        
                        
        case 'music'  : media = "Album";
                        break;
        
        default       : break;
    }
    
    return media;
}

/*
 * Function:	ShowMediaTable
 *
 * Created on Apr 05, 2013
 * Updated on Sep 28, 2013
 *
 * Description: Shows the media table.
 *
 * In:	media, page, column, sort
 * Out:	Media Table
 *
 */
function ShowMediaTable(media, page, sort)
{   
    var title = GetState("title");
    var genre = GetState("genre");
    var year  = GetState("year");
    var mode  = GetState("mode");
    
    var $header = $("#header_mode");
    
    $header.show();
    ShowInfoHeader(title, genre, year);
    
    $.ajax
    ({
        url: 'jsonfargo.php?action=' + media + '&page=' + page + '&title=' + title + '&genre=' + escape(genre) 
                                     + '&year=' + year + '&sort=' + sort,
        async: false,
        dataType: 'json',
        success: function(json)
        {
            // Return global lastpage.
            global_lastpage = json.params.lastpage;
            ShowNextPrevButtons(global_lastpage);
            
            var i = 0, j = 0;
            var img, html = [];
            var hide;
            
            if (json.media[0].id > 0)
            {
                html[i++] = '<table class="' + media + '">';
     
                $.each(json.media, function(key, value)
                {                
                    if (j == 0) {
                        html[i++] = '<tr>';
                    }
                    else if ( j == json.params.column) {
                        html[i++] = '</tr>';
                        j = 0;
                    }                    
                    
                    if (value.hide && mode == "Hide/Show") {
                        hide = " hide";
                    }
                    else {
                        hide = "";
                    }
                    
                    img = json.params.thumbs + '/' + value.xbmcid + '.jpg' + "?v=" + value.refresh;
                    html[i++] = '<td class="i' + value.id + hide + '"><img src="' + img + '"/></br>' + value.title + '</td>';
                    j++;
                });

                html[i++] = '</table>';
            }
            
            $('#display_content')[0].innerHTML = html.join('');
            
            // If images not found then show no poster.
            $("#display_content img").error(function(){
                $(this).attr('src', 'images/no_poster.jpg');
            });
            
            // Show sort character.
            $('#sort').html(sort);
            
            // Change hover color.
            if (mode) {
                ChangeModeMediaInterface(mode, media);
            }
        } // End success.
    }); // End Ajax. 
}

/*
 * Function:	ShowInfoHeader
 *
 * Created on Jul 01, 2013
 * Updated on Jul 01, 2013
 *
 * Description: Shows to info header.
 *
 * In:	lastpage
 * Out:	Info header.
 *
 */
function ShowInfoHeader(title, genre, year)
{
    var info = title;
 
    if (genre) {
        info += " / " + genre;
    }
    
    if (year) {
        info += " / " + year;
    }
        
    $("#header_info").text(info).show();
}

/*
 * Function:	ShowNextPrevButtons
 *
 * Created on Jun 30, 2013
 * Updated on Jun 30, 2013
 *
 * Description: Shows next/prev arrows buttons on page.
 *
 * In:	lastpage
 * Out:	Next/Prev
 *
 */
function ShowNextPrevButtons(lastpage)
{
    // Show Prev and Next buttons if there is more than 1 page.
    if (lastpage > 1)
    {
        $("#prev").css("visibility", "visible");
        $("#next").css("visibility", "visible");
    }
        else 
    {
        $("#prev").css("visibility", "hidden");
        $("#next").css("visibility", "hidden");            
    }    
}
