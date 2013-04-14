/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.1
 *
 * File:    fargoscript.js
 *
 * Created on Apr 05, 2013
 * Updated on Apr 10, 2013
 *
 * Description: Fargo's jQuery and Javascript functions page.
 *
 */

/*
 * Function:	ShowFargoMedia
 *
 * Created on Apr 06, 2013
 * Updated on Apr 08, 2013
 *
 * Description: Load the media from Fargo.
 *
 * In:	page
 * Out:	Media
 *
 */

function ShowFargoMedia()
{
    
}


/*
 * Function:	LoadFargoMedia
 *
 * Created on Apr 06, 2013
 * Updated on Apr 08, 2013
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
            // Initialize start values.
            var media='movies';
            $("#movies").addClass("on");
            var page=1;
            var sort = '';
            
            // Show Prev and Next buttons if there is more than 1 page.
            if (json.lastpage > 1)
            {
                $("#prev").css("visibility", "visible");
                $("#next").css("visibility", "visible");
            }    
            
            // Check if key is pressed.
            $(document).keypress(function(event) 
            {
               $("#sort").css("visibility", "visible");
               sort = String.fromCharCode(event.which).toUpperCase();
               
               ShowMediaTable(media, page, json.column, sort);
            });
            
          
            ShowMediaTable(media, page, json.column, sort);
            $("#movies, #tvshows, #music, #system, #prev, #next").click(function()
            {   
                switch ($(this).attr('id'))
                {
                    case 'movies'  : media = ChangeMedia('movies');
                                     break;
                             
                    case 'tvshows' : media = ChangeMedia('tvshows');
                                     break;
                             
                    case 'music'   : media = ChangeMedia('music'); 
                                     break;
            
                    case 'system'  : media = ChangeMedia('system');   
                                     break;
                         
                    case 'prev'    : page = ClickNextPrev('prev', page, json.lastpage);
                                     break;
                             
                    case 'next'    : page = ClickNextPrev('next', page, json.lastpage);
                                     break;
                }
                
                //alert(sort);
                
                ShowMediaTable(media, page, json.column, sort);
            }); 
        
        }  // End Succes.
    });    
}

/*
 * Function:	ChangeMedia
 *
 * Created on Apr 08, 2013
 * Updated on Apr 08, 2013
 *
 * Description: .
 *
 * In:	media
 * Out:	media
 *
 */
function ChangeMedia(media)
{   
    var aMedia = ['movies','tvshows','music','system'];
    
    $("#sort").css("visibility", "hidden");
    
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
 * Function:	ClickNextPrev
 *
 * Created on Apr 07, 2013
 * Updated on Apr 08, 2013
 *
 * Description: .
 *
 * In:	id, page, lastpage
 * Out:	page
 *
 */
function ClickNextPrev(id, page, lastpage)
{
    page = (id == 'next') ? page + 1 : page - 1;
                
    //Check for min.
    if (page == 0) {
        page = lastpage;
    }  
                
    //Check for max.
    else if (page==lastpage+1) {
        page = 1;
    }
      
    return page;
}


/*
 * Function:	ShowMediaTable
 *
 * Created on Apr 05, 2013
 * Updated on Apr 08, 2013
 *
 * Description: Shows the media table.
 *
 * In:	page, col
 * Out:	Media Table
 *
 */
function ShowMediaTable(media, page, col, sort)
{
    $.ajax
    ({
        url: 'jsonfargo.php?action=' + media + '&page=' + page + '&sort=' + sort,
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
            
            $('#sort').html(sort);
        }    
    });
}