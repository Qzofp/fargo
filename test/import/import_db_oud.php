<?php
/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.1
 *
 * File:    import_json.php
 *
 * Created on Jul 02, 2013
 * Updated on Jul 02, 2013
 *
 * Description: The XBMC import database functions page. 
 * 
 * Note: This page contains database functions for storing media from XBMC (used by import.php).
 *
 */

////////////////////////////////////////    Database Functions    /////////////////////////////////////////

/*
 * Function:	InsertMovie
 *
 * Created on Mar 09, 2013
 * Updated on Jun 22, 2013
 *
 * Description: Insert movie in the database.
 *
 * In:  $aMovie
 * Out:	Movie in database table "movies".
 *
 */
function InsertMovie($aMovie)
{
    
    //debug
    //echo "<pre>";
    //print_r($aMovie);
    //echo "</pre></br>";
    
    $aItems[0] = $aMovie["xbmcid"];
    $aItems[1] = $aMovie["title"]; 
    $aItems[2] = $aMovie["genre"];
    $aItems[3] = $aMovie["year"];
    
    $aItems[4] = $aMovie["rating"];
    $aItems[5] = $aMovie["director"];    
    $aItems[6] = $aMovie["trailer"];
    $aItems[7] = $aMovie["tagline"]; 
    
    $aItems[8]  = $aMovie["plot"];
    $aItems[9]  = $aMovie["plotoutline"];    
    $aItems[10] = $aMovie["originaltitle"];
    $aItems[11] = $aMovie["lastplayed"];
    
    $aItems[12] = $aMovie["playcount"];
    $aItems[13] = $aMovie["writer"];    
    $aItems[14] = $aMovie["studio"];
    $aItems[15] = $aMovie["mpaa"];
    
    $aItems[16] = $aMovie["cast"];
    $aItems[17] = $aMovie["country"];   
    $aItems[18] = $aMovie["imdbnr"];
    $aItems[19] = $aMovie["runtime"];
    
    $aItems[20] = $aMovie["fanart"];
    $aItems[21] = $aMovie["poster"];
    $aItems[22] = $aMovie["thumb"];    
    $aItems[23] = $aMovie["set"];
    
    $aItems[24] = null; //$aMovie["showlink"];      
    $aItems[25] = $aMovie["audio"];
    $aItems[26] = $aMovie["video"];    
    $aItems[27] = $aMovie["top250"];    
    
    $aItems[28] = $aMovie["votes"];
    $aItems[29] = $aMovie["file"];      
    $aItems[30] = $aMovie["sorttitle"];   
    $aItems[31] = null; //$aMovie["resume"];    
    
    $aItems[32] = $aMovie["setid"];
    $aItems[33] = $aMovie["dateadded"];      
    $aItems[34] = null; //$aMovie["tag"];    
    
    $db = OpenDatabase();
    $aItems = AddEscapeStrings($db, $aItems);
    
    $sql = "INSERT INTO movies(xbmcid, title, genre, `year`, rating, director, trailer, tagline, plot,".
           " plotoutline, originaltitle, lastplayed, playcount, writer, studio, mpaa, `cast`, country,".
           " imdbnr, runtime, fanart, poster, thumb, `set`, showlink, audio, video, top250, votes,". 
           " `file`, sorttitle, setid, dateadded, tag) ".
           "VALUES ($aItems[0], '$aItems[1]', '$aItems[2]', $aItems[3], $aItems[4], '$aItems[5]', '$aItems[6]', '$aItems[7]',".
           " '$aItems[8]', '$aItems[9]', '$aItems[10]', '$aItems[11]', $aItems[12], '$aItems[13]', '$aItems[14]', '$aItems[15]',".
           " '$aItems[16]', '$aItems[17]', '$aItems[18]', $aItems[19], '$aItems[20]', '$aItems[21]', '$aItems[22]', '$aItems[23]',". 
           " '$aItems[24]', '$aItems[25]', '$aItems[26]', $aItems[27], $aItems[28], '$aItems[29]', '$aItems[30]', $aItems[32],".
           " '$aItems[33]', '$aItems[34]')";

    ExecuteQueryWithEscapeStrings($db, $sql);
    CloseDatabase($db);    
}

