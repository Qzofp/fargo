<?php

require_once '../../tools/toolbox.php';

PageHeader("Progress Bar Test","progress.css");

echo "   <H1>Progress Bar Test</H1>\n";

?>
    <script type="text/javascript" src="js/jquery-1.5.1.min.js"></script>
    <script type="text/javascript">
        //Start the long running process
        $.ajax({
            url: 'import.php',
            success: function(data) {
            }
        });
        
        //Start receiving progress
        function getProgress(){
            $.ajax({
                url: 'status.php',
                dataType: 'json',
                success: function(data) {                
                    $("#thumb").html('<img src= "../../images/'+ data.xbmcid +'.jpg" />');
                    $("#title").html(data.title);
                    $("#progress").html(data.id);
                    if(data.id < 14){
                        setTimeout('getProgress()',1000);
                        //getProgress();
                    }
                    else {
                        $("#progress").html('Gereed!');
                        return;
                    }
                }
            });
        }

        getProgress();
    </script>

<?php    

echo "    <div id=\"thumb\"></div>\n";
echo "    <div id=\"title\"></div>\n";
echo "    <div id=\"progress\"></div>\n";

PageFooter(false);

?>
