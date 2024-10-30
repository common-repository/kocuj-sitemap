<?php

/**
 * tags.class.php
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
use KocujSitemapPlugin\Classes\Options;
use KocujSitemapPlugin\Enums\DisplayType;

// security
if ((!defined('ABSPATH')) || ((isset($_SERVER['SCRIPT_FILENAME'])) && (basename($_SERVER['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die();
}

/**
 * Tags type class
 *
 * @access public
 */
class Tags extends ElementTypeFrontendParent
{

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
        return $display === DisplayType::STANDARD || Options::getInstance()->getOption('DisplayTagsCloud') === '0';
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
        return 'T';
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
                'DisplayTags',
                'checkbox',
                '0',
                __('Display tags', 'kocuj-sitemap'),
                KocujIL\Enums\Project\Components\All\Options\OptionArray::NO,
                array(),
                array()
            ),
            array(
                'DisplayTagsUsed',
                'checkbox',
                '0',
                __('Display only used tags', 'kocuj-sitemap'),
                KocujIL\Enums\Project\Components\All\Options\OptionArray::NO,
                array(),
                array()
            ),
            array(
                'DisplayTagsCloud',
                'checkbox',
                '0',
                __('Display tags as cloud', 'kocuj-sitemap'),
                KocujIL\Enums\Project\Components\All\Options\OptionArray::NO,
                array(),
                array()
            ),
            array(
                'DisplayTagsCloudNumber',
                'integer',
                '0',
                __('Number of tags to display in cloud', 'kocuj-sitemap'),
                KocujIL\Enums\Project\Components\All\Options\OptionArray::NO,
                array(),
                array()
            ),
            array(
                'DisplayTagsSort',
                'text',
                'name',
                __('Sort tags by', 'kocuj-sitemap'),
                KocujIL\Enums\Project\Components\All\Options\OptionArray::NO,
                array(),
                array()
            ),
            array(
                'DisplayTagsOrder',
                'text',
                'asc',
                __('Sort tags order', 'kocuj-sitemap'),
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
        return __('Tags', 'kocuj-sitemap');
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
        return $element['id'];
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
            'term'
        );
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
        return array(
            'term' => __('tags', 'kocuj-sitemap')
        );
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
        return 'tag';
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
        return __('tags', 'kocuj-sitemap');
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
        return (Options::getInstance()->getOption('DisplayTagsCloud') === '0');
    }
}
