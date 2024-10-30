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
namespace KocujIL\V13a\Enums\Project\Components\Backend\SettingsForm;

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
     * Error: Controller identifier does not exist
     */
    const CONTROLLER_ID_DOES_NOT_EXIST = 1;

    /**
     * Error: Elements list controller identifier does not exist
     */
    const LIST_CONTROLLER_ID_DOES_NOT_EXIST = 2;

    /**
     * Error: Form identifier already exists
     */
    const FORM_ID_EXISTS = 3;

    /**
     * Error: Form identifier does not exist
     */
    const FORM_ID_DOES_NOT_EXIST = 4;

    /**
     * Error: Tab identifier already exists
     */
    const TAB_ID_EXISTS = 5;

    /**
     * Error: Tab identifier does not exist
     */
    const TAB_ID_DOES_NOT_EXIST = 6;

    /**
     * Error: Cannot use an array option in widget settings
     */
    const CANNOT_USE_ARRAY_OPTION_IN_WIDGET = 7;

    /**
     * Error: Wrong tabs count for widget settings
     */
    const WRONG_TABS_COUNT_IN_WIDGET = 8;
}
