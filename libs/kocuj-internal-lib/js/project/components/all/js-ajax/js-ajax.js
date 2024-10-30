/**
 * @file AJAX requests
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

/* global ajaxurl */

/* global kocujILV13aHelper */

/* global kocujILV13aAllJsAjaxVals */

/**
 * AJAX prototype constructor
 *
 * @constructs
 * @namespace kocujILV13aCAllJsAjax
 * @public
 * @return {void}
 */
function kocujILV13aCAllJsAjax() {
    'use strict';
    /* jshint validthis: true */
    // get this object
    var self = this;
    // initialize objects
    self._objHelper = kocujILV13aHelper;
    // get current script filename
    self._thisFilename = document.scripts[document.scripts.length - 1].src;
    // get settings
    var vals = kocujILV13aAllJsAjaxVals;
    self._valsThrowErrors = vals.throwErrors === '1';
    self._valsPrefix = vals.prefix;
    self._valsSecurity = vals.security;
    self._valsCanUseProxy = vals.canUseProxy === '1';
    self._valsAjaxUrl = vals.ajaxUrl;
}

/**
 * AJAX prototype
 *
 * @namespace kocujILV13aCAllJsAjax
 * @public
 */
kocujILV13aCAllJsAjax.prototype = {
    /**
     * Object kocujILV13aHelper
     *
     * @private
     * @type {Object}
     */
    _objHelper: null,

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
     * @type {boolean}
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
     * Script settings - proxy can be used (true) or not (false)
     *
     * @private
     * @type {boolean}
     */
    _valsCanUseProxy: true,

    /**
     * Script settings - AJAX URL
     *
     * @private
     * @type {string}
     */
    _valsAjaxUrl: '',

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
            this._prj['prj_' + args.projectId] = [];
        }
    },

    /**
     * Send AJAX request with GET method
     *
     * @public
     * @param {string} projectId Project identifier
     * @param {string} connectionId Connection identifier
     * @param {string} url URL
     * @param {string} dataType Data type
     * @param {Object} [data] Data to send
     * @param {Object} [callbacks] Callback functions for AJAX request; there are the following callbacks available: error (when there was an error with request), retryWait (when there will be a retring of connection after a few seconds), retryNow (when there will be a retring of connection now), success (when there was a success)
     * @return {void}
     */
    sendGet: function (projectId, connectionId, url, dataType, data, callbacks) {
        'use strict';
        // send AJAX
        this.send(projectId, connectionId, url, 'GET', dataType, data, callbacks);
    },

    /**
     * Send AJAX request with POST method
     *
     * @public
     * @param {string} projectId Project identifier
     * @param {string} connectionId Connection identifier
     * @param {string} url URL
     * @param {string} dataType Data type
     * @param {Object} [data] Data to send
     * @param {Object} [callbacks] Callback functions for AJAX request; there are the following callbacks available: error (when there was an error with request), retryWait (when there will be a retring of connection after a few seconds), retryNow (when there will be a retring of connection now), success (when there was a success)
     * @return {void}
     */
    sendPost: function (projectId, connectionId, url, dataType, data, callbacks) {
        'use strict';
        // send AJAX
        this.send(projectId, connectionId, url, 'POST', dataType, data, callbacks);
    },

    /**
     * Send AJAX request with JSON data
     *
     * @public
     * @param {string} projectId Project identifier
     * @param {string} connectionId Connection identifier
     * @param {string} url URL
     * @param {Object} [data] Data to send
     * @param {Object} [callbacks] Callback functions for AJAX request; there are the following callbacks available: error (when there was an error with request), retryWait (when there will be a retring of connection after a few seconds), retryNow (when there will be a retring of connection now), success (when there was a success)
     * @return {void}
     */
    sendJson: function (projectId, connectionId, url, data, callbacks) {
        'use strict';
        // send AJAX
        this.send(projectId, connectionId, url, 'GET', 'jsonp', data, callbacks);
    },

    /**
     * Send AJAX request
     *
     * @public
     * @param {string} projectId Project identifier
     * @param {string} connectionId Connection identifier
     * @param {string} url URL
     * @param {string} method Request method
     * @param {string} dataType Data type
     * @param {Object} [data] Data to send
     * @param {Object} [callbacks] Callback functions for AJAX request; there are the following callbacks available: error (when there was an error with request), retryWait (when there will be a retring of connection after a few seconds), retryNow (when there will be a retring of connection now), success (when there was a success)
     * @return {void}
     */
    send: function (projectId, connectionId, url, method, dataType, data, callbacks) {
        'use strict';
        // get this object
        var self = this;
        (function ($) {
            // parse arguments
            projectId = self._parseProjectId(projectId);
            connectionId = self._objHelper.initString(connectionId);
            url = self._objHelper.initString(url);
            if (url === '') {
                self._throwError('JS_AJAX_EMPTY_URL');
                return;
            }
            method = self._objHelper.initString(method);
            if (method === '') {
                self._throwError('JS_AJAX_EMPTY_METHOD');
                return;
            }
            dataType = self._objHelper.initString(dataType);
            if (dataType === '') {
                self._throwError('JS_AJAX_EMPTY_DATA_TYPE');
                return;
            }
            data = self._objHelper.initObject(data);
            if (data === '') {
                data = {};
            }
            callbacks = self._objHelper.initObject(callbacks);
            if (callbacks === '') {
                callbacks = {};
            }
            if (callbacks.error === undefined) {
                callbacks.error = false;
            }
            if (callbacks.retryWait === undefined) {
                callbacks.retryWait = false;
            }
            if (callbacks.retryNow === undefined) {
                callbacks.retryNow = false;
            }
            if (callbacks.success === undefined) {
                callbacks.success = false;
            }
            // optionally clear retries timer
            if ((typeof self._prj['prj_' + projectId]['con_' + connectionId] !== 'undefined') && (self._prj['prj_' + projectId]['con_' + connectionId].retries > 0)) {
                self._deactivateTimer(projectId, connectionId);
            }
            // initialize connection data
            if (typeof self._prj['prj_' + projectId]['con_' + connectionId] === 'undefined') {
                self._prj['prj_' + projectId]['con_' + connectionId] = {
                    timer: null,
                    retries: 0
                };
            }
            // optionally change AJAX URL from relative to absolute
            if ((typeof ajaxurl !== 'undefined') && (url === ajaxurl)) {
                url = self._valsAjaxUrl;
            }
            // optionally set proxy
            var isProxy = false;
            if ((self._valsCanUseProxy) && (url !== self._valsAjaxUrl)) {
                // add proxy settings to data
                data._kocuj_internal_lib_proxy_url = url;
                data._kocuj_internal_lib_proxy_security = self._valsSecurity;
                if (typeof data.action !== 'undefined') {
                    data._kocuj_internal_lib_proxy_old_action = data.action;
                } else {
                    data._kocuj_internal_lib_proxy_old_action = '';
                }
                data.action = self._valsPrefix + '_' + projectId + '__js_ajax';
                // change url
                url = self._valsAjaxUrl;
                // set proxy flag
                isProxy = true;
            }
            // optionally set timeout if this request is to different server
            if (url !== self._valsAjaxUrl) {
                // deactivate timer
                self._deactivateTimer(projectId, connectionId);
                // set timer
                self._prj['prj_' + projectId]['con_' + connectionId].timer = window.setTimeout(function () {
                    self._error(projectId, connectionId, isProxy, callbacks.error, callbacks.retryWait, callbacks.retryNow, null, 'timeout', '');
                }, 30000);
            }
            // send AJAX request
            $.ajax({
                url: url,
                async: true,
                cache: false,
                data: data,
                dataType: dataType,
                error: function (obj, status, err) {
                    self._error(projectId, connectionId, isProxy, callbacks.error, callbacks.retryWait, callbacks.retryNow, new Array(
                        projectId,
                        connectionId,
                        url,
                        method,
                        dataType,
                        data,
                        callbacks
                    ), obj, status, err);
                },
                success: function (data, status, obj) {
                    self._success(projectId, connectionId, isProxy, callbacks.success, data, status, obj);
                },
                timeout: 30000,
                type: method
            });
        }(jQuery));
    },

    /**
     * Deactivate timer
     *
     * @private
     * @param {string} projectId Project identifier
     * @param {string} connectionId Connection identifier
     * @return {void}
     */
    _deactivateTimer: function (projectId, connectionId) {
        'use strict';
        // clear timer
        if (this._prj['prj_' + projectId]['con_' + connectionId].timer !== null) {
            window.clearTimeout(this._prj['prj_' + projectId]['con_' + connectionId].timer);
            this._prj['prj_' + projectId]['con_' + connectionId].timer = null;
        }
    },

    /**
     * AJAX loading success
     *
     * @private
     * @param {string} projectId Project identifier
     * @param {string} connectionId Connection identifier
     * @param {boolean} isProxy AJAX has been sent through proxy (true) or directly (false)
     * @param {function} callbackSuccess Callback function for AJAX success
     * @param {*} data Data
     * @param {string} status Text status
     * @param {Object} obj Request object
     * @return {void}
     */
    _success: function (projectId, connectionId, isProxy, callbackSuccess, data, status, obj) {
        'use strict';
        // get this object
        var self = this;
        (function ($) {
            // parse arguments
            status = self._objHelper.initString(status);
            obj = self._objHelper.initObject(obj);
            if (obj === '') {
                obj = null;
            }
            // clear retries
            self._prj['prj_' + projectId]['con_' + connectionId].retries = 0;
            // optionally deactivate AJAX timeout
            if (isProxy) {
                self._deactivateTimer(projectId, connectionId);
            }
            // optionally execute callback function
            if (callbackSuccess !== false) {
                callbackSuccess(projectId, connectionId, data, status, obj);
            }
        }(jQuery));
    },

    /**
     * AJAX loading error
     *
     * @private
     * @param {string} projectId Project identifier
     * @param {string} connectionId Connection identifier
     * @param {boolean} isProxy AJAX has been sent through proxy (true) or directly (false)
     * @param {function} callbackError Callback function for AJAX error
     * @param {function} callbackRetryWait Callback function for doing something before waiting for AJAX request retry
     * @param {function} callbackRetryNow Callback function for doing something at the beginning of AJAX request retry
     * @param {Array} sendMethodArguments Argument for method send() which are used when there is trying to connect again
     * @param {Object} obj Request object
     * @param {string} status Text status
     * @param {string} err Error
     * @return {void}
     */
    _error: function (projectId, connectionId, isProxy, callbackError, callbackRetryWait, callbackRetryNow, sendMethodArguments, obj, status, err) {
        'use strict';
        // get this object
        var self = this;
        (function ($) {
            // parse arguments
            obj = self._objHelper.initObject(obj);
            if (obj === '') {
                obj = null;
            }
            status = self._objHelper.initString(status);
            err = self._objHelper.initString(err);
            // optionally deactivate AJAX timeout
            if (isProxy) {
                self._deactivateTimer(projectId, connectionId);
            }
            // optionally retry connection
            ++self._prj['prj_' + projectId]['con_' + connectionId].retries;
            if (self._prj['prj_' + projectId]['con_' + connectionId].retries < 3) {
                if (callbackRetryWait !== false) {
                    callbackRetryWait(projectId, connectionId, self._prj['prj_' + projectId]['con_' + connectionId].retries, obj, status, err);
                }
                self._prj['prj_' + projectId]['con_' + connectionId].timer = window.setTimeout(function () {
                    if (callbackRetryNow !== false) {
                        callbackRetryNow(projectId, connectionId, self._prj['prj_' + projectId]['con_' + connectionId].retries, obj, status, err);
                    }
                    self.send(sendMethodArguments[0], sendMethodArguments[1], sendMethodArguments[2], sendMethodArguments[3], sendMethodArguments[4], sendMethodArguments[5], sendMethodArguments[6]);
                }, 5000);
                return;
            }
            // clear retries
            self._prj['prj_' + projectId]['con_' + connectionId].retries = 0;
            // optionally execute callback function
            if (callbackError !== false) {
                callbackError(projectId, connectionId, obj, status, err);
            }
        }(jQuery));
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
    }
};

// initialize
var kocujILV13aAllJsAjax = new kocujILV13aCAllJsAjax();
