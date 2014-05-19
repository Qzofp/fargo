<?php
/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.5
 *
 * File:    import.php
 *
 * Created on Jul 02, 2013
 * Updated on May 17, 2014
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
 * Updated on May 12, 2014
 *
 * Description: Insert movie in the database.
 *
 * In:  $db, $aMovie
 * Out:	$dkey, $id, Movie in database table "movies".
 *
 */
function InsertMovie($db, $aMovie)
{   
    $aItems = AddEscapeStrings($db, $aMovie);
    
    $sql = "INSERT INTO movies(xbmcid, title, genre, `year`, rating, director, trailer, tagline, plot,".
           " plotoutline, originaltitle, lastplayed, playcount, writer, studio, mpaa, `cast`, country,".
           " imdbnr, runtime, `set`, audio, video, votes, `file`, sorttitle, setid, dateadded, `hash`) ".
           "VALUES ($aItems[0], '$aItems[1]', '$aItems[2]', $aItems[3], $aItems[4], '$aItems[5]', '$aItems[6]', '$aItems[7]',".
           " '$aItems[8]', '$aItems[9]', '$aItems[10]', '$aItems[11]', $aItems[12], '$aItems[13]', '$aItems[14]', '$aItems[15]',".
           " '$aItems[16]', '$aItems[17]', '$aItems[18]', $aItems[19], '$aItems[20]', '$aItems[21]', '$aItems[22]',". 
           " $aItems[23], '$aItems[24]', '$aItems[25]', $aItems[26], '$aItems[27]', unhex('$aItems[28]')) ".
           "ON DUPLICATE KEY UPDATE xbmcid = $aItems[0]";

    mysqli_query($db, $sql);
    $dkey = mysqli_affected_rows($db); // 0 = No changes, 1 = Insert, 2 = Update (0 and 2 = duplicate found).
    
    // Get the auto generated id used in the last query.
    $id = mysqli_insert_id($db);
    return array($dkey, $id);
}

/*
 * Function:	UpdateMovie
 *
 * Created on Sep 15, 2013
 * Updated on Feb 21, 2014
 *
 * Description: Update movie in the database.
 *
 * In:  $db, $id, $aMovie
 * Out:	$dbMovie in database table "movies".
 *
 */
function UpdateMovie($db, $id, $aMovie)
{   
    $aItems = AddEscapeStrings($db, $aMovie);
    
    $sql = "UPDATE movies ".
           "SET xbmcid = $aItems[0], refresh = refresh + 1, title = '$aItems[1]', genre = '$aItems[2]',".
           " `year` = $aItems[3], rating = $aItems[4], director = '$aItems[5]', trailer = '$aItems[6]',".
           " tagline = '$aItems[7]', plot = '$aItems[8]', plotoutline = '$aItems[9]', originaltitle = '$aItems[10]',".
           " lastplayed = '$aItems[11]', playcount = $aItems[12], writer = '$aItems[13]', studio = '$aItems[14]',".
           " mpaa = '$aItems[15]', `cast` = '$aItems[16]', country = '$aItems[17]', imdbnr = '$aItems[18]',".
           " runtime = $aItems[19], `set` = '$aItems[20]', audio = '$aItems[21]', video = '$aItems[22]',".
           " votes = $aItems[23], `file` = '$aItems[24]', sorttitle = '$aItems[25]',".
           " setid = $aItems[26], dateadded = '$aItems[27]', `hash` = UNHEX('$aItems[28]')".
           "WHERE id = $id"; 
    
    QueryDatabase($db, $sql);
}

/*
 * Function:	InsertMovieSet
 *
 * Created on Oct 14, 2013
 * Updated on May 12, 2014
 *
 * Description: Insert movie set in the database.
 *
 * In:  $db, $aMovie
 * Out:	$dkey, Movie in database table "sets".
 *
 */
function InsertMovieSet($db, $aMovie)
{   
    $aItems = AddEscapeStrings($db, $aMovie);
    
    $sql = "INSERT INTO sets(setid, title, sorttitle, playcount, `hash`) ".
           "VALUES ($aItems[0], '$aItems[1]', '$aItems[2]', $aItems[3], UNHEX('$aItems[4]')) ".
           "ON DUPLICATE KEY UPDATE setid = $aItems[0]";

    //$dkey = QueryDatabase($db, $sql);
    mysqli_query($db, $sql);
    $dkey = mysqli_affected_rows($db); // 0 = No changes, 1 = Insert, 2 = Update (0 and 2 = duplicate found).    
    
    return $dkey;
}