/*
 * Function:	InsertTVShow
 *
 * Created on Apr 19, 2013
 * Updated on Jun 25, 2013
 *
 * Description: Insert TV Show in the database.
 *
 * In:  $aTVShow
 * Out:	TV Show in database table "tvshows".
 *
 */
function InsertTVShow($aTVShow)
{
    //debug
    //echo "<pre>";
    //print_r($aTVShow);
    //echo "</pre></br>";    
    
    $aItems[0] = $aTVShow["xbmcid"];
    $aItems[1] = $aTVShow["title"];
    $aItems[2] = $aTVShow["genre"];
    $aItems[3] = $aTVShow["year"]; 
      
    $aItems[4] = $aTVShow["rating"];
    $aItems[5] = $aTVShow["plot"];
    $aItems[6] = $aTVShow["studio"];
    $aItems[7] = $aTVShow["mpaa"];    

    $aItems[8]  = $aTVShow["cast"];
    $aItems[9]  = $aTVShow["playcount"];
    $aItems[10] = $aTVShow["episode"];  
    $aItems[11] = $aTVShow["imdbnr" ];
    
    $aItems[12] = $aTVShow["premiered"];
    $aItems[13] = $aTVShow["votes"];
    $aItems[14] = $aTVShow["lastplayed"];  
    $aItems[15] = $aTVShow["fanart"];
        
    $aItems[16] = $aTVShow["poster"];
    $aItems[17] = $aTVShow["thumb"];
    $aItems[18] = $aTVShow["file"];
    $aItems[19] = $aTVShow["originaltitle"];   
    
    $aItems[20] = $aTVShow["sorttitle"];
    $aItems[21] = $aTVShow["episodeguide"];
    $aItems[22] = $aTVShow["season"];
    $aItems[23] = $aTVShow["watchedepisodes"];        

    $aItems[24] = $aTVShow["dateadded"]; 
    $aItems[25] = null; //$aTVShow["tag"]      = $aXbmc["tag"];
    
    $db = OpenDatabase();
    $aItems = AddEscapeStrings($db, $aItems);    
    
    $sql = "INSERT INTO tvshows(xbmcid, title, genre, `year`, rating, plot, studio, mpaa, `cast`,".
           " playcount, episode, imdbnr, premiered, votes, lastplayed, fanart, poster, thumb, `file`,".
           " originaltitle, sorttitle, episodeguide, season, watchedepisodes, dateadded, tag) ". 
           "VALUES ($aItems[0], '$aItems[1]', '$aItems[2]', $aItems[3], $aItems[4], '$aItems[5]', '$aItems[6]', '$aItems[7]',".
           " '$aItems[8]', $aItems[9], $aItems[10], '$aItems[11]', '$aItems[12]', '$aItems[13]', '$aItems[14]', '$aItems[15]',".
           " '$aItems[16]', '$aItems[17]', '$aItems[18]', '$aItems[19]', '$aItems[20]', '$aItems[21]', $aItems[22], $aItems[23],". 
           " '$aItems[24]', '$aItems[25]')";
      
    ExecuteQueryWithEscapeStrings($db, $sql);
    CloseDatabase($db);   
}

/*
 * Function:	InsertAlbum
 *
 * Created on Apr 20, 2013
 * Updated on Jun 25, 2013
 *
 * Description: Insert music album in the database.
 *
 * In:  $aAlbum
 * Out:	Music album in database table "albums".
 *
 */
