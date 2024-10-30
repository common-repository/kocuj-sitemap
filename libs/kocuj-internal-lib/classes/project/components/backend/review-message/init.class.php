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
namespace KocujIL\V13a\Classes\Project\Components\Backend\ReviewMessage;

// set namespaces aliases
use KocujIL\V13a\Classes\ComponentInitObject;
use KocujIL\V13a\Classes\Helper;
use KocujIL\V13a\Enums\ProjectCategory;

// security
if ((!defined('ABSPATH')) || ((isset ($_SERVER ['SCRIPT_FILENAME'])) && (basename($_SERVER ['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die ();
}

/**
 * Review message (component initialization) class
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
        '' => array(
            ProjectCategory::BACKEND => array(
                'installation-date',
                'message'
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
        if ((current_user_can('manage_network')) || (current_user_can('manage_options'))) {
            $this->getComponent('actions-filters-helper')->addActionWhenNeeded('admin_head', ProjectCategory::BACKEND,
                'review-message', '', 'actionAdminHead', 1);
            $this->getComponent('actions-filters-helper')->addActionWhenNeeded('admin_print_footer_scripts',
                ProjectCategory::BACKEND, 'review-message', '', 'actionPrintFooterScripts',
                Helper::getInstance()->calculateMaxPriority('admin_print_footer_scripts'));
        }
    }
}
