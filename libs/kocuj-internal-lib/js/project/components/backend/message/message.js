/**
 * @file Message
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

/* global ajaxurl */

/* global kocujILV13aHelper */
/* global kocujILV13aAllJsAjax */

/* global kocujILV13aBackendMessageVals */

/**
 * Message prototype constructor
 *
 * @constructs
 * @namespace kocujILV13aCBackendMessage
 * @public
 * @return {void}
 */
function kocujILV13aCBackendMessage() {
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
    var vals = kocujILV13aBackendMessageVals;
    self._valsThrowErrors = vals.throwErrors === '1';
    self._valsPrefix = vals.prefix;
    self._valsSecurity = vals.security;
}

/**
 * Message prototype
 *
 * @namespace kocujILV13aCBackendMessage
 * @public
 */
kocujILV13aCBackendMessage.prototype = {
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
     * Script settings - security string
     *
     * @private
     * @type {string}
     */
    _valsSecurity: '',

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
        // parse arguments
        var args = this._checkAddProject(projectId);
        // add project
        if (this._prj['prj_' + args.projectId] === undefined) {
            this._prj['prj_' + args.projectId] = true;
        }
        this._objAllJsAjax.addProjectIfNotExists(args.projectId);
    },

    /**
     * Get HTML selector for message
     *
     * @public
     * @param {string} projectId Project identifier
     * @param {string} messageId Message identifier
     * @return {string} HTML selector for message
     */
    getHTMLSelectorMessage: function (projectId, messageId) {
        'use strict';
        // parse arguments
        projectId = this._parseProjectId(projectId);
        messageId = this._objHelper.initString(messageId);
        // exit
        return '#' + this._getHTMLNameMessage(projectId, messageId);
    },

    /**
     * Add close button for message
     *
     * @public
     * @param {string} projectId Project identifier
     * @param {string} messageId Message identifier
     * @return {void}
     */
    addMessageCloseButton: function (projectId, messageId) {
        'use strict';
        // get this object
        var self = this;
        (function ($) {
            // parse arguments
            projectId = self._parseProjectId(projectId);
            messageId = self._objHelper.initString(messageId);
            // add close button event
            $(self.getHTMLSelectorMessage(projectId, messageId) + ' .notice-dismiss').click(function () {
                // close message
                $(self.getHTMLSelectorMessage(projectId, messageId)).hide();
                // save information that message has been closed
                self._objAllJsAjax.sendPost(projectId, 'message_close', ajaxurl, 'text', {
                    action: self._valsPrefix + '_' + projectId + '__message_close',
                    security: self._valsSecurity,
                    messageId: messageId
                });
            });
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
     * Get HTML prefix for message
     *
     * @private
     * @param {string} projectId Project identifier
     * @param {string} messageId Message identifier
     * @return {string} HTML prefix for message review
     */
    _getHTMLNameMessage: function (projectId, messageId) {
        'use strict';
        // exit
        return this._getHTMLPrefix(projectId) + 'message_' + messageId;
    },

    /**
     * Parse project identifier
     *
     * @private
     * @param {string} projectId Project identifier
     * @return {string} Parsed project identifier
     * @throws {kocujILV13aCException} kocujILV13aExceptionCode.EMPTY_PROJECT_ID if project identifier entered in projectId is empty
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
            projectId: projectId,
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
    }
};

// initialize
var kocujILV13aBackendMessage = new kocujILV13aCBackendMessage();
