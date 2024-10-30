<?php

/**
 * home.class.php
 *
 * @author Dominik Kocuj
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2 or later
 * @copyright Copyright (c) 2013-2020 kocuj.pl
 * @package kocuj_sitemap
 */

// set namespace
namespace KocujSitemapPlugin\Classes\ElementType\Frontend;

// set namespaces aliases
use KocujSitemapPlugin\Classes\ElementTypeFrontendParent;

// security
if ((!defined('ABSPATH')) || ((isset($_SERVER['SCRIPT_FILENAME'])) && (basename($_SERVER['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die();
}

/**
 * Home type class
 *
 * @access public
 */
class Home extends ElementTypeFrontendParent
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
        return 'H';
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
     * Check if this type is configurable in order
     *
     * @access public
     * @return bool This type is configurable in order (true) or not (false)
     */
    public function checkConfigurableOrder()
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
        return 'home';
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
        return __('link to homepage', 'kocuj-sitemap');
    }
}
