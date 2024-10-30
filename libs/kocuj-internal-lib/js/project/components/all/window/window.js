/**
 * @file Window window
 *
 * @author Dominik Kocuj
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2 or later
 * @copyright Copyright (c) 2016-2020 kocuj.pl
 */

(function () {
})(); // empty function for correct minify with comments
//'use strict'; // for jshint uncomment this and comment line above

/* jshint strict: true */
/* jshint -W034 */

/* global document */
/* global jQuery */
/* global window */

/* global kocujILV13aHelper */
/* global kocujILV13aAllJsAjax */

/* global kocujILV13aAllWindowVals */

/**
 * Windows types
 *
 * @namespace kocujILV13aAllWindowType
 * @public
 */
var kocujILV13aAllWindowType = {
    /**
     * Window with standard content
     *
     * @public
     * @const {number}
     */
    STANDARD: 0,

    /**
     * Window with AJAX content
     *
     * @public
     * @const {number}
     */
    AJAX: 1,

    /**
     * Information about maximum constant value; it should not be used in executing the window script methods
     *
     * @public
     * @const {number}
     */
    LAST: 1
};

/**
 * Window window prototype constructor
 *
 * @constructs
 * @namespace kocujILV13aCAllWindow
 * @public
 * @return {void}
 */
function kocujILV13aCAllWindow() {
    'use strict';
    /* jshint validthis: true */
    // get this object
    var self = this;
    // initialize objects
    self._objHelper = kocujILV13aHelper;
    self._objAllJsAjax = kocujILV13aAllJsAjax;
    // get current script filename
    self._thisFilename = document.scripts[document.scripts.length - 1].src;
    // get settings
    var vals = kocujILV13aAllWindowVals;
    self._valsThrowErrors = vals.throwErrors === '1';
    self._valsPrefix = vals.prefix;
    self._valsDialogCssUrl = vals.dialogCssUrl;
    self._valsTextLoading = vals.textLoading;
    self._valsTextLoadingError = vals.textLoadingError;
}

/**
 * Window prototype
 *
 * @namespace kocujILV13aCAllWindow
 * @public
 */
