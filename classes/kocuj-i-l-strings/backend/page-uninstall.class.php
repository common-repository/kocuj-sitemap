<?php

/**
 * page-uninstall.class.php
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
 * \KocujIL\V13a\Classes\Project\Components\Backend\PageUninstall classes strings
 *
 * @access public
 */
class PageUninstall implements Strings
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
            /* translators: %s: Name of this plugin */
            'SET_OPTION_CHECKBOX_LABEL' => __('Remove data for the %s plugin during its uninstallation',
                'kocuj-sitemap'),
            'SET_OPTION_UNINSTALL_LABEL' => __('Save settings', 'kocuj-sitemap'),
            'SET_OPTION_UNINSTALL_TOOLTIP' => __('Save current settings', 'kocuj-sitemap'),
            /* translators: %s: Name of this plugin */
            'SHOW_PAGE_TEXT' => __('Sometimes there is a need to uninstall the %s plugin. When it will happen, all settings will be lost forever. In this place you can set that settings should not be removed during the uninstallation. It is helpful if you want to install this plugin again later. You can disable removing the plugin settings by unchecking the checkbox and clicking on the button `save settings`.',
                'kocuj-sitemap'),
            /* translators: %s: Name of this plugin */
            'SHOW_PAGE_CHECKBOX_TOOLTIP' => __('If this option is checked, the %s plugin data will be removed during the uninstallation.',
                'kocuj-sitemap')
        );
        // exit
        return (isset($texts[$id])) ? $texts[$id] : '';
    }
}
