/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.1
 *
 * File:    fargo.common.js
 *
 * Created on Jun 08, 2013
 * Updated on Jun 22, 2013
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
 * Updated on Jun 22, 2013
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

                 break;
             
        case 2 : // Check XBMC Port
                 check = isDecimal(value);
                 break;
             
        case 3 : // Set XBMC Username

                 break;
             
        //case 4 : // Check XBMC Password
        //         break; 
             
        case 6 : // Check Fargo Username

                 break;
             
        //case 7 : // Check Fargo Password
        //         break; 
             
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
 * Updated on Jun 12, 2013
 *
 * Description: Set popup handler and show popup box.
 * 
 * In:	event
 * Out:	Popup box.
 *
 */
function SetPopupHandler(event)
{ 
    ShowPopupBox("#login_box", event.data.title);
    SetState("page", "popup");
}

/*
 * Function:	ShowPopupBox
 *
 * Created on May 08, 2013
 * Updated on Jun 12, 2013
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
        $(".title").text(title);
    }
    
    popup.fadeIn("300");
    mask.fadeIn("300");
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