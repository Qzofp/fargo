/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.6
 *
 * File:    fargo.public.media.js
 *
 * Created on Jun 08, 2013
 * Updated on Jul 04, 2014
 *
 * Description: Fargo's jQuery and Javascript common media functions page.
 *
 */

//////////////////////////////////////////    Main Functions    ///////////////////////////////////////////

/*
 * Function:	SetScrollTitleHandler
 *
 * Created on Dec 20, 2013
 * Updated on Jan 03, 2013
 *
 * Description: Set scroll title text handler.
 * 
 * In:	event
 * Out:	Scroll text.
 *
 * Note: Based on http://jsfiddle.net/sdleihssirhc/AYYQe/3/
 *
 */
function SetScrollTitleHandler(event)
{
    var $title = $(this).children().last();
    
    if ($title[0] !== undefined)
    {
        var max = $title[0].scrollWidth;

        if (event.type == "mouseenter" && max > $title.width()) 
        {
            $title.css("text-overflow", "clip");     
            ScrollTitle($title, 0, max - $title.width());
        } 
        else 
        {
            clearTimeout(gSTATE.TIMER);
            $title.css("text-overflow", "ellipsis");  
            $title.scrollLeft(0);    
        }
    }
}

/*
 * Function:	ScrollTitle
 *
 * Created on Dec 20, 2013
 * Updated on Dec 23, 2013
 *
 * Description: Scroll title.
 * 
 * In:	title
 * Out:	Scroll text.
 *
 * Note: Code from http://jsfiddle.net/sdleihssirhc/AYYQe/3/
 *
 */
function ScrollTitle(title, i, max)
{
    title.scrollLeft(i);
    //console.log(i);
    if (i < max) {
        gSTATE.TIMER = setTimeout(function(){
            ScrollTitle(title, ++i, max);
        }, 40);
    }
}

/*
 * Function:	SetInfoZoomHandler
 *
 * Created on Jul 05, 2013
 * Updated on Nov 16, 2013
 *
 * Description: Set and show the media info or zoom in on movie sets, episodes.
 * 
 * In:	-
 * Out:	Media Info
 *
 */
function SetInfoZoomHandler()
{   
    var media = GetState("media");
    var type  = GetState("type");
    var id    = $(this).attr("class").match(/\d+(_\d+)?/g);
    
    ShowInfoZoomMedia(media, type, id);
}

/*
 * Function:	SetInfoZoomHandler
 *
 * Created on Nov 09, 2013
 * Updated on Jun 29, 2014
 *
 * Description: Show the media info or zoom in on movie sets, episodes media.
 * 
 * In:	-
 * Out:	Media Info
 *
 */
function ShowInfoZoomMedia(media, type, id)
{
    if (type != "sets" && type != "series" && type != "seasons" && type != "songs") {
        ShowMediaInfo(media, type, id);
    }
    else {
        ShowMediaZoomIn(type, id);
    }    
}

/*
 * Function:	ShowMediaInfo
 *
 * Created on Aug 31, 2013
 * Updated on Jun 29, 2014
 *
 * Description: Show the media info.
 * 
 * In:	media, type, id
 * Out:	Media Info
 *
 */
function ShowMediaInfo(media, type, id)
{       
    switch(media)
    {
        case "movies"  : ShowMovieInfo(id);
                         break;

        case "tvshows" : if (type != "episodes") {
                            ShowTVShowInfo(id);
                         }
                         else {
                            ShowTVShowEpisodeInfo(id);
                         }
                         break;
                    
        case "music"   : if (type != "tracks") {
                            ShowAlbumInfo(id);
                         }
                         else {
                            ShowSongInfo(id);
                         }
                         break;        
    } 
}

/*
 * Function:	ShowMediaZoomIn
 *
 * Created on Nov 09, 2013
 * Updated on Jun 29, 2014
 *
 * Description: Show the media zoom in for movie sets, seasons, episodes.
 * 
 * In:	media, id
 * Out:	Media Info
 *
 */
