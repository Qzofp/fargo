<?php
/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.3
 *
 * File:    import_json.php
 *
 * Created on Jul 02, 2013
 * Updated on Nov 03, 2013
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
 * Updated on Nov 02, 2013
 *
 * Description: Insert movie in the database.
 *
 * In:  $aMovie
 * Out:	Movie in database table "movies".
 *
 */
function InsertMovie($aMovie)
{   
    $db = OpenDatabase();
    $aItems = AddEscapeStrings($db, $aMovie);
    
    $sql = "INSERT INTO movies(xbmcid, title, genre, `year`, rating, director, trailer, tagline, plot,".
           " plotoutline, originaltitle, lastplayed, playcount, writer, studio, mpaa, `cast`, country,".
           " imdbnr, runtime, `set`, audio, video, votes, `file`, sorttitle, setid, dateadded) ".
           "VALUES ($aItems[0], '$aItems[1]', '$aItems[2]', $aItems[3], $aItems[4], '$aItems[5]', '$aItems[6]', '$aItems[7]',".
           " '$aItems[8]', '$aItems[9]', '$aItems[10]', '$aItems[11]', $aItems[12], '$aItems[13]', '$aItems[14]', '$aItems[15]',".
           " '$aItems[16]', '$aItems[17]', '$aItems[18]', $aItems[19], '$aItems[20]', '$aItems[21]', '$aItems[22]',". 
           " $aItems[23], '$aItems[24]', '$aItems[25]', $aItems[26], '$aItems[27]')";

    ExecuteQueryWithEscapeStrings($db, $sql);
    CloseDatabase($db);    
}

/*
 * Function:	UpdateMovie
 *
 * Created on Sep 15, 2013
 * Updated on Nov 02, 2013
 *
 * Description: Update movie in the database.
 *
 * In:  $id, $aMovie
 * Out:	Movie in database table "movies".
 *
 */
function UpdateMovie($id, $aMovie)
{   
    $db = OpenDatabase();
    $aItems = AddEscapeStrings($db, $aMovie);
    
    $sql = "UPDATE movies ".
           "SET xbmcid = $aItems[0], refresh = refresh + 1, title = '$aItems[1]', genre = '$aItems[2]',".
           " `year` = $aItems[3], rating = $aItems[4], director = '$aItems[5]', trailer = '$aItems[6]',".
           " tagline = '$aItems[7]', plot = '$aItems[8]', plotoutline = '$aItems[9]', originaltitle = '$aItems[10]',".
           " lastplayed = '$aItems[11]', playcount = $aItems[12], writer = '$aItems[13]', studio = '$aItems[14]',".
           " mpaa = '$aItems[15]', `cast` = '$aItems[16]', country = '$aItems[17]', imdbnr = '$aItems[18]',".
           " runtime = $aItems[19], `set` = '$aItems[20]', audio = '$aItems[21]', video = '$aItems[22]',".
           " votes = $aItems[23], `file` = '$aItems[24]', sorttitle = '$aItems[25]',".
           " setid = $aItems[26], dateadded = '$aItems[27]'".
           "WHERE id = $id"; 
    
    ExecuteQueryWithEscapeStrings($db, $sql);
    CloseDatabase($db);    
}

/*
 * Function:	MovieToItems
 *
 * Created on Sep 15, 2013
 * Updated on Sep 15, 2013
 *
 * Description: Put movie info in items.
 *
 * In:  $aMovie
 * Out:	$aItems
 *
 */
/*function MovieToItems($aMovie)
{
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
      
    return $aItems;
}*/

/*
 * Function:	InsertMovieSet
 *
 * Created on Oct 14, 2013
 * Updated on Nov 02, 2013
 *
 * Description: Insert movie set in the database.
 *
 * In:  $aMovie
 * Out:	Movie in database table "sets".
 *
 */
function InsertMovieSet($aMovie)
{   
    $db = OpenDatabase();
    $aItems = AddEscapeStrings($db, $aMovie);
    
    $sql = "INSERT INTO sets(setid, title, playcount) ".
           "VALUES ($aItems[0], '$aItems[1]', $aItems[2])";

    ExecuteQueryWithEscapeStrings($db, $sql);
    CloseDatabase($db);    
}

