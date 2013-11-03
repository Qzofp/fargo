/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.3
 *
 * File:    fargo.private.constants.js
 *
 * Created on Oct 23, 2013
 * Updated on Nov 03, 2013
 *
 * Description: Fargo's jQuery and Javascript private contants and globals.
 *
 */

// State globals.
var gSTATE = {
    MEDIA: "movies",
    //TYPE: "titles", // Media type. 
    PAGE: 1,
    LAST: 1, // Last page.
    SORT: "",
    LIST: "",
    SETTING: ""    
};

// Button text constants.
var cBUT = {
    TITLES: "Titles",
    SETS: "Sets",
    SERIES: "Series",
    EPISODES: "Episodes",
    ALBUMS: "Albums"
};

// Text constants.
var cTXT = {
    MOVIE: "Movie",
    TVSHOW: "TV Show",
    MUSIC: "Music"
};

// Trigger globals.
var gTRIGGER = {
    START: 0,
    STARTTV: 0,
    SLACK: 0,
    END: 0,
    READY: false,
    CANCEL: false
};

// Import constants.
var cIMPORT = {
    IMPORT:   "Import",
    REFRESH:  "Refresh",
    FINISH:   "Finish"
};

var cSTATUS = {
    ONLINE:   "XBMC is online.",
    OFFLINE:  "XBMC is offline!",  
    CONNECT:  "Connecting... ",
//    START:    "Starting with [dummy] import.",
    WAIT:     "Please wait...",
    RETRY:    "Retry import...",
    FINISH:   "Import of [dummy] finished.",
    SEARCH:   "Searching for new [dummy]...",
    PROCESS:  "Processing [dummy]...", 
    IMPORT:   "Importing [dummy]...",
    REFRESH:  "Refreshing... ",
    READY:    "[dummy] is ready.",
    NOTFOUND: "No new [dummy] found.",
    SLACK:    "Slack detected...",
    SKIP:     "Skipping empty record "
};


