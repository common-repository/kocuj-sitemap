/**
 * @file Array
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

/* global kocujILV13aHelper */
/* global kocujILV13aDataHelper */

/* global kocujILV13aBackendSettingsFormArrayVals */

/**
 * Array prototype constructor
 *
 * @constructs
 * @namespace kocujILV13aCBackendSettingsFormArray
 * @public
 * @return {void}
 */
function kocujILV13aCBackendSettingsFormArray() {
    'use strict';
    /* jshint validthis: true */
    // get this object
    var self = this;
    // initialize objects
    self._objHelper = kocujILV13aHelper;
    self._objDataHelper = kocujILV13aDataHelper;
    // get current script filename
    self._thisFilename = document.scripts[document.scripts.length - 1].src;
    // get settings
    var vals = kocujILV13aBackendSettingsFormArrayVals;
    self._valsThrowErrors = vals.throwErrors === '1';
    self._valsHtmlIdFormatArrayContainer = vals.htmlIdFormatArrayContainer;
    self._valsTextButtonAddNewElement = vals.textButtonAddNewElement;
    self._valsDivElementContainer = vals.divElementContainer;
    self._valsDivField = vals.divField;
    self._valsDivControls = vals.divControls;
    self._valsImageArrowDown = vals.imageArrowDown;
    self._valsImageArrowUp = vals.imageArrowUp;
    self._valsImageDelete = vals.imageDelete;
    self._valsImageEmpty = vals.imageEmpty;
}

/**
 * Array prototype
 *
 * @namespace kocujILV13aCBackendSettingsFormArray
 * @public
 */
