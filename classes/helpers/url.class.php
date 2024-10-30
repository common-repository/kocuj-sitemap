<?php

/**
 * url.class.php
 *
 * @author Dominik Kocuj
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2 or later
 * @copyright Copyright (c) 2013-2020 kocuj.pl
 * @package kocuj_sitemap
 */

// set namespace
namespace KocujSitemapPlugin\Classes\Helpers;

// security
if ((!defined('ABSPATH')) || ((isset($_SERVER['SCRIPT_FILENAME'])) && (basename($_SERVER['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die();
}

/**
 * URL class
 *
 * @access public
 */
class Url
{

    /**
     * Singleton instance
     *
     * @access private
     * @var object
     */
    private static $instance = null;

    /**
     * Root URL without protocol
     *
     * @access private
     * @var string
     */
    private $rootUrlWithoutProtocol = '';

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
            self::$instance = new self();
        }
        // exit
        return self::$instance;
    }

    /**
     * Get this website root URL without protocol "http" or "https"
     *
     * @access public
     * @return string This website root URL without protocol
     */
    public function getRootUrlWithoutProtocol()
    {
        // get website URL
        if (!isset($this->rootUrlWithoutProtocol[0]) /* strlen($this->rootUrlWithoutProtocol) === 0 */) {
            $this->rootUrlWithoutProtocol = substr(site_url('', 'http'), 5) . '/';
        }
        // exit
        return $this->rootUrlWithoutProtocol;
    }

    /**
     * Remove protocol "http" or "https" from local URL
     *
     * @access public
     * @param string $url
     *            URL
     * @return string URL without protocol
     */
    public function removeProtocolLocal($url)
    {
        // get protocol
        $urlLower = strtolower($url);
        $protocol = '';
        if (substr($urlLower, 0, 7) === 'http://') {
            $protocol = 'http';
        } else {
            if (substr($urlLower, 0, 8) === 'https://') {
                $protocol = 'https';
            }
        }
        if (!isset($protocol[0]) /* strlen($protocol) === 0 */) {
            return $url;
        }
        // check root URL
        $rootUrl = site_url('', $protocol);
        if ($rootUrl === substr($url, 0, strlen($rootUrl))) {
            // remove protocol
            $url = substr($url, strlen($protocol) + 1);
        }
        // exit
        return $url;
    }
}
