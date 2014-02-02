/*
 * Title:   Fargo
 * Author:  Qzofp Productions
 * Version: 0.4
 *
 * File:    fargo.private.constants.js
 *
 * Created on Oct 23, 2013
 * Updated on Feb 01, 2014
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
    CONNECT:  "Connecting... ",
//    START:    "Starting with [dummy] import.",
    WAIT:     "Please wait...",
    RETRY:    "Retry import...",
    FINISH:   "Import of [dummy] finished.",
    SEARCH:   "Searching for new [dummy]...",
    PROCESS:  "Processing [dummy]...", 
    IMPORT:   "Importing [dummy]...",
    REFRESH:  "Refreshing [dummy]... ",
    READY:    "[dummy] is ready.",
    NOTFOUND: "No new [dummy] found.",
    SLACK:    "Slack detected...",
    SKIP:     "Skipping empty record ",
    NOMATCH:  "Refresh failed! Doesn't match with [dummy] in XBMC."
};


