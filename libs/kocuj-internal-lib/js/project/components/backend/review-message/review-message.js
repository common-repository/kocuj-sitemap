/**
 * @file Review message
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

/* global kocujILV13aBackendReviewMessageVals */

/**
 * Review message prototype constructor
 *
 * @constructs
 * @namespace kocujILV13aCBackendReviewMessage
 * @public
 * @return {void}
 */
function kocujILV13aCBackendReviewMessage() {
    'use strict';
    /* jshint validthis: true */
    // get this object
    var self = this;
    // initialize objects
    self._objHelper = kocujILV13aHelper;
    // get current script filename
    self._thisFilename = document.scripts[document.scripts.length - 1].src;
    // get settings
    var vals = kocujILV13aBackendReviewMessageVals;
    self._valsThrowErrors = vals.throwErrors === '1';
    self._valsPrefix = vals.prefix;
    self._valsSecurity = vals.security;
}

/**
 * Review message prototype
 *
 * @namespace kocujILV13aCBackendReviewMessage
 * @public
 */
kocujILV13aCBackendReviewMessage.prototype = {
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
     * @param {string} [voteUrl] URL for voting for
     * @param {string} [facebookUrl] URL for tell others on Facebook
     * @param {string} [twitterUrl] URL for tell others on Twitter
     * @param {string} [translationUrl] URL for project translation
     * @return {void}
     * @throws {kocujILV13aCException} kocujILV13aExceptionCode.PROJECT_ALREADY_EXISTS if project identifier entered in projectId already exists
     */
    addProject: function (projectId, voteUrl, facebookUrl, twitterUrl, translationUrl) {
        'use strict';
        // parse arguments
        var args = this._checkAddProject(projectId, voteUrl, facebookUrl, twitterUrl, translationUrl);
        // add project
        if (this._prj['prj_' + args.projectId] === undefined) {
            this.addProjectIfNotExists(args.projectId, args.voteUrl, args.facebookUrl, args.twitterUrl, args.translationUrl);
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
     * @param {string} [voteUrl] URL for voting for
     * @param {string} [facebookUrl] URL for tell others on Facebook
     * @param {string} [twitterUrl] URL for tell others on Twitter
     * @param {string} [translationUrl] URL for project translation
     * @return {void}
     */
    addProjectIfNotExists: function (projectId, voteUrl, facebookUrl, twitterUrl, translationUrl) {
        'use strict';
        // parse arguments
        var args = this._checkAddProject(projectId, voteUrl, facebookUrl, twitterUrl, translationUrl);
        // add project
        if (this._prj['prj_' + args.projectId] === undefined) {
            this._prj['prj_' + args.projectId] = {
                voteUrl: args.voteUrl,
                facebookUrl: args.facebookUrl,
                twitterUrl: args.twitterUrl,
                translationUrl: args.translationUrl
            };
        }
    },

    /**
     * Get HTML selector for message review
     *
     * @public
     * @param {string} projectId Project identifier
     * @return {string} HTML selector for message review
     */
    getHTMLSelectorMessageReview: function (projectId) {
        'use strict';
        // parse arguments
        projectId = this._parseProjectId(projectId);
        // exit
        return '#' + this._getHTMLNameMessageReview(projectId);
    },

    /**
     * Get HTML selector for message review by voting
     *
     * @public
     * @param {string} projectId Project identifier
     * @return {string} HTML selector for message review by voting
     */
    getHTMLSelectorReviewMessageVote: function (projectId) {
        'use strict';
        // parse arguments
        projectId = this._parseProjectId(projectId);
        // exit
        return '#' + this._getHTMLNameReviewMessageVote(projectId);
    },

    /**
     * Get HTML selector for message review by Facebook
     *
     * @public
     * @param {string} projectId Project identifier
     * @return {string} HTML selector for message review by Facebook
     */
    getHTMLSelectorReviewMessageFacebook: function (projectId) {
        'use strict';
        // parse arguments
        projectId = this._parseProjectId(projectId);
        // exit
        return '#' + this._getHTMLNameReviewMessageFacebook(projectId);
    },

    /**
     * Get HTML selector for message review by Twitter
     *
     * @public
     * @param {string} projectId Project identifier
     * @return {string} HTML selector for message review by Twitter
     */
    getHTMLSelectorReviewMessageTwitter: function (projectId) {
        'use strict';
        // parse arguments
        projectId = this._parseProjectId(projectId);
        // exit
        return '#' + this._getHTMLNameReviewMessageTwitter(projectId);
    },

    /**
     * Get HTML selector for message review about translation
     *
     * @public
     * @param {string} projectId Project identifier
     * @return {string} HTML selector for message review about translation
     */
    getHTMLSelectorReviewMessageTranslation: function (projectId) {
        'use strict';
        // parse arguments
        projectId = this._parseProjectId(projectId);
        // exit
        return '#' + this._getHTMLNameReviewMessageTranslation(projectId);
    },

    /**
     * Set review message events
     *
     * @public
     * @param {string} projectId Project identifier
     * @return {void}
     */
    setEvents: function (projectId) {
        'use strict';
        // get this object
        var self = this;
        (function ($) {
            // parse arguments
            projectId = self._parseProjectId(projectId);
            // add events
            if (self._prj['prj_' + projectId].voteUrl !== '') {
                self._setEvent(projectId, self.getHTMLSelectorReviewMessageVote(projectId), 'vote', self._prj['prj_' + projectId].voteUrl);
            }
            if (self._prj['prj_' + projectId].facebookUrl !== '') {
                self._setEvent(projectId, self.getHTMLSelectorReviewMessageFacebook(projectId), 'facebook', self._prj['prj_' + projectId].facebookUrl);
            }
            if (self._prj['prj_' + projectId].twitterUrl !== '') {
                self._setEvent(projectId, self.getHTMLSelectorReviewMessageTwitter(projectId), 'twitter', self._prj['prj_' + projectId].twitterUrl);
            }
            if (self._prj['prj_' + projectId].translationUrl !== '') {
                self._setEvent(projectId, self.getHTMLSelectorReviewMessageTranslation(projectId), 'translation', self._prj['prj_' + projectId].translationUrl);
            }
        }(jQuery));
    },

    /**
     * Get event name prefix
     *
     * @private
     * @param {string} projectId Project identifier
     * @return {string} Event name prefix
     */
    _getEventNamePrefix: function (projectId) {
        'use strict';
        // exit
        return this._valsPrefix + '_' + projectId + '__';
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
     * Get HTML prefix for message review
     *
     * @private
     * @param {string} projectId Project identifier
     * @return {string} HTML prefix for message review
     */
    _getHTMLNameMessageReview: function (projectId) {
        'use strict';
        // exit
        return this._getHTMLPrefix(projectId) + 'message_review';
    },

    /**
     * Get HTML prefix for message review by voting
     *
     * @private
     * @param {string} projectId Project identifier
     * @return {string} HTML prefix for message review by voting
     */
    _getHTMLNameReviewMessageVote: function (projectId) {
        'use strict';
        // exit
        return this._getHTMLPrefix(projectId) + 'review_message_vote';
    },

    /**
     * Get HTML prefix for message review by Facebook
     *
     * @private
     * @param {string} projectId Project identifier
     * @return {string} HTML prefix for message review by Facebook
     */
    _getHTMLNameReviewMessageFacebook: function (projectId) {
        'use strict';
        // exit
        return this._getHTMLPrefix(projectId) + 'review_message_facebook';
    },

    /**
     * Get HTML prefix for message review by Twitter
     *
     * @private
     * @param {string} projectId Project identifier
     * @return {string} HTML prefix for message review by Twitter
     */
    _getHTMLNameReviewMessageTwitter: function (projectId) {
        'use strict';
        // exit
        return this._getHTMLPrefix(projectId) + 'review_message_twitter';
    },

    /**
     * Get HTML prefix for message review about translation
     *
     * @private
     * @param {string} projectId Project identifier
     * @return {string} HTML prefix for message review about translation
     */
    _getHTMLNameReviewMessageTranslation: function (projectId) {
        'use strict';
        // exit
        return this._getHTMLPrefix(projectId) + 'review_message_translation';
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
     * @param {string} [voteUrl] URL for voting for
     * @param {string} [facebookUrl] URL for tell others on Facebook
     * @param {string} [twitterUrl] URL for tell others on Twitter
     * @param {string} [translationUrl] URL for project translation
     * @return {Object} Parsed arguments for adding project
     * @throws {kocujILV13aCException} kocujILV13aExceptionCode.EMPTY_PROJECT_ID if project identifier entered in projectId is empty
     */
    _checkAddProject: function (projectId, voteUrl, facebookUrl, twitterUrl, translationUrl) {
        'use strict';
        // parse arguments
        projectId = this._objHelper.initString(projectId);
        if (projectId === '') {
            this._throwError('EMPTY_PROJECT_ID');
            return;
        }
        voteUrl = this._objHelper.initString(voteUrl);
        facebookUrl = this._objHelper.initString(facebookUrl);
        twitterUrl = this._objHelper.initString(twitterUrl);
        translationUrl = this._objHelper.initString(translationUrl);
        // exit
        return {
            projectId: projectId,
            voteUrl: voteUrl,
            facebookUrl: facebookUrl,
            twitterUrl: twitterUrl,
            translationUrl: translationUrl
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
     * Set event
     *
     * @private
     * @param {string} projectId Project identifier
     * @param {string} elementPath DOM path to element inside the review message box
     * @param {string} eventNameSuffix Suffix for event name
     * @param {string} url URL opened in new window after click event
     * @return {void}
     */
    _setEvent: function (projectId, elementPath, eventNameSuffix, url) {
        'use strict';
        // get this object
        var self = this;
        (function ($) {
            // parse arguments
            projectId = self._parseProjectId(projectId);
            elementPath = self._objHelper.initString(elementPath);
            eventNameSuffix = self._objHelper.initString(eventNameSuffix);
            url = self._objHelper.initString(url);
            // get selectors
            var selectorMessageReviewButton = $(self.getHTMLSelectorMessageReview(projectId) + ' ' + elementPath);
            // set event
            if (selectorMessageReviewButton.length > 0) {
                selectorMessageReviewButton.bind('click.' + self._getEventNamePrefix(projectId) + 'review_message_' + eventNameSuffix, {
                    projectId: projectId,
                    url: url
                }, function (event) {
                    event.preventDefault();
                    window.open(event.data.url, '_blank');
                });
            }
        }(jQuery));
    }
};

// initialize
var kocujILV13aBackendReviewMessage = new kocujILV13aCBackendReviewMessage();