/*
 * Function:	InsertMovieSet
 *
 * Created on Nov 23, 2013
 * Updated on Feb 21, 2014
 *
 * Description: Update movie set in the database.
 *
 * In:  $db, $id, $aMovie
 * Out:	Movie Set in database table "sets".
 *
 */
function UpdateMovieSet($db, $id, $aMovie)
{   
    $aItems = AddEscapeStrings($db, $aMovie);
        
    $sql = "UPDATE sets ".
           "SET setid = $aItems[0], refresh = refresh + 1, title = '$aItems[1]', sorttitle = '$aItems[2]',".
           " playcount = $aItems[3], `hash` = UNHEX('$aItems[4]') ".
           "WHERE id = $id";

    QueryDatabase($db, $sql); 
}

/*
 * Function:	InsertTVShow
 *
 * Created on Apr 19, 2013
 * Updated on May 17, 2014
 *
 * Description: Insert TV Show in the database.
 *
 * In:  $db, $aTVShow
 * Out:	$dkey, $id, TV Show in database table "tvshows".
 *
 */
function InsertTVShow($db, $aTVShow)
{
    $aItems = AddEscapeStrings($db, $aTVShow);
    
    $sql = "INSERT INTO tvshows(xbmcid, title, genre, `year`, rating, plot, studio, mpaa, `cast`,".
           " playcount, episode, imdbnr, premiered, votes, lastplayed, `file`,".
           " originaltitle, sorttitle, season, episodeguide, watchedepisodes, dateadded, `hash`) ". 
           "VALUES ($aItems[0], '$aItems[1]', '$aItems[2]', $aItems[3], $aItems[4], '$aItems[5]', '$aItems[6]',".
           " '$aItems[7]', '$aItems[8]', $aItems[9], $aItems[10], '$aItems[11]', '$aItems[12]', $aItems[13],".
           " '$aItems[14]', '$aItems[15]', '$aItems[16]', '$aItems[17]', $aItems[18],".
           " '$aItems[19]', $aItems[20], '$aItems[21]', UNHEX('$aItems[22]')) ".
           "ON DUPLICATE KEY UPDATE xbmcid = $aItems[0]";
    
    //$dkey = QueryDatabase($db, $sql);
    mysqli_query($db, $sql);
    $dkey = mysqli_affected_rows($db); // 0 = No changes, 1 = Insert, 2 = Update (0 and 2 = duplicate found).    
    
    // Get the auto generated id used in the last query.
    $id = mysqli_insert_id($db); 
    return array($dkey, $id);
}

/*
 * Function:	UpdateTVShow
 *
 * Created on Sep 22, 2013
 * Updated on Feb 22, 2014
 *
 * Description: Update TV Show in the database.
 *
 * In:  $db, $id, $aTVShow
 * Out:	TV Show in database table "tvshows".
 *
 */
function UpdateTVShow($db, $id, $aTVShow)
{
    $aItems = AddEscapeStrings($db, $aTVShow);
    
    $sql = "UPDATE tvshows ".
           "SET xbmcid = $aItems[0], refresh = refresh + 1, title = '$aItems[1]', genre = '$aItems[2]',".
           " `year` = $aItems[3], rating = $aItems[4], plot = '$aItems[5]', studio = '$aItems[6]',".
           " mpaa = '$aItems[7]', `cast` = '$aItems[8]', playcount = $aItems[9], episode = $aItems[10],".
           " imdbnr = '$aItems[11]', premiered = '$aItems[12]', votes = $aItems[13], lastplayed = '$aItems[14]',".
           " `file` = '$aItems[15]', originaltitle = '$aItems[16]', sorttitle = '$aItems[17]', season = $aItems[18],".
           " episodeguide = '$aItems[19]', watchedepisodes = $aItems[20], dateadded = '$aItems[21]',".
           " `hash` = UNHEX('$aItems[22]') ".
           "WHERE id = $id";
      
    QueryDatabase($db, $sql);
}

/*
 * Function:	InsertTVShowSeason
 *
 * Created on Oct 20, 2013
 * Updated on May 17, 2014
 *
 * Description: Insert TV Show Season in the database.
 *
 * In:  $db, $aSeason
 * Out:	$dkey, Season in database table "seasons".
 *
 */
