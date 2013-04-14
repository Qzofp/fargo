<?php

require_once 'tools/toolbox.php';

PageHeader("Progress Bar Test","css/progress.css");

echo "   <H1>Another XBMC Media Catalog</H1>\n";
echo "   <H2>Progress Bar Test</H2>\n";

echo "    <div id=\"counter\"></div>\n";
echo "    <div id=\"online\"></div>\n";
echo "    <div id=\"delta\"></div>\n";

echo "    <div id=\"thumb\"></div>\n";
echo "    <div id=\"title\"></div>\n";
echo "    <div id=\"progress\"></div>\n";

// Cancel button.
echo "   <form name=\"progress\" action=\"importtest.php\" method=\"post\"></br>\n";
echo "    <input type=\"submit\" value=\"Cancel\">";
echo "   </form></br>\n";
?>
    <script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>
    <script type="text/javascript">
        // Start import process.
        function startImport() {
            $.ajax({
                url: 'jsonxbmc.php?action=import',
                success: function() {
                }
            });
        }
        
        // XBMC online check.
        function onlineCheck(){
            $.ajax({
                url: 'jsonxbmc.php?action=online',
                dataType: 'json',
                success: function(json) 
                {                      
                    if(json.online)
                    {
                        $("#online").html('XBMC is Online!');
                        
                        // Start import
                        startImport();
                        return;
                    }
                    else 
                    {
                        $("#online").html('XBMC is Offline!');
                        
                        setTimeout(function() { 
                            onlineCheck(); 
                        },1000);
                    }
                },
                    
                error: function() {
                    $("#error").html('Errorrrr!!!');
                }
            });
        }
        
        // Start receiving progress
        function getProgress(counter, online){
            $.ajax({
                url: 'jsonxbmc.php?action=status',
                dataType: 'json',
                success: function(json) 
                {                           
                    ready = false;
        
                    if (json.online) 
                    {
                        online = 'Online!';                        
                        if (json.delta > 0)
                        {
                            startImport();    
                            $("#progress").html('Movie ID: ' + json.xbmcid);
                        }   
                        else 
                        {
                            $("#progress").html('Gereed!');
                            ready = true;
                        }    
                    }
                    else {
                        online = 'Offline!';
                    }

                    $("#online").html('XBMC is ' + online);
                    $("#counter").html(counter);
                    $("#delta").html('Delta: ' + json.delta);
                    
                    if (json.id > 0 && json.online)
                    {
                        $("#thumb").html('<img src= "images/movies/thumbs/'+ json.xbmcid +'.jpg" />');
                        $("#title").html(json.title);
                    }    
                    
                    // If ready exit progress, else retry.
                    if (ready) {
                        return;
                    }
                    else {
                        setTimeout(function() {
                            getProgress(counter, online); 
                        },1000);
                    }
                    
                    counter++;               
                }
            });
        }

        $(document).ready(function() {
            getProgress(0, '...');
        });
    </script>

<?php
PageFooter("", "", false);
?>
