/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.1
 *
 * File:    fargo-common.js
 *
 * Created on May 04, 2013
 * Updated on May 09, 2013
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
 * Updated on Apr 13, 2013
 *
 * Description: Set the key from the keyboard handler
 * 
 * In:	event
 * Out:	Media
 *
 */
function SetKeyHandler(event)
{
    var key = event.charCode || event.keyCode || 0;
       
    if (!global_popup)   
    {
        SetMainKeyHandler(key, event);
    }
    else 
    {
        SetPopupKeyHandler(key);
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
 * Updated on May 08, 2013
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
    
    global_popup = true;
}


/*
 * Function:	ShowPopupBox
 *
 * Created on May 08, 2013
 * Updated on May 08, 2013
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
    $("#popup").fadeOut("300");
    
    $("#mask").fadeOut("300");
    //$("#mask").hide();
    
    global_popup = false;   
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
