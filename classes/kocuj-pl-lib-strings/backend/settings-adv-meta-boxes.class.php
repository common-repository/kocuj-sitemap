<?php

/**
 * settings-adv-meta-boxes.class.php
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
 * \KocujPlLib\V13a\Classes\Project\Components\Backend\SettingsAdvMetaBoxes classes strings
 *
 * @access public
 */
class SettingsAdvMetaBoxes implements Strings
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
            'ACTION_ADMIN_INIT_META_BOX_AUTHOR_LINKS_TITLE_PLUGIN' => __('Links to websites of author of this plugin',
                'kocuj-sitemap'),
            'ACTION_ADMIN_INIT_META_BOX_AUTHOR_LINKS_TITLE_THEME' => __('Links to websites of author of this theme',
                'kocuj-sitemap'),
            'ACTION_ADMIN_INIT_META_BOX_AUTHOR_LINKS_TEXT_1_PLUGIN' => __('You can find Dominik Kocuj (author of this plugin) and his projects in the following places:',
                'kocuj-sitemap'),
            'ACTION_ADMIN_INIT_META_BOX_AUTHOR_LINKS_TEXT_1_THEME' => __('You can find Dominik Kocuj (author of this theme) and his projects in the following places:',
                'kocuj-sitemap'),
            'ACTION_ADMIN_INIT_META_BOX_AUTHOR_LINKS_WEBSITE_1' => __('website about Dominik Kocuj and his projects (only in Polish language)',
                'kocuj-sitemap'),
            'ACTION_ADMIN_INIT_META_BOX_AUTHOR_LINKS_WEBSITE_2' => __('website with PHP libraries made by Dominik Kocuj',
                'kocuj-sitemap'),
            'ACTION_ADMIN_INIT_META_BOX_AUTHOR_LINKS_WEBSITE_3' => __('GitHub account with some projects made by Dominik Kocuj',
                'kocuj-sitemap'),
            'ACTION_ADMIN_INIT_META_BOX_AUTHOR_PORTFOLIO_WEBSITE_1' => __('Gardenia Atelier', 'kocuj-sitemap'),
            'ACTION_ADMIN_INIT_META_BOX_AUTHOR_PORTFOLIO_WEBSITE_2' => __('Star Trek: Horizon', 'kocuj-sitemap'),
            'ACTION_ADMIN_INIT_META_BOX_AUTHOR_PORTFOLIO_WEBSITE_3' => __('The Light-Life Movement in Krakow province of Capuchins',
                'kocuj-sitemap'),
            'ACTION_ADMIN_INIT_META_BOX_AUTHOR_PORTFOLIO_TITLE_PLUGIN' => __('Portfolio fragment of this plugin\'s author',
                'kocuj-sitemap'),
            'ACTION_ADMIN_INIT_META_BOX_AUTHOR_PORTFOLIO_TITLE_THEME ' => __('Portfolio fragment of this theme\'s author',
                'kocuj-sitemap'),
            'ACTION_ADMIN_INIT_META_BOX_AUTHOR_PORTFOLIO_LANGUAGES' => __('languages', 'kocuj-sitemap'),
            'ACTION_ADMIN_INIT_META_BOX_AUTHOR_PORTFOLIO_SEE_DEMO' => __('see demo of the website', 'kocuj-sitemap'),
            'ACTION_ADMIN_INIT_META_BOX_AUTHOR_PORTFOLIO_MORE' => __('click here to see the entire portfolio',
                'kocuj-sitemap'),
            'ACTION_ADMIN_INIT_META_BOX_AUTHOR_TITLE' => __('Order your own unique website', 'kocuj-sitemap'),
            /* translators: %s: Link (HTML anchor) to website */
            'ACTION_ADMIN_INIT_META_BOX_AUTHOR_TEXT_1' => __('Visit %s website for more information about ordering the website creation.',
                'kocuj-sitemap'),
            'ACTION_ADMIN_INIT_META_BOX_AUTHOR_TEXT_2' => __('About Dominik Kocuj - the author of this plugin and owner of %s website',
                'kocuj-sitemap'),
            'ACTION_ADMIN_INIT_META_BOX_AUTHOR_TEXT_3' => __('over 9 years of documented work as a programmer of websites and web applications, and almost 19 years of making programs in various technologies for personal use',
                'kocuj-sitemap'),
            'ACTION_ADMIN_INIT_META_BOX_AUTHOR_TEXT_4' => __('knowledge of structural programming, object-oriented programming, design patterns and modelling by Domain-Driven Design',
                'kocuj-sitemap'),
            'ACTION_ADMIN_INIT_META_BOX_AUTHOR_TEXT_5' => __('experience in self-employed and full-time jobs in private and public sectors',
                'kocuj-sitemap'),
            'ACTION_ADMIN_INIT_META_BOX_AUTHOR_TEXT_6' => __('extensive experience in developing plugins and websites based on WordPress',
                'kocuj-sitemap'),
            'ACTION_ADMIN_INIT_META_BOX_AUTHOR_TEXT_7' => __('many years of experience as a network and server administrator which allows to have a great attention to security of websites',
                'kocuj-sitemap'),
            'ACTION_ADMIN_INIT_META_BOX_AUTHOR_TEXT_8' => __('experience in co-creating small and large internet projects for large companies and organizations such as: Polkomtel (Plus GSM), Jagiellonian University, Grand Parade, Betfair, Ladbrokes',
                'kocuj-sitemap')
        );
        // exit
        return (isset($texts[$id])) ? $texts[$id] : '';
    }
}