function InsertAlbum($aAlbum)
{
    //debug
    //echo "<pre>";
    //print_r($aAlbum);
    //echo "</pre></br>";    
    
    $aItems[0] = $aAlbum["xbmcid"];
    $aItems[1] = $aAlbum["title"];
    $aItems[2] = $aAlbum["description"];    
    $aItems[3] = $aAlbum["artist"];

    $aItems[4] = $aAlbum["genre"];
    $aItems[5] = $aAlbum["theme"];
    $aItems[6] = $aAlbum["mood"];
    $aItems[7] = $aAlbum["style"];
    
    $aItems[8]  = $aAlbum["type"];    
    $aItems[9]  = $aAlbum["albumlabel"];
    $aItems[10] = $aAlbum["rating"];
    $aItems[11] = $aAlbum["year"];
    
    $aItems[12] = $aAlbum["mbalbumid"];        
    $aItems[13] = $aAlbum["mbalbumartistid"];
    $aItems[14] = $aAlbum["fanart"];
    $aItems[15] = $aAlbum["cover"];
    
    $aItems[16] = $aAlbum["playcount"];
    $aItems[17] = $aAlbum["displayartist"];  
    $aItems[18] = $aAlbum["sorttitle"];  
    $aItems[19] = null; //$aAlbum["genreid"];
    
    $aItems[20] = null; //$aAlbum["artistid"];

    $db = OpenDatabase();
    $aItems = AddEscapeStrings($db, $aItems);      
    
    $sql = "INSERT INTO music(xbmcid, title, description, artist, genre, theme, mood, style, type, albumlabel,".
           " rating, `year`, mbalbumid, mbalbumartistid, fanart, cover, playcount, displayartist, sorttitle,".
           " genreid, artistid) ".
           "VALUES ($aItems[0], '$aItems[1]', '$aItems[2]', '$aItems[3]', '$aItems[4]', '$aItems[5]', '$aItems[6]', '$aItems[7]',".
           " '$aItems[8]', '$aItems[9]', $aItems[10], $aItems[11], '$aItems[12]', '$aItems[13]', '$aItems[14]', '$aItems[15]',".
           " $aItems[16], '$aItems[17]', '$aItems[18]', '$aItems[19]', '$aItems[20]')";
      
    ExecuteQueryWithEscapeStrings($db, $sql);
    CloseDatabase($db);  
}

/*
 * Function:	InsertGenres
 *
 * Created on Jun 22, 2013
 * Updated on Jun 23, 2013
 *
 * Description: Insert genres in the database.
 *
 * In:  $aGenres, $media
 * Out:	Genres in database table genres.
 *
 */
function InsertGenres($aGenres, $media)
{
    foreach ($aGenres as $genre)
    {
        $find = "SELECT * FROM genres ".
                "WHERE genre = '$genre' AND media = '$media'";
        
        if (CountRowsWithQuery($find) == 0)
        {
            $sql = "INSERT INTO genres(genre, media) ".
                   "VALUES ('$genre', '$media')";
            
            ExecuteQuery($sql);
        }
    }
}

/*
 * Function:	InsertGenreToMedia
 *
 * Created on Jun 26, 2013
 * Updated on Jun 26, 2013
 *
 * Description: Insert genres linked to media in the database.
 *
 * In:  $aGenres, $media
 * Out:	Genres in database table genretomovie, genretotvshows or genretomusic table.
 *
 */
function InsertGenreToMedia($aGenres, $media)
{
    // Get highest movie id.
    $mediaid = GetLastItemFromTable("id", $media);
    
    foreach ($aGenres as $genre)
    {
        $find = "SELECT id FROM genres ".
                "WHERE genre = '$genre' AND media = '$media'";
        
        $genreid = GetItemFromDatabase("id", $find);        
        if (!empty($genreid))
        {
            switch ($media)
            {
                case "movies"  : $name = "movie";
                                 break;
                            
                case "tvshows" : $name = "tvshow";
                                 break;
                            
                case "music"   : $name = "music";
                                 break;                             
            }
            
            $sql = "INSERT INTO genreto$name(genreid, ".$name."id) ".
                   "VALUES ($genreid, $mediaid)";
            
            ExecuteQuery($sql);
        }
    }
}
?>
