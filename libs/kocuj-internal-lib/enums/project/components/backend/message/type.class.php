<?php

/**
 * type.class.php
 *
 * @author Dominik Kocuj
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2 or later
 * @copyright Copyright (c) 2016-2020 kocuj.pl
 * @package kocuj_internal_lib
 */

// set namespace
namespace KocujIL\V13a\Enums\Project\Components\Backend\Message;

// security
if ((!defined('ABSPATH')) || ((isset ($_SERVER ['SCRIPT_FILENAME'])) && (basename($_SERVER ['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die ();
}

/**
 * Messages types constants class
 *
 * @access public
 */
final class Type
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
     * It is information message
     */
    const INFORMATION = 0;

    /**
     * It is warning message
     */
    const WARNING = 1;

    /**
     * It is error message
     */
    const ERROR = 2;

    /**
     * It is success message
     */
    const SUCCESS = 3;
}
