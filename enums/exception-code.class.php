<?php

/**
 * exception-code.class.php
 *
 * @author Dominik Kocuj
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2 or later
 * @copyright Copyright (c) 2013-2020 kocuj.pl
 * @package kocuj_sitemap
 */

// set namespace
namespace KocujSitemapPlugin\Enums;

// security
if ((!defined('ABSPATH')) || ((isset($_SERVER['SCRIPT_FILENAME'])) && (basename($_SERVER['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die();
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
     * Error: Cache directory is not writable
     */
    const CACHE_DIRECTORY_IS_NOT_WRITABLE = 1;

    /**
     * Error: Unable to write to cache security file
     */
    const UNABLE_TO_WRITE_TO_CACHE_SECURITY_FILE = 2;

    /**
     * Error: Unable to clear the cache
     */
    const UNABLE_TO_CLEAR_CACHE = 3;

    /**
     * Error: Unable to write to cache "index.html" file
     */
    const UNABLE_TO_WRITE_TO_CACHE_INDEX_HTML_FILE = 4;

    /**
     * Error: Unable to write to cache file
     */
    const UNABLE_TO_WRITE_TO_CACHE_FILE = 5;

    /**
     * Error: Unable to read from cache security file
     */
    const UNABLE_TO_READ_FROM_CACHE_SECURITY_FILE = 6;

    /**
     * Error: Unable to read from cache file
     */
    const UNABLE_TO_READ_FROM_CACHE_FILE = 7;

    /**
     * Error: Wrong data in cache security file
     */
    const WRONG_DATA_IN_CACHE_SECURITY_FILE = 8;
}