function ShowMediaZoomIn(type, id)
{
   var $control = $("#control_sub");
   
   if (gSTATE.SORT) {
       $("#sort").css("visibility", "hidden");
   }
   
   if (type == "series" && id.toString().split("_")[1] == "1") 
   {
      type = "noseasons"; // Series has no seasons.
      id   = id.toString().split("_")[0] + "_-1";
   }
   
   switch (type)
   {
       case "sets"    : type = "movieset";
                        gTEMP.TITLE = GetState("title");
                        gTEMP.PAGE  = gSTATE.PAGE;
                        gTEMP.SORT  = gSTATE.SORT;
                        SetState("title", "year_asc");
                        $control.stop().slideUp("slow", function() {
                            $("#genres, #years").hide();
                            $("#type").text(cBUT.BACK + cBUT.SETS);
                            $control.slideDown("slow");
                        });
                        break;
                          
       case "series"  : type = "seasons";
                        gTEMP.TITLE = GetState("title");
                        gTEMP.PAGE  = gSTATE.PAGE;
                        gTEMP.SORT  = gSTATE.SORT;
                        gTEMP.LEVEL = id;
                        SetState("title", "season");                        
                        $control.stop().slideUp("slow", function() {
                            $("#title, #genres, #years").hide();
                            $("#type").text(cBUT.BACK + cBUT.SERIES);
                            $control.slideDown("slow");
                        });                      
                        break;
                          
       case "seasons" : type = "episodes";
                        gTEMP.TITLE2 = GetState("title");
                        gTEMP.PAGE2  = gSTATE.PAGE;
                        gTEMP.SORT2  = gSTATE.SORT;
                        SetState("title", "episode");
                        $control.stop().slideUp("slow", function() {
                            $("#title, #genres, #years").hide();
                            $("#type").text(cBUT.BACK + cBUT.SEASONS);
                            $control.slideDown("slow");
                        });                          
                        break;
                        
       case "noseasons" : type = "episodes";
                          gTEMP.TITLE = GetState("title");
                          gTEMP.PAGE  = gSTATE.PAGE;
                          gTEMP.SORT  = gSTATE.SORT;
                          gTEMP.LEVEL = id;
                          SetState("title", "episode");                        
                          $control.stop().slideUp("slow", function() {
                              $("#title, #genres, #years").hide();
                              $("#type").text(cBUT.BACK + cBUT.SERIES);
                              $control.slideDown("slow");
                          });                      
                          break;
                      
        case "songs"    : type = "tracks";
                          gTEMP.TITLE = GetState("title");
                          gTEMP.PAGE  = gSTATE.PAGE;
                          gTEMP.SORT  = gSTATE.SORT;
                          gTEMP.LEVEL = id;
                          SetState("title", "track");                        
                          $control.stop().slideUp("slow", function() {
                              $("#title, #genres, #years").hide();
                              $("#type").text(cBUT.BACK + cBUT.SONGS);
                              $control.slideDown("slow");
                          });           
                          break;  
   }
   
   // Reset page and sort globals;
   gSTATE.PAGE = 1;
   gSTATE.SORT = "";
    
   SetState("type", type);
   SetState("level", id);
    
   ShowMediaTable(gSTATE.PAGE, gSTATE.SORT);
}

/*
 * Function:	ShowMovieInfo
 *
 * Created on Jul 05, 2013
 * Updated on Jul 02, 2014
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
                         {left:"Country:",  right:json.media.country},
                         {left:"Path:",     right:json.media.path}];
            
            // Show info.
            ShowInfoTable(aInfo);
            
            // Change space size for movie info and fanart.
            $("#info_left").css("margin-right", 395); // 460
            $("#info_right").toggleClass("fanart_space", true).toggleClass("cover_space", false);            
            
            // Show fanart.
            $("#info_fanart img").error(function(){
                $(this).attr('src', 'images/no_fanart.jpg');
            })
            .attr('src', json.params.fanart + '/' + json.media.fanart + '.jpg' + '?v=' + json.media.refresh)
            .css("width", 385);
            
            // Show media flags. Path and filename is generated by PHP (jsonfargo.php).
            $("#info_video").html(json.media.video);
            $("#info_audio").html(json.media.audio);
            $("#info_aspect").html(json.media.aspect);
            $("#info_mpaa").html(json.media.mpaa);           
            
            // Show plot.
            $("#info_plot").text("Plot");
            $("#info_plot_text").html(json.media.plot).slimScroll({
                height:'120px',
                color:'gray',
                alwaysVisible:true
            });
            
            // Hide close button and add buttons (imdb, trailer).
            if (json.media.imdbnr || json.media.trailer) 
            {
                $("#info_box .close").hide();
 
                if (json.media.imdbnr){
                    buttons += '<button type="button" class="url" value="' + json.media.imdbnr + '">IMDb</button>';
                }
            
                if (json.media.trailer){
                    buttons += '<button type="button" class="url" value="' + json.media.trailer + '">Trailer</button>';
                }
            
                $("#info_box .button").append(buttons);  
            }
            else {
                $("#info_box .close").show();
            }
            
            // Show popup.
            ShowPopupBox("#info_box", "<div>" + json.media.title + "</div>");
            SetState("page", "popup");    
        } // End succes.
    }); // End Ajax.       
}

/*
 * Function:	ShowTVShowInfo
 *
 * Created on Jul 09, 2013
 * Updated on Jul 02, 2014
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
                         {left:"Rating:",   right:json.media.rating},
                         {left:"Path:",     right:json.media.path}];          
            
            // Show info.
            ShowInfoTable(aInfo);
            
            // Change space size for tv show info and fanart.
            $("#info_left").css("margin-right", 395); // 460
            $("#info_right").toggleClass("fanart_space", true).toggleClass("cover_space", false);
            
            // Show fanart.
            $("#info_fanart img").error(function(){
                $(this).attr('src', 'images/no_fanart.jpg');
            })
            .attr('src', json.params.fanart + '/' + json.media.fanart + '.jpg')
            .css("width", 385);
            
            // Show plot.
            $("#info_plot").text("Plot");
            $("#info_plot_text").html(json.media.plot).slimScroll({
                height:'120px',
                color:'gray',
                alwaysVisible:true
            });
            
            // Hide close button and add button (TheTVDB, AniDB).
            if (json.media.imdbnr)
            {
                $("#info_box .close").hide();
                
                pattern = /thetvdb/;            
                if(pattern.exec(json.media.imdbnr)) {
                   btnname = "TheTVDB";
                }
                else {
                   btnname = "AniDB";
                }
                
                buttons += '<button type="button" class="url" value="' + json.media.imdbnr + '">' + btnname + '</button>';
            
                $("#info_box .button").append(buttons);
            }
            else {
                $("#info_box .close").show();                
            }
            
            // Show popup.
            ShowPopupBox("#info_box",  "<div>" + json.media.title + "</div>");
            SetState("page", "popup");    
        } // End succes.
    }); // End Ajax.       
}

/*
 * Function:	ShowTVShowEpisodeInfo
 *
 * Created on Nov 17, 2013
 * Updated on Jul 03, 2014
 *
 * Description: Show the TV show episode info.
 * 
 * In:	id
 * Out:	TV Show Episode Info
 *
 */
