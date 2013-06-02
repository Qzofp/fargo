/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.1
 *
 * File:    fargo-common.js
 *
 * Created on May 04, 2013
 * Updated on Jun 02, 2013
 *
 * Description: Fargo's jQuery and Javascript common functions page.
 *
 */

//////////////////////////////////////////    Main Functions    ///////////////////////////////////////////

/*
 * Function:	GetFargoValues
 *
 * Created on Apr 13, 2013
 * Updated on May 04, 2013
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
 * Function:	SetState
 *
 * Created on May 09, 2013
 * Updated on May 09, 2013
 *
 * Description: Set the state of a page selector.
 * 
 * In:	name, value 
 * Out:	-
 *
 * Note: The state is set on the page in a hidden state selector which always start with the id "#state_".
 *
 */
function SetState(name, value)
{
    var state = "#state_" + name;
    $(state).text(value);
}

/*
 * Function:	GetState
 *
 * Created on May 09, 2013
 * Updated on May 09, 2013
 *
 * Description: Get the state of a page selector.
 * 
 * In:	name
 * Out:	state
 *
 * Note: The state is set on the page in a hidden state selector which always start with the id "#state_".
 *
 */
function GetState(name)
{
    var state = "#state_" + name;
    return $(state).text();
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
 * Function:	SetOptionHandler
 *
 * Created on May 12, 2013
 * Updated on Jun 02, 2013
 *
 * Description: Set the option and show the properties.
 * 
 * In:	-
 * Out:	Option
 *
 */
function SetOptionHandler()
{
    $('#display_system_left .option').removeClass('on dim');  
    $(this).addClass('on');
    
    ShowProperty($(this).text());
}

/*
 * Function:	ShowProperty
 *
 * Created on May 20, 2013
 * Updated on Jun 02, 2013
 *
 * Description: Set the option and show the properties.
 * 
 * In:	property
 * Out:	Property page
 *
 */
function ShowProperty(name)
{
    $('#display_system_right').text(""); 
    $(".option.dim").removeClass("dim");
     
    // Reset state.
    SetState("property", "");
    
    $.ajax({
        url: 'jsonfargo.php?action=option&name=' + name,
        async: false,
        dataType: 'json',
        success: function(json) 
        {    
            $('#display_system_right').append(json.html);
            
            $("#display_system_right .system_scroll").slimScroll({
                    height:'auto',
                    alwaysVisible:'true',
                    color:'dodgerblue'
            });
        } // End Success.        
    }); // End Ajax;    
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
 * Function:	SetSystemKeyHandler
 *
 * Created on May 20, 2013
 * Updated on Jun 02, 2013
 *
 * Description: Set the key from the keyboard. Perform action on the system page.
 * 
 * In:	key, event
 * Out:	System option/property
 *
 */
function SetSystemKeyHandler(key, event)
{      
    switch(key)
    {
        case 37 : // Left arrow.
                  ToggleProperty("left");
                  break;
        
        case 38 : // Up arrow.
                  event.preventDefault();
                  SelectOptionProperty("up");
                  break;
        
        case 39 : // right arrow.
                  ToggleProperty("right");
                  break;
        
        case 40 : // Down arrow.
                  SelectOptionProperty("down");
                  break;
    }
}

/*
 * Function:	SelectOptionProperty
 *
 * Created on Jun 01, 2013
 * Updated on Jun 01, 2013
 *
 * Description: Select a system option or property with the arrow keys.
 * 
 * In:	arrow
 * Out:	-
 *
 */
function SelectOptionProperty(arrow)
{
    var property = $("#display_system_right .on");
    var dim      = $(".option.dim");
    
    // Check if options (left) or properties (right) is selected.
    if (property.length || dim.length)
    {    
        SelectProperty(arrow); 
    }
    else 
    {
        SelectOption(arrow);
    }
}

/*
 * Function:	SelectOption
 *
 * Created on May 20, 2013
 * Updated on Jun 01, 2013
 *
 * Description: Select a system option by moving up or down the options list.
 * 
 * In:	action
 * Out:	-
 *
 */
function SelectOption(action)
{
    var active, target; 
    active = $('.option.on');
    
    if (action == "up")
    {
        if (active.prev('.option').length) {
            target = active.prev('.option'); 
        }
        else {
            target = $('.option:last');
        }        
    }
    else 
    {    
        if (active.next('.option').length) {
            target = active.next('.option'); 
        }
        else {
            target = $('.option:first');
        }
    }
                 
    active.removeClass('on');
    target.addClass('on');
    
    ShowProperty(target.text());
    
    // Reset state.
    SetState("property", "");
}

/*
 * Function:	ToggleProperty
 *
 * Created on Jun 01, 2013
 * Updated on Jun 02, 2013
 *
 * Description: Toggle property on or off.
 *
 * In:	-
 * Out:	-
 *
 */
function ToggleProperty(arrow)
{
    var row;
    var property = $("#display_system_right .on");
    var current = GetState("property");
    var input, value, cursor, number;    
    
    if (!property.length) // Turn property on, enter properties.
    {
        row = $( ".property");
        row.first().toggleClass("on");
        row.first().prev().children().toggleClass("on");
        row.first().children().toggleClass("on");
        
        // Get value if there is an input field.   
        input = row.first().find('input'); 
        if(input.length)
        {    
            value = input.val();
            input.focus().setCursorPosition(value.length);
            SetState("property", value);
        }      
        
        $(".option.on").toggleClass("on dim");
    }
    else // Turn property off, leave properies and return to options.
    {     
        row = $( ".property.on");
        value = current;
        
        // Get value if there is an input field.
        input = row.find('input'); 
        if (input.length)
        {    
            value = input.val();        

            // Check position cursor in text field
            cursor = input.getCursorPosition();
            if (arrow == "left" && cursor > 0) {
                return;
            }
            if (arrow == "right" && cursor < value.length) {
                return;
            }
              
            // Property has change, update value.
            if (current != value) 
            {
                number = row.closest("tr").index();
                ChangeProperty(number, value);
            }              
              
            input.blur();
        }

        row.removeClass('on');
        row.prev().children().removeClass("on");
        row.children().removeClass("on");
        $(".option.dim").toggleClass("on dim");        
    }    
}

/*
 * Function:	SelectProperty
 *
 * Created on May 29, 2013
 * Updated on Jun 02, 2013
 *
 * Description: Select system property.
 *
 * In:	arrow
 * Out:	-
 *
 */
function SelectProperty(arrow)
{
    var active, target; 
    var current = GetState("property");
    var input, value, number;
    
    value = current;
    active = $('.property.on');
    
    if (arrow == "up")
    {
        if (active.prev('.property').length) {
            target = active.prev('.property'); 
        }
        else if (active.prev('tr').children().is('th') && active.prev('tr').index() > 0) {           
            target = active.prev().prev('.property'); 
        }
        else {
            target = $('.property:last');
        }
    }
    else 
    {    
        if (active.next('.property').length) {
            target = active.next('.property');
        }
        else if (active.next('tr').children().is('th')) {
            target = active.next().next('.property'); 
        }
        else {
            target = $('.property:first');
        }        
    }
                 
    active.removeClass('on');
    active.prev().children().removeClass("on");
    active.children().removeClass("on");   
    
    // Get value if there is an input field.
    input = active.find('input');
    if(input.length){
        value = input.val(); 
    }
    // Property has change, update value.
    if (current != value) 
    {
        number = active.closest("tr").index();
        ChangeProperty(number, value);
    }
    
    target.addClass('on'); 
    target.prev().children().addClass("on");
    target.children().addClass("on");  
    
    // Get value if there is an input field.
    input = target.find('input');     
    if (input.length) 
    {
        value = input.val();
        input.focus().setCursorPosition(value.length);
        SetState("property", value);
    }     
}

/*
 * Function:	SetPropertyMouseHandler
 *
 * Created on May 26, 2013
 * Updated on Jun 02, 2013
 *
 * Description: Show property on hover and update value when changed.
 *
 * In:	event
 * Out:	-
 *
 */
function SetPropertyMouseHandler(event)
{
    var row = $(this);
    var rows = $("#display_system_right .property");
    var input = row.find('input');
    var current = GetState("property");
    var number, value;
    
    // Get value if there is an input field.
    value = current;
    if (input.length) { 
        value = input.val();
    }
    
    // Dim option.
    $(".option.on").toggleClass("on dim");
    
    // Show input text field.
    if (event.type == "mouseenter") 
    {
        // Remove "on" from all rows.
        rows.removeClass("on");
        rows.prev().children().removeClass("on");
        rows.children().removeClass("on");
        
        // Turn active row "on".
        row.addClass("on");
        row.prev().children().addClass("on");
        row.children().addClass("on");
        
        if (input.length)
        {
            input.focus().setCursorPosition(value.length);
            SetState("property", value);
        }
    }
    else 
    {        
        // Property has change, update value.
        if (current != value) 
        {
           number = row.closest("tr").index();
           ChangeProperty(number, value);
        }
    }  
}

/*
 * Function:	SetPopupKeyHandler
 *
 * Created on Apr 28, 2013
 * Updated on Apr 28, 2013
 *
 * Description: Disable popup window.
 * 
 * In:	key
 * Out:	disable popup
 *
 */
function SetPopupKeyHandler(key)
{ 
    if (key == 27) {   // ESC key
        SetMaskHandler();
    }    
}

/*
 * Function:	SetPopupHandler
 *
 * Created on Apr 28, 2013
 * Updated on May 20, 2013
 *
 * Description: Set popup handler and show popup box.
 * 
 * In:	event
 * Out:	Popup box.
 *
 */
function SetPopupHandler(event)
{ 
    ShowPopupBox(event.data.title);
    SetState("page", "popup");
    //global_popup = true;
}

/*
 * Function:	ShowPopupBox
 *
 * Created on May 08, 2013
 * Updated on May 24, 2013
 *
 * Description: Show popup box.
 * 
 * In:	event
 * Out:	Popup box.
 *
 */
function ShowPopupBox(title)
{
    var popup = $("#popup");
    var mask = $("#mask");
    
    if (title) {
        $(".title").text(title);
    }
    
    popup.fadeIn("300");
 
    //mask.show();
    mask.fadeIn("300");
}

/*
 * Function:	SetMaskHandler
 *
 * Created on Apr 28, 2013
 * Updated on Apr 28, 2013
 *
 * Description: Remove mask en popup.
 * 
 * In:	-
 * Out:	disable mask and popup
 *
 */
function SetMaskHandler()
{ 
    var media = GetState("media");
    SetState("page", media);
    
    $("#popup").fadeOut("300");    
    $("#mask").fadeOut("300");
    //$("#mask").hide();
    
    //global_popup = false;   
}

/*
 * Function:	ShowMediaTable
 *
 * Created on Apr 05, 2013
 * Updated on May 25, 2013
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

/*
 * Function:	LogEvent
 *
 * Created on May 10, 2013
 * Updated on May 10, 2013
 *
 * Description: Log event to the database log table.
 * 
 * In:	type, event
 * Out:	-
 *
 */
function LogEvent(type, event)
{
    $.ajax
    ({
        url: 'jsonfargo.php?action=log&type=' + type + '&event=' + event,
        async: false,
        dataType: 'json',
        success: function(json) 
        {
            
        } // End succes.
  }); // End Ajax.
}

/*
 * Function:	GetFargoCounter
 *
 * Created on May 12, 2013
 * Updated on May 15, 2013
 *
 * Description: Get the media counter from Fargo.
 *
 * In:	media
 * Out:	counter
 *
 */
function GetFargoCounter(media) 
{
    $.ajax({
        url: 'jsonfargo.php?action=counter&media=' + media,
        async: false,
        dataType: 'json',
        success: function(json) 
        {    
            global_total_fargo = Number(json.counter);        
        } // End Success.        
    }); // End Ajax;
}

/*
 * Function:	GetXbmcCounter
 *
 * Created on May 15, 2013
 * Updated on May 18, 2013
 *
 * Description: Get the media counter from XBMC.
 *
 * In:	media
 * Out:	counter
 *
 */
function GetXbmcCounter(media) 
{
    $.ajax({
        url: 'jsonxbmc.php?action=counter&media=' + media,
        async: false,
        dataType: 'json',
        success: function(json) 
        {    
            if (json.online == true) {
                global_total_xbmc = json.counter;
            }
            else {
                global_total_xbmc = -1;
            }
        } // End Success.        
    }); // End Ajax;
}

/*
 * Function:	setCursorPosition
 *
 * Created on May 27, 2013
 * Updated on May 27, 2013
 *
 * Description: Set the cursor position.
 *
 * In:	pos
 * Out:	-
 *
 * Note: Code from: http://www.jquery4u.com/tutorials/jqueryhtml5-input-focus-cursor-positions/
 *
 */
$.fn.setCursorPosition = function(pos) {
  this.each(function(index, elem) {
    if (elem.setSelectionRange) {
      elem.setSelectionRange(pos, pos);
    } else if (elem.createTextRange) {
      var range = elem.createTextRange();
      range.collapse(true);
      range.moveEnd('character', pos);
      range.moveStart('character', pos);
      range.select();
    }
  });
  return this;
};

/*
 * Function:	getCursorPosition
 *
 * Created on May 27, 2013
 * Updated on May 27, 2013
 *
 * Description: get the cursor position.
 *
 * In:	-
 * Out:	-
 *
 * Note: Code from: http://stackoverflow.com/questions/2897155/get-cursor-position-within-an-text-input-field
 *
 */
(function($) {
    $.fn.getCursorPosition = function() {
        var input = this.get(0);
        if (!input) return; // No (input) element found
        if (document.selection) {
            // IE
           input.focus();
        }
        return 'selectionStart' in input ? input.selectionStart:'' || Math.abs(document.selection.createRange().moveStart('character', -input.value.length));
     }
})(jQuery);

