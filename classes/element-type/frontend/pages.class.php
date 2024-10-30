<?php

/**
 * pages.class.php
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
 * Pages type class
 *
 * @access public
 */
class Pages extends ElementTypeFrontendParent
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
        return 'G';
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
                'DisplayPages',
                'checkbox',
                '1',
                __('Display pages', 'kocuj-sitemap'),
                KocujIL\Enums\Project\Components\All\Options\OptionArray::NO,
                array(),
                array()
            ),
            array(
                'DisplayPagesSort',
                'text',
                'default',
                __('Sort pages by', 'kocuj-sitemap'),
                KocujIL\Enums\Project\Components\All\Options\OptionArray::NO,
                array(),
                array()
            ),
            array(
                'DisplayPagesOrder',
                'text',
                'asc',
                __('Sort pages order', 'kocuj-sitemap'),
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
        return __('Pages', 'kocuj-sitemap');
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
            'post'
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
            'post' => __('pages', 'kocuj-sitemap')
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
        return 'page';
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
        return __('pages', 'kocuj-sitemap');
    }
}
