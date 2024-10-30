/**
 * @file Add thanks
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
/* jshint -W107 */

/* global document */
/* global jQuery */
/* global window */

/* global ajaxurl */

/* global kocujILV13aHelper */
/* global kocujILV13aAllJsAjax */

/* global kocujPLV13aBackendAddThanksVals */

/**
 * Add thanks prototype constructor
 *
 * @constructs
 * @namespace kocujPLV13aCBackendAddThanks
 * @public
 * @return {void}
 */
function kocujPLV13aCBackendAddThanks() {
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
    var vals = kocujPLV13aBackendAddThanksVals;
    if (vals.throwErrors !== undefined) {
        self._valsThrowErrors = vals.throwErrors === '1';
    }
    self._valsPrefix = vals.prefix;
    self._valsPrefixKocujIL = vals.prefixKocujIL;
    self._valsSecurity = vals.security;
    self._valsWebsiteUrl = vals.websiteUrl;
    self._valsWebsiteTitle = vals.websiteTitle;
    self._valsWebsiteDescription = vals.websiteDescription;
    self._valsTextMoreInfoLink = vals.textMoreInfoLink;
    self._valsTextSending = vals.textSending;
    self._valsTextError = vals.textError;
    self._valsTextErrorNoRetries = vals.textErrorNoRetries;
    self._valsTextErrorAlreadyExists = vals.textErrorAlreadyExists;
    self._valsTextErrorWrongResponse1 = vals.textErrorWrongResponse1;
    self._valsTextErrorWrongResponse2 = vals.textErrorWrongResponse2;
    self._valsTextSuccess = vals.textSuccess;
    self._valsImageLoadingUrl = vals.imageLoadingUrl;
}

/**
 * Add thanks prototype
 *
 * @namespace kocujPLV13aCBackendAddThanks
 * @public
 */
