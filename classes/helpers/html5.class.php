<?php

/**
 * html5.class.php
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
 * HTML5 class
 *
 * @access public
 */
class Html5
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
            self::$instance = new self();
        }
        // exit
        return self::$instance;
    }

    /**
     * Get HTML tag begin
     *
     * @access public
     * @param string $tag
     *            HTML tag name
     * @param string $id
     *            HTML tag id - default: empty
     * @param string $class
     *            HTML tag class - default: empty
     * @param string $additional
     *            Additional HTML tag parameters - default: empty
     * @return string HTML tag
     */
    public function getTagBegin($tag, $id = '', $class = '', $additional = '')
    {
        // get attributes
        $attr = '';
        if (isset($id[0]) /* strlen($id) > 0 */) {
            $attr .= ' id="' . esc_attr($id) . '"';
        }
        if (isset($additional[0]) /* strlen($additional) > 0 */) {
            $attr .= ' ' . $additional;
        }
        if (isset($class[0]) /* strlen($class) > 0 */) {
            $attr .= ' class="' . esc_attr($class) . '"';
        }
        // exit
        return '<!--[if lt IE 9]><div' . $attr . '><![endif]--><!--[if gte IE 9]><!--><' . $tag . $attr . '><!--<![endif]-->';
    }

    /**
     * Get HTML tag end
     *
     * @access public
     * @param string $tag
     *            HTML tag name
     * @return string HTML tag
     */
    public function getTagEnd($tag)
    {
        // exit
        return '<!--[if lt IE 9]></div><![endif]--><!--[if gte IE 9]><!--></' . $tag . '><!--<![endif]-->';
    }
}
