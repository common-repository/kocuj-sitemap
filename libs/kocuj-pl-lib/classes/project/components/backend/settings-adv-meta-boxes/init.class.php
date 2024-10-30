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
namespace KocujPlLib\V13a\Classes\Project\Components\Backend\SettingsAdvMetaBoxes;

// set namespaces aliases
use KocujIL\V13a as KocujIL;

// security
if ((!defined('ABSPATH')) || ((isset ($_SERVER ['SCRIPT_FILENAME'])) && (basename($_SERVER ['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die ();
}

/**
 * Settings advertisements meta boxes (component initialization) class
 *
 * @access public
 */
class Init extends KocujIL\Classes\ComponentInitObject
{

    /**
     * Required components
     *
     * @access protected
     * @var array
     */
    protected $requiredComponents = array(
        'KocujIL' => array(
            KocujIL\Enums\ProjectCategory::BACKEND => array(
                'settings-meta-boxes'
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
        $this->getComponent('actions-filters-helper')->addActionWhenNeeded('admin_init',
            KocujIL\Enums\ProjectCategory::BACKEND, 'settings-adv-meta-boxes', '', 'actionAdminInit', 1);
    }
}
