/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.6
 *
 * File:    fargo.system.js
 *
 * Created on May 04, 2013
 * Updated on Jul 28, 2014
 *
 * Description: Fargo's jQuery and Javascript common system functions page.
 *
 */

//////////////////////////////////////////    Main Functions    ///////////////////////////////////////////

/*
 * Function:	SetSystemHandler
 *
 * Created on May 04, 2013
 * Updated on May 25, 2014
 *
 * Description: Show the system page with minimum options.
 * 
 * In:	event
 * Out:	Media
 *
 */
function SetSystemHandler(event)
{            
   var media = event.data.media;
   var aOptions = event.data.options;
   var $option = $('#display_system_left .option');
   var option_menu;
   
   var last = $option.last().text();
   SetState("page", media);
   
   gSTATE.PAGE = 1;
   gSTATE.SORT = "";
   SetState("genre", "");
   
   gSTATE.MEDIA = ChangeControlBar(media);
   ChangeSubControlBar(media);
   
   $("#header_mode").hide();
   $("#header_info").hide();
   $("#header_sort").hide();
   
   $("#display_left").hide();
   $("#display_right").hide();  
   
   $('#display_content').hide();
   $('#display_thumb').html("");
   $('#display_list').html("");
   $('#bullets').hide();
   $('#bullets')[0].innerHTML = "";
   $('#display_system').show();
   
   if ($('#display_system_left #fargo').length == false) {
       option_menu = '<div id=\"fargo\">Qzofp\'s Fargo</div>';
   }
   if(last != aOptions[aOptions.length-1])
   {
        $.each(aOptions, function(i, value) {
            option_menu += '<div class="option">' + value + '</div>';
        });
        
        $('#display_system_left').append(option_menu);
   }
   
   $option.removeClass('on');
   $('#display_system_left .option').first().addClass('on');   
   
   ShowProperty("Statistics");
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
 * Updated on May 26, 2014
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
    SetState("row", "");
    
    $.ajax({
        url: 'jsonfargo.php?action=option&name=' + name,
        async: false,
        dataType: 'json',
        success: function(json) 
        {    
            $('#display_system_right').append(json.html);
            
            var width = 'auto';
            if (name == 'Event Log') {
                width = '100%';
            }
            
            $("#display_system_right .system_scroll").slimScroll({
                    width:width,
                    height:'auto',
                    alwaysVisible:true,
                    color:'gray'
            });
        } // End Success.        
    }); // End Ajax;    
}

/*
 * Function:	SetSystemKeyHandler
 *
 * Created on May 20, 2013
 * Updated on Jul 04, 2014
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
                  event.preventDefault();
                  SelectOptionProperty("down");
                  break;
                  
        case 13 : // Enter key.
                  event.preventDefault();
                  ActivateProperty();
                  break;
                  
        default : // Key pressed.
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
 * Updated on Jun 08, 2013
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
}

/*
 * Function:	ToggleProperty
 *
 * Created on Jun 01, 2013
 * Updated on Jul 28, 2014
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
            SetState("row", 1); 
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
            if (current != value && value != "") 
            {
                number = row.closest("tr").index();
                value  = CheckForPassword(input, number);
                if (CheckInput(number, value)) {
                    ChangeProperty(number, value);
                }
                else {
                    input.val(current);
                }
            }              
              
            if (input.attr("type") == "password") {
                input.val("******");
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
 * Updated on Jul 28, 2014
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
    
    // Get property row number.
    number = active.closest("tr").index();
    
    // Get value if there is an input field.
    input = active.find('input');
    if (input.length) {
        value = CheckForPassword(input, number);
    }
    
    // Property has change, update value.
    if (current != value && value != "")
    {
        //number = active.closest("tr").index();
        if (CheckInput(number, value)) {
            ChangeProperty(number, value);
        }
        else {
            input.val(current);
        }
    }
    
    target.addClass('on'); 
    target.prev().children().addClass("on");
    target.children().addClass("on");  
    
    // Get value if there is an input field.
    input = target.find('input');     
    if (input.length) 
    {
        value = input.val();
        if (input.attr("type") == "password") {
            input.val("");
        }
        
        input.focus().setCursorPosition(value.length);
        SetState("property", value);
        
        number = target.closest("tr").index();
        SetState("row", number);        
    }     
}

/*
 * Function:	SetPropertyMouseHandler
 *
 * Created on May 26, 2013
 * Updated on Jul 28, 2014
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
    var number, rownr, value;
    
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
            if (input.attr("type") == "password") {
                input.val("");
            }
            
            input.focus().setCursorPosition(value.length);
            SetState("property", value);
            
            number = row.closest("tr").index();
            SetState("row", number);
        }
    }
    else 
    {
        number = row.closest("tr").index();
        rownr  = GetState("row");
        
        if (input.length && number == rownr) {
            value = CheckForPassword(input, number);
        }
        
        // Property has change, update value.
        if (current != value && value != "" && number == rownr)
        {
            if (CheckInput(number, value)) {
                ChangeProperty(number, value);
            }
            else {
                input.val(current);
            }
        }
    }  
}

/*
 * Function:	SetPropertyClickHandler
 *
 * Created on Jun 09, 2013
 * Updated on Jul 05, 2014
 *
 * Description: Handle clicked property event.
 *
 * In:	-
 * Out:	Action
 *
 */
