<?php

/**
 * custom.class.php
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
use KocujSitemapPlugin\Enums\Widget;

// security
if ((!defined('ABSPATH')) || ((isset($_SERVER['SCRIPT_FILENAME'])) && (basename($_SERVER['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die();
}

/**
 * Custom post types class
 *
 * @access public
 */
class Custom extends ElementTypeFrontendParent
{

    /**
     * Last element type
     *
     * @access private
     * @var string Last Element type
     */
    private $lastType = '';

    /**
     * Get type letter
     *
     * @access public
     * @return string Type letter
     */
    public function getTypeLetter()
    {
        // exit
        return 'C';
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
                'DisplayCustomPosts',
                'checkbox',
                '1',
                __('Display custom post types posts', 'kocuj-sitemap'),
                KocujIL\Enums\Project\Components\All\Options\OptionArray::NO,
                array(),
                array()
            ),
            array(
                'CustomPosts',
                'text',
                array(),
                __('Custom post types list', 'kocuj-sitemap'),
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
                'DisplayCustomPostsTaxonomies',
                'checkbox',
                '1',
                __('Divide custom post types posts into taxonomies', 'kocuj-sitemap'),
                KocujIL\Enums\Project\Components\All\Options\OptionArray::NO,
                array(),
                array()
            ),
            array(
                'DisplayEmptyCustomPostsTaxonomies',
                'checkbox',
                '0',
                __('Display empty taxonomies', 'kocuj-sitemap'),
                KocujIL\Enums\Project\Components\All\Options\OptionArray::NO,
                array(),
                array()
            ),
            array(
                'DisplayCustomPostsSort',
                'text',
                'date',
                __('Sort custom post types posts by', 'kocuj-sitemap'),
                KocujIL\Enums\Project\Components\All\Options\OptionArray::NO,
                array(),
                array()
            ),
            array(
                'DisplayCustomPostsOrder',
                'text',
                'desc',
                __('Sort custom post types posts order', 'kocuj-sitemap'),
                KocujIL\Enums\Project\Components\All\Options\OptionArray::NO,
                array(),
                array()
            )
        );
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
        return false;
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
        return ((($element['tp'] === 'term') && ($section === 'term')) || (($element['tp'] === 'post') && ($section === 'post'))) ? $element['id'] : -1;
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
            'post' => __('custom types posts', 'kocuj-sitemap'),
            'term' => __('custom taxonomies', 'kocuj-sitemap')
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
        return 'custom';
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
        return __('custom post types', 'kocuj-sitemap');
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
        return (Options::getInstance()->getOption('DisplayEmptyCustomPostsTaxonomies') === '1') ? array() : array(
            'term'
        );
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
    public function getBeforeElement(array $element, $widget = Widget::NO, $locale = '')
    {
        // add section name if needed
        $output = '';
        if ((isset($element['ad'])) && (isset($element['ad'][0])) && ($this->lastType !== $element['ad'][0])) {
            $hLevel = ($widget === Widget::YES) ? Options::getInstance()->getOption('HLevelWidget') : Options::getInstance()->getOption('HLevelMain');
            $output = '</ul><h' . $hLevel . '>' . $element['ad'][1] . '</h' . $hLevel . '><ul>';
            $this->lastType = $element['ad'][0];
        }
        // exit
        return $output;
    }
}
