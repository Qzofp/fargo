/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.1
 *
 * File:    fargoscript.js
 *
 * Created on Apr 05, 2013
 * Updated on Apr 07, 2013
 *
 * Description: Fargo's jQuery and Javascript functions page.
 *
 */


/*
 * Function:	LoadFargoMedia
 *
 * Created on Apr 06, 2013
 * Updated on Apr 07, 2013
 *
 * Description: Load the media from Fargo.
 *
 * In:	page
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
            var page=1;
            ShowMediaTable(page, json.column);
            
           $("#next, #prev").click(function()
           {

               
               page = ($(this).attr('id')=='next') ? page + 1 : page - 1;
                
                //Check for min.
                if (page==0) {
                    page=json.lastpage;
                }  
                
                //Check for max.
                else if (page==json.lastpage+1) {
                    page=1;
                }
                
                ShowMediaTable(page, json.column); 
            });
                
                        
        }
    });    
}


/*
 * Function:	ClickNextPrev
 *
 * Created on Apr 07, 2013
 * Updated on Apr 07, 2013
 *
 * Description: .
 *
 * In:	page, lastpage
 * Out:	page
 *
 */
function ClickNextPrev(page, lastpage)
{
    $("#next, #prev").click(function()
    {
        page = ($(this).attr('id')=='next') ? page + 1 : page - 1;
                
        //Check for min.
        if (page==0) {
            page=lastpage;
        }  
                
        //Check for max.
        else if (page==lastpage+1) {
            page=1;
        }
    });
    
    alert(page);
    
    return page;
}

/*
 * Function:	ShowMediaTable
 *
 * Created on Apr 05, 2013
 * Updated on Apr 07, 2013
 *
 * Description: Shows the media table.
 *
 * In:	page, col
 * Out:	Media Table
 *
 */
function ShowMediaTable(page, col)
{
    $.ajax
    ({
        url: 'jsonfargo.php?action=movies&page=' + page,
        dataType: 'json',
        success: function(json)
        {  
            var i = 0, j = 0, html = [];
    
            html[i++] = '<table>';
     
            $.each(json, function(key, value)
            {                
                if (j == 0) {
                    html[i++] = '<tr>';
                }
                else if ( j == col) {
                    html[i++] = '</tr>';
                    j = 0;
                }
                
                html[i++] = '<td><img src=\"images/thumbs/' + value.xbmcid + '.jpg\"/></br>' + value.title + '</td>';
                j++;
            });
     
            html[i++] = '</table>';
  
            $('#display_content')[0].innerHTML = html.join('');
        }    
    });
}