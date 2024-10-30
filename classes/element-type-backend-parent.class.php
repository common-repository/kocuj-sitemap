<?php

/**
 * element-type-parent.class.php
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
 * Element type backend parent class
 *
 * @access public
 */
class ElementTypeBackendParent
{

    /**
     * Add authors to sitemap
     *
     * @access public
     * @param string $locale
     *            Language locale - default: empty
     * @return array Output array
     */
    public function getElementArray($locale = '')
    {
        // exit
        return array();
    }

    /**
     * Get administration page settings
     *
     * @access public
     * @return array Administration page settings
     */
    public function getAdminPageSettings()
    {
        // exit
        return array();
    }

    /**
     * Get administration page data
     *
     * @access public
     * @return array Administration page data
     */
    public function getAdminPageData()
    {
        // exit
        return array();
    }

    /**
     * Get administration panel order name
     *
     * @access public
     * @return string Administration panel order name
     */
    public function getAdminOrderName()
    {
        // exit
        return '';
    }

    /**
     * Get administration cache actions
     *
     * @access public
     * @return array Administration cache actions
     */
    public function getAdminCacheActions()
    {
        // exit
        return array();
    }

    /**
     * Get administration cache filters
     *
     * @access public
     * @return array Administration cache filters
     */
    public function getAdminCacheFilters()
    {
        // exit
        return array();
    }
}