function ShowTVShowEpisodeInfo(id)
{
    $.ajax
    ({
        url: 'jsonfargo.php?action=info&media=episodes' + '&id=' + id,
        async: false,
        dataType: 'json',
        success: function(json)
        {   
            var aInfo = [{left:"TV&nbsp;show:", right:json.media.showtitle},
                         {left:"Season:",   right:json.media.season},
                         {left:"Episode:",  right:json.media.episode},
                         {left:"Aired:",    right:json.media.firstaired},
                         {left:"Director:", right:json.media.director},
                         {left:"Writer:",   right:json.media.writer},
                         {left:"Year:",     right:json.media.year},
                         {left:"Runtime:",  right:json.media.runtime},
                         {left:"Rating:",   right:json.media.rating},
                         {left:"Path:",     right:json.media.path}];                         
            
            // Show info.
            ShowInfoTable(aInfo);
            
            // Change space size for tv show info and fanart.
            $("#info_left").css("margin-right", 395); // 460
            $("#info_right").toggleClass("fanart_space", true).toggleClass("cover_space", false);
            
            // Show fanart.
            $("#info_fanart img").error(function(){
                $(this).attr('src', 'images/no_fanart.jpg');
            })
            .attr('src', json.params.thumbs + '/' + json.media.fanart + '.jpg')
            .css("width", 385);
            
            // Show media flags. Path and filename is generated by PHP (jsonfargo.php).
            $("#info_video").html(json.media.video);
            $("#info_audio").html(json.media.audio);
            $("#info_aspect").html(json.media.aspect);         
            
            // Show plot.
            $("#info_plot").text("Plot");
            $("#info_plot_text").html(json.media.plot).slimScroll({
                height:'120px',
                color:'gray',
                alwaysVisible:true
            });
            
            // Show buttons close button.
            $("#info_box .close").show();
            
            // Show popup.
            ShowPopupBox("#info_box",  "<div>" + json.media.title + "</div>");
            SetState("page", "popup");    
        } // End succes.
    }); // End Ajax.       
}

/*
 * Function:	ShowAlbumInfo
 *
 * Created on Jul 10, 2013
 * Updated on Jul 03, 2014
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
        url: 'jsonfargo.php?action=info&media=albums' + '&id=' + id,
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
            $("#info_left").css("margin-right", 270);
            $("#info_left td div").css("width", 400);
            $("#info_right").toggleClass("fanart_space", false).toggleClass("cover_space", true);
            
            // Show fanart.
            $("#info_fanart img").error(function(){
                $(this).attr('src', 'images/no_fanart.jpg');
            })
            .attr('src', json.params.covers + '/' + json.media.fanart + '.jpg')
            .css("width", 260); 
  
            // Show plot.
            $("#info_plot").text("Description");
            $("#info_plot_text").html(json.media.description).slimScroll({
                height:'120px',
                color:'gray',
                alwaysVisible:true
            });
            
            // Show buttons close button.
            $("#info_box .close").show();            
            
            // Show popup.
            ShowPopupBox("#info_box",  "<div>" + json.media.title + "</div>");
            SetState("page", "popup");    
        } // End succes.
    }); // End Ajax.        
}

/*
 * Function:	ShowSongInfo
 *
 * Created on Jun 29, 2014
 * Updated on Jul 03, 2014
 *
 * Description: Show the song info.
 * 
 * In:	id
 * Out:	Song Info
 *
 */
function ShowSongInfo(id)
{
    $.ajax
    ({
        url: 'jsonfargo.php?action=info&media=songs' + '&id=' + id,
        async: false,
        dataType: 'json',
        success: function(json)
        {              
            var aInfo = [{left:"Artist:",   right:json.media.artist},
                         {left:"Album:",    right:json.media.album},
                         {left:"Genre:",    right:json.media.genre},
                         {left:"Year:",     right:json.media.year},
                         {left:"Disc:",     right:json.media.disc}, 
                         {left:"Track:",    right:json.media.track},
                         {left:"Duration:", right:json.media.duration},
                         {left:"Rating:",   right:json.media.rating},
                         {left:"Path:",     right:json.media.path}];
            
            // Show info.
            ShowInfoTable(aInfo);
            
            // Change space size for album info and cover.
            $("#info_left").css("margin-right", 270);
            $("#info_left td div").css("width", 400);
            $("#info_right").toggleClass("fanart_space", false).toggleClass("cover_space", true);
            
            // Show fanart.
            $("#info_fanart img").error(function(){
                $(this).attr('src', 'images/no_fanart.jpg');
            })
            .attr('src', json.params.covers + '/' + json.media.fanart + '.jpg')
            .css("width", 260);
  
            // Show plot.
            $("#info_plot").text("Comment");
            $("#info_plot_text").html(json.media.comment).slimScroll({
                height:'120px',
                color:'gray',
                alwaysVisible:true
            });
            
            // Show buttons close button.
            $("#info_box .close").show();            
            
            // Show popup.
            ShowPopupBox("#info_box",  "<div>" + json.media.title + "</div>");
            SetState("page", "popup");    
        } // End succes.
    }); // End Ajax.        
}

