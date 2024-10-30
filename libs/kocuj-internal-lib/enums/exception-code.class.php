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
namespace KocujIL\V13a\Enums;

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
     * Error: OK
     */
    const OK = 0;

    /**
     * Error: No required setting data
     */
    const NO_REQUIRED_SETTING_DATA = 1;

    /**
     * Error: No required library
     */
    const NO_REQUIRED_LIBRARY = 2;

    /**
     * Error: No required component
     */
    const NO_REQUIRED_COMPONENT = 3;

    /**
     * Error: Cannot use components after shutdown
     */
    const CANNOT_USE_COMPONENTS_AFTER_SHUTDOWN = 4;
}
