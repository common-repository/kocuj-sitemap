<?php

/**
 * menus.class.php
 *
 * @author Dominik Kocuj
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2 or later
 * @copyright Copyright (c) 2013-2020 kocuj.pl
 * @package kocuj_sitemap
 */

// set namespace
namespace KocujSitemapPlugin\Classes\ElementType\Frontend;

// set namespaces aliases
use KocujIL\V13a as KocujIL;
use KocujSitemapPlugin\Classes\ElementTypeFrontendParent;

// security
if ((!defined('ABSPATH')) || ((isset($_SERVER['SCRIPT_FILENAME'])) && (basename($_SERVER['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die();
}

/**
 * Menus type class
 *
 * @access public
 */
class Menus extends ElementTypeFrontendParent
{

    /**
     * Get type letter
     *
     * @access public
     * @return string Type letter
     */
    public function getTypeLetter()
    {
        // exit
        return 'M';
    }

    /**
     * Get configuration options
     *
     * @access public
     * @return array Configuration options
     */
    public function getConfigOptions()
    {
        // exit
        return array(
            array(
                'DisplayMenus',
                'checkbox',
                '1',
                __('Display menus', 'kocuj-sitemap'),
                KocujIL\Enums\Project\Components\All\Options\OptionArray::NO,
                array(),
                array()
            ),
            array(
                'Menus',
                'integer',
                array(),
                __('Menus list', 'kocuj-sitemap'),
                KocujIL\Enums\Project\Components\All\Options\OptionArray::YES,
                array(
                    'allowchangeorder' => true,
                    'deletebutton' => true,
                    'addnewbutton' => false,
                    'autoadddeleteifempty' => true
                ),
                array()
            ),
            array(
                'HideMenusDuplicates',
                'checkbox',
                '0',
                __('Remove menu items duplicates from sitemap', 'kocuj-sitemap'),
                KocujIL\Enums\Project\Components\All\Options\OptionArray::NO,
                array(),
                array()
            )
        );
    }

    /**
     * Get section name
     *
     * @access public
     * @return string Section name
     */
    public function getSectionName()
    {
        // exit
        return __('Menus', 'kocuj-sitemap');
    }

    /**
     * Get ID to check
     *
     * @access public
     * @param array $element
     *            Element
     * @param string $section
     *            Section name
     * @return int ID to check
     */
    public function getIDToCheck(array $element, $section)
    {
        // exit
        return ((($element['tp'] === 'category') && ($section === 'category')) || (($element['tp'] === 'post') && ($section === 'post')) || (($element['tp'] === 'term') && ($section === 'term'))) ? $element['id'] : -1;
    }

    /**
     * Get sections to check
     *
     * @access public
     * @param array $element
     *            Element
     * @return array Sections to check
     */
    public function getSectionsToCheck(array $element)
    {
        // exit
        return array(
            'post',
            'category',
            'term'
        );
    }

    /**
     * Check if there are exclude parameters (true) or not (false)
     *
     * @access public
     * @return bool There are exclude parameters (true) or not (false)
     */
    public function checkExcludeParameters()
    {
        // exit
        return false;
    }

    /**
     * Get hide type parameters value
     *
     * @access public
     * @return string Hide parameters type value
     */
    public function getHideTypeParameterValue()
    {
        // exit
        return 'menu';
    }

    /**
     * Get hide type parameters description
     *
     * @access public
     * @return string Hide parameters type description
     */
    public function getHideTypeParameterDescription()
    {
        // exit
        return __('menus', 'kocuj-sitemap');
    }
}
