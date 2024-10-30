<?php

/**
 * init.class.php
 *
 * @author Dominik Kocuj
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2 or later
 * @copyright Copyright (c) 2016-2020 kocuj.pl
 * @package kocuj_internal_lib
 */

// set namespace
namespace KocujIL\V13a\Classes\Project\Components\All\JsAjax;

// set namespaces aliases
use KocujIL\V13a\Classes\ComponentInitObject;
use KocujIL\V13a\Enums\ProjectCategory;

// security
if ((!defined('ABSPATH')) || ((isset ($_SERVER ['SCRIPT_FILENAME'])) && (basename($_SERVER ['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die ();
}

/**
 * Configuration (component initialization) class
 *
 * @access public
 */
class Init extends ComponentInitObject
{

    /**
     * Allow actions and filters in "customizer" (true) or not (false)
     *
     * @access protected
     * @var bool
     */
    protected $allowActionsAndFiltersInCustomizer = false;

    /**
     * Initialize actions and filters
     *
     * @access public
     * @return void
     */
    public function actionsAndFilters()
    {
        // add actions for AJAX
        $this->getComponent('actions-filters-helper')->addActionWhenNeeded('wp_ajax_' . $this->getComponent('project-helper')->getPrefix() . '__js_ajax',
            ProjectCategory::ALL, 'js-ajax', 'ajax', 'actionAjaxProxy');
    }
}
