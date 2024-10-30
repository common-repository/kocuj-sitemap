<?php

/**
 * page-about.class.php
 *
 * @author Dominik Kocuj
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2 or later
 * @copyright Copyright (c) 2013-2020 kocuj.pl
 * @package kocuj_sitemap
 */

// set namespace
namespace KocujSitemapPlugin\Classes\KocujILStrings\Backend;

// set namespaces aliases
use KocujIL\V13a\Interfaces\Strings;

// security
if ((!defined('ABSPATH')) || ((isset($_SERVER['SCRIPT_FILENAME'])) && (basename($_SERVER['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die();
}

/**
 * \KocujIL\V13a\Classes\Project\Components\Backend\PageAbout classes strings
 *
 * @access public
 */
class PageAbout implements Strings
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
     * Get string
     *
     * @access public
     * @param string $id
     *            String id
     * @return string Output string
     */
    public function getString($id)
    {
        // get string
        $texts = array(
            'SHOW_PAGE_AUTHOR' => __('Author', 'kocuj-sitemap'),
            'SHOW_PAGE_LICENSE' => __('License', 'kocuj-sitemap'),
            /* translators: 1: link (HTML anchor) to first website, 2: link (HTML anchor) to license for resources from first website, 3: link (HTML anchor) to second website */
            'SHOW_PAGE_LICENSE_ICONS' => __('Some icons are from %1$s website (%2$s license) and from %3$s website.',
                'kocuj-sitemap'),
            'SHOW_PAGE_REPOSITORY' => __('Repository on wordpress.org', 'kocuj-sitemap'),
            'SHOW_PAGE_SUPPORT' => __('Support', 'kocuj-sitemap'),
            'SHOW_PAGE_TRANSLATION_PLUGIN' => __('You can help in translating this plugin here', 'kocuj-sitemap'),
            'SHOW_PAGE_TRANSLATION_THEME' => __('You can help in translating this theme here', 'kocuj-sitemap'),
            'SHOW_PAGE_TELL_OTHERS_PLUGIN' => __('Tell others about this plugin', 'kocuj-sitemap'),
            'SHOW_PAGE_TELL_OTHERS_THEME' => __('Tell others about this theme', 'kocuj-sitemap')
        );
        // exit
        return (isset($texts[$id])) ? $texts[$id] : '';
    }
}
