<?php

/**
 * exception-code.class.php
 *
 * @author Dominik Kocuj
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2 or later
 * @copyright Copyright (c) 2016-2020 kocuj.pl
 * @package kocuj_internal_lib
 */

// set namespace
namespace KocujIL\V13a\Enums\Project\Components\All\Options;

// security
if ((!defined('ABSPATH')) || ((isset ($_SERVER ['SCRIPT_FILENAME'])) && (basename($_SERVER ['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die ();
}

/**
 * Exceptions codes constants class
 *
 * @access public
 */
final class ExceptionCode
{

    /**
     * Empty constructor for blocking of creating an instance of this class
     *
     * @access private
     * @var void
     */
    private function __construct()
    {
    }

    /**
     * Error: Options type identifier already exists
     */
    const TYPE_ID_EXISTS = 1;

    /**
     * Error: Options type identifier does not exist
     */
    const TYPE_ID_DOES_NOT_EXIST = 2;

    /**
     * Error: Options container identifier already exists
     */
    const CONTAINER_ID_EXISTS = 3;

    /**
     * Error: Options container identifier does not exist
     */
    const CONTAINER_ID_DOES_NOT_EXIST = 4;

    /**
     * Error: Option definition identifier already exists
     */
    const DEFINITION_ID_EXISTS = 5;

    /**
     * Error: Option definition identifier does not exist
     */
    const DEFINITION_ID_DOES_NOT_EXIST = 6;

    /**
     * Error: Wrong container type for use with this method
     */
    const WRONG_CONTAINER_TYPE_FOR_THIS_METHOD = 7;

    /**
     * Error: Cannot use an array option in widget settings
     */
    const CANNOT_USE_ARRAY_OPTION_IN_WIDGET = 8;

    /**
     * Error: Cannot use option as search key
     */
    const CANNOT_USE_OPTION_AS_SEARCH_KEY = 9;
}
