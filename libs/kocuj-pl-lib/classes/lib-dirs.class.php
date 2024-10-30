<?php

/**
 * lib-dirs.class.php
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

// security
if ((!defined('ABSPATH')) || ((isset ($_SERVER ['SCRIPT_FILENAME'])) && (basename($_SERVER ['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die ();
}

/**
 * Library directories class
 *
 * @access public
 */
final class LibDirs extends KocujIL\Classes\LibDirs
{

    /**
     * Singleton instance
     *
     * @access private
     * @var object
     */
    private static $instance = null;

    /**
     * Constructor
     *
     * @access protected
     * @return void
     */
    protected function __construct()
    {
        // set library directories
        $this->mainDir = realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR);
        // execute parent constructor
        parent::__construct();
    }

    /**
     * Disable cloning of object
     *
     * @access private
     * @return void
     */
    private function __clone()
    {
    }

    /**
     * Get singleton instance
     *
     * @access public
     * @return object Singleton instance
     */
    public static function getInstance()
    {
        // optionally create new instance
        if (!self::$instance) {
            self::$instance = new self ();
        }
        // exit
        return self::$instance;
    }
}