/*
 * Function:	ShowInfoTable
 *
 * Created on Jul 06, 2013
 * Updated on Dec 31, 2013
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
        if (this.right)
        {    
            table += '<tr>';
            table += '<td class="left">' + this.left + '</td>';
            table += '<td><div>' + this.right + '</div></td>';
            table += '</tr>';
        }
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
 * Updated on Feb 28, 2014
 *
 * Description: Set the media and show the media table.
 * 
 * In:	event
 * Out:	Media
 *
 */
function SetMediaHandler(event)
{            
   var media = event.data.media;
   SetState("page", media);
   SetState("level", "");

   // Initialize parameters.
   gSTATE.PAGE = 1;
   gSTATE.SORT = "";
   SetState("title", "name_asc");
   SetState("genre", "");
   SetState("year", "");
   
   $('#display_system').hide();
   $('#display_system_left').html(""); 
   $('#display_system_right').html("");   
   $('#display_content').show();
   $('#bullets').show();
   
   ChangeControlBar(media);
   ChangeSubControlBar(media);
    
   $("#display_left").show();
   $("#display_right").show();
   
   ShowMediaTable(gSTATE.PAGE, gSTATE.SORT);
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
 * Updated on Nov 10, 2013
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

    if ($this.text() == "Sort"){ // By title
        aList = [cSORT.NAME, cSORT.YEAR, cSORT.ASC, cSORT.DESC, cSORT.NEW, cSORT.OLD];
    }
    else if ($this.text() == "Manage") {
        aList = ["Information", "Hide/Show", "Import", "Refresh", "Remove"]; 
    }
    else
    {
        // Returns gSTATE.LIST
        GetFargoSortList($this.text(), media);
        aList = gSTATE.LIST;
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
   
        // Change button box position.
        //$("#buttons_box").css('top', '15%');
   
        ShowPopupBox("#buttons_box", $this.text());
        SetState("page", "popup");
    }
}

/*
 * Function:	SetShowButtonHandler
 *
 * Created on Jun 27, 2013
 * Updated on Feb 25, 2014
 *
 * Description: Show the sort, genre, years or manage action.
 * 
 * In:	-
 * Out:	-
 *
 */
