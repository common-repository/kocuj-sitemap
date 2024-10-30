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
 * @namespace kocujPLV13aExceptionCode
 * @public
 */
var kocujPLV13aExceptionCode = {
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
     * Error: Empty window function for add thanks script
     *
     * @public
     * @const {number}
     */
    ADD_THANKS_EMPTY_WINDOW_FUNCTION: 4,

    /**
     * Error: Empty API URL for add thanks script
     *
     * @public
     * @const {number}
     */
    ADD_THANKS_EMPTY_API_URL: 5,

    /**
     * Error: Empty API login for add thanks script
     *
     * @public
     * @const {number}
     */
    ADD_THANKS_EMPTY_API_LOGIN: 6
};
