/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.3
 *
 * File:    fargo.common.js
 *
 * Created on Jun 08, 2013
 * Updated on Oct 13, 2013
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
 * Updated on Jun 03, 2013
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
                 if (value > 0 && value <= 10) {
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
 * Updated on Jun 22, 2013
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
        GetFargoSetting("Hash"); //Returns global_setting_fargo
        password = CryptoJS.MD5(CryptoJS.MD5(string) + global_setting_fargo);
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
 * Updated on Sep 01, 2013
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
    var popup = $(".popup" + type); 
    var mask = $("#mask");
        
    if (title) {
        $(type + " .title").text(title);
    }
    
    if (title == "Login") {
        $("#password").attr("type", "password");
    }
    
    popup.fadeIn("300");
    mask.fadeIn("300");
}

/*
 * Function:	SetCloseHandler
 *
 * Created on Jun 09, 2013
 * Updated on Oct 06, 2013
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
        global_cancel = true;
    }
    
    switch($popup.attr('id'))
    {
        case "action_box"  : // Post ActionBox actions.
                             switch($popup.find(".title").text().split(" ")[0])
                             {
                                case "Import"  : //alert("Close import");
                                                 SetImportCancelHandler(); //Abort import.
                                                 break;
                                                 
                                case "Refresh" : //alert("Finish Refresh"); 
                                                 RefreshMediaTable($popup);   
                                                 break;  
                                             
                                case "Remove"  : //alert("Finish Remove");
                                                 ShowMediaTable(global_media, global_page, global_sort);
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
 * Updated on Sep 22, 2013
 *
 * Description: When refresh media is finished then refresh media thumb on media table.
 * 
 * In:	popup
 * Out:	-
 *
 */
function RefreshMediaTable(popup)
{
    var id, $refresh;
    
    if (popup.find(".cancel").text() == "Finish") 
    {
        id = popup.find(".id").text();                                                   
        $refresh = $("#action_thumb img").attr("src");

        // Refresh image in media table.
        $("#display_content .i" + id).find("img").attr("src", $refresh);
                                                    
    }    
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
 * Updated on Oct 05, 2013
 *
 * Description: Get sort list from one of the Fargo databases.
 * 
 * In:	type, media
 * Out:	global_list_fargo
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
            global_list_fargo = json.list;
        } // End succes.
    }); // End Ajax.
}

/*
 * Function:	GetFargoSetting
 *
 * Created on Jun 08, 2013
 * Updated on Jun 08, 2013
 *
 * Description: Get value from the Fargo settings database.
 * 
 * In:	name
 * Out:	global_setting_fargo
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
            global_setting_fargo = json.value;
        } // End succes.
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
            
            // debug
            //alert(global_total_fargo);
        } // End Success.        
    }); // End Ajax;
}

/*
 * Function:	GetXbmcMediaLimits
 *
 * Created on Jul 22, 2013
 * Updated on Oct 07, 2013
 *
 * Description: Get the XBMC media limits (start and end values).
 *
 * In:	media
 * Out:	counter
 *
 */
function GetXbmcMediaLimits(media) 
{
    $.ajax({
        url: 'jsonfargo.php?action=counter&media=' + media,
        async: false,
        dataType: 'json',
        success: function(json) 
        {           
            global_xbmc_start = Number(json.xbmc.start);
            global_xbmc_end   = Number(json.xbmc.end);
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
/*function GetXbmcCounter(media) 
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
}*/

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