function SetShowButtonHandler()
{
    var state, media; 
    var $this = $(this);
    var choice = GetState("choice");
    
    switch (choice)
    {
        case "Sort"   : state = "title";
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
    
    switch($this.text())
    {                         
        case cSORT.NAME     : SetState(state, "name_asc");
                              break;
                          
        case cSORT.YEAR     : SetState(state, "year_asc");
                              break;
                              
        case cSORT.ASC      : if (GetState(state).split("_")[0] == "name") {
                                 SetState(state, "name_asc");
                              }
                              else if (GetState(state).split("_")[0] == "year") {
                                 SetState(state, "year_asc");
                              }
                              break;
                              
        case cSORT.DESC     : if (GetState(state).split("_")[0] == "name") {
                                 SetState(state, "name_desc");
                              }
                              else if (GetState(state).split("_")[0] == "year") {
                                 SetState(state, "year_desc");
                              }
                              break;
                          
        case cSORT.NEW      : SetState(state, "latest");
                              break;
                              
        case cSORT.OLD      : SetState(state, "oldest");
                              break;                         
                          
        case "- Show All -" : SetState(state, "");
                              break;
                              
        case "Import"       : media = GetState("media");
                              setTimeout(function(){
                                SetImportPopupHandler(media);
                              }, 500);                      
                              break;
                              
        default :             SetState(state, $this.text());
                              break;
    }
       
    if (choice != "Manage") 
    {    
        // Reset page and sort globals;
        gSTATE.PAGE = 1;
        gSTATE.SORT = "";
        $("#sort").css("visibility", "hidden");   
    }
    
    // Show media table.
    ShowMediaTable(gSTATE.PAGE, gSTATE.SORT);  
}

/*
 * Function:	SetButtonsTypeHandler
 *
 * Created on Nov 03, 2013
 * Updated on Jul 01, 2014
 *
 * Description: Set the buttons type handler and perform the right action.
 * 
 * In:	-
 * Out:	-
 *
 */
function SetButtonsTypeHandler()
{
    switch ($(this).text())
    {
        case cBUT.TITLES : if (GetState("media") == "movies") 
                           {
                                SetState("type", "titles");
                                ShowMediaTypePage(cBUT.SETS, true);
                           }
                           else {
                                SetState("type", "tvtitles");
                                ShowMediaTypePage(cBUT.SERIES, false);
                           }
                           break;
        
        case cBUT.SETS   : SetState("type", "sets");
                           ShowMediaTypePage(cBUT.TITLES, true);
                           break;
        
        case cBUT.BACK + 
             cBUT.SETS   : BackToMedia("sets");
                           break;
                                   
        case cBUT.SERIES : SetState("type", "series");
                           ShowMediaTypePage(cBUT.TITLES, false);
                           break;                         
                       
        case cBUT.BACK +
             cBUT.SERIES : BackToMedia("series");
                           break;
                       
        case cBUT.BACK +
             cBUT.SEASONS: BackToMedia("seasons");
                           break;    
                           
        case cBUT.ALBUMS : SetState("type", "albums");
                           ShowMediaTypePage(cBUT.SONGS, false);
                           break;  
                       
        case cBUT.SONGS  : SetState("type", "songs");
                           ShowMediaTypePage(cBUT.ALBUMS, false);
                           break;  
                       
        case cBUT.BACK +
             cBUT.SONGS  : BackToMedia("songs");
                           break;                      
    }
}

/*
 * Function:	BackToMedia
 *
 * Created on Nov 09, 2013
 * Updated on Jun 29, 2014
 *
 * Description: Go back to media, one level up. E.g. from set movies to sets.
 * 
 * In:	type
 * Out:	-
 *
 */
function BackToMedia(type)
{
    var $control = $("#control_sub");
    SetState("type", type);
    SetState("level", "");    
    
    switch (type)
    {
        case "sets"    : gSTATE.PAGE = gTEMP.PAGE;
                         gSTATE.SORT = gTEMP.SORT;   
                         SetState("title", gTEMP.TITLE);
                         $control.stop().slideUp("slow", function() {
                            $("#title, #genres, #years").show();
                            $("#type").text(cBUT.TITLES);
                            $control.slideDown("slow");
                         }); 
                         break;
                          
        case "series"  : gSTATE.PAGE = gTEMP.PAGE;
                         gSTATE.SORT = gTEMP.SORT;
                         SetState("title", gTEMP.TITLE);
                         $control.stop().slideUp("slow", function() {
                            $("#title, #genres, #years").show();
                            $("#type").text(cBUT.TITLES);
                            $control.slideDown("slow");
                         });                         
                         break;
                          
        case "seasons" : gSTATE.PAGE = gTEMP.PAGE2;
                         gSTATE.SORT = gTEMP.SORT2;
                         SetState("title", gTEMP.TITLE2);
                         SetState("level", gTEMP.LEVEL);
                         $control.stop().slideUp("slow", function() {
                            $("#type").text(cBUT.BACK + cBUT.SERIES);
                            $control.slideDown("slow");
                         });             
                         break;
                         
        case "songs"   : gSTATE.PAGE = gTEMP.PAGE;
                         gSTATE.SORT = gTEMP.SORT;   
                         SetState("title", gTEMP.TITLE);
                         $control.stop().slideUp("slow", function() {
                            $("#title, #genres, #years").show();
                            $("#type").text(cBUT.ALBUMS);
                            $control.slideDown("slow");
                         });
    }
   
    if (gSTATE.SORT) {
       $("#sort").css("visibility", "visible");
    }
   
    ShowMediaTable(gSTATE.PAGE, gSTATE.SORT);   
}

/*
 * Function:	ShowTypeMediaPage
 *
 * Created on Nov 12, 2013
 * Updated on Dec 23, 2013
 *
 * Description: Show the media type.
 * 
 * In:	type, reset
 * Out:	-
 *
 */
function ShowMediaTypePage(type, reset)
{
    var $control = $("#control_sub");
        
    // Reset page and sort globals;
    if (reset)
    {    
        gSTATE.PAGE = 1;
        gSTATE.SORT = "";
        $("#sort").css("visibility", "hidden");  
    }
    
    // Show media table.
    ShowMediaTable(gSTATE.PAGE, gSTATE.SORT);
    
    // Change sub control bar.
    $control.stop().slideUp("slow", function() {
        $("#type").text(type);
        $control.slideDown("slow");
     }); 
}

/*
 * Function:	SetButtonsScreenHandler
 *
 * Created on May 24, 2014
 * Updated on May 24, 2014
 *
 * Description: Set the buttons screen handler and perform the right action.
 * 
 * In:	-
 * Out:	Screen show media in a list or as thumnails.
 *
 */
function SetButtonsScreenHandler()
{
    var screen, $control = $("#control_sub");
    
    if ($(this).text() == cBUT.LIST) {
        screen = cBUT.THUMB;
    }
    else {
        screen = cBUT.LIST;  
    }  
    
    // Set state screen (list or thumbnail).
    gSTATE.SCREEN = screen;
    
    // Show media table.
    ShowMediaTable(gSTATE.PAGE, gSTATE.SORT); 
    
    // Change sub control bar.
    $control.stop().slideUp("slow", function() {
        $("#screen").text(screen);
        $control.slideDown("slow");
    });     
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
 * Updated on Nov 08, 2013
 *
 * Description: Change the media on the control bar from Movies, TV Shows, Music or to System.
 *
 * In:	media
 * Out:	-
 *
 */
function ChangeControlBar(media)
{   
    var id, aMedia = ['movies','tvshows','music','system'];
 
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
   
    //return media;
}

/*
 * Function:	SetPageHandler
 *
 * Created on Apr 13, 2013
 * Updated on Nov 07, 2013
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
    var offset = gSTATE.LAST;
    
    gSTATE.PAGE = (action == "n") ? gSTATE.PAGE + 1 : gSTATE.PAGE - 1;
    
    // Check for min.
    if (gSTATE.PAGE == 0) {
        gSTATE.PAGE = offset;
    }  
                
    // Check for max.
    else if (gSTATE.PAGE == offset+1) {
        gSTATE.PAGE = 1;
    } 
        
    ShowMediaTable(gSTATE.PAGE, gSTATE.SORT);
} 

/*
 * Function:	SetBulletHandler
 *
 * Created on Mar 01, 2013
 * Updated on Mar 03, 2013
 *
 * Description: Get the page and show the media table.
 * 
 * In:	-
 * Out:	Media
 *
 */
function SetBulletHandler()
{
    gSTATE.PAGE = Number($(this).text());
    ShowMediaTable(gSTATE.PAGE, gSTATE.SORT);
    
    //alert($(this).text());
  
}

/*
 * Function:	SetArrowHandler
 *
 * Created on Apr 13, 2013
 * Updated on Oct 31, 2013
 *
 * Description: Set the page and show the media table.
 * 
 * In:	event
 * Out:	Media
 *
 */
function SetArrowHandler(action)
{
    var offset = gSTATE.LAST;
    
    gSTATE.PAGE = (action == "n") ? gSTATE.PAGE + 1 : gSTATE.PAGE - 1;
    
    // Check for min.
    if (gSTATE.PAGE == 0) {
        gSTATE.PAGE = offset;
    }  
                
    // Check for max.
    else if (gSTATE.PAGE == offset+1) {
        gSTATE.PAGE = 1;
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
 * Updated on Nov 08, 2013
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
        gSTATE.PAGE = 1; 
        gSTATE.SORT = String.fromCharCode(event.which).toUpperCase();
    }
    // key between 0 and 9.
    else if (key >= 48 && key <= 57)
    {
        gSTATE.PAGE = 1; 
        gSTATE.SORT = String.fromCharCode(event.which);
    }   
    else if (key != 37 && key != 39)
    {
        gSTATE.PAGE = 1; 
        gSTATE.SORT = "";       
    }
    
    // The previous and next with the left and right arrow keys.
    if (key == 37) {
        SetArrowHandler("p");
    }
    else if (key == 39) {
        SetArrowHandler("n");
    }        
    
    if (gSTATE.SORT == "") {
        $("#sort").css("visibility", "hidden");
    }
    
    ShowMediaTable(gSTATE.PAGE, gSTATE.SORT);    
}

/*
 * Function:	ConvertMedia
 *
 * Created on May 10, 2013
 * Updated on Jun 27, 2014
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
        case 'movies'  : media = "Movies";
                         break;
                        
        case 'sets'    : media = "Movie Sets";
                         break;                        
                        
        case 'tvshows' : media = "TV Shows";
                         break;
                        
        case 'seasons' : media = "Seasons";
                         break;   
                    
        case 'episodes': media = "Episodes";
                         break;                     

        case 'albums'  : media = "Albums";
                         break;  
                     
        case 'songs'   : media = "Songs";
                         break;                         
        
        case 'music'   : media = "Music";
                         break;
                        
        case 'system'  : media = "System";
                         break;
        
        default       : break;
    }
    
    return media;
}

/*
 * Function:	ConvertMediaToSingular
 *
 * Created on Sep 07, 2013
 * Updated on Jun 30, 2014
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
        case 'movies'   : media = "Movie";
                          break;

        case 'titles'   : media = "Movie";
                          break;        
        
        case 'sets'     : media = "Movie Set";
                          break;
                      
        case 'movieset' : media = "Movie";
                          break;                        
                        
        case 'tvshows'  : media = "TV Show";
                          break;
                         
        case 'tvtitles' : media = "TV Show";
                          break;
                      
        case 'series'   : media = "Season"; //"TV Show";
                          break;                             
                        
        case 'seasons'  : media = "Season";
                          break;    
                    
        case 'episodes' : media = "Episode";
                          break;                         
                        
        case 'music'    : media = "Album";
                          break;
                          
        case 'albums'   : media = "Album";
                          break;   
                      
        case 'songs'    : media = "Song";
                          break; 
                      
        case 'tracks'   : media = "Track";
                          break;                        
    }
    
    return media;
}

/*
 * Function:	ShowMediaTable
 *
 * Created on Apr 05, 2013
 * Updated on Jun 01, 2014
 *
 * Description: Shows the media table.
 *
 * In:	page, sort
 * Out:	Media Table
 *
 */
function ShowMediaTable(page, sort)
{   
    var media = GetState("media");
    var type  = GetState("type");
    var title = GetState("title");
    var level = GetState("level");
    var genre = GetState("genre");
    var year  = GetState("year");
    var mode  = GetState("mode"); 
    
    $("#header_mode").show();

    $.ajax
    ({
        url: 'jsonfargo.php?action=media' + '&type=' + type + '&page=' + page + '&title=' + title + '&genre=' + encodeURIComponent(genre) 
                                          + '&year=' + year + '&sort=' + sort + '&level=' + level,
        async: false,
        dataType: 'json',
        success: function(json)
        {
            // Return gSTATE.LAST.
            gSTATE.LAST = json.params.lastpage;
            ShowNextPrevButtons(gSTATE.LAST);       
            ShowInfoHeader(type, json.params.header, title, genre, year);
            
            if (gSTATE.SCREEN != cBUT.THUMB) {
                ShowMediaTableThumbs(json, media, type, mode);
            }
            else {
                ShowMediaTableList(json, media, type, mode);
            }
            
            // Show sort character.
            $('#sort').html(sort);
            
            // Show pagination (bullets).
            ShowBullets(json.params.lastpage, page, 12, 3);
            
            // Change hover color.
            if (mode) {
                ChangeModeMediaInterface(mode, media);
            }
            
        } // End success.
    }); // End Ajax. 
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

/*
 * Function:	ShowInfoHeader
 *
 * Created on Jul 01, 2013
 * Updated on Jun 29, 2014
 *
 * Description: Shows the info header.
 *
 * In:	type, title, sort, genre, year
 * Out:	Info header.
 *
 */
function ShowInfoHeader(type, title, sort, genre, year)
{    
    var info1 = "&nbsp;";
    var info2 = "&nbsp;";
        
    if (title) {
        info1 = title;
    }
 
    if (type != "movieset" && type != "seasons" && type != "episodes")
    {    
        if (genre) {
            info1 = genre;
        }  
        
        if (year) 
        {
            if (genre) {
                info1 += " / " + year;   
            }
            else {
                info1 = year;
            } 
        }
    }
    
    switch (sort) 
    {
        case "name_asc"  : info2 = cSORT.NAME + " " + cSORT.ASC;
                           break;

        case "name_desc" : info2 = cSORT.NAME + " " + cSORT.DESC;
                           break;   
                       
        case "year_asc"  : info2 = cSORT.YEAR + " " + cSORT.ASC;
                           break;

        case "year_desc" : info2 = cSORT.YEAR + " " + cSORT.DESC;
                           break;
                       
        case "latest"    : info2 = cSORT.NEW;
                           break;
                           
        case "oldest"    : info2 = cSORT.OLD;
                           break;
                       
        case "season"    : info2 = cSORT.SEASON; 
                           break;
                           
        case "episode"   : info2 = cSORT.EPISODE; 
                           break;
                       
        case "track"     : info2 = cSORT.TRACK; 
                           break;                      
    }
        
    $("#header_info").html(info1).show();
    $("#header_sort").html(info2).show();
}

/*
 * Function:	ShowMediaTableThumbs
 *
 * Created on May 24, 2014
 * Updated on Jul 04, 2014
 *
 * Description: Shows the media table with media as thumbnails.
 *
 * In:	json, media, type, mode
 * Out:	Thumbs table
 *
 */
function ShowMediaTableThumbs(json, media, type, mode)
{
    var i = 0, j = 0;
    var img, html = [];
    var hide, noposter;
    
    // Clear list page.
    $('#display_list').hide().html("");
    
    if (json.media[0].id != 0)
    {
        if (media != "music") {
           noposter = 'images/no_poster.jpg'; 
        }
        else {
           noposter = 'images/no_cover.jpg';  
        }
        
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
                    
            if (value.poster) {
                img = json.params.thumbs + '/' + value.poster + '.jpg' + "?v=" + value.refresh;
            }
            else {
                img = noposter;
            }
        
            html[i]  = '<td class="i' + value.id + hide + '">';                    
            html[i] += '<img src="' + img + '"/>';
            
            // If sets, series or songs then label covers.
            if (type == "sets" || type == "series" || type == "songs") {
                html[i] += '<img class="label" src="images/' + type + '.png"/>';
            }
            
            if (value.playcount > 0) {
                html[i] += '<img class="mark" src="images/watched.png"/>';
            }
            else if (value.playcount < 0) {
                html[i] += '<img class="mark" src="images/deleted.png"/>';
            }
                   
            html[i] += '<div>' + value.title + '</div></td>';
            i++; j++;
        });

        html[i++] = '</table>';
    }
            
    $('#display_thumb')[0].innerHTML = html.join('');
    $('#display_thumb').show();
            
    // Change table cells and image size.
    if (type == "episodes")
    {
        $("#display_thumb td").width(250);
        $("#display_thumb img").not(".mark").width(220);
        $("#display_thumb td div").width(240);
    }  
    
    // If images not found then show no poster.
    $("#display_thumb img").error(function()
    {
        if (media != "music") {
            $(this).attr('src', 'images/no_poster.jpg');
        }
        else {
            $(this).attr('src', 'images/no_cover.jpg');
        }
    });
}

/*
 * Function:	ShowMediaTableList
 *
 * Created on May 25, 2014
 * Updated on Jul 04, 2014
 *
 * Description: Shows the media table with media in a list.
 *
 * In:	json, media, type, mode
 * Out:	List table
 *
 */
function ShowMediaTableList(json, media, type, mode)
{
    var i = 0;
    var img, html = [];
    var hide, sub, noposter, label;
    
    // Clear thumbnail page.
    $('#display_thumb').hide().html("");
    
    if (json.media[0].id != 0)
    {
        if (media != "music") {
           noposter = 'images/no_poster.jpg'; 
        }
        else {
           noposter = 'images/no_cover.jpg';  
        }       
        
        html[i++] = '<div class="display_scroll"><table class="' + media + '">';
     
        $.each(json.media, function(key, value)
        {      
            if (value.hide && mode == "Hide/Show") {
                hide = " hide";
            }
            else {
                hide = "";
            }
            
            html[i] = '<tr class="i' + value.id + hide + '">';
                    
            if (value.poster) {
                img = json.params.thumbs + '/' + value.poster + '.jpg' + "?v=" + value.refresh;
            }
            else {
                img = noposter;
            }
              
            // If sets, series or songs then label covers.
            if (type == "sets" || type == "series" || type == "songs") {
                label = '<img class="label" src="images/' + type + '.png"/>';
            }
            else {
                label = "";
            }
            
            html[i] += '<td class="poster"><img src="' + img + '"/>' + label + '</td>';                  
            html[i] += '<td class="title"><div>' + value.title + '</div></td>';
            
            sub = "";
            switch(type) 
            {                              
                case "sets"     : sub = " movie";
                                  if (value.sub > 1) { sub += "s"; }
                                  break;
                                
                case "tvtitles" : sub = " episode";
                                  if (value.sub > 1) { sub += "s"; }
                                  break;
                              
                case "series"   : sub = " season";
                                  if (value.sub > 1) { sub += "s"; }
                                  break;
                                  
                case "seasons"  : sub = " episode";
                                  if (value.sub > 1) { sub += "s"; }
                                  break;
                              
                case "songs"    : sub = " track";
                                  if (value.sub > 1) { sub += "s"; }
                                  break;                              
            }  
            
            html[i] += '<td class="sub"><div>' + value.sub + sub + '</div></td>';
            
            if (value.aux) {
                html[i] += '<td class="aux">' + value.aux + '</td>';
            } 
            
            if (value.playcount > 0) {
                html[i] += '<td class="mark"><img src="images/watched.png"/>';
            }
            else if (value.playcount < 0) {
                html[i] += '<td class="mark"><img src="images/deleted.png"/>';
            }      
            else if (value.playcount == 0) {    
                html[i] += '<td class="mark">&nbsp;</td>';
            }
            
            html[i] += '</tr>';
            i++;
        });

        html[i++] = '</table></div>';
    }    
    
    $('#display_list')[0].innerHTML = html.join('');
    $('#display_list').show();
    
    // If images not found then show no poster.
    $("#display_list img").error(function(){
        if (media != "music") {
            $(this).attr('src', 'images/no_poster.jpg');
        }
        else {
            $(this).attr('src', 'images/no_cover.jpg');
        }
    });
    
    // Change table cells and image size.
    if (type == "episodes") 
    {
        $("#display_list .tvshows .poster").width(57);
        $("#display_list .tvshows .poster img").width(50);
    }      
    
    
    $("#display_list .display_scroll").slimScroll({
        width:'100%',
        height:'auto',
        alwaysVisible:true,
        color:'gray'
    }); 
}

/*
 * Function:	ShowBullets
 *
 * Created on Feb 28, 2014
 * Updated on Apr 14, 2014
 *
 * Description: Shows the page bullets.
 *
 * In:	lastpage, page, show, offset
 * Out:	Bullets
 *
 */
function ShowBullets(lastpage, page, show, offset)
{
    var start, end, space;
    var left, right;
    var html = [];
    
    // Show the small bullets on the left side.
    left = Math.floor((page - 1)/show);
    if (left)
    {
        start = 1;
        end   = left;
        space = -1;
        if (end > offset) 
        {
            end = offset + 1;
            space = end;
        }
        
        html = ShowSmallBullets(html, start, end, space, show, 0);
    }     
    
    // Show the middle bullets
    start = page;
    start %= show;
    if (start == 0) 
    {
        start = page - show + 1;
        end   = page; 
    }
    else 
    {
        start = page - start + 1; 
        end   = start + show - 1;
    }
       
    if (end >= lastpage) {
        end = lastpage;
    }   
    
    html = ShowLargeBullets(html, page, start, end); 
    
    // Show the small bullets on the right side.
    right = Math.ceil(lastpage/show) - Math.ceil(page/show);
    if (right)
    {
        start = 1;
        end   = right;        
        space = -1;
        if (right > offset) 
        {
            end   = offset + 1;
            space = start;
        }
        
        html = ShowSmallBullets(html, start, end, space, show, lastpage);        
    }        
     
    // Show bullets on page.
    if (lastpage > 1) {
        $('#bullets')[0].innerHTML = html.join('');
    }
    else {
        $('#bullets')[0].innerHTML = "";
    }     
}    

/*
 * Function:	ShowLargeBullets
 *
 * Created on Mar 04, 2014
 * Updated on Mar 07, 2014
 *
 * Description: Shows the large bullets.
 *
 * In:	html, html, page, start, end
 * Out:	html
 *
 */
function ShowLargeBullets(html, page, start, end)
{  
    for (var i = html.length, j = start; j <= end; i++, j++)
    {
        if (j == page){ 
            html[i] = '<div class=\"bullet active\">' + j + '</div>';
        } 
        else {
            html[i] = '<div class=\"bullet\">' + j + '</div>'; 
        }
    }
    
    return html;
}

/*
 * Function:	ShowSmallBullets
 *
 * Created on Mar 02, 2014
 * Updated on Apr 14, 2014
 *
 * Description: Shows the small bullets.
 *
 * In:	html, start, end, space, show, last
 * Out:	html
 *
 */
function ShowSmallBullets(html, start, end, space, show, last)
{
    var page;
    for (var i = html.length, j = start; j <= end; i++, j++) 
    {
        if (j == space) {
            html[i] = '<div class=\"space\">&nbsp;</div>'; 
        } 
        else 
        {
            if (last) 
            {
                page = (Math.ceil(last/show) - end + j) * show;
                if (page > last) {
                    page = last;
                }
            }
            else {
                page = 1 + (j - 1) * show;
            }
            html[i] = '<div class=\"bullet small\">' + page + '</div>';
        }    
    }
      
    return html;
}
