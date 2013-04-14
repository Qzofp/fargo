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
echo "   <form name=\"progress\" action=\"index.php\" method=\"post\"></br>\n";
echo "    <input type=\"submit\" value=\"Cancel\">";
echo "   </form></br>\n";

PageFooter(false);
?>
    <script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>
    <script type="text/javascript">
        //Start import process.
        function startImport() {
            $.ajax({
                url: 'xbmc.php?action=import',
                success: function() {
                }
            });
        }
        
        // XBMC online check.
        function onlineCheck(){
            $.ajax({
                url: 'xbmc.php?action=online',
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

        //Start receiving progress
        function getProgress(counter){
            $.ajax({
                url: 'xbmc.php?action=status',
                dataType: 'json',
                success: function(json) 
                { 
                    $("#counter").html(counter++);
                    $("#thumb").html('<img src= "images/'+ json.xbmcid +'.jpg" />');
                    $("#title").html(json.title);
                    $("#progress").html('Movie ID: ' + json.id);

                    if (json.delta > 0) {
                        onlineCheck();
                        $("#delta").html('Delta: ' + json.delta);
                        //setTimeout('getProgress()',1000);
                        
                        setTimeout(function() {
                            getProgress(counter); 
                        },1000);
                        
                    }   
                    else {
                        $("#progress").html('Gereed!');
                        return;
                    }
                }
            });
        }

        getProgress(0);
        
    </script>


