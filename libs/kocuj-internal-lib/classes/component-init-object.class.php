<?php

/**
 * component-init-object.class.php
 *
 * @author Dominik Kocuj
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2 or later
 * @copyright Copyright (c) 2016-2020 kocuj.pl
 * @package kocuj_internal_lib
 */

// set namespace
namespace KocujIL\V13a\Classes;

// security
if ((!defined('ABSPATH')) || ((isset ($_SERVER ['SCRIPT_FILENAME'])) && (basename($_SERVER ['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die ();
}

/**
 * Component object (component initialization) class
 *
 * @access public
 */
class ComponentInitObject extends ComponentObject
{

    /**
     * Required components
     *
     * @access protected
     * @var array
     */
    protected $requiredComponents = array();

    /**
     * Allow actions and filters in "customizer" (true) or not (false)
     *
     * @access protected
     * @var bool
     */
    protected $allowActionsAndFiltersInCustomizer = true;

    /**
     * Errors
     *
     * @access protected
     * @var array
     */
    protected $errors = array();

    /**
     * Initialize actions and filters
     *
     * @access public
     * @return void
     */
    public function actionsAndFilters()
    {
    }

    /**
     * Get required components
     *
     * @access public
     * @return array Required components
     */
    public function getRequiredComponents()
    {
        // exit
        return $this->requiredComponents;
    }

    /**
     * Get if allow actions and filters in "customizer"
     *
     * @access public
     * @return bool Allow actions and filters in "customizer" (true) or not (false)
     */
    public function getAllowActionsAndFiltersInCustomizer()
    {
        // exit
        return $this->allowActionsAndFiltersInCustomizer;
    }

    /**
     * Get errors
     *
     * @access public
     * @return array Errors
     */
    public function getErrors()
    {
        // exit
        return $this->errors;
    }
}
