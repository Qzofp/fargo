/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.3
 *
 * File:    fargo.private.constants.js
 *
 * Created on Oct 23, 2013
 * Updated on Oct 31, 2013
 *
 * Description: Fargo's jQuery and Javascript private contants and globals.
 *
 */

// Global variables?!? jQuery sucks or I don't get it!!!
//var global_media = ""; // Obsolete.
//var global_page  = 1; // Obsolete.
//var global_sort  = ""; // Obsolete.

//var global_lastpage = 1; // Obsolete.

//var global_ready = false;  // Obsolete.
//var global_cancel = false; // 0bsolete. 

// Xbmc media limits.
//var global_xbmc_start; // 0bsolete. 
//var global_xbmc_end;   // 0bsolete. 

//var global_status_counter;

// Fargo globals.
//var global_setting_fargo; // Obsolete.
//var global_list_fargo; // Obsolete.

// Ajax requests.
//var global_status_request;
//var global_import_request;

// State globals.
var gSTATE = {
    MEDIA: "",
    PAGE: 1,
    LAST: 1, // Last page.
    SORT: "",
    LIST: "",
    SETTING: ""    
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


