<?php

/**
 * component.class.php
 *
 * @author Dominik Kocuj
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2 or later
 * @copyright Copyright (c) 2016-2020 kocuj.pl
 * @package kocuj_internal_lib
 */

// set namespace
namespace KocujIL\V13a\Classes\Project\Components\Backend\PageRestore;

// set namespace
use KocujIL\V13a\Classes\ComponentObject;
use KocujIL\V13a\Enums\ProjectCategory;
use KocujIL\V13a\Enums\ProjectType;

// security
if ((!defined('ABSPATH')) || ((isset ($_SERVER ['SCRIPT_FILENAME'])) && (basename($_SERVER ['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die ();
}

/**
 * Restore page class
 *
 * @access public
 */
class Component extends ComponentObject
{

    /**
     * Options container id
     *
     * @access private
     * @var string
     */
    private $optionsContainerId = '';

    /**
     * Get form id
     *
     * @access public
     * @param object $componentObj
     *            Component object
     * @return string Form id
     */
    public static function getFormId($componentObj)
    {
        // exit
        return $componentObj->getComponent('project-helper')->getPrefix() . '__restore__' . $componentObj->getOptionsContainerId();
    }

    /**
     * Set options container identifier
     *
     * @access public
     * @param string $optionsContainerId
     *            Options container identifier
     * @return void
     */
    public function setOptionsContainerId($optionsContainerId)
    {
        // set value
        $this->optionsContainerId = $optionsContainerId;
        // set restore form
        $formId = self::getFormId($this);
        if ($this->getComponent('settings-form', ProjectCategory::BACKEND)->checkForm($formId)) {
            $this->getComponent('settings-form', ProjectCategory::BACKEND)->removeForm($formId);
        }
        $this->getComponent('settings-form', ProjectCategory::BACKEND)->addForm($formId,
            $optionsContainerId, array(
                'restore'
            ), array(
                'isrestore' => true,
                'restorelabel' => $this->getStrings('page-restore',
                    ProjectCategory::BACKEND)->getString('SET_OPTIONS_CONTAINER_ID_RESTORE_LABEL'),
                'restoretooltip' => $this->getStrings('page-restore',
                    ProjectCategory::BACKEND)->getString('SET_OPTIONS_CONTAINER_ID_RESTORE_TOOLTIP')
            ));
        // add tabs to form
        $this->getComponent('settings-form', ProjectCategory::BACKEND)->addTab($formId, 'restore');
        // add fields to form
        if ($this->getProjectObj()->getMainSettingType() === ProjectType::PLUGIN) {
            $this->getComponent('settings-form', ProjectCategory::BACKEND)->addHtmlToTab($formId,
                'restore', sprintf($this->getStrings('page-restore',
                    ProjectCategory::BACKEND)->getString('SHOW_PAGE_TEXT_PLUGIN_1'),
                    $this->getProjectObj()->getMainSettingTitleOriginal(),
                    $this->getProjectObj()->getMainSettingTitleOriginal()));
        } else {
            $this->getComponent('settings-form', ProjectCategory::BACKEND)->addHtmlToTab($formId,
                'restore', sprintf($this->getStrings('page-restore',
                    ProjectCategory::BACKEND)->getString('SHOW_PAGE_TEXT_THEME_1'),
                    $this->getProjectObj()->getMainSettingTitleOriginal(),
                    $this->getProjectObj()->getMainSettingTitleOriginal()));
        }
        $this->getComponent('settings-form', ProjectCategory::BACKEND)->addHtmlToTab($formId,
            'restore', $this->getStrings('page-restore',
                ProjectCategory::BACKEND)->getString('SHOW_PAGE_TEXT_2'));
    }

    /**
     * Get options container identifier
     *
     * @access public
     * @return string Options container identifier
     */
    public function getOptionsContainerId()
    {
        // exit
        return $this->optionsContainerId;
    }

    /**
     * Show page with restoring settings
     *
     * @access public
     * @return void
     */
    public function showPage()
    {
        // show form
        $this->getComponent('settings-form', ProjectCategory::BACKEND)->showForm();
    }
}
