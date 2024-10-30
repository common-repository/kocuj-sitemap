<?php

/**
 * options.class.php
 *
 * @author Dominik Kocuj
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2 or later
 * @copyright Copyright (c) 2013-2020 kocuj.pl
 * @package kocuj_sitemap
 */

// set namespace
namespace KocujSitemapPlugin\Classes\KocujILStrings\All;

// set namespaces aliases
use KocujIL\V13a\Interfaces\Strings;

// security
if ((!defined('ABSPATH')) || ((isset($_SERVER['SCRIPT_FILENAME'])) && (basename($_SERVER['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die();
}

/**
 * \KocujIL\V13a\Classes\Project\Components\All\Options classes strings
 *
 * @access public
 */
class Options implements Strings
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
            /* translators: %s: Minimum value */
            'TYPE_VALIDATION_HELPER_INTEGER_OR_FLOAT__ARRAY_ERROR_BELOW_MINIMUM_VALUE' => __('one of the values is below minimum - %s can be the lowest value',
                'kocuj-sitemap'),
            /* translators: %s: Minimum value */
            'TYPE_VALIDATION_HELPER_INTEGER_OR_FLOAT__ERROR_BELOW_MINIMUM_VALUE' => __('value is below minimum - %s can be the lowest value',
                'kocuj-sitemap'),
            /* translators: %s: Maximum value */
            'TYPE_VALIDATION_HELPER_INTEGER_OR_FLOAT__ARRAY_ERROR_ABOVE_MAXIMUM_VALUE' => __('one of the values is above maximum - %s can be the highest value',
                'kocuj-sitemap'),
            /* translators: %s: Maximum value */
            'TYPE_VALIDATION_HELPER_INTEGER_OR_FLOAT__ERROR_ABOVE_MAXIMUM_VALUE' => __('value is above maximum - %s can be the highest value',
                'kocuj-sitemap'),
            'TYPE_VALIDATION_HELPER_INTEGER_OR_FLOAT__ARRAY_ERROR_NO_INTEGER' => __('one of the values is not integer',
                'kocuj-sitemap'),
            'TYPE_VALIDATION_HELPER_INTEGER_OR_FLOAT__ERROR_NO_INTEGER' => __('value is not integer', 'kocuj-sitemap'),
            'TYPE_VALIDATION_HELPER_INTEGER_OR_FLOAT__ARRAY_ERROR_NO_NUMERIC' => __('one of the values is not numeric',
                'kocuj-sitemap'),
            'TYPE_VALIDATION_HELPER_INTEGER_OR_FLOAT__ERROR_NO_NUMERIC' => __('value is not numeric', 'kocuj-sitemap'),
            'TYPE_VALIDATION_CHECKBOX_ARRAY_ERROR' => __('one of the values is neither checked or unchecked',
                'kocuj-sitemap'),
            'TYPE_VALIDATION_CHECKBOX_ERROR' => __('value is neither checked or unchecked', 'kocuj-sitemap'),
            /* translators: %s: Minimum allowed characters */
            'SET_OPTION_WITH_RETURNED_ARRAY_TEXT_TOO_FEW_CHARACTERS' => __('one of the values has too few characters - minimum %s allowed characters',
                'kocuj-sitemap'),
            /* translators: %s: Minimum allowed characters */
            'SET_OPTION_WITH_RETURNED_TEXT_TOO_FEW_CHARACTERS' => __('value has too few characters - minimum %s allowed characters',
                'kocuj-sitemap'),
            /* translators: %s: Maximum allowed characters */
            'SET_OPTION_WITH_RETURNED_ARRAY_TEXT_TOO_MANY_CHARACTERS' => __('one of the values has too many characters - maximum %s allowed characters',
                'kocuj-sitemap'),
            /* translators: %s: Maximum allowed characters */
            'SET_OPTION_WITH_RETURNED_TEXT_TOO_MANY_CHARACTERS' => __('value has too many characters - maximum %s allowed characters',
                'kocuj-sitemap'),
            'SET_OPTION_WITH_RETURNED_TEXT_NOT_UNIQUE' => __('value is not unique', 'kocuj-sitemap'),
            'SET_OPTION_WITH_RETURNED_TEXT_NOT_AVAILABLE' => __('value is not available', 'kocuj-sitemap')
        );
        // exit
        return (isset($texts[$id])) ? $texts[$id] : '';
    }
}
