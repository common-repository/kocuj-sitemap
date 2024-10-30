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
namespace KocujIL\V13a\Classes\Project\Components\Backend\EditorTextButtons;

// set namespaces aliases
use KocujIL\V13a\Classes\ComponentInitObject;
use KocujIL\V13a\Classes\Helper;
use KocujIL\V13a\Enums\Project\Components\Backend\EditorTextButtons\ExceptionCode;
use KocujIL\V13a\Enums\ProjectCategory;

// security
if ((!defined('ABSPATH')) || ((isset ($_SERVER ['SCRIPT_FILENAME'])) && (basename($_SERVER ['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die ();
}

/**
 * Text editor buttons (component initialization) class
 *
 * @access public
 */
class Init extends ComponentInitObject
{

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
            ExceptionCode::BUTTON_ID_EXISTS => 'Button identifier already exists',
            ExceptionCode::BUTTON_ID_DOES_NOT_EXIST => 'Button identifier does not exist'
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
        $this->getComponent('actions-filters-helper')->addActionWhenNeeded('admin_print_footer_scripts',
            ProjectCategory::BACKEND, 'editor-text-buttons', '', 'actionPrintFooterScripts',
            Helper::getInstance()->calculateMaxPriority('admin_print_footer_scripts'));
    }
}