/*
 * Function:	InsertTVShow
 *
 * Created on Apr 19, 2013
 * Updated on Nov 02, 2013
 *
 * Description: Insert TV Show in the database.
 *
 * In:  $aTVShow
 * Out:	TV Show in database table "tvshows".
 *
 */
function InsertTVShow($aTVShow)
{
    //echo "<pre>";
    //echo print_r($aTVShow);
    //echo "</pre>";
    
    $db = OpenDatabase();
    $aItems = AddEscapeStrings($db, $aTVShow);
    
    $sql = "INSERT INTO tvshows(xbmcid, title, genre, `year`, rating, plot, studio, mpaa, `cast`,".
           " playcount, episode, imdbnr, premiered, votes, lastplayed, `file`,".
           " originaltitle, sorttitle, season, watchedepisodes, dateadded) ". 
           "VALUES ($aItems[0], '$aItems[1]', '$aItems[2]', $aItems[3], $aItems[4], '$aItems[5]', '$aItems[6]',".
           " '$aItems[7]', '$aItems[8]', $aItems[9], $aItems[10], '$aItems[11]', '$aItems[12]', $aItems[13],".
           " '$aItems[14]', '$aItems[15]', '$aItems[16]', '$aItems[17]', $aItems[18],".
           " $aItems[19], '$aItems[20]')";
 
    echo $sql;
    
    ExecuteQueryWithEscapeStrings($db, $sql);
    CloseDatabase($db);   
}

/*
 * Function:	UpdateTVShow
 *
 * Created on Sep 22, 2013
 * Updated on Nov 02, 2013
 *
 * Description: Update TV Show in the database.
 *
 * In:  $id, $aTVShow
 * Out:	TV Show in database table "tvshows".
 *
 */
function UpdateTVShow($id, $aTVShow)
{    
    $db = OpenDatabase();
    $aItems = AddEscapeStrings($db, $aTVShow);
    
    $sql = "UPDATE tvshows ".
           "SET xbmcid = $aItems[0], refresh = refresh + 1, title = '$aItems[1]', genre = '$aItems[2]',".
           " `year` = $aItems[3], rating = $aItems[4], plot = '$aItems[5]', studio = '$aItems[6]',".
           " mpaa = '$aItems[7]', `cast` = '$aItems[8]', playcount = $aItems[9], episode = $aItems[10],".
           " imdbnr = '$aItems[11]', premiered = '$aItems[12]', votes = $aItems[13], lastplayed = '$aItems[14]',".
           " `file` = '$aItems[15]', originaltitle = '$aItems[16]', sorttitle = '$aItems[17]',".
           " season = $aItems[18], watchedepisodes = $aItems[19], dateadded = '$aItems[20]' ".
           "WHERE id = $id";
      
    ExecuteQueryWithEscapeStrings($db, $sql);
    CloseDatabase($db);   
}

/*
 * Function:	TVShowToItems
 *
 * Created on Sep 22, 2013
 * Updated on Sep 22, 2013
 *
 * Description: Put TV show info in items.
 *
 * In:  $aMovie
 * Out:	$aItems
 *
 */
/*function TVShowToItems($aTVShow)
{
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
    
    return $aItems;    
}*/    

/*
 * Function:	InsertTVShowSeason
 *
 * Created on Oct 20, 2013
 * Updated on Nov 02, 2013
 *
 * Description: Insert TV Show Season in the database.
 *
 * In:  $aSeason
 * Out:	Season in database table "seasons".
 *
 */
function InsertTVShowSeason($aSeason)
{   
    $db = OpenDatabase();
    $aItems = AddEscapeStrings($db, $aSeason);
    
    $sql = "INSERT INTO seasons(tvshowid, title, showtitle, playcount, season, episode, watchedepisodes) ".
           "VALUES ($aItems[0], '$aItems[1]', '$aItems[2]', $aItems[3], $aItems[4], $aItems[5], $aItems[6])";

    ExecuteQueryWithEscapeStrings($db, $sql);
    CloseDatabase($db);    
}

