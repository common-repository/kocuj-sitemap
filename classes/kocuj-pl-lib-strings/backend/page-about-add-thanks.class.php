<?php

/**
 * page-about-add-thanks.class.php
 *
 * @author Dominik Kocuj
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2 or later
 * @copyright Copyright (c) 2013-2020 kocuj.pl
 * @package kocuj_sitemap
 */

// set namespace
namespace KocujSitemapPlugin\Classes\KocujPlLibStrings\Backend;

// set namespaces aliases
use KocujIL\V13a\Interfaces\Strings;

// security
if ((!defined('ABSPATH')) || ((isset($_SERVER['SCRIPT_FILENAME'])) && (basename($_SERVER['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die();
}

/**
 * \KocujPlLib\V13a\Classes\Project\Components\Backend\PageAboutAddThanks classes strings
 *
 * @access public
 */
class PageAboutAddThanks implements Strings
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
            'ACTION_AFTER_PAGE_ABOUT_TEXT_PLUGIN' => __('If you want to appreciate the plugin\'s author work, please send an information about your website address to him.',
                'kocuj-sitemap'),
            'ACTION_AFTER_PAGE_ABOUT_TEXT_THEME' => __('If you want to appreciate the theme\'s author work, please send an information about your website address to him.',
                'kocuj-sitemap'),
            /* translators: 1: begin of HTML anchor ("<a href=..."), 2: end of HTML anchor ("</a>") */
            'ACTION_AFTER_PAGE_ABOUT_TEXT' => __('Click on the button below to do this. Click %1$shere%2$s to see more information about this procedure.',
                'kocuj-sitemap'),
            'ACTION_AFTER_PAGE_ABOUT_ADD_THANKS' => __('Send your website address to author', 'kocuj-sitemap')
        );
        // exit
        return (isset($texts[$id])) ? $texts[$id] : '';
    }
}
