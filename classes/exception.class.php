<?php

/**
 * exception.class.php
 *
 * @author Dominik Kocuj
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2 or later
 * @copyright Copyright (c) 2013-2020 kocuj.pl
 * @package kocuj_sitemap
 */

// set namespace
namespace KocujSitemapPlugin\Classes;

// set namespaces aliases
use KocujIL\V13a as KocujIL;
use KocujSitemapPlugin\Enums\ExceptionCode;

// security
if ((!defined('ABSPATH')) || ((isset($_SERVER['SCRIPT_FILENAME'])) && (basename($_SERVER['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die();
}

/**
 * Exception class
 *
 * @access public
 */
final class Exception extends KocujIL\Classes\Exception
{

    /**
     * Namespace prefix
     *
     * @access protected
     * @var string
     */
    protected $namespacePrefix = '';

    /**
     * Constructor
     *
     * @access public
     * @param int $code
     *            Error code
     * @param string $file
     *            Filename where there was an error; should be set to __FILE__ during throwing an exception
     * @param int $line
     *            Line number where there was an error; should be set to __LINE__ during throwing an exception
     * @param string $param
     *            Optional argument for error message - default: empty
     * @return void
     */
    public function __construct($code, $file, $line, $param = '')
    {
        // execute parent constructor
        parent::__construct(null, $code, $file, $line, $param);
    }

    /**
     * Set errors data
     *
     * @access protected
     * @return void
     */
    protected function setErrors()
    {
        // initialize errors
        $this->errors = array(
            ExceptionCode::OK => 'OK',
            ExceptionCode::CACHE_DIRECTORY_IS_NOT_WRITABLE => 'Cache directory is not writable',
            ExceptionCode::UNABLE_TO_WRITE_TO_CACHE_SECURITY_FILE => 'Unable to write to cache security file',
            ExceptionCode::UNABLE_TO_CLEAR_CACHE => 'Unable to clear the cache',
            ExceptionCode::UNABLE_TO_WRITE_TO_CACHE_INDEX_HTML_FILE => 'Unable to write to cache "index.html" file',
            ExceptionCode::UNABLE_TO_WRITE_TO_CACHE_FILE => 'Unable to write to cache file',
            ExceptionCode::UNABLE_TO_READ_FROM_CACHE_SECURITY_FILE => 'Unable to read from cache security file',
            ExceptionCode::UNABLE_TO_READ_FROM_CACHE_FILE => 'Unable to read from cache file',
            ExceptionCode::WRONG_DATA_IN_CACHE_SECURITY_FILE => 'Wrong data in cache security file'
        );
    }
}
