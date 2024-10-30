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
namespace KocujIL\V13a\Enums\Project\Components\Backend\SettingsFields;

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
     * Error: Field type identifier already exists
     */
    const TYPE_ID_EXISTS = 1;

    /**
     * Error: Field type identifier does not exist
     */
    const TYPE_ID_DOES_NOT_EXIST = 2;

    /**
     * Error: Field type has not been declared for current site
     */
    const TYPE_ID_NOT_DECLARED_FOR_CURRENT_SITE = 3;

    /**
     * Error: Cannot use this field type in widget settings
     */
    const CANNOT_USE_TYPE_ID_IN_WIDGET = 4;

    /**
     * Error: Cannot use an array option in widget settings
     */
    const CANNOT_USE_ARRAY_OPTION_IN_WIDGET = 5;

    /**
     * Error: No widget object
     */
    const NO_WIDGET_OBJECT = 6;

    /**
     * Error: Wrong action for method
     */
    const WRONG_ACTION_FOR_METHOD = 7;
}