function InsertTVShowSeason($db, $aSeason)
{   
    $aItems = AddEscapeStrings($db, $aSeason);
    
    $sql = "INSERT INTO seasons(seasonid, title, tvshowid, showtitle, playcount, season, episode,".
           " watchedepisodes, hash) ".
           "VALUES ($aItems[0], '$aItems[1]', $aItems[2], '$aItems[3]', $aItems[4], $aItems[5], $aItems[6],".
           " $aItems[7], UNHEX('$aItems[8]')) ".
           "ON DUPLICATE KEY UPDATE seasonid = $aItems[0]";

    //$dkey = QueryDatabase($db, $sql);
    mysqli_query($db, $sql);
    $dkey = mysqli_affected_rows($db); // 0 = No changes, 1 = Insert, 2 = Update (0 and 2 = duplicate found).    
    
    return $dkey;
}

/*
 * Function:	UpdateTVShowSeason
 *
 * Created on Dec 02, 2013
 * Updated on Feb 21, 2014
 *
 * Description: Update TV Show Season in the database.
 *
 * In:  $db, $aSeason
 * Out:	Season in database table "seasons".
 *
 */
function UpdateTVShowSeason($db, $id, $aSeason)
{   
    $aItems = AddEscapeStrings($db, $aSeason);

    $sql = "UPDATE seasons ".
           "SET seasonid = $aItems[0], refresh = refresh + 1, title = '$aItems[1]', tvshowid = $aItems[2],".
           " showtitle = '$aItems[3]', playcount = $aItems[4], season = $aItems[5], episode = $aItems[6],".
           "watchedepisodes = $aItems[7], `hash` = UNHEX('$aItems[22]') ".
           "WHERE id = $id";

    QueryDatabase($db, $sql); 
}

/*
 * Function:	InsertTVShowEpisode
 *
 * Created on Oct 26, 2013
 * Updated on May 17, 2014
 *
 * Description: Insert TV Show Episode in the database.
 *
 * In:  $db, $aEpisode
 * Out:	$dkey, Episode in database table "episodes".
 *
 */
function InsertTVShowEpisode($db, $aEpisode)
{   
    $aItems = AddEscapeStrings($db, $aEpisode);
    
    $sql = "INSERT INTO episodes(episodeid, tvshowid, title, originaltitle, rating, writer, director, `cast`,".
           " plot, playcount, episode, firstaired, lastplayed, dateadded, votes, `file`, showtitle, season, audio,".
           " video, runtime, `hash`) ".
           "VALUES ($aItems[0], $aItems[1], '$aItems[2]', '$aItems[3]', $aItems[4], '$aItems[5]', '$aItems[6]',".
           " '$aItems[7]', '$aItems[8]', $aItems[9], $aItems[10], '$aItems[11]', '$aItems[12]', '$aItems[13]',".
           " $aItems[14], '$aItems[15]', '$aItems[16]', $aItems[17], '$aItems[18]', '$aItems[19]',".
           " $aItems[20], UNHEX('$aItems[21]')) ".
           "ON DUPLICATE KEY UPDATE episodeid = $aItems[0]"; 
    
    //$dkey = QueryDatabase($db, $sql); 
    mysqli_query($db, $sql);
    $dkey = mysqli_affected_rows($db); // 0 = No changes, 1 = Insert, 2 = Update (0 and 2 = duplicate found).    
    
    return $dkey;
}

/*
 * Function:	UpdateTVShowEpisode
 *
 * Created on Nov 29, 2013
 * Updated on Feb 21, 2014
 *
 * Description: Refresh TV Show Episode in the database.
 *
 * In:  $db, $id, $aEpisode
 * Out:	Episode in database table "episodes".
 *
 */
function UpdateTVShowEpisode($db, $id, $aEpisode)
{   
    $aItems = AddEscapeStrings($db, $aEpisode);
     
    $sql = "UPDATE episodes ".
           "SET episodeid = $aItems[0], refresh = refresh + 1, tvshowid = $aItems[1], title = '$aItems[2]',".
           " originaltitle = '$aItems[3]', rating = $aItems[4], writer = '$aItems[5]', director = '$aItems[6]',".
           " `cast` = '$aItems[7]', plot = '$aItems[8]', playcount = $aItems[9], episode = $aItems[10],".
           " firstaired = '$aItems[11]', lastplayed = '$aItems[12]', dateadded = '$aItems[13]', votes = $aItems[14],".
           " `file` = '$aItems[15]', showtitle = '$aItems[16]', season = $aItems[17], audio = '$aItems[18]',".
           " video = '$aItems[19]', runtime = $aItems[20], `hash` = UNHEX('$aItems[21]') ".
           "WHERE id = $id";
    
    QueryDatabase($db, $sql);
}