kocujILV13aCAllWindow.prototype = {
    /**
     * Object kocujILV13aHelper
     *
     * @private
     * @type {Object}
     */
    _objHelper: null,

    /**
     * Object kocujILV13aAllJsAjax
     *
     * @private
     * @type {Object}
     */
    _objAllJsAjax: null,

    /**
     * Current script filename
     *
     * @private
     * @type {string}
     */
    _thisFilename: '',

    /**
     * Projects list
     *
     * @private
     * @type {Array}
     */
    _prj: [],

    /**
     * Script settings - throw errors (true) or not (false)
     *
     * @private
     * @type {string}
     */
    _valsThrowErrors: false,

    /**
     * Script settings - prefix
     *
     * @private
     * @type {string}
     */
    _valsPrefix: '',

    /**
     * Script settings - dialog window CSS URL
     *
     * @private
     * @type {string}
     */
    _valsDialogCssUrl: '',

    /**
     * Script settings - text for loading
     *
     * @private
     * @type {string}
     */
    _valsTextLoading: '',

    /**
     * Script settings - text for loading error
     *
     * @private
     * @type {string}
     */
    _valsTextLoadingError: '',

    /**
     * Add project
     *
     * @public
     * @param {string} projectId Project identifier
     * @return {void}
     * @throws {kocujILV13aCException} kocujILV13aExceptionCode.PROJECT_ALREADY_EXISTS if project identifier entered in projectId already exists
     */
    addProject: function (projectId) {
        'use strict';
        // parse arguments
        var args = this._checkAddProject(projectId);
        // add project
        if (this._prj['prj_' + args.projectId] === undefined) {
            this.addProjectIfNotExists(args.projectId);
        } else {
            this._throwError('PROJECT_ALREADY_EXISTS', args.projectId);
            return;
        }
    },

    /**
     * Add project if not exists
     *
     * @public
     * @param {string} projectId Project identifier
     * @return {void}
     */
    addProjectIfNotExists: function (projectId) {
        'use strict';
        // get this object
        var self = this;
        (function ($) {
            // parse arguments
            var args = self._checkAddProject(projectId);
            // add project
            if (self._prj['prj_' + args.projectId] === undefined) {
                // add project
                self._prj['prj_' + args.projectId] = {
                    timer: null,
                    cssLoaded: false,
                    windowsTimers: []
                };
                // add stylesheet
                var stylesheet = $('<link id="' + self._getHTMLNameStylesheet(args.projectId) + '" rel="stylesheet" href="' + self._valsDialogCssUrl + '" type="text/css" media="all" />');
                stylesheet.load(function () {
                    self._checkStylesheet(args.projectId);
                });
                $('head').append(stylesheet);
                // add dummy element for checking style loading
                $('body').prepend('<div id="' + self._getHTMLNameDummy(args.projectId) + '" class="ui-dialog" style="display:none;"></div>');
                // set timer for waiting
                if (!self._checkStylesheet(args.projectId)) {
                    self._prj['prj_' + args.projectId].timer = window.setInterval(function () {
                        // check stylesheet
                        if (self._checkStylesheet(args.projectId)) {
                            // clear timer
                            window.clearInterval(self._prj['prj_' + args.projectId].timer);
                            self._prj['prj_' + args.projectId].timer = null;
                        }
                    }, 100);
                }
            }
            self._objAllJsAjax.addProjectIfNotExists(args.projectId);
        }(jQuery));
    },

    /**
     * Get HTML selector for stylesheet
     *
     * @public
     * @param {string} projectId Project identifier
     * @return {string} HTML selector for stylesheet
     */
    getHTMLSelectorStylesheet: function (projectId) {
        'use strict';
        // parse arguments
        projectId = this._parseProjectId(projectId);
        // exit
        return '#' + this._getHTMLNameStylesheet(projectId);
    },

    /**
     * Get HTML selector for dummy element
     *
     * @public
     * @param {string} projectId Project identifier
     * @return {string} HTML selector for dummy element
     */
    getHTMLSelectorDummy: function (projectId) {
        'use strict';
        // parse arguments
        projectId = this._parseProjectId(projectId);
        // exit
        return '#' + this._getHTMLNameDummy(projectId);
    },

    /**
     * Get HTML selector for window
     *
     * @public
     * @param {string} projectId Project identifier
     * @param {string} windowId Window identifier
     * @return {string} HTML selector for window
     */
    getHTMLSelectorWindow: function (projectId, windowId) {
        'use strict';
        // parse arguments
        projectId = this._parseProjectId(projectId);
        windowId = this._objHelper.initString(windowId);
        // exit
        return '#' + this._getHTMLNameWindow(projectId, windowId);
    },

    /**
     * Get HTML selector for window content
     *
     * @public
     * @param {string} projectId Project identifier
     * @param {string} windowId Window identifier
     * @return {string} HTML selector for window content
     */
    getHTMLSelectorContent: function (projectId, windowId) {
        'use strict';
        // parse arguments
        projectId = this._parseProjectId(projectId);
        windowId = this._objHelper.initString(windowId);
        // exit
        return '#' + this._getHTMLNameContent(projectId, windowId);
    },

    /**
     * Get HTML selector for window content inside
     *
     * @public
     * @param {string} projectId Project identifier
     * @param {string} windowId Window identifier
     * @return {string} HTML selector for window content inside
     */
    getHTMLSelectorContentInside: function (projectId, windowId) {
        'use strict';
        // parse arguments
        projectId = this._parseProjectId(projectId);
        windowId = this._objHelper.initString(windowId);
        // exit
        return '#' + this._getHTMLNameContentInside(projectId, windowId);
    },

    /**
     * Show window
     *
     * @public
     * @param {string} projectId Project identifier
     * @param {string} windowId Window identifier
     * @param {number} type Window type
     * @param {Object} attr Window attributes
     * @return {void}
     */
    show: function (projectId, windowId, type, attr) {
        'use strict';
        // get this object
        var self = this;
        (function ($) {
            // parse arguments
            projectId = self._parseProjectId(projectId);
            windowId = self._objHelper.initString(windowId);
            type = self._objHelper.initNumeric(type);
            if (type > kocujILV13aAllWindowType.LAST) {
                self._throwError('WINDOW_WRONG_TYPE');
                return;
            }
            attr = self._objHelper.initObject(attr);
            switch (type) {
                case kocujILV13aAllWindowType.STANDARD:
                    if (!('content' in attr)) {
                        self._throwError('WINDOW_WRONG_ATTRIBUTES');
                        return;
                    }
                    break;
                case kocujILV13aAllWindowType.AJAX:
                    if ((!('url' in attr)) || (!('ajaxData' in attr))) {
                        self._throwError('WINDOW_WRONG_ATTRIBUTES');
                        return;
                    }
                    break;
            }
            // show window
            if (self._checkStylesheet(projectId)) {
                self._showWindow(projectId, windowId, type, attr);
            } else {
                self._prj['prj_' + projectId].windowsTimers['win_' + windowId] = window.setInterval(function () {
                    // optionally show window
                    if (self._checkStylesheet(projectId)) {
                        // clear timer
                        window.clearInterval(self._prj['prj_' + projectId].windowsTimers['win_' + windowId]);
                        self._prj['prj_' + projectId].windowsTimers['win_' + windowId] = null;
                        // show window
                        self._showWindow(projectId, windowId, type, attr);
                    }
                }, 2000);
            }
        }(jQuery));
    },

    /**
     * AJAX loading success callback
     *
     * @public
     * @param {string} projectId Project identifier
     * @param {string} connectionId Connection identifier
     * @param {*} data Data
     * @param {string} status Text status
     * @param {Object} obj Request object
     * @return {void}
     */
    ajaxSuccessCallback: function (projectId, connectionId, data, status, obj) {
        'use strict';
        // get this object
        var self = kocujILV13aAllWindow;
        (function ($) {
            // parse parameters
            data = self._objHelper.initString(data);
            // get window identifier
            var tmp = connectionId.split('__');
            var windowId = tmp[tmp.length - 1];
            // set HTML data
            $(self.getHTMLSelectorContentInside(projectId, windowId)).html(data);
        }(jQuery));
    },

    /**
     * AJAX loading error callback
     *
     * @public
     * @param {string} projectId Project identifier
     * @param {string} connectionId Connection identifier
     * @param {Object} obj Request object
     * @param {string} status Text status
     * @param {string} err Error
     * @return {void}
     */
    ajaxErrorCallback: function (projectId, connectionId, obj, status, err) {
        'use strict';
        // get this object
        var self = kocujILV13aAllWindow;
        (function ($) {
            // get window identifier
            var tmp = connectionId.split('__');
            var windowId = tmp[tmp.length - 1];
            // set HTML data
            $(self.getHTMLSelectorContentInside(projectId, windowId)).html(self._valsTextLoadingError);
        }(jQuery));
    },

    /**
     * Get HTML prefix
     *
     * @private
     * @param {string} projectId Project identifier
     * @return {string} HTML prefix
     */
    _getHTMLPrefix: function (projectId) {
        'use strict';
        // exit
        return this._valsPrefix + '_' + projectId + '__';
    },

    /**
     * Get HTML prefix for stylesheet
     *
     * @private
     * @param {string} projectId Project identifier
     * @return {string} HTML prefix for stylesheet
     */
    _getHTMLNameStylesheet: function (projectId) {
        'use strict';
        // exit
        return this._getHTMLPrefix(projectId) + 'window_stylesheet';
    },

    /**
     * Get HTML prefix for dummy element
     *
     * @private
     * @param {string} projectId Project identifier
     * @return {string} HTML prefix for dummy element
     */
    _getHTMLNameDummy: function (projectId) {
        'use strict';
        // exit
        return this._getHTMLPrefix(projectId) + 'window_dummy';
    },

    /**
     * Get HTML prefix for window
     *
     * @private
     * @param {string} projectId Project identifier
     * @param {string} windowId Window identifier
     * @return {string} HTML prefix for window
     */
    _getHTMLNameWindow: function (projectId, windowId) {
        'use strict';
        // exit
        return this._getHTMLPrefix(projectId) + 'window__' + windowId;
    },

    /**
     * Get HTML prefix for window content
     *
     * @private
     * @param {string} projectId Project identifier
     * @param {string} windowId Window identifier
     * @return {string} HTML prefix for window content
     */
    _getHTMLNameContent: function (projectId, windowId) {
        'use strict';
        // exit
        return this._getHTMLPrefix(projectId) + 'window_content__' + windowId;
    },

    /**
     * Get HTML prefix for window content inside
     *
     * @private
     * @param {string} projectId Project identifier
     * @param {string} windowId Window identifier
     * @return {string} HTML prefix for window content inside
     */
    _getHTMLNameContentInside: function (projectId, windowId) {
        'use strict';
        // exit
        return this._getHTMLPrefix(projectId) + 'window_content_inside__' + windowId;
    },

    /**
     * Parse project identifier
     *
     * @private
     * @param {string} projectId Project identifier
     * @return {string} Parsed project identifier
     * @throws {kocujILV13aCException} kocujILV13aExceptionCode.EMPTY_PROJECT_ID if project identifier entered in projectId is empty
     * @throws {kocujILV13aCException} kocujILV13aExceptionCode.PROJECT_DOES_NOT_EXIST if project identifier entered in projectId does not exist
     */
    _parseProjectId: function (projectId) {
        'use strict';
        // parse project identifier
        projectId = this._objHelper.initString(projectId);
        if (projectId === '') {
            this._throwError('EMPTY_PROJECT_ID');
            return;
        }
        // check if project exists
        if (this._prj['prj_' + projectId] === undefined) {
            this._throwError('PROJECT_DOES_NOT_EXIST', projectId);
            return;
        }
        // exit
        return projectId;
    },

    /**
     * Check arguments for adding project
     *
     * @private
     * @param {string} projectId Project identifier
     * @return {Object} Parsed arguments for adding project
     * @throws {kocujILV13aCException} kocujILV13aExceptionCode.EMPTY_PROJECT_ID if project identifier entered in projectId is empty
     */
    _checkAddProject: function (projectId) {
        'use strict';
        // parse arguments
        projectId = this._objHelper.initString(projectId);
        if (projectId === '') {
            this._throwError('EMPTY_PROJECT_ID');
            return;
        }
        // exit
        return {
            projectId: projectId
        };
    },

    /**
     * Throw an error if debugging is enabled
     *
     * @private
     * @param {string} codeString Error code in string format
     * @param {string} [param] Parameter for error information
     * @return {void}
     */
    _throwError: function (codeString, param) {
        'use strict';
        // parse arguments
        codeString = this._objHelper.initString(codeString);
        if (codeString === '') {
            return;
        }
        param = this._objHelper.initString(param);
        // throw an error
        if (this._valsThrowErrors) {
            /* jshint evil: true */
            eval('throw new kocujILV13aCException(kocujILV13aExceptionCode.' + codeString + ', this._thisFilename, param);');
        }
    },

    /**
     * Check window stylesheet
     *
     * @private
     * @param {string} projectId Project identifier
     * @return {boolean} Stylesheet has been loaded (true) or not (false)
     */
    _checkStylesheet: function (projectId) {
        'use strict';
        // check if stylesheet has not been loaded already
        if (this._prj['prj_' + projectId].cssLoaded) {
            return true;
        }
        // get this object
        var self = this;
        (function ($) {
            // set flag for loaded CSS
            if ($(self.getHTMLSelectorDummy(projectId)).css('position') === 'absolute') {
                self._prj['prj_' + projectId].cssLoaded = true;
            }
        }(jQuery));
        // exit
        return this._prj['prj_' + projectId].cssLoaded;
    },

    /**
     * Show window now
     *
     * @private
     * @param {string} projectId Project identifier
     * @param {string} windowId Window identifier
     * @param {number} type Window type
     * @param {Object} attr Window attributes
     * @return {void}
     */
    _showWindow: function (projectId, windowId, type, attr) {
        'use strict';
        // get this object
        var self = this;
        (function ($) {
            // remove old dialog content
            if ($(self.getHTMLSelectorWindow(projectId, windowId)).length !== 0) {
                $(self.getHTMLSelectorWindow(projectId, windowId)).remove();
            }
            // add dialog content
            $('body').append('<div id="' + self._getHTMLNameWindow(projectId, windowId) + '" style="padding:0;"><div id="' + self._getHTMLNameContent(projectId, windowId) + '" style="height:100%;padding:0;margin:0;overflow-x:hidden;overflow-y:scroll;"><div id="' + self._getHTMLNameContentInside(projectId, windowId) + '" style="padding:10px;"></div></div></div>');
            $(self.getHTMLSelectorContentInside(projectId, windowId)).html('');
            switch (type) {
                case kocujILV13aAllWindowType.STANDARD:
                    $(self.getHTMLSelectorContentInside(projectId, windowId)).html(attr.content);
                    break;
                case kocujILV13aAllWindowType.AJAX:
                    $(self.getHTMLSelectorContentInside(projectId, windowId)).html(self._valsTextLoading);
                    break;
            }
            // set dialog content styles
            if ('contentCss' in attr) {
                $.each(attr.contentCss, function (key, value) {
                    $(self.getHTMLSelectorContentInside(projectId, windowId)).css(key, value.split('&quot;').join('"'));
                });
            }
            // show window
            $(self.getHTMLSelectorWindow(projectId, windowId)).dialog({
                width: attr.width,
                height: attr.height,
                title: attr.title,
                modal: true,
                draggable: false,
                resizable: false,
                responsive: true,
                show: 'fade',
                hide: 'fade',
                open: function () {
                    $('.ui-widget-overlay').hide().fadeIn();
                },
                beforeClose: function () {
                    $('.ui-widget-overlay').remove();
                    $('<div />', {
                        'class': 'ui-widget-overlay'
                    }).css({
                        width: $(document).width(),
                        height: $(document).height(),
                        zIndex: 1001
                    }).appendTo('body').fadeOut(function () {
                        $(this).remove();
                    });
                }
            });
            // change title bar style
            $(self.getHTMLSelectorWindow(projectId, windowId)).parent().find('.ui-dialog-title').css({
                'overflow': 'hidden',
                'display': 'block',
                'height': '100%'
            });
            // optionally send AJAX request
            if (type === kocujILV13aAllWindowType.AJAX) {
                self._objAllJsAjax.sendPost(projectId, 'window__' + windowId, attr.url, 'text', attr.ajaxData, {
                    success: self.ajaxSuccessCallback,
                    error: self.ajaxErrorCallback
                });
            }
        }(jQuery));
    }
};

// initialize
var kocujILV13aAllWindow = new kocujILV13aCAllWindow();
