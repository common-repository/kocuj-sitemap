<?php

/**
 * review-message.class.php
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
 * \KocujIL\V13a\Classes\Project\Components\Backend\ReviewMessage classes strings
 *
 * @access public
 */
class ReviewMessage implements Strings
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
            /* translators: 1: plugin name, 2: days count from installation date of this plugin */
            'ACTION_ADMIN_HEAD_PLUGIN_DAYS' => __('Thank you for using plugin %1$s more than %2$d days.',
                'kocuj-sitemap'),
            'ACTION_ADMIN_HEAD_THINGS_SUPPORT_PLUGIN' => __('Please, consider to do some things that will support this plugin.',
                'kocuj-sitemap'),
            /* translators: 1: theme name, 2: days count from installation date of this theme */
            'ACTION_ADMIN_HEAD_THEME_DAYS' => __('Thank you for using theme %1$s more than %2$d days.',
                'kocuj-sitemap'),
            'ACTION_ADMIN_HEAD_THINGS_SUPPORT_THEME' => __('Please, consider to do some things that will support this theme.',
                'kocuj-sitemap'),
            'ACTION_ADMIN_HEAD_VOTE_PLUGIN' => __('Vote for this plugin', 'kocuj-sitemap'),
            'ACTION_ADMIN_HEAD_VOTE_THEME' => __('Vote for this theme', 'kocuj-sitemap'),
            'ACTION_ADMIN_HEAD_FACEBOOK' => __('Tell friends on Facebook', 'kocuj-sitemap'),
            'ACTION_ADMIN_HEAD_TWITTER' => __('Tell friends on Twitter', 'kocuj-sitemap'),
            'ACTION_ADMIN_HEAD_TRANSLATION_PLUGIN' => __('Help in translation of this plugin', 'kocuj-sitemap'),
            'ACTION_ADMIN_HEAD_TRANSLATION_THEME' => __('Help in translation of this theme', 'kocuj-sitemap'),
            'ACTION_ADMIN_HEAD_NEWS_CHANNELS_TEXT_PLUGIN' => __('News channels for this plugin', 'kocuj-sitemap'),
            'ACTION_ADMIN_HEAD_NEWS_CHANNELS_TEXT_THEME' => __('News channels for this theme', 'kocuj-sitemap')
        );
        // exit
        return (isset($texts[$id])) ? $texts[$id] : '';
    }
}
