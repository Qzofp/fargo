/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.3
 *
 * File:    fargo.private.constants.js
 *
 * Created on Oct 23, 2013
 * Updated on Oct 24, 2013
 *
 * Description: Fargo's jQuery and Javascript private contants and globals.
 *
 */

// Global variables?!? jQuery sucks or I don't get it!!!
var global_media = "";
var global_page  = 1;
var global_sort  = "";

var global_lastpage = 1;

var global_ready = false;
//var global_cancel = false; // 0bsolete. 

// Xbmc media limits.
//var global_xbmc_start; // 0bsolete. 
//var global_xbmc_end;   // 0bsolete. 

//var global_status_counter;

// Fargo globals.
var global_setting_fargo;
var global_list_fargo;

// Ajax requests.
//var global_status_request;
//var global_import_request;

// State globals.
var gSTATE = {
    MEDIA: "",
    PAGE: 1,
    LAST: 1,
    SORT: ""
};

// Trigger globals.
var gTRIGGER = {
    START: 0,
    STARTTV: 0,
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
    CONNECT:  "Connecting...",
    START:    "Starting with [dummy] import.",
    RETRY:    "Retry import...",
    SEARCH:   "Searching for new [dummy]...",
    PROCESS:  "Processing [dummy]...", 
    IMPORT:   "Importing [dummy]...",
    REFRESH:  "Refreshing...",
    READY:    "[dummy] is ready.",
    NOTFOUND: "No new [dummy] found."
};


