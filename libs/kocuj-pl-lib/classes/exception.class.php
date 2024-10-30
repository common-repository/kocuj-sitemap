<?php

/**
 * exception.class.php
 *
 * @author Dominik Kocuj
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2 or later
 * @copyright Copyright (c) 2016-2020 kocuj.pl
 * @package kocuj_internal_lib\kocuj_pl_lib
 */

// set namespace
namespace KocujPlLib\V13a\Classes;

// set namespaces aliases
use KocujIL\V13a as KocujIL;
use KocujPlLib\V13a\Enums\ExceptionCode;

// security
if ((!defined('ABSPATH')) || ((isset ($_SERVER ['SCRIPT_FILENAME'])) && (basename($_SERVER ['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die ();
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
    protected $namespacePrefix = 'KocujPlLib\\V13a';

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
            ExceptionCode::OBJECT_IS_NOT_KOCUJ_INTERNAL_LIB_PROJECT => 'Object is not Kocuj Internal Lib project',
            ExceptionCode::NO_REQUIRED_SETTING_DATA => 'No required setting data'
        );
    }
}
