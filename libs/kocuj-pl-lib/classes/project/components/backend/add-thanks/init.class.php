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
namespace KocujPlLib\V13a\Classes\Project\Components\Backend\AddThanks;

// set namespaces aliases
use KocujIL\V13a as KocujIL;
use KocujIL\V13a\Classes\ComponentInitObject;

// security
if ((!defined('ABSPATH')) || ((isset ($_SERVER ['SCRIPT_FILENAME'])) && (basename($_SERVER ['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die ();
}

/**
 * Adding thanks (component initialization) class
 *
 * @access public
 */
class Init extends ComponentInitObject
{

    /**
     * Required components
     *
     * @access protected
     * @var array
     */
    protected $requiredComponents = array(
        'KocujIL' => array(
            KocujIL\Enums\ProjectCategory::ALL => array(
                'js-ajax',
                'window'
            )
        )
    );

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
        // add actions
        if (((!KocujIL\Classes\Helper::getInstance()->checkUserIPLocal()) || (!KocujIL\Classes\Helper::getInstance()->checkServerIPLocal())) && ((current_user_can('manage_network_plugins')) || (current_user_can('activate_plugins')) || (current_user_can('install_plugins')))) {
            $this->getComponent('actions-filters-helper')->addActionWhenNeeded('admin_footer',
                KocujIL\Enums\ProjectCategory::BACKEND, 'add-thanks', '', 'actionAdminFooter', 1);
        }
        // add actions for AJAX
        if (((!KocujIL\Classes\Helper::getInstance()->checkUserIPLocal()) || (!KocujIL\Classes\Helper::getInstance()->checkServerIPLocal())) && ((current_user_can('manage_network_plugins')) || (current_user_can('activate_plugins')) || (current_user_can('install_plugins')))) {
            $this->getComponent('actions-filters-helper')->addActionWhenNeeded('wp_ajax_' . $this->getComponent('project-helper')->getPrefix() . '__add_thanks_finished',
                KocujIL\Enums\ProjectCategory::BACKEND, 'add-thanks', 'ajax', 'actionAjaxFinished');
            $this->getComponent('actions-filters-helper')->addActionWhenNeeded('wp_ajax_' . $this->getComponent('project-helper')->getPrefix() . '__add_thanks_more_info_window_display',
                KocujIL\Enums\ProjectCategory::BACKEND, 'add-thanks', 'ajax', 'actionAjaxMoreInfoWindowDisplay');
        }
    }
}