/*
 * Function:	InsertTVShowEpisode
 *
 * Created on Oct 26, 2013
 * Updated on Nov 02, 2013
 *
 * Description: Insert TV Show Season in the database.
 *
 * In:  $aSeason
 * Out:	Season in database table "seasons".
 *
 */
function InsertTVShowEpisode($aEpisode)
{   
    $db = OpenDatabase();
    $aItems = AddEscapeStrings($db, $aEpisode);
    
    $sql = "INSERT INTO episodes(episodeid, tvshowid, title, originaltitle, rating, writer, director, `cast`,".
           " plot, playcount, episode, firstaired, lastplayed, dateadded, votes, `file`, showtitle, season, audio,".
           " video, runtime) ".
           "VALUES ($aItems[0], $aItems[1], '$aItems[2]', '$aItems[3]', $aItems[4], '$aItems[5]', '$aItems[6]',".
           " '$aItems[7]', '$aItems[8]', $aItems[9], $aItems[10], '$aItems[11]', '$aItems[12]', '$aItems[13]',".
           " $aItems[14], '$aItems[15]', '$aItems[16]', $aItems[17], '$aItems[18]', '$aItems[19]', $aItems[20])";
    
    ExecuteQueryWithEscapeStrings($db, $sql);
    CloseDatabase($db);    
}

/*
 * Function:	InsertAlbum
 *
 * Created on Apr 20, 2013
 * Updated on Nov 03, 2013
 *
 * Description: Insert music album in the database.
 *
 * In:  $aAlbum
 * Out:	Music album in database table "albums".
 *
 */
function InsertAlbum($aAlbum)
{
    $db = OpenDatabase();
    $aItems = AddEscapeStrings($db, $aAlbum);      
    
    $sql = "INSERT INTO music(xbmcid, title, description, artist, genre, theme, mood, style, `type`, albumlabel,".
           " rating, `year`, mbalbumid, mbalbumartistid, playcount, displayartist, sorttitle) ".
           "VALUES ($aItems[0], '$aItems[1]', '$aItems[2]', '$aItems[3]', '$aItems[4]', '$aItems[5]', '$aItems[6]',".
           " '$aItems[7]', '$aItems[8]', '$aItems[9]', $aItems[10], $aItems[11], '$aItems[12]', '$aItems[13]',".
           " $aItems[14], '$aItems[15]', '$aItems[16]')";
      
    ExecuteQueryWithEscapeStrings($db, $sql);
    CloseDatabase($db);  
}

/*
 * Function:	UpdateAlbum
 *
 * Created on Sep 22, 2013
 * Updated on Nov 03, 2013
 *
 * Description: Update music album in the database.
 *
 * In:  $aAlbum
 * Out:	Music album in database table "albums".
 *
 */
function UpdateAlbum($id, $aAlbum)
{
    $db = OpenDatabase();
    $aItems = AddEscapeStrings($db, $aAlbum); 
    
    $sql = "UPDATE music ".
           "SET xbmcid = $aItems[0], refresh = refresh + 1, title = '$aItems[1]', description = '$aItems[2]',".
           " artist = '$aItems[3]', genre = '$aItems[4]', theme = '$aItems[5]', mood = '$aItems[6]',".
           " style = '$aItems[7]', type = '$aItems[8]', albumlabel = '$aItems[9]', rating = $aItems[10],".
           " `year` = $aItems[11], mbalbumid = '$aItems[12]', mbalbumartistid = '$aItems[13]',".
           " playcount = $aItems[14], displayartist = '$aItems[15]', sorttitle = '$aItems[16]' ".
           "WHERE id = $id";   
            
    ExecuteQueryWithEscapeStrings($db, $sql);
    CloseDatabase($db);  
}

/*
 * Function:	AlbumToItems
 *
 * Created on Sep 22, 2013
 * Updated on Sep 22, 2013
 *
 * Description: Put album info in items.
 *
 * In:  $aMovie
 * Out:	$aItems
 *
 */
/*function AlbumToItems($aAlbum)
{
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
    
    return $aItems; 
}*/

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
