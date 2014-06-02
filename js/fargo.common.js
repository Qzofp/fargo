/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.5
 *
 * File:    fargo.common.js
 *
 * Created on Jun 08, 2013
 * Updated on Jun 02, 2014
 *
 * Description: Fargo's jQuery and Javascript common functions page.
 *
 */

//////////////////////////////////////////    Main Functions    ///////////////////////////////////////////

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
 * Function:	CheckForPassword
 *
 * Created on Jun 08, 2013
 * Updated on Jun 09, 2013
 *
 * Description: Check if there is a password and if so then hash password.
 * 
 * In:	input
 * Out:	value
 *
 */
function CheckForPassword(input)
{ 
    var value = input.val();
    
    if (input.attr("type") == "password") 
    {
        // [Future option] Password length check.
        /*if (value.length < 1) {
            value = "";
            // Show message.
        }*/

        // Hash password.
        value = HashPassword(value);
            
        input.val("******");
    }
    
    return value;
}

/*
 * Function:	CheckInput
 *
 * Created on Jun 22, 2013
 * Updated on Jun 22, 2013
 *
 * Description: Check input fields. If value is wrong return current value.
 * 
 * In:	number, value
 * Out:	true|false
 *
 */
function CheckInput(number, value)
{ 
    var check = false;
    var option = $('#display_system_left .dim').text();
    
    switch (option)
    {
        case "Settings" : check = CheckSettings(number, value);
                          break;
        
        default         : break;
    }
    
    return check;
}

/*
 * Function:	CheckSettings
 *
 * Created on Jun 22, 2013
 * Updated on Dec 11, 2013
 *
 * Description: Check input fields. If value is wrong return current value.
 * 
 * In:	number, value
 * Out:	true|false
 *
 */
function CheckSettings(number, value)
{
    var check = false;
    
    switch (number)
    {
        case 1 : // Check XBMC Connection
                 check = true;
                 break;
             
        case 2 : // Check XBMC Port
                 check = isDecimal(value);
                 break;
             
        case 3 : // Set XBMC Username
                 check = true;
                 break;
             
        case 4 : // Check XBMC Password
                 check = true;
                 break; 
             
        case 6 : // Check Fargo Username
                 check = true;
                 break;
             
        case 7 : // Check Fargo Password
                 check = true;
                 break; 
             
        case 9 : // Check Timer
                 if (value > 299 && value <= 3000) {
                     check = true;
                 }    
                 break;  
    }
    
    return check;
}

/*
 * Function:	isDecimal
 *
 * Created on Jun 22, 2013
 * Updated on Jun 22, 2013
 *
 * Description: Check decimal.
 * 
 * In:	n
 * Out:	true|false
 *
 * Note: Code from http://stackoverflow.com/questions/2304052/check-if-a-number-has-a-decimal-place-is-a-whole-number
 *
 */
function isDecimal(n){
    if(n == "")
        return false;

    var strCheck = "0123456789";
    var i;

    for(i in n){
        if(strCheck.indexOf(n[i]) == -1)
            return false;
    }
    return true;
}

/*
 * Function:	HashPassword
 *
 * Created on Jun 09, 2013
 * Updated on Oct 31, 2013
 *
 * Description: Hash password.
 * 
 * In:	string
 * Out:	password
 *
 */
function HashPassword(string)
{
    var password = string;
    
    if ($.trim(string).length)
    {    
        GetFargoSetting("Hash"); //Returns gSTATE.SETTING
        password = CryptoJS.MD5(CryptoJS.MD5(string) + gSTATE.SETTING);
    }
    
    return password;
}

/*
 * Function:	SetPopupHandler
 *
 * Created on Apr 28, 2013
 * Updated on Jun 27, 2013
 *
 * Description: Set popup handler and show popup box.
 * 
 * In:	event
 * Out:	Popup box.
 *
 */
function SetPopupHandler(event)
{     
    ShowPopupBox(event.data.type, event.data.title);
    SetState("page", "popup");
}

/*
 * Function:	ShowPopupBox
 *
 * Created on May 08, 2013
 * Updated on Feb 26, 2014
 *
 * Description: Show popup box.
 * 
 * In:	type, title
 * Out:	Popup box.
 * 
 * Note: Type is the popup box class i.e. login_size, import, clean, etc.
 *
 */
function ShowPopupBox(type, title)
{
    var $popup = $(".popup" + type); 
    var $mask = $("div#mask");
    
    if (title) {
        $(type + " .title").html(title);
    }
    
    if (title == "Login") {
        $("#password").attr("type", "password");
    }
    
    $popup.fadeIn("300");
    $mask.fadeIn("300");
}

/*
 * Function:	SetCloseHandler
 *
 * Created on Jun 09, 2013
 * Updated on Feb 23, 2013
 *
 * Description: Close import or other popup window.
 * 
 * In:	-
 * Out:	disable mask and popup
 *
 */
function SetCloseHandler()
{
    var $popup = $(".popup:visible");
    
    if ($popup.find(".no").text() == "Cancel") {
        gTRIGGER.CANCEL = true;
    }
    
    switch($popup.attr('id'))
    {
        case "action_box"  : // Post ActionBox actions.
                             switch($popup.find(".title").text().split(" ")[0])
                             {
                                case "Import"  : SetImportCancelHandler(); //Finish or abort import.
                                                 break;
                                                 
                                case "Refresh" : //alert("Finish Refresh"); 
                                                 RefreshMediaTable($popup);   
                                                 break;  
                                             
                                case "Remove"  : if ($popup.find(".title").text().split(" ")[1] != "library") {
                                                    ShowMediaTable(gSTATE.PAGE, gSTATE.SORT);
                                                 }
                                                 break;
                             }
                             ClearActionBox();
                             break;
                          
        case "buttons_box" : //alert ("buttons");
                             ClearButtonsBox();
                             break;
                         
        case "info_box"    : //alert ("info");
                             ClearInfoBox();
                             break;                     
    }
    
    // Close popup.
    SetMaskHandler();
}

