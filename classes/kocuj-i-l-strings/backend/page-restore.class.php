<?php

/**
 * page-restore.class.php
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
 * \KocujIL\V13a\Classes\Project\Components\Backend\PageRestore classes strings
 *
 * @access public
 */
class PageRestore implements Strings
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
            'SET_OPTIONS_CONTAINER_ID_RESTORE_LABEL' => __('Restore default settings', 'kocuj-sitemap'),
            'SET_OPTIONS_CONTAINER_ID_RESTORE_TOOLTIP' => __('Restore default settings', 'kocuj-sitemap'),
            /* translators: %s: Name of this plugin */
            'SHOW_PAGE_TEXT_PLUGIN_1' => __('Sometimes there is a need to return to the settings that were set up immediately after installing the %s plugin. These settings are known as `factory settings` or `default settings`. %s plugin gives you possibility to restore these settings.',
                'kocuj-sitemap'),
            /* translators: %s: Name of this theme */
            'SHOW_PAGE_TEXT_THEME_1' => __('Sometimes there is a need to return to the settings that were set up immediately after installing the %s theme. These settings are known as `factory settings` or `default settings`. %s theme gives you possibility to restore these settings.',
                'kocuj-sitemap'),
            'SHOW_PAGE_TEXT_2' => __('To restore default settings, click on the button `restore default settings`. When a window will appear asking you to confirm this action, click on the `OK` button. Note that this operation can not be undone.',
                'kocuj-sitemap')
        );
        // exit
        return (isset($texts[$id])) ? $texts[$id] : '';
    }
}
