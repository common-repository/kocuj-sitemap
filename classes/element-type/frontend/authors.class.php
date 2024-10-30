<?php

/**
 * authors.class.php
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
 * Authors type class
 *
 * @access public
 */
class Authors extends ElementTypeFrontendParent
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
        return 'A';
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
                'DisplayAuthors',
                'checkbox',
                '0',
                __('Display authors', 'kocuj-sitemap'),
                KocujIL\Enums\Project\Components\All\Options\OptionArray::NO,
                array(),
                array()
            ),
            array(
                'DisplayOnlyAuthorsWithArticles',
                'checkbox',
                '0',
                __('Display only authors with at least one article', 'kocuj-sitemap'),
                KocujIL\Enums\Project\Components\All\Options\OptionArray::NO,
                array(),
                array()
            ),
            array(
                'DisplayAuthorsSort',
                'text',
                'name',
                __('Sort authors by', 'kocuj-sitemap'),
                KocujIL\Enums\Project\Components\All\Options\OptionArray::NO,
                array(),
                array()
            ),
            array(
                'DisplayAuthorsOrder',
                'text',
                'asc',
                __('Sort authors order', 'kocuj-sitemap'),
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
        return __('Authors', 'kocuj-sitemap');
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
            'author'
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
            'author' => __('authors', 'kocuj-sitemap')
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
        return 'author';
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
        return __('authors', 'kocuj-sitemap');
    }
}