kocujPLV13aCBackendAddThanks.prototype = {
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
     * Script settings - prefix for Kocuj Internal Lib
     *
     * @private
     * @type {string}
     */
    _valsPrefixKocujIL: '',

    /**
     * Script settings - security string
     *
     * @private
     * @type {string}
     */
    _valsSecurity: '',

    /**
     * Script settings - website URL
     *
     * @private
     * @type {string}
     */
    _valsWebsiteUrl: '',

    /**
     * Script settings - website title
     *
     * @private
     * @type {string}
     */
    _valsWebsiteTitle: '',

    /**
     * Script settings - website description
     *
     * @private
     * @type {string}
     */
    _valsWebsiteDescription: '',

    /**
     * Script settings - text for more information link
     *
     * @private
     * @type {string}
     */
    _valsTextMoreInfoLink: '',

    /**
     * Script settings - text for sending
     *
     * @private
     * @type {string}
     */
    _valsTextSending: '',

    /**
     * Script settings - text for error
     *
     * @private
     * @type {string}
     */
    _valsTextError: '',

    /**
     * Script settings - text for error with no more retries
     *
     * @private
     * @type {string}
     */
    _valsTextErrorNoRetries: '',

    /**
     * Script settings - text for data which already exists
     *
     * @private
     * @type {string}
     */
    _valsTextErrorAlreadyExists: '',

    /**
     * Script settings - text for wrong response from server (line 1)
     *
     * @private
     * @type {string}
     */
    _valsTextErrorWrongResponse1: '',

    /**
     * Script settings - text for wrong response from server (line 2)
     *
     * @private
     * @type {string}
     */
    _valsTextErrorWrongResponse2: '',

    /**
     * Script settings - text for success
     *
     * @private
     * @type {string}
     */
    _valsTextSuccess: '',

    /**
     * Script settings - text for loading URL
     *
     * @private
     * @type {string}
     */
    _valsImageLoadingUrl: '',

    /**
     * Styles are saved (true) or not (false)
     *
     * @private
     * @type {boolean}
     */
    _stylesSaved: false,

    /**
     * Saved styles
     *
     * @private
     * @type {string}
     */
    _styles: '',

    /**
     * Add project
     *
     * @public
     * @param {string} projectId Project identifier
     * @param {string} windowFunction Function name for window
     * @param {string} projectApiUrl Project API URL
     * @param {string} projectApiLogin Project API login
     * @param {string} [projectApiPassword] Project API password
     * @return {void}
     * @throws {kocujILV13aCException} kocujILV13aExceptionCode.PROJECT_ALREADY_EXISTS if project identifier entered in projectId already exists
     */
    addProject: function (projectId, windowFunction, projectApiUrl, projectApiLogin, projectApiPassword) {
        'use strict';
        // parse arguments
        var args = this._checkAddProject(projectId, windowFunction, projectApiUrl, projectApiLogin, projectApiPassword);
        // add project
        if (this._prj['prj_' + args.projectId] === undefined) {
            this.addProjectIfNotExists(args.projectId, args.windowFunction, args.projectApiUrl, args.projectApiLogin, args.projectApiPassword);
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
     * @param {string} windowFunction Function name for window
     * @param {string} projectApiUrl Project API URL
     * @param {string} projectApiLogin Project API login
     * @param {string} [projectApiPassword] Project API password
     * @return {void}
     */
    addProjectIfNotExists: function (projectId, windowFunction, projectApiUrl, projectApiLogin, projectApiPassword) {
        'use strict';
        // parse arguments
        var args = this._checkAddProject(projectId, windowFunction, projectApiUrl, projectApiLogin, projectApiPassword);
        // add project
        if (this._prj['prj_' + args.projectId] === undefined) {
            this._prj['prj_' + args.projectId] = {
                windowFunction: args.windowFunction,
                projectApiUrl: args.projectApiUrl,
                projectApiLogin: args.projectApiLogin,
                projectApiPassword: args.projectApiPassword,
                timer: null,
            };
        }
        this._objAllJsAjax.addProjectIfNotExists(args.projectId);
    },

    /**
     * Get HTML selector for review message
     *
     * @public
     * @param {string} projectId Project identifier
     * @return {string} HTML selector for review message
     */
    getHTMLSelectorReviewMessageDiv: function (projectId) {
        'use strict';
        // parse arguments
        projectId = this._parseProjectId(projectId);
        // exit
        return '#' + this._getHTMLNameReviewMessageDiv(projectId);
    },

    /**
     * Get HTML selector for style
     *
     * @public
     * @param {string} projectId Project identifier
     * @return {string} HTML selector for style
     */
    getHTMLSelectorAddThanksStyle: function (projectId) {
        'use strict';
        // parse arguments
        projectId = this._parseProjectId(projectId);
        // exit
        return '#' + this._getHTMLNameAddThanksStyle(projectId);
    },

    /**
     * Get HTML selector for more information link div in review message
     *
     * @public
     * @param {string} projectId Project identifier
     * @return {string} HTML selector for more information link div in review message
     */
    getHTMLSelectorAddThanksReviewMessageMoreInfoLinkDiv: function (projectId) {
        'use strict';
        // parse arguments
        projectId = this._parseProjectId(projectId);
        // exit
        return '#' + this._getHTMLNameAddThanksReviewMessageMoreInfoLinkDiv(projectId);
    },

    /**
     * Get HTML selector for more information link in review message
     *
     * @public
     * @param {string} projectId Project identifier
     * @return {string} HTML selector for more information link in review message
     */
    getHTMLSelectorAddThanksReviewMessageMoreInfoLink: function (projectId) {
        'use strict';
        // parse arguments
        projectId = this._parseProjectId(projectId);
        // exit
        return this.getHTMLSelectorAddThanksReviewMessageMoreInfoLinkDiv(projectId) + ' #' + this._getHTMLNameAddThanksReviewMessageMoreInfoLink(projectId);
    },

    /**
     * Get HTML selector for send button in review message
     *
     * @public
     * @param {string} projectId Project identifier
     * @return {string} HTML selector for send button in review message
     */
    getHTMLSelectorAddThanksReviewMessageSend: function (projectId) {
        'use strict';
        // parse arguments
        projectId = this._parseProjectId(projectId);
        // exit
        return '#' + this._getHTMLNameAddThanksReviewMessageSend(projectId);
    },

    /**
     * Get HTML selector for close message in review message
     *
     * @public
     * @param {string} projectId Project identifier
     * @return {string} HTML selector for close message in review message
     */
    getHTMLSelectorReviewMessageClose: function (projectId) {
        'use strict';
        // parse arguments
        projectId = this._parseProjectId(projectId);
        // exit
        return '#' + this._getHTMLNameReviewMessageClose(projectId);
    },

    /**
     * Get HTML selector for loading image in review message
     *
     * @public
     * @param {string} projectId Project identifier
     * @return {string} HTML selector for loading image in review message
     */
    getHTMLSelectorAddThanksReviewMessageLoadingImage: function (projectId) {
        'use strict';
        // parse arguments
        projectId = this._parseProjectId(projectId);
        // exit
        return '#' + this._getHTMLNameAddThanksReviewMessageLoadingImage(projectId);
    },

    /**
     * Get HTML selector for adding thanks div in about page
     *
     * @public
     * @param {string} projectId Project identifier
     * @return {string} HTML selector for adding thanks div in about page
     */
    getHTMLSelectorAddThanksPageAboutDiv: function (projectId) {
        'use strict';
        // parse arguments
        projectId = this._parseProjectId(projectId);
        // exit
        return '#' + this._getHTMLNameAddThanksPageAboutDiv(projectId);
    },

    /**
     * Get HTML selector for more information link in about page
     *
     * @public
     * @param {string} projectId Project identifier
     * @return {string} HTML selector for more information link in about page
     */
    getHTMLSelectorAddThanksPageAboutMoreInfoLink: function (projectId) {
        'use strict';
        // parse arguments
        projectId = this._parseProjectId(projectId);
        // exit
        return this.getHTMLSelectorAddThanksPageAboutDiv(projectId) + ' #' + this._getHTMLNameAddThanksPageAboutMoreInfoLink(projectId);
    },

    /**
     * Get HTML selector for sending button in about page
     *
     * @public
     * @param {string} projectId Project identifier
     * @return {string} HTML selector for sending button in about page
     */
    getHTMLSelectorAddThanksPageAboutSend: function (projectId) {
        'use strict';
        // parse arguments
        projectId = this._parseProjectId(projectId);
        // exit
        return this.getHTMLSelectorAddThanksPageAboutDiv(projectId) + ' #' + this._getHTMLNameAddThanksPageAboutSend(projectId);
    },

    /**
     * Get HTML selector for status div in about page
     *
     * @public
     * @param {string} projectId Project identifier
     * @return {string} HTML selector for status div in about page
     */
    getHTMLSelectorAddThanksPageAboutStatusDiv: function (projectId) {
        'use strict';
        // parse arguments
        projectId = this._parseProjectId(projectId);
        // exit
        return this.getHTMLSelectorAddThanksPageAboutDiv(projectId) + ' #' + this._getHTMLNameAddThanksPageAboutStatusDiv(projectId);
    },

    /**
     * Get HTML selector for loading div in about page
     *
     * @public
     * @param {string} projectId Project identifier
     * @return {string} HTML selector for loading div in about page
     */
    getHTMLSelectorAddThanksPageAboutLoadingDiv: function (projectId) {
        'use strict';
        // parse arguments
        projectId = this._parseProjectId(projectId);
        // exit
        return this.getHTMLSelectorAddThanksPageAboutDiv(projectId) + ' #' + this._getHTMLNameAddThanksPageAboutLoadingDiv(projectId);
    },

    /**
     * Set adding thanks for review message
     *
     * @public
     * @param {string} projectId Project identifier
     * @return {void}
     */
    setReviewMessageAddThanks: function (projectId) {
        'use strict';
        // get this object
        var self = this;
        (function ($) {
            // parse arguments
            projectId = self._parseProjectId(projectId);
            // add link to more information
            $(self.getHTMLSelectorAddThanksReviewMessageMoreInfoLinkDiv(projectId)).html('<a href="#" id="' + self._getHTMLNameAddThanksReviewMessageMoreInfoLink(projectId) + '">' + self._valsTextMoreInfoLink + '</a>');
            var selectorAddThanksReviewMessageMoreInfoLink = $(self.getHTMLSelectorAddThanksReviewMessageMoreInfoLink(projectId));
            selectorAddThanksReviewMessageMoreInfoLink.attr('href', 'javascript:void(0);');
            selectorAddThanksReviewMessageMoreInfoLink.bind('click', {
                self: self
            }, function (event) {
                // disable default event
                event.preventDefault();
                // show window with more information
                var fn = window[event.data.self._prj['prj_' + projectId].windowFunction];
                fn();
            });
            // set button event to add thanks
            $(self.getHTMLSelectorAddThanksReviewMessageSend(projectId)).bind('click', {
                self: self,
                projectId: projectId
            }, function (event) {
                // disable default event
                event.preventDefault();
                // optionally restore original styles
                if (event.data.self._stylesSaved) {
                    $(event.data.self.getHTMLSelectorAddThanksStyle(projectId)).html(event.data.self._styles);
                }
                // add thanks
                event.data.self._addThanks(event.data.projectId);
            });
        }(jQuery));
    },

    /**
     * Set adding thanks for about page
     *
     * @public
     * @param {string} projectId Project identifier
     * @return {void}
     */
    setPageAboutAddThanks: function (projectId) {
        'use strict';
        // get this object
        var self = this;
        (function ($) {
            // parse arguments
            projectId = self._parseProjectId(projectId);
            // get selectors
            var selectorAddThanksPageAboutMoreInfoLink = $(self.getHTMLSelectorAddThanksPageAboutMoreInfoLink(projectId));
            // add link to more information
            selectorAddThanksPageAboutMoreInfoLink.attr('href', 'javascript:void(0);');
            selectorAddThanksPageAboutMoreInfoLink.bind('click', {
                self: self
            }, function (event) {
                // disable default event
                event.preventDefault();
                // show window with more information
                var fn = window[event.data.self._prj['prj_' + projectId].windowFunction];
                fn();
            });
            // set button event to add thanks
            $(self.getHTMLSelectorAddThanksPageAboutSend(projectId)).bind('click', {
                self: self,
                projectId: projectId
            }, function (event) {
                // disable default event
                event.preventDefault();
                // add thanks
                event.data.self._addThanks(event.data.projectId);
            });
        }(jQuery));
    },

    /**
     * AJAX loading success callback for adding thanks
     *
     * @public
     * @param {string} projectId Project identifier
     * @param {string} connectionId Connection identifier
     * @param {*} data Data
     * @param {string} status Text status
     * @param {Object} obj Request object
     * @return {void}
     */
    ajaxAddThanksSuccessCallback: function (projectId, connectionId, data, status, obj) {
        'use strict';
        // get this object
        var self = kocujPLV13aBackendAddThanks;
        (function ($) {
            // prepare text to display
            var text = self._valsTextSuccess;
            // check results
            if ((data.status !== undefined) && (data.status.id !== undefined)) {
                if (data.status.id !== 'OK') {
                    if (data.status.id === 'CT_ADD_THANKS_URLALREADYEXISTS') {
                        text = self._valsTextErrorAlreadyExists;
                    } else {
                        self.ajaxAddThanksErrorCallback(projectId, connectionId, null, 'api_error', '');
                        return;
                    }
                }
            } else {
                self.ajaxAddThanksErrorCallback(projectId, connectionId, null, 'api_error', '');
                return;
            }
            // change HTML elements in review message
            self._removeReviewMessageLoadingImageElement(projectId);
            self._showReviewMessageCloseElement(projectId);
            // save information about added thanks
            self._objAllJsAjax.sendPost(projectId, 'add_thanks_finished', ajaxurl, 'text', {
                action: self._valsPrefix + '_' + projectId + '__add_thanks_finished',
                security: self._valsSecurity
            });
            // get selectors
            var selectorAddThanksReviewMessageMoreInfoLinkDiv = $(self.getHTMLSelectorAddThanksReviewMessageMoreInfoLinkDiv(projectId));
            var selectorAddThanksPageAboutStatusDiv = $(self.getHTMLSelectorAddThanksPageAboutStatusDiv(projectId));
            // show information about success
            if (selectorAddThanksReviewMessageMoreInfoLinkDiv.length > 0) {
                selectorAddThanksReviewMessageMoreInfoLinkDiv.html('<strong>' + text + '</strong>');
            }
            if (selectorAddThanksPageAboutStatusDiv.length > 0) {
                selectorAddThanksPageAboutStatusDiv.html('<strong>' + text + '</strong>');
                selectorAddThanksPageAboutStatusDiv.show();
                $(self.getHTMLSelectorAddThanksPageAboutLoadingDiv(projectId)).hide();
            }
            // add timeout to remove adding thanks div
            if (($(self.getHTMLSelectorAddThanksReviewMessageSend(projectId)).length > 0) || ($(self.getHTMLSelectorAddThanksPageAboutDiv(projectId)).length > 0)) {
                self._prj['prj_' + projectId].timer = window.setTimeout(function () {
                    // clear timer
                    window.clearTimeout(self._prj['prj_' + projectId].timer);
                    self._prj['prj_' + projectId].timer = null;
                    // get selectors
                    var selectorAddThanksReviewMessageSend = $(self.getHTMLSelectorAddThanksReviewMessageSend(projectId));
                    var selectorAddThanksPageAboutDiv = $(self.getHTMLSelectorAddThanksPageAboutDiv(projectId));
                    // remove adding thanks div
                    if (selectorAddThanksReviewMessageSend.length > 0) {
                        selectorAddThanksReviewMessageSend.parent().fadeOut('slow', function () {
                            selectorAddThanksReviewMessageSend.parent().remove();
                        });
                    }
                    if (selectorAddThanksPageAboutDiv.length > 0) {
                        selectorAddThanksPageAboutDiv.fadeOut('slow', function () {
                            selectorAddThanksPageAboutDiv.remove();
                        });
                    }
                }, 5000);
            }
        }(jQuery));
    },

    /**
     * AJAX loading error callback for adding thanks
     *
     * @public
     * @param {string} projectId Project identifier
     * @param {string} connectionId Connection identifier
     * @param {Object} obj Request object
     * @param {string} status Text status
     * @param {string} err Error
     * @return {void}
     */
    ajaxAddThanksErrorCallback: function (projectId, connectionId, obj, status, err) {
        'use strict';
        // get this object
        var self = kocujPLV13aBackendAddThanks;
        (function ($) {
            // change HTML elements in review message
            self._removeReviewMessageLoadingImageElement(projectId);
            self._showReviewMessageCloseElement(projectId);
            if ($(self.getHTMLSelectorAddThanksReviewMessageSend(projectId)).length > 0) {
                $(self.getHTMLSelectorAddThanksReviewMessageSend(projectId)).removeAttr('disabled');
            }
            if ($(self.getHTMLSelectorAddThanksPageAboutSend(projectId)).length > 0) {
                $(self.getHTMLSelectorAddThanksPageAboutSend(projectId)).removeAttr('disabled');
            }
            // get selectors
            var selectorAddThanksReviewMessageMoreInfoLinkDiv = $(self.getHTMLSelectorAddThanksReviewMessageMoreInfoLinkDiv(projectId));
            var selectorAddThanksPageAboutStatusDiv = $(self.getHTMLSelectorAddThanksPageAboutStatusDiv(projectId));
            // show information about error
            var infoReview = self._valsTextErrorNoRetries;
            var infoPageAbout = infoReview;
            if (status === 'api_error') {
                infoReview = self._valsTextErrorWrongResponse1 + '<br />' + self._valsTextErrorWrongResponse2;
                infoPageAbout = self._valsTextErrorWrongResponse1 + ' ' + self._valsTextErrorWrongResponse2;
            }
            if (selectorAddThanksReviewMessageMoreInfoLinkDiv.length > 0) {
                selectorAddThanksReviewMessageMoreInfoLinkDiv.html('<strong>' + infoReview + '</strong>');
                if (status === 'api_error') {
                    self._styles = $(self.getHTMLSelectorAddThanksStyle(projectId)).html();
                    $(self.getHTMLSelectorAddThanksStyle(projectId)).html(self._styles + "\n" +
                        '.' + self._valsPrefix + '_' + projectId + '__add_thanks_review_message_element_div {' + "\n" +
                        'height:80px;' + "\n" +
                        '}' + "\n" +
                        '@media screen and (max-width: 782px) {' + "\n" +
                        '.' + self._valsPrefix + '_' + projectId + '__add_thanks_review_message_element_div {' + "\n" +
                        'height:105px;' + "\n" +
                        '}' + "\n" +
                        '}'
                    );
                    self._stylesSaved = true;
                }
            }
            if (selectorAddThanksPageAboutStatusDiv.length > 0) {
                selectorAddThanksPageAboutStatusDiv.html('<strong>' + infoPageAbout + '</strong>');
                selectorAddThanksPageAboutStatusDiv.show();
                $(self.getHTMLSelectorAddThanksPageAboutLoadingDiv(projectId)).hide();
            }
        }(jQuery));
    },

    /**
     * Doing something before waiting for AJAX request retry callback for adding thanks
     *
     * @public
     * @param {string} projectId Project identifier
     * @param {string} connectionId Connection identifier
     * @param {numeric} retries Connection retries
     * @param {Object} obj Request object
     * @param {string} status Text status
     * @param {string} err Error
     * @return {void}
     */
    ajaxAddThanksRetryWaitCallback: function (projectId, connectionId, retries, obj, status, err) {
        'use strict';
        // get this object
        var self = kocujPLV13aBackendAddThanks;
        (function ($) {
            // change HTML elements in review message
            self._removeReviewMessageLoadingImageElement(projectId);
            // get selectors
            var selectorAddThanksReviewMessageMoreInfoLinkDiv = $(self.getHTMLSelectorAddThanksReviewMessageMoreInfoLinkDiv(projectId));
            var selectorAddThanksPageAboutStatusDiv = $(self.getHTMLSelectorAddThanksPageAboutStatusDiv(projectId));
            // show information about error
            if (selectorAddThanksReviewMessageMoreInfoLinkDiv.length > 0) {
                selectorAddThanksReviewMessageMoreInfoLinkDiv.html('<strong>' + self._valsTextError + '</strong>');
            }
            if (selectorAddThanksPageAboutStatusDiv.length > 0) {
                selectorAddThanksPageAboutStatusDiv.html('<strong>' + self._valsTextError + '</strong>');
                selectorAddThanksPageAboutStatusDiv.show();
                $(self.getHTMLSelectorAddThanksPageAboutLoadingDiv(projectId)).hide();
            }
        }(jQuery));
    },

    /**
     * Doing something at the beginning of AJAX request retry callback for adding thanks
     *
     * @public
     * @param {string} projectId Project identifier
     * @param {string} connectionId Connection identifier
     * @param {numeric} retries Connection retries
     * @param {Object} obj Request object
     * @param {string} status Text status
     * @param {string} err Error
     * @return {void}
     */
    ajaxAddThanksRetryNowCallback: function (projectId, connectionId, retries, obj, status, err) {
        'use strict';
        // get this object
        var self = kocujPLV13aBackendAddThanks;
        (function ($) {
            // change HTML elements
            self._changeElementsBeforeAddThanksAjaxSend(projectId);
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
     * Get HTML prefix for Kocuj Internal Lib
     *
     * @private
     * @param {string} projectId Project identifier
     * @return {string} HTML prefix for Kocuj Internal Lib
     */
    _getHTMLPrefixKocujIL: function (projectId) {
        'use strict';
        // exit
        return this._valsPrefixKocujIL + '_' + projectId + '__';
    },

    /**
     * Get HTML name for review message div
     *
     * @private
     * @param {string} projectId Project identifier
     * @return {string} HTML name for review message div
     */
    _getHTMLNameReviewMessageDiv: function (projectId) {
        'use strict';
        // exit
        return this._getHTMLPrefixKocujIL(projectId) + 'message_review';
    },

    /**
     * Get HTML name for style
     *
     * @private
     * @param {string} projectId Project identifier
     * @return {string} HTML name for style
     */
    _getHTMLNameAddThanksStyle: function (projectId) {
        'use strict';
        // exit
        return this._getHTMLPrefix(projectId) + 'add_thanks_review_message_style';
    },

    /**
     * Get HTML name for more information link div in review message
     *
     * @private
     * @param {string} projectId Project identifier
     * @return {string} HTML name for more information link div in review message
     */
    _getHTMLNameAddThanksReviewMessageMoreInfoLinkDiv: function (projectId) {
        'use strict';
        // exit
        return this._getHTMLPrefix(projectId) + 'add_thanks_review_message_more_info_link_div';
    },

    /**
     * Get HTML name for more information link in review message
     *
     * @private
     * @param {string} projectId Project identifier
     * @return {string} HTML name for more information link in review message
     */
    _getHTMLNameAddThanksReviewMessageMoreInfoLink: function (projectId) {
        'use strict';
        // exit
        return this._getHTMLPrefix(projectId) + 'add_thanks_review_message_more_info_link';
    },

    /**
     * Get HTML name for sending button in review message
     *
     * @private
     * @param {string} projectId Project identifier
     * @return {string} HTML name for sending button in review message
     */
    _getHTMLNameAddThanksReviewMessageSend: function (projectId) {
        'use strict';
        // exit
        return this._getHTMLPrefix(projectId) + 'add_thanks_review_message_send';
    },

    /**
     * Get HTML name for close message in review message
     *
     * @private
     * @param {string} projectId Project identifier
     * @return {string} HTML name for close message in review message
     */
    _getHTMLNameReviewMessageClose: function (projectId) {
        'use strict';
        // exit
        return this._getHTMLPrefixKocujIL(projectId) + 'review_message_close';
    },

    /**
     * Get HTML name for loading image in review message
     *
     * @private
     * @param {string} projectId Project identifier
     * @return {string} HTML name for loading image in review message
     */
    _getHTMLNameAddThanksReviewMessageLoadingImage: function (projectId) {
        'use strict';
        // exit
        return this._getHTMLPrefixKocujIL(projectId) + 'add_thanks_review_message_loading_image';
    },

    /**
     * Get HTML name for adding thanks div in page about
     *
     * @private
     * @param {string} projectId Project identifier
     * @return {string} HTML name for adding thanks div in page about
     */
    _getHTMLNameAddThanksPageAboutDiv: function (projectId) {
        'use strict';
        // exit
        return this._getHTMLPrefix(projectId) + 'add_thanks_page_about_div';
    },

    /**
     * Get HTML name for more information link in page about
     *
     * @private
     * @param {string} projectId Project identifier
     * @return {string} HTML name for more information link in page about
     */
    _getHTMLNameAddThanksPageAboutMoreInfoLink: function (projectId) {
        'use strict';
        // exit
        return this._getHTMLPrefix(projectId) + 'add_thanks_page_about_more_info_link';
    },

    /**
     * Get HTML name for sending button in page about
     *
     * @private
     * @param {string} projectId Project identifier
     * @return {string} HTML name for sending button in page about
     */
    _getHTMLNameAddThanksPageAboutSend: function (projectId) {
        'use strict';
        // exit
        return this._getHTMLPrefix(projectId) + 'add_thanks_page_about_send';
    },

    /**
     * Get HTML name for status div in page about
     *
     * @private
     * @param {string} projectId Project identifier
     * @return {string} HTML name for status div in page about
     */
    _getHTMLNameAddThanksPageAboutStatusDiv: function (projectId) {
        'use strict';
        // exit
        return this._getHTMLPrefix(projectId) + 'add_thanks_page_about_status_div';
    },

    /**
     * Get HTML name for loading div in page about
     *
     * @private
     * @param {string} projectId Project identifier
     * @return {string} HTML name for loading div in page about
     */
    _getHTMLNameAddThanksPageAboutLoadingDiv: function (projectId) {
        'use strict';
        // exit
        return this._getHTMLPrefix(projectId) + 'add_thanks_page_about_loading_div';
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
            return '';
        }
        // check if project exists
        if (this._prj['prj_' + projectId] === undefined) {
            this._throwError('PROJECT_DOES_NOT_EXIST', projectId);
            return '';
        }
        // exit
        return projectId;
    },

    /**
     * Check arguments for adding project
     *
     * @private
     * @param {string} projectId Project identifier
     * @param {string} windowFunction Function name for window
     * @param {string} projectApiUrl Project API URL
     * @param {string} projectApiLogin Project API login
     * @param {string} [projectApiPassword] Project API password
     * @return {Object} Parsed arguments for adding project
     * @throws {kocujILV13aCException} kocujILV13aExceptionCode.EMPTY_PROJECT_ID if project identifier entered in projectId is empty
     * @throws {kocujILV13aCException} kocujILV13aExceptionCode.ADD_THANKS_EMPTY_API_URL if API URL entered in projectApiUrl is empty
     * @throws {kocujILV13aCException} kocujILV13aExceptionCode.ADD_THANKS_EMPTY_API_LOGIN if API login entered in projectApiLogin is empty
     */
    _checkAddProject: function (projectId, windowFunction, projectApiUrl, projectApiLogin, projectApiPassword) {
        'use strict';
        // parse arguments
        projectId = this._objHelper.initString(projectId);
        if (projectId === '') {
            this._throwError('EMPTY_PROJECT_ID');
            return;
        }
        windowFunction = this._objHelper.initString(windowFunction);
        if (windowFunction === '') {
            this._throwError('ADD_THANKS_EMPTY_WINDOW_FUNCTION');
            return;
        }
        projectApiUrl = this._objHelper.initString(projectApiUrl);
        if (projectApiUrl === '') {
            this._throwError('ADD_THANKS_EMPTY_API_URL');
            return;
        }
        projectApiLogin = this._objHelper.initString(projectApiLogin);
        if (projectApiLogin === '') {
            this._throwError('ADD_THANKS_EMPTY_API_LOGIN');
            return;
        }
        projectApiPassword = this._objHelper.initString(projectApiPassword);
        // exit
        return {
            projectId: projectId,
            windowFunction: windowFunction,
            projectApiUrl: projectApiUrl,
            projectApiLogin: projectApiLogin,
            projectApiPassword: projectApiPassword
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
            eval('throw new kocujPLV13aCException(kocujPLV13aExceptionCode.' + codeString + ', this._thisFilename, param);');
        }
    },

    /**
     * Add thanks
     *
     * @private
     * @param {string} projectId Project identifier
     * @return {void}
     */
    _addThanks: function (projectId) {
        'use strict';
        // get this object
        var self = this;
        (function ($) {
            // parse arguments
            projectId = self._parseProjectId(projectId);
            // change HTML elements
            self._changeElementsBeforeAddThanksAjaxSend(projectId);
            // add thanks
            self._objAllJsAjax.sendJson(projectId, 'add_thanks', self._prj['prj_' + projectId].projectApiUrl, {
                'requestType': 'parameters',
                'responseType': 'JSON',
                'requestMethod': 'GET',
                'data': {
                    'PARAMETER_version': 1,
                    'header': {
                        'login': self._prj['prj_' + projectId].projectApiLogin,
                        'password': self._prj['prj_' + projectId].projectApiPassword
                    },
                    'request': {
                        'PARAMETER_command': 'ADD_THANKS',
                        'url': self._valsWebsiteUrl,
                        'title': self._valsWebsiteTitle,
                        'description': self._valsWebsiteDescription
                    }
                }
            }, {
                success: self.ajaxAddThanksSuccessCallback,
                error: self.ajaxAddThanksErrorCallback,
                retryWait: self.ajaxAddThanksRetryWaitCallback,
                retryNow: self.ajaxAddThanksRetryNowCallback
            });
        }(jQuery));
    },

    /**
     * Change HTML elements before sending AJAX for adding thanks
     *
     * @private
     * @param {string} projectId Project identifier
     * @return {void}
     */
    _changeElementsBeforeAddThanksAjaxSend: function (projectId) {
        'use strict';
        // get this object
        var self = this;
        (function ($) {
            // get selectors
            var selectorAddThanksReviewMessageSend = $(self.getHTMLSelectorAddThanksReviewMessageSend(projectId));
            var selectorAddThanksPageAboutSend = $(self.getHTMLSelectorAddThanksPageAboutSend(projectId));
            var selectorAddThanksReviewMessageMoreInfoLinkDiv = $(self.getHTMLSelectorAddThanksReviewMessageMoreInfoLinkDiv(projectId));
            var selectorAddThanksPageAboutStatusDiv = $(self.getHTMLSelectorAddThanksPageAboutStatusDiv(projectId));
            var selectorReviewMessageClose = $(self.getHTMLSelectorReviewMessageClose(projectId));
            // show information about sending process
            if (selectorAddThanksReviewMessageSend.length > 0) {
                selectorAddThanksReviewMessageSend.attr('disabled', true);
            }
            if (selectorAddThanksPageAboutSend.length > 0) {
                selectorAddThanksPageAboutSend.attr('disabled', true);
            }
            if (selectorAddThanksReviewMessageMoreInfoLinkDiv.length > 0) {
                selectorAddThanksReviewMessageMoreInfoLinkDiv.html('<em>' + self._valsTextSending + '</em>');
            }
            if (selectorAddThanksPageAboutStatusDiv.length > 0) {
                selectorAddThanksPageAboutStatusDiv.html('<em>' + self._valsTextSending + '</em>');
                selectorAddThanksPageAboutStatusDiv.show();
                $(self.getHTMLSelectorAddThanksPageAboutLoadingDiv(projectId)).show();
            }
            // disable closing review message
            if (selectorReviewMessageClose.length > 0) {
                selectorReviewMessageClose.hide();
                $(self.getHTMLSelectorReviewMessageDiv(projectId)).append('<div id="' + self._getHTMLNameAddThanksReviewMessageLoadingImage(projectId) + '" style="margin-left:5px;margin-bottom:5px;"><img src="' + self._valsImageLoadingUrl + '" alt="" /></div>');
            }
        }(jQuery));
    },

    /**
     * Remove of loading image element from review message
     *
     * @private
     * @param {string} projectId Project identifier
     * @return {void}
     */
    _removeReviewMessageLoadingImageElement: function (projectId) {
        'use strict';
        // get this object
        var self = this;
        (function ($) {
            // remove of loading image element
            if ($(self.getHTMLSelectorAddThanksReviewMessageLoadingImage(projectId)).length > 0) {
                $(self.getHTMLSelectorAddThanksReviewMessageLoadingImage(projectId)).remove();
            }
        }(jQuery));
    },

    /**
     * Show close element for review message
     *
     * @private
     * @param {string} projectId Project identifier
     * @return {void}
     */
    _showReviewMessageCloseElement: function (projectId) {
        'use strict';
        // get this object
        var self = this;
        (function ($) {
            // show close element
            if ($(self.getHTMLSelectorReviewMessageClose(projectId)).length > 0) {
                $(self.getHTMLSelectorReviewMessageClose(projectId)).show();
            }
        }(jQuery));
    }
};

// initialize
var kocujPLV13aBackendAddThanks = new kocujPLV13aCBackendAddThanks();