/*
 * Function:	RefreshMediaTable
 *
 * Created on Sep 22, 2013
 * Updated on Jun 02, 2014
 *
 * Description: When refresh media is finished then refresh media thumb on media table.
 * 
 * In:	popup
 * Out:	-
 *
 */
function RefreshMediaTable(popup)
{
    var id, $img, $title;
    
    if (popup.find(".cancel").text() == "Finish") 
    {
        id   = popup.find(".id").text();                                                   
        $img = $("#action_thumb img").attr("src");
           
        if ($("#screen").text() == cBUT.LIST)
        {
            switch(GetState("type"))
            {
                case "seasons"  : $title = "<div>" + $("#action_sub").html() + "</div>";
                                  break;
                             
                case "episodes" : $title = '<div style="width: 240px; text-overflow: ellipsis;">' + $("#action_sub").html() + '</div>';
                                  break;
                              
                default : $title = "<div>" + $("#action_title").html() + "</div>";
                          break;
            }              
            
            // Refresh image in media thumb table.
            $("#display_thumb [class^='i" + id + "'] img:first").attr("src", $img);
        
            // Refresh title in media table.
            $("#display_thumb [class^='i" + id + "']").contents().last().replaceWith($title);
        }
        else 
        {
            switch(GetState("type"))
            {
                case "seasons"  : $title = $("#action_sub").html();
                                  break;
                             
                case "episodes" : $title = $("#action_sub").html();
                                  break;
                                  
                case "series"   : $title = $("#action_title").html();
                                  id += "_";
                                  break;
                              
                default : $title = $("#action_title").html();
                          break;
            }  
            
            if (GetState("type") == "seasons" || GetState("type") == "episodes") {
                $title = $("#action_sub").html();
            }
            else {
                $title = $("#action_title").html();
            }
            
            // Refresh image in media list table.
            $("#display_list [class^='i" + id + "'] .poster img").attr("src", $img);
            
            // Refresh title in media list table.
            $("#display_list [class^='i" + id + "'] .title").text($title);
        }     
    }
}

/*
 * Function:	ShortenString
 *
 * Created on Dec 06, 2013
 * Updated on Dec 06, 2013
 *
 * Description: Shorten string and add '...'.
 *
 * In:	string, length
 * Out:	short
 *
 */
function ShortenString(string, length)
{
    var short = string;
      
    if (string.length - length > 0) {
        short = short.substring(0, length).trim() + '...';
    }
    
    return short;
}

/*
 * Function:	SetMaskHandler
 *
 * Created on Apr 28, 2013
 * Updated on Jun 15, 2013
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
    
    $(".popup").fadeOut("300");    
    $("#mask").fadeOut("300"); 
}

/*
 * Function:	GetFargoSortList
 *
 * Created on Jun 27, 2013
 * Updated on Oct 31, 2013
 *
 * Description: Get sort list from one of the Fargo databases.
 * 
 * In:	type, media
 * Out:	gSTATE.LIST
 *
 */
function GetFargoSortList(type, media)
{
    var filter;
    
    if (type == 'Genres') {
        filter = GetState("year");
    }
    else {
        filter = GetState("genre");
    }
    
    $.ajax
    ({
        url: 'jsonfargo.php?action=list&type=' + type + '&filter=' + escape(filter) + '&media=' + media,
        async: false,
        dataType: 'json',
        success: function(json) 
        {
            gSTATE.LIST = json.list;
        } // End succes.
    }); // End Ajax.
}

/*
 * Function:	GetFargoSetting
 *
 * Created on Jun 08, 2013
 * Updated on Oct 31, 2013
 *
 * Description: Get value from the Fargo settings database.
 * 
 * In:	name
 * Out:	gSTATE.SETTING
 *
 */
function GetFargoSetting(name)
{
    $.ajax
    ({
        url: 'jsonfargo.php?action=setting&name=' + name,
        async: false,
        dataType: 'json',
        success: function(json) 
        {
            gSTATE.SETTING = json.value;
        } // End succes.
  }); // End Ajax.
}

/*
 * Function:	LogEvent
 *
 * Created on May 10, 2013
 * Updated on Nov 21, 2013
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
        url: 'jsonmanage.php?action=log&type=' + type + '&event=' + event,
        async: false,
        dataType: 'json',
        success: function(json) 
        {
            
        } // End succes.
  }); // End Ajax.
}

/*
 * Function:	DisplayMessage
 *
 * Created on Jun 15, 2013
 * Updated on Jun 15, 2013
 *
 * Description: Display message.
 *
 * In:	str1, str2, btn, end
 * Out:	message
 *
 */
function DisplayMessage(str1, str2, btn, end)
{   
    var i = 0; 
    
    $(".message").html(str1);
    var timer = setInterval(function(){
    
        i++; 
	
        // End interval loop.
        if (i > end)
        {
            clearInterval(timer);
            $(".message").html(str2);
            $(btn).html("Ok");           
        }  
        
    }, 500);	
}