/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.3
 *
 * File:    fargo.private.constants.js
 *
 * Created on Oct 23, 2013
 * Updated on Dec 11, 2013
 *
 * Description: Fargo's jQuery and Javascript private contants and globals.
 *
 */

// Trigger globals.
var gTRIGGER = {
    START: 0,
    STARTTV: 0,
    END: 0,
    RETRY: 5,
    SLACK: 0,
    READY: false,
    CANCEL: false
};

// Import constants.
var cIMPORT = {
    IMPORT:   "Import",
    REFRESH:  "Refresh",
    FINISH:   "Finish",
    START:    "Do you want to start importing [dummy]?",
    WARNING:  "Warning:",
    RUNNING:  "Import is running!",
    CANCEL:   "Do you want to cancel the current import?"
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


