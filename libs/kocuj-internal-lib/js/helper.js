/**
 * @file Helper methods
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

/**
 * Helper prototype constructor
 *
 * @constructs
 * @namespace kocujILV13aCHelper
 * @public
 * @return {void}
 */
function kocujILV13aCHelper() {
}

/**
 * Helper prototype
 *
 * @namespace kocujILV13aCHelper
 * @public
 */
kocujILV13aCHelper.prototype = {
    /**
     * Initialize numeric integer value
     *
     * @public
     * @param {(number|string)} value Value to initialize
     * @return {(number|string)} Initialized value or empty string if there was an error
     */
    initNumeric: function (value) {
        'use strict';
        // initialize numeric value
        if (value !== undefined) {
            value = parseInt(value, 10);
            if (isNaN(value)) {
                return '';
            }
        } else {
            return '';
        }
        // exit
        return value;
    },

    /**
     * Initialize numeric float value
     *
     * @public
     * @param {(number|string)} value Value to initialize
     * @return {(number|string)} Initialized value or empty string if there was an error
     */
    initNumericFloat: function (value) {
        'use strict';
        // initialize numeric value
        if (value !== undefined) {
            value = parseFloat(value);
            if (isNaN(value)) {
                return '';
            }
        } else {
            return '';
        }
        // exit
        return value;
    },

    /**
     * Initialize string value
     *
     * @public
     * @param {string} value Value to initialize
     * @return {string} Initialized value or empty string if there was an error
     */
    initString: function (value) {
        'use strict';
        // initialize string value
        if (typeof value !== 'string') {
            if (typeof value === 'number') {
                return value.toString();
            } else {
                return '';
            }
        }
        // exit
        return value;
    },

    /**
     * Initialize boolean value
     *
     * @public
     * @param {(boolean|number|string)} value Value to initialize; 1 means true and 0 means false
     * @return {boolean} Initialized value or empty string if there was an error
     */
    initBoolean: function (value) {
        'use strict';
        // initialize boolean value
        if (typeof value !== 'boolean') {
            if (typeof value === 'number') {
                if ((value !== 0) && (value !== 1)) {
                    return false;
                }
                value = (value === 1);
            } else {
                return false;
            }
        }
        // exit
        return value;
    },

    /**
     * Initialize function value
     *
     * @public
     * @param {function} value Value to initialize
     * @return {(boolean|function)} Initialized value or false if there was an error
     */
    initFunction: function (value) {
        'use strict';
        // initialize function value
        if ((typeof value !== 'function') && (typeof value !== 'object')) {
            return false;
        }
        // exit
        return value;
    },

    /**
     * Initialize object value
     *
     * @public
     * @param {Object} value Value to initialize
     * @return {Object} Initialized value or empty string if there was an error
     */
    initObject: function (value) {
        'use strict';
        // initialize function value
        if (typeof value !== 'object') {
            return '';
        }
        // exit
        return value;
    },

    /**
     * Initialize any value
     *
     * @public
     * @param {*} value Value to initialize
     * @return {*} Initialized value or empty string if there was an error
     */
    initAny: function (value) {
        'use strict';
        // initialize any value
        if (value === undefined) {
            return '';
        }
        // exit
        return value;
    }
};

// initialize
var kocujILV13aHelper = new kocujILV13aCHelper();
