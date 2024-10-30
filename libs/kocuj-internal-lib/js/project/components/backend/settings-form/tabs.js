/**
 * @file Tabs
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

/* global kocujILV13aHelper */

/* global kocujILV13aBackendSettingsFormTabsVals */

/**
 * Tabs prototype constructor
 *
 * @constructs
 * @namespace kocujILV13aCBackendSettingsFormTabs
 * @public
 * @return {void}
 */
function kocujILV13aCBackendSettingsFormTabs() {
    'use strict';
    /* jshint validthis: true */
    // get this object
    var self = this;
    // initialize objects
    self._objHelper = kocujILV13aHelper;
    // get current script filename
    self._thisFilename = document.scripts[document.scripts.length - 1].src;
    // get settings
    var vals = kocujILV13aBackendSettingsFormTabsVals;
    self._valsThrowErrors = vals.throwErrors === '1';
    self._valsPrefix = vals.prefix;
}

/**
 * Tabs prototype
 *
 * @namespace kocujILV13aCBackendSettingsFormTabs
 * @public
 */
kocujILV13aCBackendSettingsFormTabs.prototype = {
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
     * Add project
     *
     * @public
     * @param {string} projectId Project identifier
     * @param {string} [projectName] Project name
     * @return {void}
     * @throws {kocujILV13aCException} kocujILV13aExceptionCode.PROJECT_ALREADY_EXISTS if project identifier entered in projectId already exists
     */
    addProject: function (projectId, projectName) {
        'use strict';
        // parse arguments
        var args = this._checkAddProject(projectId, projectName);
        // add project
        if (this._prj['prj_' + args.projectId] === undefined) {
            this.addProjectIfNotExists(args.projectId, args.projectName);
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
     * @param {string} [projectName] Project name
     * @return {void}
     */
    addProjectIfNotExists: function (projectId, projectName) {
        'use strict';
        // parse arguments
        var args = this._checkAddProject(projectId, projectName);
        // add project
        if (this._prj['prj_' + args.projectId] === undefined) {
            this._prj['prj_' + args.projectId] = {
                projectName: args.projectName
            };
        }
    },

    /**
     * Get HTML selector for tab
     *
     * @public
     * @param {string} projectId Project identifier
     * @param {number} tabId Tab identifier
     * @param {string} formId Form identifier
     * @return {string} HTML selector for tab
     */
    getHTMLSelectorTab: function (projectId, formId, tabId) {
        'use strict';
        // parse arguments
        projectId = this._parseProjectId(projectId);
        formId = this._objHelper.initString(formId);
        tabId = this._objHelper.initNumeric(tabId);
        // exit
        return '#' + this._getHTMLNameTab(projectId, formId, tabId);
    },

    /**
     * Get HTML selector for tab div
     *
     * @public
     * @param {string} projectId Project identifier
     * @param {string} formId Form identifier
     * @param {number} tabId Tab identifier
     * @return {string} HTML selector for tab div
     */
    getHTMLSelectorTabDiv: function (projectId, formId, tabId) {
        'use strict';
        // parse arguments
        projectId = this._parseProjectId(projectId);
        formId = this._objHelper.initString(formId);
        tabId = this._objHelper.initNumeric(tabId);
        // exit
        return '#' + this._getHTMLNameTabDiv(projectId, formId, tabId);
    },

    /**
     * Process form tabs
     *
     * @public
     * @param {string} projectId Project identifier
     * @param {string} formId Form identifier
     * @param {number} tabsCount Tabs count
     * @return {void}
     */
    process: function (projectId, formId, tabsCount) {
        'use strict';
        // get this object
        var self = this;
        (function ($) {
            // parse parameters
            projectId = self._parseProjectId(projectId);
            formId = self._objHelper.initString(formId);
            tabsCount = self._objHelper.initNumeric(tabsCount);
            // activate first tab
            self._eventTabClick(projectId, formId, 0, tabsCount);
            // add events
            for (var z = 0; z < tabsCount; z++) {
                $(self.getHTMLSelectorTab(projectId, formId, z)).bind('click', {
                    projectId: projectId,
                    formId: formId,
                    pos: z,
                    tabsCount: tabsCount
                }, function (event) {
                    event.preventDefault();
                    self._eventTabClick(event.data.projectId, event.data.formId, event.data.pos, event.data.tabsCount);
                });
            }
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
     * Get HTML tab name
     *
     * @private
     * @param {string} projectId Project identifier
     * @param {string} formId Form identifier
     * @param {number} tabId Tab identifier
     * @return {string} HTML tab name
     */
    _getHTMLNameTab: function (projectId, formId, tabId) {
        'use strict';
        // exit
        return this._getHTMLPrefix(projectId) + 'form__' + formId + '__tab__' + tabId;
    },

    /**
     * Get HTML tab div name
     *
     * @private
     * @param {string} projectId Project identifier
     * @param {string} formId Form identifier
     * @param {number} tabId Tab identifier
     * @return {string} HTML tab div name
     */
    _getHTMLNameTabDiv: function (projectId, formId, tabId) {
        'use strict';
        // exit
        return this._getHTMLPrefix(projectId) + 'form__' + formId + '__tab_div__' + tabId;
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
     * @param {string} [projectName] Project name
     * @return {Object} Parsed arguments for adding project
     * @throws {kocujILV13aCException} kocujILV13aExceptionCode.EMPTY_PROJECT_ID if project identifier entered in projectId is empty
     */
    _checkAddProject: function (projectId, projectName) {
        'use strict';
        // parse arguments
        projectId = this._objHelper.initString(projectId);
        if (projectId === '') {
            this._throwError('EMPTY_PROJECT_ID');
            return;
        }
        projectName = this._objHelper.initString(projectName);
        // exit
        return {
            projectId: projectId,
            projectName: projectName
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
     * Event for clicking on tab
     *
     * @private
     * @param {string} projectId Project identifier
     * @param {string} formId Form identifier
     * @param {number} tabId Tab identifier
     * @param {number} tabsCount Tabs count
     * @return {void}
     */
    _eventTabClick: function (projectId, formId, tabId, tabsCount) {
        'use strict';
        // get this object
        var self = this;
        (function ($) {
            // change all tabs to no selected
            for (var z = 0; z < tabsCount; z++) {
                if (z !== tabId) {
                    $(self.getHTMLSelectorTab(projectId, formId, z)).attr('class', 'nav-tab');
                    $(self.getHTMLSelectorTabDiv(projectId, formId, z)).css('visibility', 'hidden');
                    $(self.getHTMLSelectorTabDiv(projectId, formId, z)).css('position', 'absolute');
                }
            }
            // set selected tab as active
            $(self.getHTMLSelectorTab(projectId, formId, tabId)).attr('class', 'nav-tab nav-tab-active');
            $(self.getHTMLSelectorTabDiv(projectId, formId, tabId)).css('visibility', 'visible');
            $(self.getHTMLSelectorTabDiv(projectId, formId, tabId)).css('position', 'static');
        }(jQuery));
    }
};

// initialize
var kocujILV13aBackendSettingsFormTabs = new kocujILV13aCBackendSettingsFormTabs();