function SetPropertyClickHandler()
{
    var option = $("#display_system_left .dim").text();
    var number = $(".property.on").closest("tr").index();
    
    //console.log(number);
    
    if (option == "Library") 
    {
        switch(number)
        {
            case 1: //Remove Movies library...
                    CleanPopupBox(ConvertMedia("movies") + " library");
                    break;
                    
            case 2: //Turn Movies "Remove library... with posters & fanart" on or off.
                    $(".property.on .xradio").toggleClass( "on" );
                    break;
                    
            case 3: //Import Movies library.
                    SetState("media", "movies");
                    SetImportPopupHandler("movies");
                    break;
                    
            case 5: //Remove TV Shows library...
                    CleanPopupBox(ConvertMedia("tvshows") + " library");
                    break;
                    
            case 6: //Turn TV Shows "Remove library... with posters & fanart" on or off.
                    $(".property.on .xradio").toggleClass( "on" );
                    break;                    
                    
            case 7: //Import TV Shows library.
                    SetState("media", "tvshows");
                    SetImportPopupHandler("tvshows");               
                    break;
                    
            case 9: //Remove Music library...
                    CleanPopupBox(ConvertMedia("music") + " library");
                    break;
                    
            case 10: //Turn Music "Remove library... with covers" on or off.
                    $(".property.on .xradio").toggleClass( "on" );
                    break;                    
                    
            case 11: //Import Music library.
                    SetState("media", "music");
                    SetImportPopupHandler("music");           
                    break;                
        }
    }
    
    if (option == "Event Log" && number == 1) {
        CleanPopupBox("event log");
    }
}

/*
 * Function:	ActivateProperty
 *
 * Created on Jun 10, 2013
 * Updated on Jun 10, 2013
 *
 * Description: Activate property when the enter key is pressed.
 *
 * In:	-
 * Out:	-
 *
 */
function ActivateProperty()
{
    SetPropertyClickHandler();
}

/*
 * Function:	CleanPopupBox
 *
 * Created on Jun 09, 2013
 * Updated on Feb 21, 2014
 *
 * Description: Show removing library popup box. This was known as cleaning library, hence the name.
 *
 * In:	msg
 * Out:	Popup box
 *
 */
function CleanPopupBox(msg)
{       
    $("#action_box .message").text(cSYSTEM.MESSAGE2.replace("[dummy]", msg));
    $("#action_wrapper").hide();
    $("#action_title").html("&nbsp;");
    $("#action_sub").text("");
    
    if (msg == "event log") {
        ShowPopupBox("#action_box", cSYSTEM.TITLE2);
    }
    else{
        ShowPopupBox("#action_box", cSYSTEM.TITLE1);
    }
    
    SetState("page", "popup");
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