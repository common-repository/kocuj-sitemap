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
namespace KocujIL\V13a\Classes\Project\Components\All\Window;

// set namespaces aliases
use KocujIL\V13a\Classes\ComponentInitObject;
use KocujIL\V13a\Enums\Project\Components\All\Window\ExceptionCode;
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
     * Required components
     *
     * @access protected
     * @var array
     */
    protected $requiredComponents = array(
        '' => array(
            ProjectCategory::ALL => array(
                'js-ajax'
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
            ExceptionCode::WINDOW_ID_EXISTS => 'Window identifier already exists',
            ExceptionCode::WINDOW_ID_DOES_NOT_EXIST => 'Window identifier does not exist'
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
        if ((is_admin()) || (is_network_admin())) {
            $this->getComponent('actions-filters-helper')->addActionWhenNeeded('admin_print_footer_scripts',
                ProjectCategory::ALL, 'window', '', 'actionPrintFooterScripts', 1);
        } else {
            $this->getComponent('actions-filters-helper')->addActionWhenNeeded('wp_print_footer_scripts',
                ProjectCategory::ALL, 'window', '', 'actionPrintFooterScripts', 1);
        }
    }
}
