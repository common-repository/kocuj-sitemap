<?php

/**
 * strings.class.php
 *
 * @author Dominik Kocuj
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2 or later
 * @copyright Copyright (c) 2016-2020 kocuj.pl
 * @package kocuj_internal_lib
 */

// set namespace
namespace KocujIL\V13a\Interfaces;

// security
if ((!defined('ABSPATH')) || ((isset ($_SERVER ['SCRIPT_FILENAME'])) && (basename($_SERVER ['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die ();
}

/**
 * Interface for strings
 */
interface Strings
{

    /**
     * Get singleton instance
     *
     * @access public
     * @return object Singleton instance
     */
    public static function getInstance();

    /**
     * Get string
     *
     * @access public
     * @param string $id
     *            String id
     * @return string Output string
     */
    public function getString($id);
}