kocujILV13aCBackendSettingsFormArray.prototype = {
    /**
     * Object kocujILV13aHelper
     *
     * @private
     * @type {Object}
     */
    _objHelper: null,

    /**
     * Object kocujILV13aDataHelper
     *
     * @private
     * @type {Object}
     */
    _objDataHelper: null,

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
     * Script settings - HTML identifier format for array container
     *
     * @private
     * @type {string}
     */
    _valsHtmlIdFormatArrayContainer: '',

    /**
     * Script settings - text for button for adding new element
     *
     * @private
     * @type {string}
     */
    _valsTextButtonAddNewElement: '',

    /**
     * Script settings - div for element container
     *
     * @private
     * @type {string}
     */
    _valsDivElementContainer: '',

    /**
     * Script settings - div for element field
     *
     * @private
     * @type {string}
     */
    _valsDivField: '',

    /**
     * Script settings - div for element controls
     *
     * @private
     * @type {string}
     */
    _valsDivControls: '',

    /**
     * Script settings - image with down arrow
     *
     * @private
     * @type {string}
     */
    _valsImageArrowDown: '',

    /**
     * Script settings - image with up arrow
     *
     * @private
     * @type {string}
     */
    _valsImageArrowUp: '',

    /**
     * Script settings - image with delete button
     *
     * @private
     * @type {string}
     */
    _valsImageDelete: '',

    /**
     * Script settings - empty image
     *
     * @private
     * @type {string}
     */
    _valsImageEmpty: '',

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
                projectName: args.projectName,
                arrays: []
            };
        }
    },

    /**
     * Get HTML selector for array container
     *
     * @public
     * @param {string} projectId Project identifier
     * @param {string} formId Form identifier
     * @param {string} fieldId Field identifier
     * @return {string} HTML selector for array container
     */
    getHTMLSelectorArrayContainer: function (projectId, formId, fieldId) {
        'use strict';
        // parse arguments
        projectId = this._parseProjectId(projectId);
        formId = this._objHelper.initString(formId);
        fieldId = this._objHelper.initString(fieldId);
        // exit
        return '#' + this._getHTMLNameArrayContainer(projectId, formId, fieldId);
    },

    /**
     * Get HTML selector for array element container
     *
     * @public
     * @param {string} projectId Project identifier
     * @param {string} formId Form identifier
     * @param {string} fieldId Field identifier
     * @param {number} number Number of element in array
     * @return {string} HTML selector for array element container
     */
    getHTMLSelectorArrayElementContainer: function (projectId, formId, fieldId, number) {
        'use strict';
        // parse arguments
        projectId = this._parseProjectId(projectId);
        formId = this._objHelper.initString(formId);
        fieldId = this._objHelper.initString(fieldId);
        number = this._objHelper.initNumeric(number);
        // exit
        return '#' + this._getHTMLNameArrayContainer(projectId, formId, fieldId) + ' div[data-type="element-container"][data-number="' + number + '"]';
    },

    /**
     * Get HTML selector for array empty element container
     *
     * @public
     * @param {string} projectId Project identifier
     * @param {string} formId Form identifier
     * @param {string} fieldId Field identifier
     * @return {string} HTML selector for array empty element container
     */
    getHTMLSelectorArrayEmptyElementContainer: function (projectId, formId, fieldId) {
        'use strict';
        // parse arguments
        projectId = this._parseProjectId(projectId);
        formId = this._objHelper.initString(formId);
        fieldId = this._objHelper.initString(fieldId);
        // exit
        return '#' + this._getHTMLNameArrayContainer(projectId, formId, fieldId) + ' div[data-type="element-container"][data-next="na"]';
    },

    /**
     * Get HTML selector for array element container field div
     *
     * @public
     * @param {string} projectId Project identifier
     * @param {string} formId Form identifier
     * @param {string} fieldId Field identifier
     * @param {number} number Number of element in array
     * @return {string} HTML selector for array element container field div
     */
    getHTMLSelectorArrayElementContainerFieldDiv: function (projectId, formId, fieldId, number) {
        'use strict';
        // parse arguments
        projectId = this._parseProjectId(projectId);
        formId = this._objHelper.initString(formId);
        fieldId = this._objHelper.initString(fieldId);
        number = this._objHelper.initNumeric(number);
        // exit
        return this.getHTMLSelectorArrayElementContainer(projectId, formId, fieldId, number) + ' div[data-type="field"]';
    },

    /**
     * Get HTML selector for array element container field value
     *
     * @public
     * @param {string} projectId Project identifier
     * @param {string} formId Form identifier
     * @param {string} fieldId Field identifier
     * @param {number} number Number of element in array
     * @return {string} HTML selector for array element container field value
     */
    getHTMLSelectorArrayElementContainerFieldValue: function (projectId, formId, fieldId, number) {
        'use strict';
        // parse arguments
        projectId = this._parseProjectId(projectId);
        formId = this._objHelper.initString(formId);
        fieldId = this._objHelper.initString(fieldId);
        number = this._objHelper.initNumeric(number);
        // exit
        return this.getHTMLSelectorArrayElementContainer(projectId, formId, fieldId, number) + ' [data-type="field-value"]';
    },

    /**
     * Get HTML selector for array element container controls div
     *
     * @public
     * @param {string} projectId Project identifier
     * @param {string} formId Form identifier
     * @param {string} fieldId Field identifier
     * @param {number} number Number of element in array
     * @return {string} HTML selector for array element container controls div
     */
    getHTMLSelectorArrayElementContainerControlsDiv: function (projectId, formId, fieldId, number) {
        'use strict';
        // parse arguments
        projectId = this._parseProjectId(projectId);
        formId = this._objHelper.initString(formId);
        fieldId = this._objHelper.initString(fieldId);
        number = this._objHelper.initNumeric(number);
        // exit
        return this.getHTMLSelectorArrayElementContainer(projectId, formId, fieldId, number) + ' div[data-type="controls"]';
    },

    /**
     * Get HTML selector for array element down arrow
     *
     * @public
     * @param {string} projectId Project identifier
     * @param {string} formId Form identifier
     * @param {string} fieldId Field identifier
     * @param {number} number Number of element in array
     * @return {string} HTML selector for array element down arrow
     */
    getHTMLSelectorArrayElementArrowDown: function (projectId, formId, fieldId, number) {
        'use strict';
        // parse arguments
        projectId = this._parseProjectId(projectId);
        formId = this._objHelper.initString(formId);
        fieldId = this._objHelper.initString(fieldId);
        number = this._objHelper.initNumeric(number);
        // exit
        return this.getHTMLSelectorArrayElementContainer(projectId, formId, fieldId, number) + ' div[data-type="controls"] a[data-type="down"]';
    },

    /**
     * Get HTML selector for array element up arrow
     *
     * @public
     * @param {string} projectId Project identifier
     * @param {string} formId Form identifier
     * @param {string} fieldId Field identifier
     * @param {number} number Number of element in array
     * @return {string} HTML selector for array element up arrow
     */
    getHTMLSelectorArrayElementArrowUp: function (projectId, formId, fieldId, number) {
        'use strict';
        // parse arguments
        projectId = this._parseProjectId(projectId);
        formId = this._objHelper.initString(formId);
        fieldId = this._objHelper.initString(fieldId);
        number = this._objHelper.initNumeric(number);
        // exit
        return this.getHTMLSelectorArrayElementContainer(projectId, formId, fieldId, number) + ' div[data-type="controls"] a[data-type="up"]';
    },

    /**
     * Get HTML selector for array element delete
     *
     * @public
     * @param {string} projectId Project identifier
     * @param {string} formId Form identifier
     * @param {string} fieldId Field identifier
     * @param {number} number Number of element in array
     * @return {string} HTML selector for array element delete
     */
    getHTMLSelectorArrayElementDelete: function (projectId, formId, fieldId, number) {
        'use strict';
        // parse arguments
        projectId = this._parseProjectId(projectId);
        formId = this._objHelper.initString(formId);
        fieldId = this._objHelper.initString(fieldId);
        number = this._objHelper.initNumeric(number);
        // exit
        return this.getHTMLSelectorArrayElementContainer(projectId, formId, fieldId, number) + ' div[data-type="controls"] a[data-type="delete"]';
    },

    /**
     * Process array elements
     *
     * @public
     * @param {string} projectId Project identifier
     * @param {string} formId Form identifier
     * @param {string} fieldId Field identifier
     * @param {string} emptyField Empty field
     * @param {number} elementsCount Array elements count
     * @param {Object} arraySettings Array settings
     * @return {void}
     */
    process: function (projectId, formId, fieldId, emptyField, elementsCount, arraySettings) {
        'use strict';
        // get this object
        var self = this;
        (function ($) {
            // parse parameters
            projectId = self._parseProjectId(projectId);
            formId = self._objHelper.initString(formId);
            fieldId = self._objHelper.initString(fieldId);
            emptyField = self._objHelper.initString(emptyField);
            elementsCount = self._objHelper.initNumeric(elementsCount);
            arraySettings = self._objHelper.initObject(arraySettings);
            // remember array of fields
            var id = self._prj['prj_' + projectId].arrays.length;
            self._prj['prj_' + projectId].arrays['arr_' + id] = {
                formId: formId,
                fieldId: fieldId,
                arraySettings: arraySettings,
                emptyField: emptyField,
                newNumber: elementsCount
            };
            self._objDataHelper.setDataInDom($(self.getHTMLSelectorArrayContainer(projectId, formId, fieldId)), 'array-id', id);
            // update controls in fields
            for (var z = 0; z < elementsCount; z++) {
                var prevNumber = '';
                if (z > 0) {
                    prevNumber = z - 1;
                }
                var nextNumber = '';
                if (z < elementsCount - 1) {
                    nextNumber = z + 1;
                }
                self._updateControls(projectId, id, z, true, prevNumber, true, nextNumber);
            }
            // optionally add empty field
            if (arraySettings.autoadddeleteifempty) {
                self._addNewEmptyField(projectId, formId, fieldId);
            }
            // optionally add button for adding a new field
            if (arraySettings.addnewbutton) {
                // get selector
                var selectorArrayContainer = $(self.getHTMLSelectorArrayContainer(projectId, formId, fieldId));
                // add button
                selectorArrayContainer.append('<div data-type="add-new-button"><input type="button" class="button" value="' + self._valsTextButtonAddNewElement + '" /></div>');
                // add button event
                selectorArrayContainer.find('div[data-type="add-new-button"] input').bind('click', {
                    self: self,
                    projectId: projectId,
                    formId: formId,
                    fieldId: fieldId
                }, function (event) {
                    // disable default event
                    event.preventDefault();
                    // add new empty field if there is none
                    if ($(event.data.self.getHTMLSelectorArrayEmptyElementContainer(event.data.projectId, event.data.formId, event.data.fieldId)).length === 0) {
                        event.data.self._addNewEmptyField(event.data.projectId, event.data.formId, event.data.fieldId);
                        $(this).insertAfter($(event.data.self.getHTMLSelectorArrayEmptyElementContainer(event.data.projectId, event.data.formId, event.data.fieldId)));
                    }
                });
            }
        }(jQuery));
    },

    /**
     * Get HTML array container name
     *
     * @private
     * @param {string} projectId Project identifier
     * @param {string} formId Form identifier
     * @param {string} fieldId Field identifier
     * @return {string} HTML array container name
     */
    _getHTMLNameArrayContainer: function (projectId, formId, fieldId) {
        'use strict';
        // exit
        return this._valsHtmlIdFormatArrayContainer.replace('####FORM_ID####', formId).replace('####FIELD_ID####', fieldId);
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
     * Update controls
     *
     * @private
     * @param {string} projectId Project identifier
     * @param {number} arrayId Identifier of array of fields
     * @param {number} number Number of element in array
     * @param {boolean} changePrevNumber Change previous number (true) or leave it as it is (false)
     * @param {number|string} prevNumber Previous number or empty string if there is none
     * @param {boolean} changeNextNumber Change next number (true) or leave it as it is (false)
     * @param {number|string} nextNumber Next number or empty string if there is none
     * @return {void}
     */
    _updateControls: function (projectId, arrayId, number, changePrevNumber, prevNumber, changeNextNumber, nextNumber) {
        'use strict';
        // get this object
        var self = this;
        (function ($) {
            // get array data
            var arrayData = self._prj['prj_' + projectId].arrays['arr_' + arrayId];
            // clear controls
            var selectorArrayElementArrowDown = $(self.getHTMLSelectorArrayElementArrowDown(projectId, arrayData.formId, arrayData.fieldId, number));
            var selectorArrayElementArrowUp = $(self.getHTMLSelectorArrayElementArrowUp(projectId, arrayData.formId, arrayData.fieldId, number));
            var selectorArrayElementDelete = $(self.getHTMLSelectorArrayElementDelete(projectId, arrayData.formId, arrayData.fieldId, number));
            if (selectorArrayElementArrowDown.length > 0) {
                selectorArrayElementArrowDown.remove();
            }
            if (selectorArrayElementArrowUp.length > 0) {
                selectorArrayElementArrowUp.remove();
            }
            if (selectorArrayElementDelete.length > 0) {
                selectorArrayElementDelete.remove();
            }
            // get array settings
            var arraySettings = arrayData.arraySettings;
            // get array element container selector
            var selectorArrayElementContainer = $(self.getHTMLSelectorArrayElementContainer(projectId, arrayData.formId, arrayData.fieldId, number));
            // get array element container controls selector
            var selectorArrayElementContainerControls = $(self.getHTMLSelectorArrayElementContainerControlsDiv(projectId, arrayData.formId, arrayData.fieldId, number));
            // add data attributes to array element container for controls
            self._objDataHelper.setDataInDom(selectorArrayElementContainer, 'number', number);
            if (changePrevNumber) {
                self._objDataHelper.setDataInDom(selectorArrayElementContainer, 'prev', prevNumber);
            }
            if (changeNextNumber) {
                self._objDataHelper.setDataInDom(selectorArrayElementContainer, 'next', nextNumber);
            }
            // prepare buttons link
            var buttonsLink = '<a href="#" data-type="####DATA_TYPE####"><img src="####IMAGE_URL####" alt="" style="float:left;border-width:0;border-style:none;" /></a>';
            // add down arrow
            var selector = '';
            var imageUrl = self._valsImageEmpty;
            if ((arraySettings.allowchangeorder) && (selectorArrayElementContainer.data('next') !== '')) {
                selector = self.getHTMLSelectorArrayElementArrowDown(projectId, arrayData.formId, arrayData.fieldId, number);
                imageUrl = self._valsImageArrowDown;
            }
            selectorArrayElementContainerControls.append(buttonsLink.replace('####DATA_TYPE####', 'down').replace('####IMAGE_URL####', imageUrl));
            if (selector !== '') {
                $(selector).bind('click.element', {
                    self: self,
                    projectId: projectId,
                    formId: arrayData.formId,
                    fieldId: arrayData.fieldId
                }, function (event) {
                    // disable default event
                    event.preventDefault();
                    // change HTML contents
                    event.data.self._changeFields(event.data.projectId, event.data.formId, event.data.fieldId, parseInt($(this).parent().parent().data('number'), 10), $(this).parent().parent().data('next'));
                });
            }
            // add up arrow
            selector = '';
            imageUrl = self._valsImageEmpty;
            if ((arraySettings.allowchangeorder) && (selectorArrayElementContainer.data('prev') !== '')) {
                selector = self.getHTMLSelectorArrayElementArrowUp(projectId, arrayData.formId, arrayData.fieldId, number);
                imageUrl = self._valsImageArrowUp;
            }
            selectorArrayElementContainerControls.append(buttonsLink.replace('####DATA_TYPE####', 'up').replace('####IMAGE_URL####', imageUrl));
            if (selector !== '') {
                $(selector).bind('click.element', {
                    self: self,
                    projectId: projectId,
                    formId: arrayData.formId,
                    fieldId: arrayData.fieldId
                }, function (event) {
                    // disable default event
                    event.preventDefault();
                    // change HTML contents
                    event.data.self._changeFields(event.data.projectId, event.data.formId, event.data.fieldId, $(this).parent().parent().data('prev'), parseInt($(this).parent().parent().data('number'), 10));
                });
            }
            // add delete button
            selector = '';
            imageUrl = self._valsImageEmpty;
            if (arraySettings.deletebutton) {
                selector = self.getHTMLSelectorArrayElementDelete(projectId, arrayData.formId, arrayData.fieldId, number);
                imageUrl = self._valsImageDelete;
            }
            selectorArrayElementContainerControls.append(buttonsLink.replace('####DATA_TYPE####', 'delete').replace('####IMAGE_URL####', imageUrl));
            if (selector !== '') {
                $(selector).bind('click.element', {
                    self: self,
                    projectId: projectId,
                    formId: arrayData.formId,
                    fieldId: arrayData.fieldId
                }, function (event) {
                    // disable default event
                    event.preventDefault();
                    // delete field
                    event.data.self._deleteField(event.data.projectId, event.data.formId, event.data.fieldId, parseInt($(this).parent().parent().data('number'), 10));
                });
            }
            // add automatic adding and deleting of elements
            if (arraySettings.autoadddeleteifempty) {
                // add automatic deleting of element
                $(self.getHTMLSelectorArrayElementContainerFieldValue(projectId, arrayData.formId, arrayData.fieldId, number)).bind('change.element', {
                    self: self,
                    projectId: projectId,
                    formId: arrayData.formId,
                    fieldId: arrayData.fieldId
                }, function (event) {
                    // disable default event
                    event.preventDefault();
                    // optionally delete field
                    if ($(this).val() === '') {
                        event.data.self._deleteField(event.data.projectId, event.data.formId, event.data.fieldId, parseInt($(this).parent().parent().data('number'), 10));
                    }
                });
            }
        }(jQuery));
    },

    /**
     * Change fields in array
     *
     * @private
     * @param {string} projectId Project identifier
     * @param {string} formId Form identifier
     * @param {string} fieldId Field identifier
     * @param {number} numberAbove First number of element in array
     * @param {number} numberBelow Second number of element in array
     * @return {void}
     */
    _changeFields: function (projectId, formId, fieldId, numberAbove, numberBelow) {
        'use strict';
        // get this object
        var self = this;
        (function ($) {
            // get array identifier
            var arrayId = $(self.getHTMLSelectorArrayContainer(projectId, formId, fieldId)).data('array-id');
            // get selectors
            var selectorArrayElementContainer1 = $(self.getHTMLSelectorArrayElementContainer(projectId, formId, fieldId, numberAbove));
            var selectorArrayElementContainer2 = $(self.getHTMLSelectorArrayElementContainer(projectId, formId, fieldId, numberBelow));
            // change positions
            selectorArrayElementContainer1.insertAfter(selectorArrayElementContainer2);
            // get previous and next numbers
            var prev1 = selectorArrayElementContainer1.data('prev');
            var prev2 = selectorArrayElementContainer2.data('prev');
            var next1 = selectorArrayElementContainer1.data('next');
            var next2 = selectorArrayElementContainer2.data('next');
            // update controls for previous array element
            if (prev1 !== '') {
                self._updateControls(projectId, arrayId, prev1, false, '', true, numberBelow);
            }
            // update controls for next array element
            if (next2 !== '') {
                self._updateControls(projectId, arrayId, next2, true, numberAbove, false, '');
            }
            // update controls
            self._updateControls(projectId, arrayId, numberAbove, true, numberBelow, true, next2);
            self._updateControls(projectId, arrayId, numberBelow, true, prev1, true, numberAbove);
        }(jQuery));
    },

    /**
     * Delete field in array
     *
     * @private
     * @param {string} projectId Project identifier
     * @param {string} formId Form identifier
     * @param {string} fieldId Field identifier
     * @param {number} number Number of element in array
     * @return {void}
     */
    _deleteField: function (projectId, formId, fieldId, number) {
        'use strict';
        // get this object
        var self = this;
        (function ($) {
            // get array element container selector
            var selectorArrayElementContainer = $(self.getHTMLSelectorArrayElementContainer(projectId, formId, fieldId, number));
            // get previous and next numbers
            var prevNumber = selectorArrayElementContainer.data('prev');
            var nextNumber = selectorArrayElementContainer.data('next');
            // get array identifier
            var arrayId = $(self.getHTMLSelectorArrayContainer(projectId, formId, fieldId)).data('array-id');
            // update previous element
            if (prevNumber !== '') {
                var selectorArrayElementContainerPrev = $(self.getHTMLSelectorArrayElementContainer(projectId, formId, fieldId, prevNumber));
                self._updateControls(projectId, arrayId, prevNumber, false, '', true, nextNumber);
            }
            // update next element
            if (nextNumber !== '') {
                var selectorArrayElementContainerNext = $(self.getHTMLSelectorArrayElementContainer(projectId, formId, fieldId, nextNumber));
                self._updateControls(projectId, arrayId, nextNumber, true, prevNumber, false, '');
            }
            // delete element
            selectorArrayElementContainer.remove();
        }(jQuery));
    },

    /**
     * Add empty field
     *
     * @private
     * @param {string} projectId Project identifier
     * @param {string} formId Form identifier
     * @param {string} fieldId Field identifier
     * @return {void}
     */
    _addNewEmptyField: function (projectId, formId, fieldId) {
        'use strict';
        // get this object
        var self = this;
        (function ($) {
            // get array container selector
            var selectorArrayContainer = $(self.getHTMLSelectorArrayContainer(projectId, formId, fieldId));
            // get array identifier
            var arrayId = $(self.getHTMLSelectorArrayContainer(projectId, formId, fieldId)).data('array-id');
            // get array data
            var arrayData = self._prj['prj_' + projectId].arrays['arr_' + arrayId];
            // add new empty field
            selectorArrayContainer.append(self._valsDivElementContainer.replace('####DATA_TYPE_ELEMENT_CONTAINER####', 'element-container').replace('####DATA_ATTRS####', 'data-number="" data-prev="na" data-next="na"') + self._valsDivField.replace('####DATA_TYPE_FIELD####', 'field') + arrayData.emptyField + '</div>' + self._valsDivControls.replace('####DATA_TYPE_CONTROLS####', 'controls') + '</div><div style="clear:both;"></div></div>');
            // get empty array element container
            var selectorEmptyArrayElementContainer = selectorArrayContainer.find('div[data-type="element-container"]:last');
            // update array element number
            self._objDataHelper.setDataInDom(selectorEmptyArrayElementContainer, 'number', arrayData.newNumber);
            self._prj['prj_' + projectId].arrays['arr_' + arrayId].newNumber = arrayData.newNumber + 1;
            // add adding controls to empty field after changing its value
            selectorEmptyArrayElementContainer.find('[data-type="field-value"]').bind('change.empty', {
                self: self,
                projectId: projectId,
                formId: formId,
                fieldId: fieldId,
                arrayId: arrayId,
                arraySettings: arrayData.arraySettings
            }, function (event) {
                // disable default event
                event.preventDefault();
                // optionally add new empty field
                if ($(this).val() !== '') {
                    // update last array element
                    var previous = '';
                    var selectorArrayElementContainer = selectorArrayContainer.find('div[data-type="element-container"][data-next=""]');
                    if (selectorArrayElementContainer.length > 0) {
                        previous = parseInt(selectorArrayElementContainer.data('number'), 10);
                        var number = parseInt($(this).parent().parent().data('number'), 10);
                        event.data.self._updateControls(event.data.projectId, event.data.arrayId, parseInt(selectorArrayElementContainer.data('number'), 10), false, '', true, number);
                    }
                    // update controls
                    event.data.self._updateControls(event.data.projectId, event.data.arrayId, parseInt($(this).parent().parent().data('number'), 10), true, previous, true, '');
                    // add new empty field
                    if (event.data.arraySettings.autoadddeleteifempty) {
                        event.data.self._addNewEmptyField(event.data.projectId, event.data.formId, event.data.fieldId);
                    }
                    // remove this event
                    $(this).unbind('change.empty');
                }
            });
        }(jQuery));
    }
};

// initialize
var kocujILV13aBackendSettingsFormArray = new kocujILV13aCBackendSettingsFormArray();
