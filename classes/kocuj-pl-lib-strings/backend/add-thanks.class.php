<?php

/**
 * add-thanks.class.php
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
 * \KocujPlLib\V13a\Classes\Project\Components\Backend\AddThanks classes strings
 *
 * @access public
 */
class AddThanks implements Strings
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
            'ACTION_ADMIN_FOOTER_WINDOW_TITLE' => __('More information', 'kocuj-sitemap'),
            'ACTION_ADMIN_FOOTER_SCRIPT_MORE_INFO_LINK' => __('see more information about this', 'kocuj-sitemap'),
            'ACTION_ADMIN_FOOTER_SCRIPT_SENDING' => __('Sending, please wait...', 'kocuj-sitemap'),
            'ACTION_ADMIN_FOOTER_SCRIPT_ERROR' => __('Error! Retrying in few seconds.', 'kocuj-sitemap'),
            'ACTION_ADMIN_FOOTER_SCRIPT_ERROR_NO_RETRIES' => __('Error! No data has been sent!', 'kocuj-sitemap'),
            'ACTION_ADMIN_FOOTER_SCRIPT_ERROR_ALREADY_EXISTS' => __('Website address already exists.', 'kocuj-sitemap'),
            'ACTION_ADMIN_FOOTER_SCRIPT_ERROR_WRONG_RESPONSE_1' => __('Error! Wrong response from server!',
                'kocuj-sitemap'),
            'ACTION_ADMIN_FOOTER_SCRIPT_ERROR_WRONG_RESPONSE_2_PLUGIN' => __('Update your plugin to the newest version.',
                'kocuj-sitemap'),
            'ACTION_ADMIN_FOOTER_SCRIPT_ERROR_WRONG_RESPONSE_2_THEME' => __('Update your theme to the newest version.',
                'kocuj-sitemap'),
            'ACTION_ADMIN_FOOTER_SCRIPT_SUCCESS' => __('Data has been sent.', 'kocuj-sitemap'),
            'AJAX_ACTION_AJAX_MORE_INFO_WINDOW_DISPLAY_WEBSITES_AND' => __('and', 'kocuj-sitemap'),
            /* translators: %s: List of websites (no links) */
            'AJAX_ACTION_AJAX_MORE_INFO_WINDOW_DISPLAY_MORE_1_PLUGIN' => __('If you want to appreciate the plugin\'s author work, please send an information about your website address to him. This information will be used only for statistical purposes and for adding public information about your website on the following websites: %s. Please, keep in mind, that this information can be used in future on another websites which belongs to the plugin\'s author.',
                'kocuj-sitemap'),
            /* translators: %s: List of websites (no links) */
            'AJAX_ACTION_AJAX_MORE_INFO_WINDOW_DISPLAY_MORE_1_THEME' => __('If you want to appreciate the theme\'s author work, please send an information about your website address to him. This information will be used only for statistical purposes and for adding public information about your website on the following websites: %s. Please, keep in mind, that this information can be used in future on another websites which belongs to the theme\'s author.',
                'kocuj-sitemap'),
            'AJAX_ACTION_AJAX_MORE_INFO_WINDOW_DISPLAY_MORE_2' => __('Only the following information about your website will be send to author:',
                'kocuj-sitemap'),
            'AJAX_ACTION_AJAX_MORE_INFO_WINDOW_DISPLAY_MORE_3' => __('website address (URL)', 'kocuj-sitemap'),
            'AJAX_ACTION_AJAX_MORE_INFO_WINDOW_DISPLAY_MORE_4' => __('website title', 'kocuj-sitemap'),
            'AJAX_ACTION_AJAX_MORE_INFO_WINDOW_DISPLAY_MORE_5' => __('website description', 'kocuj-sitemap'),
            'AJAX_ACTION_AJAX_MORE_INFO_WINDOW_DISPLAY_MORE_6' => __('In addition, there are standard information for network connection (for example, IP address of the sender) in the connection header. This information can be also saved in author database. However, these information will be used only for necessary actions to provide secure saving of received data and it will not be made known to public in any way.',
                'kocuj-sitemap')
        );
        // exit
        return (isset($texts[$id])) ? $texts[$id] : '';
    }
}
