/**
 * @file Exceptions handler errors codes
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
 * Exception handler errors codes
 *
 * @namespace kocujILV13aExceptionCode
 * @public
 */
var kocujILV13aExceptionCode = {
    /**
     * Error: OK
     *
     * @public
     * @const {number}
     */
    OK: 0,

    /**
     * Error: Empty project identifier
     *
     * @public
     * @const {number}
     */
    EMPTY_PROJECT_ID: 1,

    /**
     * Error: Project does not exist
     *
     * @public
     * @const {number}
     */
    PROJECT_DOES_NOT_EXIST: 2,

    /**
     * Error: Project already exists
     *
     * @public
     * @const {number}
     */
    PROJECT_ALREADY_EXISTS: 3,

    /**
     * Error: Empty element path
     *
     * @public
     * @const {number}
     */
    EMPTY_ELEMENT_PATH: 4,

    /**
     * Error: Element does not exist
     *
     * @public
     * @const {number}
     */
    ELEMENT_DOES_NOT_EXIST: 5,

    /**
     * Error: Empty URL for AJAX script
     *
     * @public
     * @const {number}
     */
    JS_AJAX_EMPTY_URL: 6,

    /**
     * Error: Empty method for AJAX script
     *
     * @public
     * @const {number}
     */
    JS_AJAX_EMPTY_METHOD: 7,

    /**
     * Error: Empty data type for AJAX script
     *
     * @public
     * @const {number}
     */
    JS_AJAX_EMPTY_DATA_TYPE: 8,

    /**
     * Error: Wrong window type
     *
     * @public
     * @const {number}
     */
    WINDOW_WRONG_TYPE: 9,

    /**
     * Error: Wrong window attributes
     *
     * @public
     * @const {number}
     */
    WINDOW_WRONG_ATTRIBUTES: 10
};
