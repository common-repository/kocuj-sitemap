<?php

/**
 * settings-form.class.php
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
 * \KocujIL\V13a\Classes\Project\Components\Backend\SettingsForm classes strings
 *
 * @access public
 */
class SettingsForm implements Strings
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
            'CONSTRUCT_EDIT' => __('Edit', 'kocuj-sitemap'),
            'CONSTRUCT_DELETE' => __('Delete', 'kocuj-sitemap'),
            'SAVE_OR_RESTORE_IN_CONTROLLER_ERROR' => __('There were some wrong settings values. These values have not been saved.',
                'kocuj-sitemap'),
            'SAVE_OR_RESTORE_IN_CONTROLLER_RESTORE' => __('Default settings values has been restored.',
                'kocuj-sitemap'),
            'SAVE_OR_RESTORE_IN_CONTROLLER_UPDATE' => __('Settings have been updated.', 'kocuj-sitemap'),
            'FIELD_TO_ADD_NOW_ELEMENT_DOES_NOT_EXIST' => __('Element does not exist.', 'kocuj-sitemap'),
            'FIELD_TO_ADD_NOW_BUTTON_ADD_NEW_ELEMENT' => __('Add new element', 'kocuj-sitemap'),
            'SHOW_FORM_ADD_NEW_ELEMENT' => __('Add new element', 'kocuj-sitemap'),
            /* translators: %s: Tab number */
            'SHOW_FORM_TAB' => __('Tab %s', 'kocuj-sitemap'),
            'SHOW_FORM_CONFIRM_RESTORE' => __('Are you sure do you want to restore default settings?', 'kocuj-sitemap'),
            'ACTION_CONTROLLER_SECURITY_ERROR' => __('Security error!', 'kocuj-sitemap'),
            'ACTION_CONTROLLER_DATA_DOES_NOT_EXIST' => __('Data does not exist', 'kocuj-sitemap'),
            'ACTION_CONTROLLER_ELEMENT_DOES_NOT_EXIST' => __('Element does not exist.', 'kocuj-sitemap'),
            'ACTION_FORM_HEADER_ELEMENTS' => __('Elements', 'kocuj-sitemap')
        );
        // exit
        return (isset($texts[$id])) ? $texts[$id] : '';
    }
}
