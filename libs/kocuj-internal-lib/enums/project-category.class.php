<?php

/**
 * project-category.class.php
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
 * Projects categories constants class
 *
 * @access public
 */
final class ProjectCategory
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
     * It is "core" category
     */
    const CORE = 0;

    /**
     * It is "all" category
     */
    const ALL = 1;

    /**
     * It is "frontend" category
     */
    const FRONTEND = 2;

    /**
     * It is "backend" category
     */
    const BACKEND = 3;
}
