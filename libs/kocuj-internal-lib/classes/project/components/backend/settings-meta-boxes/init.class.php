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
namespace KocujIL\V13a\Classes\Project\Components\Backend\SettingsMetaBoxes;

// set namespaces aliases
use KocujIL\V13a\Classes\ComponentInitObject;
use KocujIL\V13a\Enums\Project\Components\Backend\SettingsMetaBoxes\ExceptionCode;
use KocujIL\V13a\Enums\ProjectCategory;

// security
if ((!defined('ABSPATH')) || ((isset ($_SERVER ['SCRIPT_FILENAME'])) && (basename($_SERVER ['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die ();
}

/**
 * Meta boxes for settings (component initialization) class
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
                'settings-menu'
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
            ExceptionCode::SETTINGS_META_BOX_ID_EXISTS => 'Settings meta box identifier already exists',
            ExceptionCode::SETTINGS_META_BOX_ID_DOES_NOT_EXIST => 'Settings meta box identifier does not exist'
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
        $this->getComponent('actions-filters-helper')->addActionWhenNeeded('admin_enqueue_scripts',
            ProjectCategory::BACKEND, 'settings-meta-boxes', '', 'actionEnqueueScripts', 10);
        $this->getComponent('actions-filters-helper')->addActionWhenNeeded($this->getComponent('project-helper')->getPrefix() . '__before_wrapinside_div',
            ProjectCategory::BACKEND, 'settings-meta-boxes', '', 'actionBeforeFormDiv');
        $this->getComponent('actions-filters-helper')->addActionWhenNeeded($this->getComponent('project-helper')->getPrefix() . '__after_wrapinside_div',
            ProjectCategory::BACKEND, 'settings-meta-boxes', '', 'actionAfterFormDiv');
    }
}