/*
 * Function:	InsertAlbum
 *
 * Created on Apr 20, 2013
 * Updated on May 17, 2014
 *
 * Description: Insert music album in the database.
 *
 * In:  $db, $aAlbum
 * Out:	$dkey, $id, Music album in database table "albums".
 *
 */
function InsertAlbum($db, $aAlbum)
{
    $aItems = AddEscapeStrings($db, $aAlbum);      
    
    $sql = "INSERT INTO albums(xbmcid, title, description, artist, genre, theme, mood, style, `type`, albumlabel,".
           " rating, `year`, mbalbumid, mbalbumartistid, playcount, displayartist, sorttitle, `hash`) ".
           "VALUES ($aItems[0], '$aItems[1]', '$aItems[2]', '$aItems[3]', '$aItems[4]', '$aItems[5]', '$aItems[6]',".
           " '$aItems[7]', '$aItems[8]', '$aItems[9]', $aItems[10], $aItems[11], '$aItems[12]', '$aItems[13]',".
           " $aItems[14], '$aItems[15]', '$aItems[16]', UNHEX('$aItems[17]')) ".
           "ON DUPLICATE KEY UPDATE xbmcid = $aItems[0]";
      
    //$dkey = QueryDatabase($db, $sql); 
    mysqli_query($db, $sql);
    $dkey = mysqli_affected_rows($db); // 0 = No changes, 1 = Insert, 2 = Update (0 and 2 = duplicate found).        
    
    // Get the auto generated id used in the last query.
    $id = mysqli_insert_id($db); 
    return array($dkey, $id);
}

/*
 * Function:	UpdateAlbum
 *
 * Created on Sep 22, 2013
 * Updated on Feb 21, 2014
 *
 * Description: Update music album in the database.
 *
 * In:  $db, $id, $aAlbum
 * Out:	Music album in database table "albums".
 *
 */
function UpdateAlbum($db, $id, $aAlbum)
{
    $aItems = AddEscapeStrings($db, $aAlbum); 
    
    $sql = "UPDATE albums ".
           "SET xbmcid = $aItems[0], refresh = refresh + 1, title = '$aItems[1]', description = '$aItems[2]',".
           " artist = '$aItems[3]', genre = '$aItems[4]', theme = '$aItems[5]', mood = '$aItems[6]',".
           " style = '$aItems[7]', type = '$aItems[8]', albumlabel = '$aItems[9]', rating = $aItems[10],".
           " `year` = $aItems[11], mbalbumid = '$aItems[12]', mbalbumartistid = '$aItems[13]',".
           " playcount = $aItems[14], displayartist = '$aItems[15]', sorttitle = '$aItems[16]',".
           " `hash` = UNHEX('$aItems[17]') ". 
           "WHERE id = $id";
            
    QueryDatabase($db, $sql);
}

/*
 * Function:	InsertGenres
 *
 * Created on Jun 22, 2013
 * Updated on Feb 20, 2014
 *
 * Description: Insert genres in the database.
 *
 * In:  $db, $aGenres, $media
 * Out:	Genres in database table genres.
 *
 * Note: http://stackoverflow.com/questions/1361340/how-to-insert-if-not-exists-in-mysql
 * 
 */
function InsertGenres($db, $aGenres, $media)
{
    foreach ($aGenres as $genre)
    {
        $genre = mysqli_real_escape_string($db, $genre);
 
        $sql = "INSERT INTO genres(media, genre) ".
               "VALUES ('$media','$genre') ".
               "ON DUPLICATE KEY UPDATE id = id";
            
        QueryDatabase($db, $sql);
    }
}

/*
 * Function:	InsertGenreToMedia
 *
 * Created on Jun 26, 2013
 * Updated on Feb 20, 2014
 *
 * Description: Insert genres linked to media in the database.
 *
 * In:  $db, $aGenres, $mediaid, $media
 * Out:	Genres in database table genretomovie, genretotvshows or genretomusic table.
 *
 */
function InsertGenreToMedia($db, $aGenres, $mediaid, $media)
{ 
    // Remove dublicate entries.
    $aGenres = array_unique($aGenres);
    
    foreach ($aGenres as $genre)
    {
        $genre = mysqli_real_escape_string($db, $genre);
        
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
               "SELECT id, $mediaid FROM genres ".
               "WHERE genre = '$genre' AND media = '$media'" ; 
            
        QueryDatabase($db, $sql);
    }
}
