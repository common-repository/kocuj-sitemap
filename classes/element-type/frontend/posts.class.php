<?php

/**
 * posts.class.php
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

// security
if ((!defined('ABSPATH')) || ((isset($_SERVER['SCRIPT_FILENAME'])) && (basename($_SERVER['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die();
}

/**
 * Posts type class
 *
 * @access public
 */
class Posts extends ElementTypeFrontendParent
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
        return 'P';
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
                'DisplayPosts',
                'checkbox',
                '1',
                __('Display posts', 'kocuj-sitemap'),
                KocujIL\Enums\Project\Components\All\Options\OptionArray::NO,
                array(),
                array()
            ),
            array(
                'DisplayPostsCategories',
                'checkbox',
                '1',
                __('Divide posts into categories', 'kocuj-sitemap'),
                KocujIL\Enums\Project\Components\All\Options\OptionArray::NO,
                array(),
                array()
            ),
            array(
                'DisplayEmptyPostsCategories',
                'checkbox',
                '0',
                __('Display empty categories', 'kocuj-sitemap'),
                KocujIL\Enums\Project\Components\All\Options\OptionArray::NO,
                array(),
                array()
            ),
            array(
                'DisplayPostsSort',
                'text',
                'date',
                __('Sort posts by', 'kocuj-sitemap'),
                KocujIL\Enums\Project\Components\All\Options\OptionArray::NO,
                array(),
                array()
            ),
            array(
                'DisplayPostsOrder',
                'text',
                'desc',
                __('Sort posts order', 'kocuj-sitemap'),
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
        return __('Posts', 'kocuj-sitemap');
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
        return ((($element['tp'] === 'category') && ($section === 'category')) || (($element['tp'] === 'post') && ($section === 'post'))) ? $element['id'] : -1;
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
            'category'
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
            'post' => __('posts', 'kocuj-sitemap'),
            'category' => __('categories', 'kocuj-sitemap')
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
        return 'post';
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
        return __('posts', 'kocuj-sitemap');
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
        return (Options::getInstance()->getOption('DisplayEmptyPostsCategories') === '1') ? array() : array(
            'category'
        );
    }
}
