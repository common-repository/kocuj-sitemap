/**
 * @file Data helper methods
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

/* global jQuery */

/* global kocujILV13aHelper */

/**
 * Data helper prototype constructor
 *
 * @constructs
 * @namespace kocujILV13aCDataHelper
 * @public
 * @return {void}
 */
function kocujILV13aCDataHelper() {
    'use strict';
    /* jshint validthis: true */
    // get this object
    var self = this;
    // initialize objects
    self._objHelper = kocujILV13aHelper;
}

/**
 * Data helper prototype
 *
 * @namespace kocujILV13aCDataHelper
 * @public
 */
kocujILV13aCDataHelper.prototype = {
    /**
     * Object kocujILV13aHelper
     *
     * @private
     * @type {Object}
     */
    _objHelper: null,

    /**
     * Set HTML data in DOM
     *
     * @public
     * @param {Object} element HTML element in jQuery format
     * @param {string} dataId Data identifier
     * @param {string|numeric} value Data value
     * @return {void}
     */
    setDataInDom: function (element, dataId, value) {
        'use strict';
        // get this object
        var self = this;
        (function ($) {
            // parse arguments
            element = self._objHelper.initObject(element);
            dataId = self._objHelper.initString(dataId);
            value = self._objHelper.initString(value);
            // set HTML data
            element.attr('data-' + dataId, value);
            element.data(dataId, value);
        }(jQuery));
    }
};

// initialize
var kocujILV13aDataHelper = new kocujILV13aCDataHelper();
