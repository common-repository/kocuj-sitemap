<?php

/**
 * element-type-frontend-parent.class.php
 *
 * @author Dominik Kocuj
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2 or later
 * @copyright Copyright (c) 2013-2020 kocuj.pl
 * @package kocuj_sitemap
 */

// set namespace
namespace KocujSitemapPlugin\Classes;

// security
if ((!defined('ABSPATH')) || ((isset($_SERVER['SCRIPT_FILENAME'])) && (basename($_SERVER['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die();
}

/**
 * Element type frontend parent class
 *
 * @access public
 */
class ElementTypeFrontendParent
{

    /**
     * Check if this type exists
     *
     * @access public
     * @return bool This type exists (true) or not (false)
     */
    public function checkExists()
    {
        // exit
        return true;
    }

    /**
     * Check if this type should be displayed for this display type
     *
     * @access public
     * @param int $display
     *            Display type; must be one of the following constants from \KocujSitemapPlugin\Enums\DisplayType: STANDARD (when it is standard display) or DROPDOWN (when it is drop-down list display)
     * @return bool This type should be displayed (true) or not (false)
     */
    public function checkForDisplay($display)
    {
        // exit
        return true;
    }

    /**
     * Get type letter
     *
     * @access public
     * @return string Type letter
     */
    public function getTypeLetter()
    {
        // exit
        return '';
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
        return array();
    }

    /**
     * Check section name
     *
     * @access public
     * @return bool Section name exists (true) or not (false)
     */
    public function checkSectionName()
    {
        // exit
        return true;
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
        return '';
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
        return -1;
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
        return array();
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
        return true;
    }

    /**
     * Get exclude parameters suffixes
     *
     * @access public
     * @return array Exclude parameters suffixes
     */
    public function getExcludeParametersSuffixes()
    {
        // exit
        return array();
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
        return '';
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
        return '';
    }

    /**
     * Get types which should be removed when they have no children
     *
     * @access public
     * @return array Types which should be removed when they have no children
     */
    public function getTypesRequiresChildren()
    {
        // exit
        return array();
    }

    /**
     * Check if there should be an "ul" list tag
     *
     * @access public
     * @return bool There should be an "ul" list tag (true) or not (false)
     */
    public function checkListTag()
    {
        // exit
        return true;
    }

    /**
     * Check if this type is configurable in order
     *
     * @access public
     * @return bool This type is configurable in order (true) or not (false)
     */
    public function checkConfigurableOrder()
    {
        // exit
        return true;
    }

    /**
     * Get text before element
     *
     * @access public
     * @param array $element
     *            Element
     * @param int $widget
     *            It is widget or not; must be one of the following constants from \KocujSitemapPlugin\Enums\Widget: NO (when it is not widget) or YES (when it is widget) - default: \KocujSitemapPlugin\Enums\Widget::NO
     * @param string $locale
     *            Language locale - default: empty
     * @return string Text before element
     */
    public function getBeforeElement(array $element, $widget = \KocujSitemapPlugin\Enums\Widget::NO, $locale = '')
    {
        // exit
        return '';
    }

    /**
     * Get text after element
     *
     * @access public
     * @param array $element
     *            Element
     * @param int $widget
     *            It is widget or not; must be one of the following constants from \KocujSitemapPlugin\Enums\Widget: NO (when it is not widget) or YES (when it is widget) - default: \KocujSitemapPlugin\Enums\Widget::NO
     * @param string $locale
     *            Language locale - default: empty
     * @return string Text after element
     */
    public function getAfterElement(array $element, $widget = \KocujSitemapPlugin\Enums\Widget::NO, $locale = '')
    {
        // exit
        return '';
    }
}
