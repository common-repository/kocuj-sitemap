<?php

/**
 * closable.class.php
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
 * Messages closable status constants class
 *
 * @access public
 */
final class Closable
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
     * Message is not closable
     */
    const NOT_CLOSABLE = 0;

    /**
     * Message is closable
     */
    const CLOSABLE = 1;

    /**
     * Message is closable temporary
     */
    const CLOSABLE_TEMPORARY = 2;
}
