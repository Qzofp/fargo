###############################################################################
#
# Title:   Fargo
# Author:  Qzofp Productions
# Version: 0.5
#
# File:    fargo.py
#
# Created on Jun 18, 2014
# Updated on Jun 19, 2014
#
# Description: Export addon configuration to the fargo.addon.settings.js file.
#
###############################################################################

import xbmcaddon
import xbmcgui

__settings__ = xbmcaddon.Addon("webinterface.fargo")
cWeb = __settings__.getSetting("website")
cPath = xbmc.translatePath(__settings__.getAddonInfo('path'))
cFilename = 'fargo.addon.settings.js'

cFile = os.path.join(cPath, cFilename)

FileHandler = open(cFile, 'w')
FileHandler.write('var cFARGOSITE = "' + cWeb + '";')
FileHandler.close()

xbmcgui.Dialog().ok("Fargo Export", "Web Site Address successfully exported.")