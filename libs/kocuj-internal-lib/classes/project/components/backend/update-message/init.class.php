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
namespace KocujIL\V13a\Classes\Project\Components\Backend\UpdateMessage;

// set namespaces aliases
use KocujIL\V13a\Classes\ComponentInitObject;
use KocujIL\V13a\Classes\Helper;
use KocujIL\V13a\Enums\Project\Components\Backend\UpdateMessage\ExceptionCode;
use KocujIL\V13a\Enums\ProjectCategory;

// security
if ((!defined('ABSPATH')) || ((isset ($_SERVER ['SCRIPT_FILENAME'])) && (basename($_SERVER ['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die ();
}

/**
 * Update message (component initialization) class
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
            ProjectCategory::ALL => array(
                'js-ajax',
                'version'
            ),
            ProjectCategory::BACKEND => array(
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
     * Constructor
     *
     * @access public
     * @param object $projectObj
     *            \KocujIL\V13a\Classes\Project object for current project
     * @return void
     */
    public function __construct($projectObj)
    {
        // execute parent
        parent::__construct($projectObj);
        // set errors
        $this->errors = array(
            ExceptionCode::UPDATE_MESSAGE_ID_DOES_NOT_EXIST => 'Update message identifier does not exist'
        );
    }

    /**
     * Initialize actions and filters
     *
     * @access public
     * @return void
     */
    public function actionsAndFilters()
    {
        // add actions
        $this->getComponent('actions-filters-helper')->addActionWhenNeeded('admin_init', ProjectCategory::BACKEND,
            'update-message', '', 'actionAdminInit', Helper::getInstance()->calculateMaxPriority('admin_init'));
        $this->getComponent('actions-filters-helper')->addActionWhenNeeded('admin_print_footer_scripts',
            ProjectCategory::BACKEND, 'update-message', '', 'actionPrintFooterScripts',
            Helper::getInstance()->calculateMaxPriority('admin_print_footer_scripts'));
        // add actions for AJAX
        if ((current_user_can('manage_network')) || (current_user_can('manage_options'))) {
            $this->getComponent('actions-filters-helper')->addActionWhenNeeded('wp_ajax_' . $this->getComponent('project-helper')->getPrefix() . '__update_message_display',
                ProjectCategory::BACKEND, 'update-message', 'ajax', 'actionAjaxDisplay');
            $this->getComponent('actions-filters-helper')->addActionWhenNeeded('wp_ajax_' . $this->getComponent('project-helper')->getPrefix() . '__update_message_close',
                ProjectCategory::BACKEND, 'update-message', 'ajax', 'actionAjaxClose');
        }
    }
}