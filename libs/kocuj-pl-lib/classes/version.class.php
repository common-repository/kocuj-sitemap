<?php

/**
 * version.class.php
 *
 * @author Dominik Kocuj
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2 or later
 * @copyright Copyright (c) 2016-2020 kocuj.pl
 * @package kocuj_internal_lib\kocuj_pl_lib
 */

// set namespace
namespace KocujPlLib\V13a\Classes;

// security
if ((!defined('ABSPATH')) || ((isset ($_SERVER ['SCRIPT_FILENAME'])) && (basename($_SERVER ['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die ();
}

/**
 * Version class
 *
 * @access public
 */
final class Version
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
     * @access private
     * @return void
     */
    private function __construct()
    {
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

    /**
     * Get version major number
     *
     * @access public
     * @return int Version major number
     */
    public function getVersionMajor()
    {
        // exit
        return 0;
    }

    /**
     * Get version minor number
     *
     * @access public
     * @return int Version minor number
     */
    public function getVersionMinor()
    {
        // exit
        return 0;
    }

    /**
     * Get version revision number
     *
     * @access public
     * @return int Version revision number
     */
    public function getVersionRevision()
    {
        // exit
        return 13;
    }

    /**
     * Get version number
     *
     * @access public
     * @return string Version number
     */
    public function getVersion()
    {
        // exit
        return $this->getVersionMajor() . '.' . $this->getVersionMinor() . '.' . $this->getVersionRevision();
    }

    /**
     * Get version internal number used by some names
     *
     * @access public
     * @return string Version internal number
     */
    public function getVersionInternal()
    {
        // exit
        return 'v13a';
    }
}
