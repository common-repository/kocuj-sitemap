<?php

/**
 * lib-urls.class.php
 *
 * @author Dominik Kocuj
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2 or later
 * @copyright Copyright (c) 2016-2020 kocuj.pl
 * @package kocuj_internal_lib
 */

// set namespace
namespace KocujIL\V13a\Classes;

// security
if ((!defined('ABSPATH')) || ((isset ($_SERVER ['SCRIPT_FILENAME'])) && (basename($_SERVER ['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die ();
}

/**
 * Library URL-s class
 *
 * @access public
 */
class LibUrls
{

    /**
     * Singleton instance
     *
     * @access private
     * @var object
     */
    private static $instance = null;

    /**
     * Kocuj Internal Lib main directory
     *
     * @access protected
     * @var string
     */
    protected $mainDir = '';

    /**
     * Kocuj Internal Lib main URL
     *
     * @access private
     * @var string
     */
    private $mainUrl;

    /**
     * Kocuj Internal Lib URL-s
     *
     * @access private
     * @var array
     */
    private $urls;

    /**
     * Constructor
     *
     * @access protected
     * @return void
     */
    protected function __construct()
    {
        // set URL-s
        if (!isset ($this->mainDir [0]) /* strlen($this->mainDir) === 0 */) {
            $this->mainDir = realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR);
        }
        $path = ABSPATH;
        if ($path [strlen($path) - 1] === DIRECTORY_SEPARATOR) {
            $path = substr($path, 0, -1);
        }
        $this->mainUrl = site_url() . '/' . substr(str_replace(DIRECTORY_SEPARATOR, '/', $this->mainDir),
                strlen($path));
        $this->urls = array(
            'css',
            'images',
            'js',
            'vendors'
        );
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
     * Get Kocuj Internal Lib main URL
     *
     * @access public
     * @return string Kocuj Internal Lib main URL
     */
    public function getMain()
    {
        // get Kocuj Internal Lib main URL
        return $this->mainUrl;
    }

    /**
     * Get Kocuj Internal Lib URL with the selected type
     *
     * @access public
     * @param string $type
     *            Type
     * @return string Kocuj Internal Lib URL with the selected type
     */
    public function get($type)
    {
        // exit
        return (in_array($type, $this->urls)) ? $this->mainUrl . '/' . $type : '';
    }
}
