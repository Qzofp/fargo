/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.5
 *
 * File:    fargo.private.constants.js
 *
 * Created on Oct 23, 2013
 * Updated on May 12, 2014
 *
 * Description: Fargo's jQuery and Javascript private contants and globals.
 *
 */

// Trigger globals.
var gTRIGGER = {
    START: 0,
    COUNTER: 1,
    END: 0,
    RETRY: 15, // 15 - 25?
    STEP1: 1,
    STEP2: 2,
    READY: false,
    CANCEL: false,
    BULK: 25, // 250; // Max. items imported.
    STATUS : -1    
};

var gMEDIA = {
    TITLE: "",
    SUB: "",
    THUMBS : "",
    XBMCID : 0
};

// Connections constants.
var gCONNECT = {
    CONNECTION : "",
    PORT : 0,
    TIMEOUT : 0,
    KEY : "",
    STATUS: "Offline"
};

// Transfer (Import/Refresh) codes. Must be smaller then or equal to 0.
// Note: These codes corresponds with the codes from settings.php.
var cTRANSFER = {
    INVALID: -32602,
    ERROR: -999,
    DUPLICATE: -300,
    NOTFOUND: -200,
    READY: -100,
    WAIT: -1,
    NOMATCH: 0
};

// Import constants.
var cIMPORT = {
    IMPORT:   "Import",
    REFRESH:  "Refresh",
    FINISH:   "Finish",
    START:    "Do you want to start importing [dummy]?",
    WARNING:  "Warning",
    RUNNING:  "Import is already running! Please wait until it is finished."
};

var cSTATUS = {
    ONLINE:   "XBMC is online.",
    OFFLINE:  "XBMC is offline!",
    LOST:     "XBMC connection lost!",
    METAERR:  "Search failed! Please retry.", // Meta import error.
    CONNECT:  "Connecting... ",
    WAIT:     "Please wait...",
    RETRY:    "Retry import...",
    FINISH:   "Import of [dummy] finished.",
    SEARCH:   "Searching for [dummy]...",
    EXISTS:   "Duplicate [dummy] found!",
    PROCESS:  "Processing [dummy]...", 
    IMPORT:   "Importing [dummy]...",
    REFRESH:  "Refreshing [dummy]... ",
    READY:    "[dummy] is ready.",
    NOTFOUND: "No new [dummy] found.",
    SLACK:    "Slack detected...",
    SKIP:     "Skipping empty record ",
    NOMATCH:  "Refresh failed! [dummy] cannot be found in XBMC."
};

var cSYSTEM = {
    REMOVE:   "Remove",
    REMTITLE: "Remove library",
    MESSAGE1: "Removing library",
    MESSAGE2: "Do you want to remove the [dummy]?",
    MESSAGE3: "Library removed!",
    MESSAGE4: "Removing event log",
    MESSAGE5: "Event log removed!"
};
