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
namespace KocujIL\V13a\Classes\Project\Components\Backend\SettingsForm;

// set namespaces aliases
use KocujIL\V13a\Classes\ComponentObject;
use KocujIL\V13a\Classes\CurrentScreenIdHelper;
use KocujIL\V13a\Classes\DbDataHelper;
use KocujIL\V13a\Classes\Exception;
use KocujIL\V13a\Classes\Helper;
use KocujIL\V13a\Classes\JsHelper;
use KocujIL\V13a\Classes\LibUrls;
use KocujIL\V13a\Enums\Project\Components\All\Options\ContainerType;
use KocujIL\V13a\Enums\Project\Components\Backend\Message\Type;
use KocujIL\V13a\Enums\Project\Components\Backend\SettingsFields\FieldForWidget;
use KocujIL\V13a\Enums\Project\Components\Backend\SettingsFields\OptionArray;
use KocujIL\V13a\Enums\Project\Components\Backend\SettingsFields\OrderInWidget;
use KocujIL\V13a\Enums\Project\Components\Backend\SettingsForm\BulkAvailable;
use KocujIL\V13a\Enums\Project\Components\Backend\SettingsForm\ExceptionCode;
use KocujIL\V13a\Enums\Project\Components\Backend\SettingsForm\NonceRequired;
use KocujIL\V13a\Enums\Project\Components\Backend\SettingsForm\RedirectAfter;
use KocujIL\V13a\Enums\ProjectCategory;

// security
if ((!defined('ABSPATH')) || ((isset ($_SERVER ['SCRIPT_FILENAME'])) && (basename($_SERVER ['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die ();
}

/**
 * Settings form class
 *
 * @access public
 */
class Component extends ComponentObject
{

    /**
     * Element container for field with array
     */
    const ARRAY_DIV_ELEMENT_CONTAINER = '<div data-type="####DATA_TYPE_ELEMENT_CONTAINER####" ####DATA_ATTRS#### style="padding-top:5px;padding-bottom:5px;">';

    /**
     * Field for field with array
     */
    const ARRAY_DIV_FIELD = '<div data-type="####DATA_TYPE_FIELD####" style="float:left;width:50%;">';

    /**
     * Controls for field with array
     */
    const ARRAY_DIV_CONTROLS = '<div data-type="####DATA_TYPE_CONTROLS####" style="float:left;">';

    /**
     * Settings controllers
     *
     * @access private
     * @var array
     */
    private $controllers = array();

    /**
     * Data set controllers
     *
     * @access private
     * @var array
     */
    private $dataSetControllers = array();

    /**
     * Settings forms
     *
     * @access private
     * @var array
     */
    private $forms = array();

    /**
     * Forms tabs to display
     *
     * @access private
     * @var array
     */
    private $formsTabs = array();

    /**
     * Fields to add to form
     *
     * @access private
     * @var array
     */
    private $fieldsToAdd = array();

    /**
     * Fields to display on list
     *
     * @access private
     * @var array
     */
    private $fieldsToDisplayOnList = array();

    /**
     * Sortable fields on list
     *
     * @access private
     * @var array
     */
    private $sortableFieldsOnList = array();

    /**
     * Order field on list
     *
     * @access private
     * @var array
     */
    private $orderFieldOnList = array();

    /**
     * Display field on list callback
     *
     * @access private
     * @var array
     */
    private $displayFieldOnListCallback = array();

    /**
     * Forms to display on current settings page
     *
     * @access private
     * @var array
     */
    private $formsToDisplayOnCurrentScreen = array();

    /**
     * Forms arrays
     *
     * @access private
     * @var array
     */
    private $formsArrays = array();

    /**
     * Active forms arrays
     *
     * @access private
     * @var array
     */
    private $activeFormsArrays = array();

    /**
     * Array script added (true) or not (false)
     *
     * @access private
     * @var bool
     */
    private $libArrayScriptAdded = false;

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
        // execute parent constructor
        parent::__construct($projectObj);
        // add controllers
        $this->addController('save', array(
            __CLASS__,
            'controllerSave'
        ));
        $this->addController('restore', array(
            __CLASS__,
            'controllerRestore'
        ));
        // add data set controllers
        $this->addDataSetController('edit',
            $this->getStrings('settings-form', ProjectCategory::BACKEND)->getString('CONSTRUCT_EDIT'));
        $this->addDataSetController('delete',
            $this->getStrings('settings-form', ProjectCategory::BACKEND)->getString('CONSTRUCT_DELETE'), array(
                __CLASS__,
                'controllerDataSetDelete'
            ), NonceRequired::YES, RedirectAfter::YES, BulkAvailable::YES);
        // add button field type for current site
        $this->getComponent('settings-fields', ProjectCategory::BACKEND)->addFieldTypeForCurrentSite('button');
    }

    /**
     * Get form id
     *
     * @access public
     * @param object $componentObj
     *            Component object
     * @param string $id
     *            Form identifier
     * @return string Form id
     */
    public static function getFormId($componentObj, $id)
    {
        // exit
        return $componentObj->getComponent('project-helper')->getPrefix() . '__form';
    }

    /**
     * Get field id
     *
     * @access public
     * @param object $componentObj
     *            Component object
     * @param string $formId
     *            Form identifier
     * @param string $fieldId
     *            Field identifier
     * @return string Field id
     */
    public static function getFieldId($componentObj, $formId, $fieldId)
    {
        // exit
        return $componentObj->getComponent('project-helper')->getPrefix() . '__form_field__' . $formId . '__' . $fieldId;
    }

    /**
     * Get nonce identifier
     *
     * @access public
     * @param object $componentObj
     *            Component object
     * @param string $id
     *            Form identifier
     * @return string Nonce identifier
     */
    public static function getNonceId($componentObj, $id)
    {
        // exit
        return $componentObj->getComponent('project-helper')->getPrefix() . '__form__' . $id;
    }

    /**
     * Get data set nonce identifier
     *
     * @access public
     * @param object $componentObj
     *            Component object
     * @param string $id
     *            Container identifier
     * @return string Data set nonce identifier
     */
    public static function getDataSetNonceId($componentObj, $id)
    {
        // exit
        return $componentObj->getProjectObj()->getMainSettingInternalName() . '__data_set__' . $id;
    }

    /**
     * Get field HTML id for action
     *
     * @access public
     * @param object $componentObj
     *            Component object
     * @param string $id
     *            Form identifier
     * @return string Field HTML id for action
     */
    public static function getFieldIdAction($componentObj, $id)
    {
        // exit
        return $componentObj->getComponent('project-helper')->getPrefix() . '__form_action__' . $id;
    }

    /**
     * Get field name for action
     *
     * @access public
     * @param object $componentObj
     *            Component object
     * @return string Field name for action
     */
    public static function getFieldNameAction($componentObj)
    {
        // exit
        return $componentObj->getComponent('project-helper')->getPrefix() . '__form_action';
    }

    /**
     * Get field HTML id for data set element identifier
     *
     * @access public
     * @param object $componentObj
     *            Component object
     * @param string $id
     *            Form identifier
     * @return string Field HTML id for data set element identifier
     */
    public static function getFieldIdDataSetElementId($componentObj, $id)
    {
        // exit
        return $componentObj->getComponent('project-helper')->getPrefix() . '__data_set_element__' . $id;
    }

    /**
     * Get field name for data set element identifier
     *
     * @access public
     * @param object $componentObj
     *            Component object
     * @return string Field name for data set element identifier
     */
    public static function getFieldNameDataSetElementId($componentObj)
    {
        // exit
        return $componentObj->getComponent('project-helper')->getPrefix() . '__data_set_element';
    }

    /**
     * Get field name for form id
     *
     * @access public
     * @param object $componentObj
     *            Component object
     * @return string Field name for form id
     */
    public static function getFieldNameFormId($componentObj)
    {
        // exit
        return $componentObj->getComponent('project-helper')->getPrefix() . '__form_id';
    }

    /**
     * Get field HTML id for tab
     *
     * @access public
     * @param object $componentObj
     *            Component object
     * @param string $formId
     *            Form identifier
     * @param int $number
     *            Position number
     * @return string Field HTML id for tab
     */
    public static function getFieldIdTab($componentObj, $formId, $number)
    {
        // exit
        return $componentObj->getComponent('project-helper')->getPrefix() . '__form__' . $formId . '__tab__' . $number;
    }

    /**
     * Get field HTML id for tab div
     *
     * @access public
     * @param object $componentObj
     *            Component object
     * @param string $formId
     *            Form identifier
     * @param int $number
     *            Position number
     * @return string Field HTML id for tab div
     */
    public static function getFieldIdTabDiv($componentObj, $formId, $number)
    {
        // exit
        return $componentObj->getComponent('project-helper')->getPrefix() . '__form__' . $formId . '__tab_div__' . $number;
    }

    /**
     * Get field HTML id for default button
     *
     * @access public
     * @param object $componentObj
     *            Component object
     * @param string $formId
     *            Form identifier
     * @param string $buttonId
     *            Button identifier
     * @return string Field HTML id for default button
     */
    public static function getFieldIdDefaultButton($componentObj, $formId, $buttonId)
    {
        // exit
        return $componentObj->getComponent('project-helper')->getPrefix() . '__form__' . $formId . '__default_button__' . $buttonId;
    }

    /**
     * Get field HTML id for button
     *
     * @access public
     * @param object $componentObj
     *            Component object
     * @param string $formId
     *            Form identifier
     * @param string $buttonId
     *            Button identifier
     * @return string Field HTML id for button
     */
    public static function getFieldIdButton($componentObj, $formId, $buttonId)
    {
        // exit
        return $componentObj->getComponent('project-helper')->getPrefix() . '__form__' . $formId . '__button__' . $buttonId;
    }

    /**
     * Get array container id
     *
     * @access public
     * @param object $componentObj
     *            Component object
     * @param string $formId
     *            Form identifier
     * @param string $fieldId
     *            Field identifier
     * @return string Array container id
     */
    public static function getArrayContainerId($componentObj, $formId, $fieldId)
    {
        // exit
        return $componentObj->getComponent('project-helper')->getPrefix() . '__form__' . $formId . '__array_container__' . $fieldId;
    }

    /**
     * Get HTML id for button for adding new element to data set
     *
     * @access public
     * @param object $componentObj
     *            Component object
     * @param string $id
     *            Form identifier
     * @return string HTML id for button for adding new element to data set
     */
    public static function getButtonAddNewElementDataSetId($componentObj, $id)
    {
        // exit
        return $componentObj->getComponent('project-helper')->getPrefix() . '__form__' . $id . '__data_set_add_new';
    }

    /**
     * Get field HTML id with data set
     *
     * @access public
     * @param object $componentObj
     *            Component object
     * @return string Field HTML id with data set
     */
    public static function getFieldIdDataSet($componentObj)
    {
        // exit
        return $componentObj->getProjectObj()->getMainSettingInternalName() . '__data_set';
    }

    /**
     * Add controller
     *
     * @access public
     * @param string $id
     *            Controller identifier
     * @param array|string $callbackController
     *            Callback function or method name for controller; can be global function or method from any class
     * @return int Position in controllers list
     */
    public function addController($id, $callbackController)
    {
        // add controller
        if (!isset ($this->controllers [$id])) {
            $this->controllers [$id] = array();
        }
        $pos = count($this->controllers [$id]);
        $this->controllers [$id] [$pos] = $callbackController;
        // exit
        return $pos;
    }

    /**
     * Get controllers data
     *
     * @access public
     * @return array Controllers data; each controller data has multiple callbacks for controller
     */
    public function getControllers()
    {
        // exit
        return $this->controllers;
    }

    /**
     * Check if controller exists
     *
     * @access public
     * @param string $id
     *            Controller identifier
     * @return bool Controller exists (true) or not (false)
     */
    public function checkController($id)
    {
        // exit
        return isset ($this->controllers [$id]);
    }

    /**
     * Get controller data by id
     *
     * @access public
     * @param string $id
     *            Controller identifier
     * @return array|bool Controller data or false if not exists; controller data have multiple callbacks for controller
     */
    public function getController($id)
    {
        // exit
        return (isset ($this->controllers [$id])) ? $this->controllers [$id] : false;
    }

    /**
     * Remove controller
     *
     * @access public
     * @param string $id
     *            Controller identifier
     * @return void
     * @throws Exception
     */
    public function removeController($id)
    {
        // check if this controller identifier exists
        if (!isset ($this->controllers [$id])) {
            throw new Exception ($this, ExceptionCode::CONTROLLER_ID_DOES_NOT_EXIST, __FILE__, __LINE__, $id);
        }
        // remove controller
        unset ($this->controllers [$id]);
    }

    /**
     * Controller for save or restore options
     *
     * @access private
     * @param object $componentObj
     *            Component object
     * @param string $formId
     *            Form identifier
     * @param bool $isDataSet
     *            Container is for data set
     * @param int $dataSetElementId
     *            Data set element identifier if $isDataSet is set to true
     * @param bool $isRestore
     *            It is controller for restore (true) or save (false)
     * @param
     *            string &$outputText Output text
     * @return bool Controller has been executed correctly (true) or not (false)
     */
    private static function saveOrRestoreInController(
        $componentObj,
        $formId,
        $isDataSet,
        $dataSetElementId,
        $isRestore,
        &$outputText
    ) {
        // get form
        $form = $componentObj->getForm($formId);
        // get options definitions
        $optionsDefinitions = $componentObj->getComponent('options',
            ProjectCategory::ALL)->getDefinitions($form ['optionscontainerid']);
        // update options
        foreach ($optionsDefinitions as $optionId => $optionDef) {
            $post = $_REQUEST;
            if (((!$isRestore) && (isset ($post [$optionId]))) || ($isRestore)) {
                if ((!$isRestore) && ($optionDef ['array']) && (isset ($optionDef ['arraysettings'] ['autoadddeleteifempty'])) && ($optionDef ['arraysettings'] ['autoadddeleteifempty']) && (count($post [$optionId]) > 0)) {
                    unset ($post [$optionId] [count($post [$optionId]) - 1]);
                }
                $optionText = '';
                if (!$componentObj->getComponent('options',
                    ProjectCategory::ALL)->setOptionWithReturnedText($form ['optionscontainerid'],
                    $optionId, ($isRestore) ? $optionDef ['defaultvalue'] : sanitize_text_field($post [$optionId]),
                    $optionText, ($isDataSet) ? $dataSetElementId : 0)) {
                    $outputText .= '<li>"' . $optionDef ['label'] . '": ' . $optionText . '</li>';
                }
            }
        }
        // update database
        $componentObj->getComponent('options',
            ProjectCategory::ALL)->updateContainerInDb($form ['optionscontainerid']);
        // add status text
        if (isset ($outputText [0]) /* strlen($outputText) > 0 */) {
            $outputText = $componentObj->getStrings('settings-form',
                    ProjectCategory::BACKEND)->getString('SAVE_OR_RESTORE_IN_CONTROLLER_ERROR') . '<br /><ul>' . $outputText . '</ul>';
            return false;
        } else {
            $outputText = $isRestore ? $componentObj->getStrings('settings-form',
                ProjectCategory::BACKEND)->getString('SAVE_OR_RESTORE_IN_CONTROLLER_RESTORE') : $componentObj->getStrings('settings-form',
                ProjectCategory::BACKEND)->getString('SAVE_OR_RESTORE_IN_CONTROLLER_UPDATE');
        }
        // exit
        return true;
    }

    /**
     * Controller for save options
     *
     * @access private
     * @param object $componentObj
     *            Component object
     * @param string $formId
     *            Form identifier
     * @param bool $isDataSet
     *            Container is for data set
     * @param int $dataSetElementId
     *            Data set element identifier if $isDataSet is set to true
     * @param
     *            string &$outputText Output text
     * @return bool Controller has been executed correctly (true) or not (false)
     */
    private static function controllerSave($componentObj, $formId, $isDataSet, $dataSetElementId, &$outputText)
    {
        // exit
        return self::saveOrRestoreInController($componentObj, $formId, $isDataSet, $dataSetElementId, false,
            $outputText);
    }

    /**
     * Controller for restore options
     *
     * @access private
     * @param object $componentObj
     *            Component object
     * @param string $formId
     *            Form identifier
     * @param bool $isDataSet
     *            Container is for data set
     * @param int $dataSetElementId
     *            Data set element identifier if $isDataSet is set to true
     * @param
     *            string &$outputText Output text
     * @return bool Controller has been executed correctly (true) or not (false)
     */
    private static function controllerRestore($componentObj, $formId, $isDataSet, $dataSetElementId, &$outputText)
    {
        // exit
        return self::saveOrRestoreInController($componentObj, $formId, $isDataSet, $dataSetElementId, true,
            $outputText);
    }

    /**
     * Add data set list controller
     *
     * @param string $id
     *            Controller identifier
     * @param string $title
     *            Link title
     * @param array|string $callbackController
     *            Callback function or method name for controller; can be global function or method from any class; can be set to NULL to make an edit controller - default: NULL
     * @param int $nonceRequired
     *            Nonce is required or not; must be one of the following constants from \KocujIL\V13a\Enums\Project\Components\Backend\SettingsForm\NonceRequired: NO (when nonce are not required) or YES (when nonce are required) - default: \KocujIL\V13a\Enums\Project\Components\Backend\SettingsForm\NonceRequired::NO
     * @param int $redirectAfter
     *            Redirection after action is available or not; must be one of the following constants from \KocujIL\V13a\Enums\Project\Components\Backend\SettingsForm\RedirectAfter: NO (when redirection after action is not available) or YES (when redirection after action is available) - default: \KocujIL\V13a\Enums\Project\Components\Backend\SettingsForm\BulkAvailable::NO
     * @param int $bulkAvailable
     *            Bulk action is available or not; must be one of the following constants from \KocujIL\V13a\Enums\Project\Components\Backend\SettingsForm\BulkAvailable: NO (when bulk action is not available) or YES (when bulk action is available) - default: \KocujIL\V13a\Enums\Project\Components\Backend\SettingsForm\BulkAvailable::NO
     * @return void
     * @todo disallow $id = "add-new"
     * @access public
     */
    public function addDataSetController(
        $id,
        $title,
        $callbackController = null,
        $nonceRequired = NonceRequired::NO,
        $redirectAfter = RedirectAfter::NO,
        $bulkAvailable = BulkAvailable::NO
    ) {
        // add data set controller
        $this->dataSetControllers [$id] = array(
            'title' => $title,
            'callback' => $callbackController
        );
        if ($nonceRequired !== NonceRequired::NO) {
            $this->dataSetControllers [$id] ['noncerequired'] = $nonceRequired;
        }
        if ($redirectAfter !== RedirectAfter::NO) {
            $this->dataSetControllers [$id] ['redirectafter'] = $redirectAfter;
        }
        if ($bulkAvailable !== BulkAvailable::NO) {
            $this->dataSetControllers [$id] ['bulkavailable'] = $bulkAvailable;
        }
    }

    /**
     * Get data set controllers data
     *
     * @access public
     * @return array Controllers data; each controller data has the following fields: "bulkavailable" (int type; set if bulk action is available), "callback" (array or string type; callback for controller), "noncerequired" (int type; set if nonce is required by data set controller), "redirectafter" (int type; set if there should be redirection after action), "title" (string type; link title)
     */
    public function getDataSetControllers()
    {
        // prepare data set controllers
        $dataSetControllers = $this->dataSetControllers;
        foreach ($dataSetControllers as $key => $val) {
            if (!isset ($val ['noncerequired'])) {
                $dataSetControllers [$key] ['noncerequired'] = NonceRequired::NO;
            }
            if (!isset ($val ['redirectafter'])) {
                $dataSetControllers [$key] ['redirectafter'] = RedirectAfter::NO;
            }
            if (!isset ($val ['bulkavailable'])) {
                $dataSetControllers [$key] ['bulkavailable'] = BulkAvailable::NO;
            }
        }
        // exit
        return $dataSetControllers;
    }

    /**
     * Check if data set controller exists
     *
     * @access public
     * @param string $id
     *            Controller identifier
     * @return bool Controller exists (true) or not (false)
     */
    public function checkDataSetController($id)
    {
        // exit
        return isset ($this->dataSetControllers [$id]);
    }

    /**
     * Get data set controller data by id
     *
     * @access public
     * @param string $id
     *            Controller identifier
     * @return array|bool Controller data or false if not exists; controller data have the following fields: "bulkavailable" (int type; set if bulk action is available), "callback" (array or string type; callback for controller), "noncerequired" (int type; set if nonce is required by data set controller), "redirectafter" (int type; set if there should be redirection after action), "title" (string type; link title)
     */
    public function getDataSetController($id)
    {
        // get data set controllers
        $dataSetControllers = $this->getDataSetControllers();
        // exit
        return (isset ($dataSetControllers [$id])) ? $dataSetControllers [$id] : false;
    }

    /**
     * Remove data set controller
     *
     * @access public
     * @param string $id
     *            Controller identifier
     * @return void
     * @throws Exception
     */
    public function removeDataSetController($id)
    {
        // check if this data set controller identifier exists
        if (!isset ($this->dataSetControllers [$id])) {
            throw new Exception ($this,
                ExceptionCode::LIST_CONTROLLER_ID_DOES_NOT_EXIST,
                __FILE__, __LINE__, $id);
        }
        // remove data set controller
        unset ($this->dataSetControllers [$id]);
    }

    /**
     * Data set controller for delete element
     *
     * @access private
     * @param object $componentObj
     *            Component object
     * @param string $containerId
     *            Container identifier
     * @param int $dataSetElementId
     *            Data set element identifier
     * @param
     *            string &$outputText Output text
     * @return void
     */
    private static function controllerDataSetDelete($componentObj, $containerId, $dataSetElementId, &$outputText)
    {
        // delete element
        $componentObj->getComponent('options',
            ProjectCategory::ALL)->removeDataSetElement($containerId, $dataSetElementId);
    }

    /**
     * Add form
     *
     * @access public
     * @param string $id
     *            Form identifier
     * @param string $optionsContainerId
     *            Options container identifier for form
     * @param array $settingsMenusIds
     *            List of settings menus identifiers for which this form will be allowed to display - default: empty
     * @param array $defaultButtons
     *            Definition of default buttons to add below form; there are available the following attributes: "isrestore" (bool type; if true, restore button will be displayed), "issubmit" (bool type; if true, submit button will be displayed), "restorelabel" (string type; restore button label), "restoretooltip" (string type; restore button tooltip), "submitlabel" (string type; submit button label), "submittooltip" (string type; submit button tooltip) - default: empty
     * @param array $buttons
     *            Definition of additional buttons to add below form; each button data identifier will be used as button identifier; each button data has the following fields: "events" (array type; HTML events), "isprimary" (bool type; if true, button is primary), "label" (string type; button label), "tooltip" (string type; button tooltip) - default: empty
     * @param array $additional
     *            Additional settings for form; there are the following additional settings which can be used: "global_datasetcontrollers" (array type; if it is a data set, it is list of data set controllers), "global_isdatasetadd" (bool type; if it is data set and it is set to true, there will be possibility to add new element to data set)
     * @return void
     * @throws Exception
     */
    public function addForm(
        $id,
        $optionsContainerId,
        array $settingsMenusIds = array(),
        array $defaultButtons = array(),
        $buttons = array(),
        array $additional = array()
    ) {
        // check if this form identifier does not already exist
        if (isset ($this->forms [$id])) {
            throw new Exception ($this,
                ExceptionCode::FORM_ID_EXISTS, __FILE__,
                __LINE__, $id);
        }
        // add form
        $this->forms [$id] = array(
            'optionscontainerid' => $optionsContainerId
        );
        if (!empty ($settingsMenusIds)) {
            $this->forms [$id] ['settingsmenusids'] = $settingsMenusIds;
        }
        if (!empty ($defaultButtons)) {
            $this->forms [$id] ['defaultbuttons'] = $defaultButtons;
        }
        if (!empty ($buttons)) {
            $this->forms [$id] ['buttons'] = $buttons;
        }
        if (!empty ($additional)) {
            $this->forms [$id] ['additional'] = $additional;
        }
    }

    /**
     * Get forms data
     *
     * @access public
     * @return array Forms data; each form data has the following fields: "additional" (array type; additional settings), "buttons" (array type; definition of buttons below form), "defaultbuttons" (array type; definition of default buttons below form), "optionscontainerid" (string type; identifier of options container for form), "settingsmenusids" (array type; list of settings menu pages where this form will be displayed), "tabs" (array type; tabs with form fields)
     */
    public function getForms()
    {
        // prepare forms
        $forms = $this->forms;
        foreach ($forms as $key => $val) {
            if (!isset ($val ['settingsmenusids'])) {
                $forms [$key] ['settingsmenusids'] = array();
            }
            if (!isset ($val ['tabs'])) {
                $forms [$key] ['tabs'] = array();
            }
            if (!isset ($val ['defaultbuttons'])) {
                $forms [$key] ['defaultbuttons'] = array();
            }
            if (!isset ($val ['buttons'])) {
                $forms [$key] ['buttons'] = array();
            }
            if (!isset ($val ['additional'])) {
                $forms [$key] ['additional'] = array();
            }
        }
        // exit
        return $forms;
    }

    /**
     * Check if form exists
     *
     * @access public
     * @param string $id
     *            Form identifier
     * @return bool Form exists (true) or not (false)
     */
    public function checkForm($id)
    {
        // exit
        return isset ($this->forms [$id]);
    }

    /**
     * Get form data by id
     *
     * @access public
     * @param string $id
     *            Form identifier
     * @return array|bool Form data or false if not exists; form data have the following fields: "additional" (array type; additional settings), "buttons" (array type; definition of buttons below form), "defaultbuttons" (array type; definition of default buttons below form), "optionscontainerid" (string type; identifier of options container for form), "settingsmenusids" (array type; list of settings menu pages where this form will be displayed), "tabs" (array type; tabs with form fields)
     */
    public function getForm($id)
    {
        // get forms
        $forms = $this->getForms();
        // exit
        return (isset ($forms [$id])) ? $forms [$id] : false;
    }

    /**
     * Remove form
     *
     * @access public
     * @param string $id
     *            Form identifier
     * @return void
     * @throws Exception
     */
    public function removeForm($id)
    {
        // check if this form identifier exists
        if (!isset ($this->forms [$id])) {
            throw new Exception ($this,
                ExceptionCode::FORM_ID_DOES_NOT_EXIST,
                __FILE__, __LINE__, $id);
        }
        // remove form
        unset ($this->forms [$id]);
    }

    /**
     * Add tab to form
     *
     * @access public
     * @param string $formId
     *            Form identifier
     * @param string $tabId
     *            Tab identifier; must be unique in this project
     * @param string $title
     *            Tab title; if empty, default title will be used - default: empty
     * @return void
     * @throws Exception
     */
    public function addTab($formId, $tabId, $title = '')
    {
        // check if this form identifier exists
        if (!isset ($this->forms [$formId])) {
            throw new Exception ($this,
                ExceptionCode::FORM_ID_DOES_NOT_EXIST,
                __FILE__, __LINE__, $formId);
        }
        // optionally initialize array
        if (!isset ($this->forms [$formId] ['tabs'])) {
            $this->forms [$formId] ['tabs'] = array();
        }
        // check if this tab identifier does not already exist
        if (isset ($this->forms [$formId] ['tabs'] [$tabId])) {
            throw new Exception ($this,
                ExceptionCode::TAB_ID_EXISTS, __FILE__,
                __LINE__, $tabId);
        }
        // add settings tab
        $this->forms [$formId] ['tabs'] [$tabId] = array(
            'title' => $title
        );
    }

    /**
     * Get tab data
     *
     * @access public
     * @param string $id
     *            Form identifier
     * @return array Tab data; each tab data has the following fields: "fields" (array type; fields data; each element contains text with field in HTML to display), "title" (string type; tab title)
     * @throws Exception
     */
    public function getTabs($id)
    {
        // check if this form identifier exists
        if (!isset ($this->forms [$id])) {
            throw new Exception ($this,
                ExceptionCode::FORM_ID_DOES_NOT_EXIST,
                __FILE__, __LINE__, $id);
        }
        // prepare tabs
        $tabs = $this->forms [$id] ['tabs'];
        foreach ($tabs as $key => $val) {
            if (!isset ($val ['fields'])) {
                $tabs [$key] ['fields'] = array();
            }
        }
        // exit
        return $tabs;
    }

    /**
     * Check if tab exists
     *
     * @access public
     * @param string $formId
     *            Form identifier
     * @param string $tabId
     *            Tab identifier
     * @return bool Tab exists (true) or not (false)
     * @throws Exception
     */
    public function checkTab($formId, $tabId)
    {
        // check if this form identifier exists
        if (!isset ($this->forms [$formId])) {
            throw new Exception ($this,
                ExceptionCode::FORM_ID_DOES_NOT_EXIST,
                __FILE__, __LINE__, $formId);
        }
        // exit
        return isset ($this->forms [$formId] ['tabs'] [$tabId]);
    }

    /**
     * Get tab data by id
     *
     * @access public
     * @param string $formId
     *            Form identifier
     * @param string $tabId
     *            Tab identifier
     * @return array|bool Tab data or false if not exists; tab data have the following fields: "fields" (array type; fields data; each element contains text with field in HTML to display), "title" (string type; tab title)
     * @throws Exception
     */
    public function getTab($formId, $tabId)
    {
        // check if this form identifier exists
        if (!isset ($this->forms [$formId])) {
            throw new Exception ($this,
                ExceptionCode::FORM_ID_DOES_NOT_EXIST,
                __FILE__, __LINE__, $formId);
        }
        // get tabs
        $tabs = $this->getTabs($formId);
        // exit
        return (isset ($tabs [$tabId])) ? $tabs [$tabId] : false;
    }

    /**
     * Remove tab
     *
     * @access public
     * @param string $formId
     *            Form identifier
     * @param string $tabId
     *            Tab identifier
     * @return void
     * @throws Exception
     */
    public function removeTab($formId, $tabId)
    {
        // check if this form identifier exists
        if (!isset ($this->forms [$formId])) {
            throw new Exception ($this,
                ExceptionCode::FORM_ID_DOES_NOT_EXIST,
                __FILE__, __LINE__, $formId);
        }
        // check if this tab identifier exists
        if (!isset ($this->forms [$formId] ['tabs'] [$tabId])) {
            throw new Exception ($this,
                ExceptionCode::TAB_ID_DOES_NOT_EXIST,
                __FILE__, __LINE__, $tabId);
        }
        // remove tab
        unset ($this->forms [$formId] ['tabs'] [$tabId]);
    }

    /**
     * Check if there is parameter for action and identifier to select current element in data set
     *
     * @access private
     * @param object $componentObj
     *            Component object
     * @param string $containerToCheck
     *            Container identifier to check; if empty, no container identifier is checked
     * @param bool $allowArrayOfIds
     *            Allow array of identifiers (true) or not (false)
     * @return bool There is parameter for action and identifier to select current element in data set (true) or not (false)
     */
    private static function checkDataSetActionAndId($componentObj, $containerToCheck, $allowArrayOfIds)
    {
        // exit
        return ((((isset ($_REQUEST ['action'])) && (isset ($_REQUEST ['action'] [0]) /* strlen($_REQUEST['action']) > 0 */)) || ((isset ($_REQUEST ['action2'])) && (isset ($_REQUEST ['action2'] [0]) /* strlen($_REQUEST['action2']) > 0 */))) && (isset ($_REQUEST ['id'])) && ((is_numeric($_REQUEST ['id'])) || (($allowArrayOfIds) && (is_array($_REQUEST ['id'])))) && (isset ($_REQUEST [self::getFieldIdDataSet($componentObj)])) && ($componentObj->getComponent('options',
                ProjectCategory::ALL)->checkContainer($_REQUEST [self::getFieldIdDataSet($componentObj)])) && ((!isset ($containerToCheck [0]) /* strlen($containerToCheck) === 0 */) || ((isset ($containerToCheck [0]) /* strlen($containerToCheck) > 0 */) && ($_REQUEST [self::getFieldIdDataSet($componentObj)] === $containerToCheck))));
    }

    /**
     * Check if there is parameter for action to add new element in data set
     *
     * @access private
     * @return bool There is parameter for action to add new element in data set
     */
    private static function checkDataSetActionAddNew()
    {
        // exit
        return ((isset ($_REQUEST ['action'])) && (sanitize_text_field($_REQUEST ['action']) === 'add-new'));
    }

    /**
     * Add field or HTML code to form tab
     *
     * @access private
     * @param string $formId
     *            Form identifier
     * @param string $tabId
     *            Form tab identifier
     * @param string $fieldType
     *            Field type
     * @param string $fieldId
     *            Field identifier
     * @param bool $forceFieldValue
     *            Force field value (true) or not (false)
     * @param string $fieldValue
     *            Field value
     * @param string $tipText
     *            Tooltip text
     * @param string $html
     *            HTML code to add in field area
     * @param string $htmlLeft
     *            HTML code to add in label area
     * @param array $events
     *            HTML events
     * @param array $additional
     *            Additional settings; each field type can use different additional settings; there are the following additional settings which can be always used: "global_addinfo" (string type; text to add below field), "global_addlabel" (string type; text to add to label), "global_displayonlist" (bool type; if true, field information will be displayed on list if this container is for data set), "global_displayonlistcallback" (array or string type; callback method or function for displaying on list if this container is for data set), "global_hidelabel" (bool type; if true, label will be hidden), "global_widgetobj" (object type; it must be set to widget object, when it is widget)
     * @return int Identifier
     * @throws Exception
     */
    private function addFieldOrHtmlToTab(
        $formId,
        $tabId,
        $fieldType,
        $fieldId,
        $forceFieldValue,
        $fieldValue,
        $tipText,
        $html,
        $htmlLeft,
        array $events,
        array $additional
    ) {
        // get new field identifier
        if (!isset ($this->forms [$formId] ['tabs'] [$tabId] ['fields'])) {
            $this->forms [$formId] ['tabs'] [$tabId] ['fields'] = array();
        }
        $id = count($this->forms [$formId] ['tabs'] [$tabId] ['fields']);
        // check if this form identifier exists
        if (!isset ($this->forms [$formId])) {
            throw new Exception ($this,
                ExceptionCode::FORM_ID_DOES_NOT_EXIST,
                __FILE__, __LINE__, $formId);
        }
        // get form data
        $form = $this->getForm($formId);
        // get container identifier
        $containerId = $form ['optionscontainerid'];
        // get container data
        $container = $this->getComponent('options',
            ProjectCategory::ALL)->getContainer($containerId);
        // add field
        if (isset ($fieldType [0]) /* strlen($fieldType) > 0 */) {
            $this->getComponent('settings-fields',
                ProjectCategory::BACKEND)->addFieldTypeForCurrentSite($fieldType);
        }
        $addElementToDataSet = ((($container ['type'] === ContainerType::DATA_SET_SITE) || ($container ['type'] === ContainerType::DATA_SET_NETWORK)) && (isset ($form ['additional'] ['global_isdatasetadd'])) && ($form ['additional'] ['global_isdatasetadd']) && (self::checkDataSetActionAddNew()));
        if ((did_action('current_screen') > 0) || ((($container ['type'] === ContainerType::DATA_SET_SITE) || ($container ['type'] === ContainerType::DATA_SET_NETWORK)) && (!self::checkDataSetActionAndId($this,
                    $containerId, false)) && (!$addElementToDataSet))) {
            $fieldText = $this->fieldToAddNow($formId, $tabId, $fieldType, $fieldId, $forceFieldValue, $fieldValue,
                $tipText, $html, $htmlLeft, $events, $additional);
        } else {
            if (!isset ($this->fieldsToAdd [$formId])) {
                $this->fieldsToAdd [$formId] = array();
            }
            $this->fieldsToAdd [$formId] [] = array(
                $id,
                $formId,
                $tabId,
                $fieldType,
                $fieldId,
                $forceFieldValue,
                $fieldValue,
                $tipText,
                $html,
                $htmlLeft,
                $events,
                $additional
            );
            $fieldText = '';
        }
        // remember field text
        $this->forms [$formId] ['tabs'] [$tabId] ['fields'] [$id] = $fieldText;
        // exit
        return $id;
    }

    /**
     * Action for adding fields
     *
     * @access public
     * @return void
     * @throws Exception
     */
    public function actionAddFields()
    {
        // add fields
        if (!empty ($this->fieldsToAdd)) {
            foreach ($this->fieldsToAdd as $formData) {
                foreach ($formData as $fieldToAdd) {
                    $this->forms [$fieldToAdd [1]] ['tabs'] [$fieldToAdd [2]] ['fields'] [$fieldToAdd [0]] = $this->fieldToAddNow($fieldToAdd [1],
                        $fieldToAdd [2], $fieldToAdd [3], $fieldToAdd [4], $fieldToAdd [5], $fieldToAdd [6],
                        $fieldToAdd [7], $fieldToAdd [8], $fieldToAdd [9], $fieldToAdd [10], $fieldToAdd [11]);
                }
            }
            $this->fieldsToAdd = array();
        }
    }

    /**
     * Add field or HTML code to form tab now
     *
     * @access private
     * @param string $formId
     *            Form identifier
     * @param string $tabId
     *            Form tab identifier
     * @param string $fieldType
     *            Field type
     * @param string $fieldId
     *            Field identifier
     * @param bool $forceFieldValue
     *            Force field value (true) or not (false)
     * @param string $fieldValue
     *            Field value
     * @param string $tipText
     *            Tooltip text
     * @param string $html
     *            HTML code to add in field area
     * @param string $htmlLeft
     *            HTML code to add in label area
     * @param array $events
     *            HTML events
     * @param array $additional
     *            Additional settings; each field type can use different additional settings; there are the following additional settings which can be always used: "global_addinfo" (string type; text to add below field), "global_addlabel" (string type; text to add to label), "global_displayonlist" (bool type; if true, field information will be displayed on list if this container is for data set), "global_displayonlistcallback" (array or string type; callback method or function for displaying on list if this container is for data set), "global_hidelabel" (bool type; if true, label will be hidden), "global_widgetobj" (object type; it must be set to widget object, when it is widget)
     * @return int Identifier
     * @throws Exception
     */
    private function fieldToAddNow(
        $formId,
        $tabId,
        $fieldType,
        $fieldId,
        $forceFieldValue,
        $fieldValue,
        $tipText,
        $html,
        $htmlLeft,
        array $events,
        array $additional
    ) {
        // check if this form identifier exists
        if (!isset ($this->forms [$formId])) {
            throw new Exception ($this,
                ExceptionCode::FORM_ID_DOES_NOT_EXIST,
                __FILE__, __LINE__, $formId);
        }
        // check if this settings tab identifier exists
        if (!isset ($this->forms [$formId] ['tabs'] [$tabId])) {
            throw new Exception ($this,
                ExceptionCode::TAB_ID_DOES_NOT_EXIST,
                __FILE__, __LINE__, $tabId);
        }
        // initialize
        $fieldText = '';
        // get form data
        $form = $this->getForm($formId);
        // get container identifier
        $containerId = $form ['optionscontainerid'];
        // get container data
        $container = $this->getComponent('options',
            ProjectCategory::ALL)->getContainer($containerId);
        // check if it is adding new element to data set
        $addElementToDataSet = ((($container ['type'] === ContainerType::DATA_SET_SITE) || ($container ['type'] === ContainerType::DATA_SET_NETWORK)) && (isset ($form ['additional'] ['global_isdatasetadd'])) && ($form ['additional'] ['global_isdatasetadd']) && (self::checkDataSetActionAddNew()));
        // check if field should be added
        $addField = true;
        $dataSetId = 0;
        if (($container ['type'] === ContainerType::DATA_SET_SITE) || ($container ['type'] === ContainerType::DATA_SET_NETWORK)) {
            if ((!self::checkDataSetActionAndId($this, $containerId, false)) && (!$addElementToDataSet)) {
                $addField = false;
                $fieldText = '';
            }
        }
        // add field
        if ($addField) {
            // get option value
            if ((($container ['type'] === ContainerType::DATA_SET_SITE) || ($container ['type'] === ContainerType::DATA_SET_NETWORK)) && (self::checkDataSetActionAndId($this,
                    $containerId, false))) {
                $dataSetId = sanitize_text_field($_REQUEST ['id']);
                if (!$this->getComponent('options',
                    ProjectCategory::ALL)->checkDataSetElementId($containerId, $_REQUEST ['id'])) {
                    wp_die($this->getStrings('settings-form',
                        ProjectCategory::BACKEND)->getString('FIELD_TO_ADD_NOW_ELEMENT_DOES_NOT_EXIST'));
                }
            }
            if ($forceFieldValue) {
                $optionValue = $fieldValue;
            } else {
                if (isset ($fieldType [0]) /* strlen($fieldType) > 0 */) {
                    if ($addElementToDataSet) {
                        $def = $this->getComponent('options',
                            ProjectCategory::ALL)->getDefinition($containerId, $fieldId);
                        $optionValue = $def ['defaultvalue'];
                    } else {
                        $optionValue = $this->getComponent('options',
                            ProjectCategory::ALL)->getOption($containerId, $fieldId, $dataSetId);
                    }
                } else {
                    $optionValue = '';
                }
            }
            // check if this field is for widget settings
            $widget = ($container ['type'] === ContainerType::WIDGET) ? FieldForWidget::YES : FieldForWidget::NO;
            // get option definition
            $optionDefinition = $this->getComponent('options',
                ProjectCategory::ALL)->getDefinition($containerId, $fieldId);
            // optionally get option definition
            $arrayMode = OptionArray::NO;
            if (isset ($fieldType [0]) /* strlen($fieldType) > 0 */) {
                // check if option is array
                if (!$forceFieldValue) {
                    if ((isset ($fieldType [0]) /* strlen($fieldType) > 0 */) && ($widget === FieldForWidget::NO)) {
                        if ($optionDefinition ['array'] === \KocujIL\V13a\Enums\Project\Components\All\Options\OptionArray::YES) {
                            $arrayMode = OptionArray::YES;
                        }
                        if (($arrayMode === OptionArray::YES) && ($optionValue === false)) {
                            $optionValue = $optionDefinition ['defaultvalue'];
                        }
                    }
                }
            }
            // check if this field is not for array in widget settings
            if (($widget === FieldForWidget::YES) && ($arrayMode === OptionArray::YES)) {
                throw new Exception ($this,
                    ExceptionCode::CANNOT_USE_ARRAY_OPTION_IN_WIDGET,
                    __FILE__, __LINE__);
            }
            // get label and HTML element order in widget
            $type = $this->getComponent('settings-fields',
                ProjectCategory::BACKEND)->getType($optionDefinition ['type']);
            $orderInWidget = $type ['orderinwidget'];
            // add field beginning
            $fieldText = $widget === FieldForWidget::YES ? '<p>' : '<tr>';
            // add field label
            $optionLabel = '';
            if ((((!isset ($fieldType [0]) /* strlen($fieldType) === 0 */) && (isset ($htmlLeft [0]) /* strlen($htmlLeft) > 0 */))) || ((isset ($fieldType [0]) /* strlen($fieldType) > 0 */) && ((!isset ($additional ['global_hidelabel'])) || ((isset ($additional ['global_hidelabel'])) && (!$additional ['global_hidelabel']))))) {
                $optionLabel = (isset ($fieldType [0]) /* strlen($fieldType) > 0 */) ? '<label for="' . esc_attr($widget === FieldForWidget::YES ? $additional ['global_widgetobj']->get_field_id($fieldId) : self::getFieldId($this,
                        $formId,
                        $fieldId)) . '">' . $optionDefinition ['label'] . (((isset ($additional ['global_addlabel'])) && (isset ($additional ['global_addlabel'] [0]) /* strlen($additional['global_addlabel']) > 0 */)) ? '<br />' . $additional ['global_addlabel'] : '') . (($widget === FieldForWidget::YES) && ($orderInWidget === OrderInWidget::FIRST_LABEL) ? ':' : '') . '</label>' : $htmlLeft;
                if ($widget === FieldForWidget::YES) {
                    if ($orderInWidget === OrderInWidget::FIRST_LABEL) {
                        $fieldText .= $optionLabel;
                    }
                } else {
                    $fieldText .= '<th scope="row">' . $optionLabel . '</th>';
                }
            }
            // prepare "colspan" tag
            if ($widget === FieldForWidget::YES) {
                $colspanTag = '';
            } else {
                $colspanTag = ((!isset ($fieldType [0]) /* strlen($fieldType) === 0 */) && (!isset ($htmlLeft [0]) /* strlen($htmlLeft) === 0 */)) ? ' colspan="2"' : '';
            }
            // prepare class and style for option field
            $class = ($widget === FieldForWidget::YES) ? 'widefat' : '';
            $style = ($widget === FieldForWidget::YES) ? '' : ($arrayMode === OptionArray::YES ? '' : 'width:50%;');
            $classWithTag = isset ($class [0]) /* strlen($class) > 0 */ ?
                ' class="' . esc_attr($class) . '"' : '';
            $styleWithTag = isset ($style [0]) /* strlen($style) > 0 */ ?
                ' style="' . esc_attr($style) . '"' : '';
            $classAndStyle = array(
                'class' => $class,
                'style' => $style,
                'classwithtag' => $classWithTag,
                'stylewithtag' => $styleWithTag,
                'all' => $classWithTag . $styleWithTag
            );
            // add field content
            if (isset ($fieldType [0]) /* strlen($fieldType) > 0 */) {
                if ($arrayMode === OptionArray::YES) {
                    // initialize
                    $fieldContent = '';
                    $pos = 0;
                    // show array
                    $fieldContent .= '<div id="' . esc_attr(self::getArrayContainerId($this, $formId, $fieldId)) . '">';
                    foreach ($optionValue as $val) {
                        $fieldContent .= str_replace('####DATA_ATTRS####',
                                'data-number="' . esc_attr($pos) . '" data-prev="" data-next=""',
                                str_replace('####DATA_TYPE_ELEMENT_CONTAINER####', 'element-container',
                                    self::ARRAY_DIV_ELEMENT_CONTAINER)) . str_replace('####DATA_TYPE_FIELD####',
                                'field', self::ARRAY_DIV_FIELD) . $this->getComponent('settings-fields',
                                ProjectCategory::BACKEND)->getField($fieldType, $fieldId,
                                ($widget === FieldForWidget::YES) ? $fieldId : self::getFieldId($this,
                                    $formId, $fieldId), $val, $tipText, $classAndStyle, $widget, $arrayMode, $events,
                                $additional) . '</div>' . str_replace('####DATA_TYPE_CONTROLS####', 'controls',
                                self::ARRAY_DIV_CONTROLS) . '</div>' . '<div style="clear:both;">' . '</div>' . '</div>';
                        ++$pos;
                    }
                    $fieldContent .= '</div>';
                    // remember array data
                    if (!isset ($this->formsArrays [$formId])) {
                        $this->formsArrays [$formId] = array();
                    }
                    $this->formsArrays [$formId] [$fieldId] = array(
                        'arraysettings' => $optionDefinition ['arraysettings'],
                        'count' => count($optionValue),
                        'emptyfield' => $this->getComponent('settings-fields',
                            ProjectCategory::BACKEND)->getField($fieldType, $fieldId,
                            $widget === FieldForWidget::YES ? $fieldId : self::getFieldId($this,
                                $formId, $fieldId), '', $tipText, $classAndStyle, $widget, $arrayMode, $events,
                            $additional)
                    );
                } else {
                    $fieldContent = $this->getComponent('settings-fields',
                        ProjectCategory::BACKEND)->getField($fieldType, $fieldId,
                        $widget === FieldForWidget::YES ? $fieldId : self::getFieldId($this,
                            $formId, $fieldId), $optionValue, $tipText, $classAndStyle, $widget, $arrayMode, $events,
                        $additional);
                }
            } else {
                $fieldContent = $html;
            }
            if ($widget === FieldForWidget::YES) {
                $fieldText .= $fieldContent;
            } else {
                $fieldText .= '<td' . $colspanTag . '>' . $fieldContent . '</td>';
            }
            // optionally add label
            if (($widget === FieldForWidget::YES) && ($orderInWidget === OrderInWidget::FIRST_ELEMENT)) {
                $fieldText .= $optionLabel;
            }
            // add field ending
            $fieldText .= ($widget === FieldForWidget::YES) ? '</p>' : '</tr>';
        }
        // optionally set this for list
        if ((($container ['type'] === ContainerType::DATA_SET_SITE) || ($container ['type'] === ContainerType::DATA_SET_NETWORK)) && ((isset ($fieldType [0]) /* strlen($fieldType) > 0 */) && (!$forceFieldValue) && (isset ($additional ['global_displayonlist'])) && ($additional ['global_displayonlist']))) {
            // get option definition
            $def = $this->getComponent('options', ProjectCategory::ALL)->getDefinition($containerId,
                $fieldId);
            // add information for displaying on list
            if (!isset ($this->fieldsToDisplayOnList [$formId])) {
                $this->fieldsToDisplayOnList [$formId] = array();
            }
            $this->fieldsToDisplayOnList [$formId] [$fieldId] = $def ['label'];
            // optionally add information for sortable option in data set
            if ((isset ($def ['datasetsettings'] ['global_searchkey'])) && ($def ['datasetsettings'] ['global_searchkey'])) {
                if (!isset ($this->sortableFieldsOnList [$formId])) {
                    $this->sortableFieldsOnList [$formId] = array();
                }
                $this->sortableFieldsOnList [$formId] [] = $fieldId;
            }
            // optionally add information about order column
            if ((isset ($def ['datasetsettings'] ['global_ordervalue'])) && ($def ['datasetsettings'] ['global_ordervalue'])) {
                $this->orderFieldOnList [$formId] = $fieldId;
            }
            // optionally add callback for displaying on list
            if (isset ($additional ['global_displayonlistcallback'])) {
                if (!isset ($this->displayFieldOnListCallback [$formId])) {
                    $this->displayFieldOnListCallback [$formId] = array();
                }
                $this->displayFieldOnListCallback [$formId] [$fieldId] = $additional ['global_displayonlistcallback'];
            }
        }
        // exit
        return $fieldText;
    }

    /**
     * Add option field to form tab
     *
     * @access public
     * @param string $formId
     *            Form identifier
     * @param string $tabId
     *            Form tab identifier
     * @param string $fieldType
     *            Field type
     * @param string $fieldId
     *            Field identifier
     * @param string $tipText
     *            Tooltip text - default: empty
     * @param array $events
     *            HTML events - default: empty
     * @param array $additional
     *            Additional settings; each field type can use different additional settings; there are the following additional settings which can be always used: "global_addinfo" (string type; text to add below field), "global_addlabel" (string type; text to add to label), "global_displayonlist" (bool type; if true, field information will be displayed on list if this container is for data set), "global_displayonlistcallback" (array or string type; callback method or function for displaying on list if this container is for data set), "global_hidelabel" (bool type; if true, label will be hidden), "global_widgetobj" (object type; it must be set to widget object, when it is widget) - default: empty
     * @return int Identifier
     * @throws Exception
     */
    public function addOptionFieldToTab(
        $formId,
        $tabId,
        $fieldType,
        $fieldId,
        $tipText = '',
        array $events = array(),
        array $additional = array()
    ) {
        // exit
        return $this->addFieldOrHtmlToTab($formId, $tabId, $fieldType, $fieldId, false, '', $tipText, '', '', $events,
            $additional);
    }

    /**
     * Add field to form tab
     *
     * @access public
     * @param string $formId
     *            Form identifier
     * @param string $tabId
     *            Form tab identifier
     * @param string $fieldType
     *            Field type
     * @param string $fieldId
     *            Field identifier
     * @param string $fieldValue
     *            Field value
     * @param string $tipText
     *            Tooltip text - default: empty
     * @param array $events
     *            HTML events - default: empty
     * @param array $additional
     *            Additional settings; each field type can use different additional settings; there are the following additional settings which can be always used: "global_addinfo" (string type; text to add below field), "global_addlabel" (string type; text to add to label), "global_hidelabel" (bool type; if true, label will be hidden), "global_widgetobj" (object type; it must be set to widget object, when it is widget) - default: empty
     * @return int Identifier
     * @throws Exception
     */
    public function addFieldToTab(
        $formId,
        $tabId,
        $fieldType,
        $fieldId,
        $fieldValue,
        $tipText = '',
        array $events = array(),
        array $additional = array()
    ) {
        // exit
        return $this->addFieldOrHtmlToTab($formId, $tabId, $fieldType, $fieldId, true, $fieldValue, $tipText, '', '',
            $events, $additional);
    }

    /**
     * Add HTML code to form tab
     *
     * @access public
     * @param string $formId
     *            Form identifier
     * @param string $tabId
     *            Form tab identifier
     * @param string $html
     *            HTML code to add in field area
     * @param string $htmlLeft
     *            HTML code to add in label area - default: empty
     * @return int Identifier
     * @throws Exception
     */
    public function addHtmlToTab($formId, $tabId, $html, $htmlLeft = '')
    {
        // exit
        return $this->addFieldOrHtmlToTab($formId, $tabId, '', '', false, '', '', $html, $htmlLeft, array(), array());
    }

    /**
     * Show form
     *
     * @access public
     * @param string $formIdForWidget
     *            Form identifier to display for widget - default: empty
     * @return void
     * @throws Exception
     */
    public function showForm($formIdForWidget = '')
    {
        // optionally set for for widget
        if (isset ($formIdForWidget [0]) /* strlen($formIdForWidget) > 0 */ && $this->checkForm($formIdForWidget)) {
            // get form
            $form = $this->getForm($formIdForWidget);
            // get container
            $container = $this->getComponent('options',
                ProjectCategory::ALL)->getContainer($form ['optionscontainerid']);
            // check if this form is for widget
            if ($container ['type'] === ContainerType::WIDGET && !in_array($formIdForWidget,
                    $this->formsToDisplayOnCurrentScreen)) {
                $this->formsToDisplayOnCurrentScreen [] = $formIdForWidget;
            }
        }
        // display all forms
        if (!empty ($this->formsToDisplayOnCurrentScreen)) {
            foreach ($this->formsToDisplayOnCurrentScreen as $formId) {
                // get form
                $form = $this->getForm($formId);
                // get container
                $container = $this->getComponent('options',
                    ProjectCategory::ALL)->getContainer($form ['optionscontainerid']);
                // check if this form is for widget
                $isWidget = ($container ['type'] === ContainerType::WIDGET);
                // get tabs count
                $tabsCount = count($form ['tabs']);
                // check if there is exactly one tab when form is for widget
                if (($isWidget) && ($tabsCount !== 1)) {
                    throw new Exception ($this,
                        ExceptionCode::WRONG_TABS_COUNT_IN_WIDGET,
                        __FILE__, __LINE__, $tabsCount);
                }
                // check if it is adding new element to data set
                $addElementToDataSet = ((($container ['type'] === ContainerType::DATA_SET_SITE) || ($container ['type'] === ContainerType::DATA_SET_NETWORK)) && (isset ($form ['additional'] ['global_isdatasetadd'])) && ($form ['additional'] ['global_isdatasetadd']) && (self::checkDataSetActionAddNew()));
                // display data set list or form
                if ((($container ['type'] === ContainerType::DATA_SET_NETWORK) || ($container ['type'] === ContainerType::DATA_SET_SITE)) && (!self::checkDataSetActionAndId($this,
                        $form ['optionscontainerid'], false)) && (!$addElementToDataSet)) {
                    // optionally load library
                    if (!class_exists('WP_List_Table')) {
                        include ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
                    }
                    // initialize list
                    $listTable = new ListTable ($this, $form ['optionscontainerid'],
                        (isset ($this->fieldsToDisplayOnList [$formId])) ? $this->fieldsToDisplayOnList [$formId] : array(),
                        (isset ($this->sortableFieldsOnList [$formId])) ? $this->sortableFieldsOnList [$formId] : array(),
                        (isset ($this->orderFieldOnList [$formId])) ? $this->orderFieldOnList [$formId] : '',
                        (isset ($this->displayFieldOnListCallback [$formId])) ? $this->displayFieldOnListCallback [$formId] : array(),
                        $this->getDataSetControllers(),
                        (isset ($form ['additional'] ['global_datasetcontrollers'])) ? $form ['additional'] ['global_datasetcontrollers'] : array());
                    // prepare items
                    $listTable->prepare_items();
                    // display list
                    echo '<form method="get">';
                    echo '<input type="hidden" name="' . esc_attr(self::getFieldIdDataSet($this)) . '" value="' . esc_attr($form ['optionscontainerid']) . '" />';
                    echo '<input type="hidden" name="page" value="' . ((isset ($_REQUEST ['page'])) ? esc_attr(sanitize_text_field($_REQUEST ['page'])) : '') . '" />';
                    if ((isset ($form ['additional'] ['global_isdatasetadd'])) && ($form ['additional'] ['global_isdatasetadd'])) {
                        echo '<input type="button" class="button button-secondary" id="' . esc_attr(self::getButtonAddNewElementDataSetId($this,
                                $formId)) . '" value="' . esc_attr($this->getStrings('settings-form',
                                ProjectCategory::BACKEND)->getString('SHOW_FORM_ADD_NEW_ELEMENT')) . '" />';
                        ?>
                        <script type="text/javascript">
                            /* <![CDATA[ */
                            (function ($) {
                                $(document).ready(function () {
                                    $('#<?php echo esc_js(self::getButtonAddNewElementDataSetId($this,
                                        $formId)); ?>').click(function () {
                                        window.location = '<?php

                                            echo esc_js(Helper::getInstance()->getCurrentUrl()) . '?page=' . ((isset ($_REQUEST ['page'])) ? sanitize_text_field($_REQUEST ['page']) : '') . '&action=add-new&return_to=' . ((isset ($_SERVER ['REQUEST_URI'])) ? urlencode($_SERVER ['REQUEST_URI']) : '');
                                            ?>';
                                    });
                                });
                            }(jQuery));
                            /* ]]> */
                        </script>
                        <?php
                    }
                    $listTable->display();
                    echo '</form>';
                } else {
                    // show form header
                    if (!$isWidget) {
                        // show form start
                        $this->getComponent('project-helper')->doAction('before_form_div');
                        echo '<div' . $this->getComponent('project-helper')->applyFiltersForHTMLStyleAndClass('form_div') . ' id="' . esc_attr($this->getComponent('project-helper')->getPrefix() . '__div_form') . '">';
                        $this->getComponent('project-helper')->doAction('before_form');
                        echo '<form method="post" action="#" name="' . esc_attr(self::getFormId($this,
                                $formId)) . '" id="' . esc_attr(self::getFormId($this, $formId)) . '">';
                        $this->getComponent('project-helper')->doAction('inside_form_begin');
                        // set nonce
                        wp_nonce_field(self::getNonceId($this, $formId));
                        // show form identifier field
                        echo '<input type="hidden" name="' . esc_attr(self::getFieldNameFormId($this)) . '" value="' . esc_attr($formId) . '" />';
                        // show action data field
                        echo '<input type="hidden" name="' . esc_attr(self::getFieldNameAction($this)) . '" id="' . esc_attr(self::getFieldIdAction($this,
                                $formId)) . '" value="" />';
                        // optionally show fields for data set
                        if (($container ['type'] === ContainerType::DATA_SET_NETWORK) || ($container ['type'] === ContainerType::DATA_SET_SITE)) {
                            echo '<input type="hidden" name="' . esc_attr(self::getFieldNameDataSetElementId($this)) . '" id="' . esc_attr(self::getFieldIdDataSetElementId($this,
                                    $formId)) . '" value="' . esc_attr((isset ($_REQUEST ['id'])) ? sanitize_text_field($_REQUEST ['id']) : 0) . '" />';
                            echo '<input type="hidden" name="return_to" value="' . esc_attr((isset ($_REQUEST ['return_to'])) ? sanitize_text_field($_REQUEST ['return_to']) : '') . '" />';
                        }
                        // optionally add script
                        if ((empty ($this->formsTabs)) && ($tabsCount > 1)) {
                            JsHelper::getInstance()->addLibScript('backend-settings-form-tabs',
                                'project/components/backend/settings-form', 'tabs', array(
                                    'helper'
                                ), array(), 'kocujILV13aBackendSettingsFormTabsVals', array(
                                    'prefix' => Helper::getInstance()->getPrefix()
                                ));
                        }
                        // remember form tabs
                        if ($tabsCount > 1) {
                            $this->formsTabs [$formId] = $tabsCount;
                        }
                        // show tabs header
                        if ($tabsCount > 1) {
                            echo '<h2 class="nav-tab-wrapper">';
                            $tabPos = 0;
                            foreach ($form ['tabs'] as $tabData) {
                                $tabTitle = (isset ($tabData ['title'] [0]) /* strlen($tabData['title']) > 0 */) ? $tabData ['title'] : sprintf($this->getStrings('settings-form',
                                    ProjectCategory::BACKEND)->getString('SHOW_FORM_TAB'),
                                    $tabPos + 1);
                                echo '<a href="#" id="' . esc_attr(self::getFieldIdTab($this, $formId,
                                        $tabPos)) . '" class="nav-tab">' . $tabTitle . '</a>';
                                ++$tabPos;
                            }
                            echo '</h2>';
                        } else {
                            if (isset ($form ['tabs'] [0] ['title'] [0]) /* strlen($form['tabs'][0]['title']) > 0 */) {
                                echo '<h2>' . $form ['tabs'] [0] ['title'] . '</h2>';
                            }
                        }
                    }
                    // show tabs content
                    if (!empty ($form ['tabs'])) {
                        $tabPos = 0;
                        foreach ($form ['tabs'] as $tabData) {
                            if (!$isWidget) {
                                echo '<div id="' . esc_attr(self::getFieldIdTabDiv($this, $formId,
                                        $tabPos)) . '"' . (($tabsCount > 1) ? ' style="visibility:hidden;position:absolute;"' : '') . '>';
                            }
                            if (isset ($tabData ['fields'])) {
                                if (!$isWidget) {
                                    echo '<table class="form-table"><tbody><tr style="display:none;"><th scope="row">&nbsp;</th><td>&nbsp;</td></tr>';
                                }
                                foreach ($tabData ['fields'] as $field) {
                                    echo $field;
                                }
                                if (!$isWidget) {
                                    echo '</tbody></table>';
                                }
                            }
                            if (!$isWidget) {
                                echo '</div>';
                            }
                            ++$tabPos;
                        }
                    }
                    // initialize bottom buttons
                    $bottomButtons = '';
                    // initialize class and style for bottom buttons
                    $classAndStyle = array(
                        'class' => '',
                        'style' => '',
                        'classwithtag' => '',
                        'stylewithtag' => '',
                        'all' => ''
                    );
                    // optionally show submit button
                    if ((!$isWidget) && (isset ($form ['defaultbuttons'] ['issubmit'])) && ($form ['defaultbuttons'] ['issubmit'])) {
                        $bottomButtons .= $this->getComponent('settings-fields',
                            ProjectCategory::BACKEND)->getField('button',
                            self::getFieldIdDefaultButton($this, $formId, 'submit'), '', '',
                            $form ['defaultbuttons'] ['submittooltip'], $classAndStyle,
                            FieldForWidget::NO,
                            OptionArray::NO, array(
                                'click' => '$(\'#' . esc_js(self::getFieldIdAction($this,
                                        $formId)) . '\').val(\'save\');' . PHP_EOL . '$(\'' . esc_js('#' . self::getFormId($this,
                                            $formId)) . '\').submit();' . PHP_EOL
                            ), array(
                                'buttonlabel' => $form ['defaultbuttons'] ['submitlabel'],
                                'buttonprimary' => true
                            ));
                    }
                    // optionally show restore button
                    if ((!$isWidget) && (isset ($form ['defaultbuttons'] ['isrestore'])) && ($form ['defaultbuttons'] ['isrestore'])) {
                        $bottomButtons .= $this->getComponent('settings-fields',
                            ProjectCategory::BACKEND)->getField('button',
                            self::getFieldIdDefaultButton($this, $formId, 'restore'), '', '',
                            $form ['defaultbuttons'] ['restoretooltip'], $classAndStyle,
                            FieldForWidget::NO,
                            OptionArray::NO, array(
                                'click' => 'if (confirm(\'' . esc_js($this->getStrings('settings-form',
                                        ProjectCategory::BACKEND)->getString('SHOW_FORM_CONFIRM_RESTORE')) . '\')) {' . PHP_EOL . '$(\'#' . esc_js(self::getFieldIdAction($this,
                                        $formId)) . '\').val(\'restore\');' . PHP_EOL . '$(\'' . esc_js('#' . self::getFormId($this,
                                            $formId)) . '\').submit();' . PHP_EOL . '}' . PHP_EOL
                            ), array(
                                'buttonlabel' => $form ['defaultbuttons'] ['restorelabel']
                            ));
                    }
                    // optionally show other buttons
                    if (!empty ($form ['buttons'])) {
                        foreach ($form ['buttons'] as $buttonId => $buttonData) {
                            $bottomButtons .= $this->getComponent('settings-fields',
                                ProjectCategory::BACKEND)->getField('button',
                                ($isWidget) ? $buttonId : self::getFieldIdButton($this, $formId, $buttonId), '', '',
                                $buttonData ['tooltip'], $classAndStyle,
                                FieldForWidget::NO,
                                OptionArray::NO,
                                $buttonData ['events'], array(
                                    'buttonlabel' => $buttonData ['label']
                                ));
                        }
                    }
                    // optionally display bottom buttons
                    if (isset ($bottomButtons [0]) /* strlen($bottomButtons) > 0 */) {
                        echo '<br />' . $bottomButtons;
                    }
                    // show form end
                    if (!$isWidget) {
                        $this->getComponent('project-helper')->doAction('inside_form_end');
                        echo '</form>';
                        $this->getComponent('project-helper')->doAction('after_form');
                        echo '</div>';
                        $this->getComponent('project-helper')->doAction('after_form_div');
                    }
                }
                // optionally set form array as active
                if (isset ($this->formsArrays [$formId])) {
                    $this->activeFormsArrays [$formId] = true;
                }
                // optionally add script for arrays
                if (!$this->libArrayScriptAdded && isset ($this->formsArrays [$formId]) && !empty ($this->formsArrays [$formId])) {
                    JsHelper::getInstance()->addLibScript('backend-settings-form-array',
                        'project/components/backend/settings-form', 'array', array(
                            'helper',
                            'data-helper'
                        ), array(), 'kocujILV13aBackendSettingsFormArrayVals', array(
                            'htmlIdFormatArrayContainer' => $this->getArrayContainerId($this, '####FORM_ID####',
                                '####FIELD_ID####'),
                            'textButtonAddNewElement' => $this->getStrings('settings-form',
                                ProjectCategory::BACKEND)->getString('FIELD_TO_ADD_NOW_BUTTON_ADD_NEW_ELEMENT'),
                            'divElementContainer' => self::ARRAY_DIV_ELEMENT_CONTAINER,
                            'divField' => self::ARRAY_DIV_FIELD,
                            'divControls' => self::ARRAY_DIV_CONTROLS,
                            'imageArrowUp' => LibUrls::getInstance()->get('images') . '/project/components/backend/settings-form/arrow-up.png',
                            'imageArrowDown' => LibUrls::getInstance()->get('images') . '/project/components/backend/settings-form/arrow-down.png',
                            'imageDelete' => LibUrls::getInstance()->get('images') . '/project/components/backend/settings-form/delete.png',
                            'imageEmpty' => LibUrls::getInstance()->get('images') . '/project/components/backend/settings-form/empty.png'
                        ));
                    $this->libArrayScriptAdded = true;
                }
            }
        }
    }

    /**
     * Filter for setting screen option when list of elements is displayed
     *
     * @access public
     * @param string $status
     *            Status
     * @param string $option
     *            Option
     * @param float|int|string $value
     *            Value
     * @return float|int|string Output value
     */
    public function filterSetScreenOption($status, $option, $value)
    {
        // exit
        return $value;
    }

    /**
     * Action for controllers
     *
     * @return void
     * @todo change adding settings options when it is data set, because when there is an error, main identifier for database table is increasing at any time when this controller is checking for errors; probably controllers should be divided into two types - first which only checks data and second which is saving data
     * @access public
     */
    public function actionController()
    {
        // optionally execute controller or data set controller
        if ($this->getComponent('settings-menu',
            ProjectCategory::BACKEND)->checkCurrentPageIsSettingsForProject()) {
            // initialize
            $controllerText = '';
            $controllerStatus = true;
            // optionally execute data set controller
            if ((self::checkDataSetActionAndId($this, '',
                    true)) && (isset ($_REQUEST [self::getFieldIdDataSet($this)]))) {
                // get container identifier
                $containerId = sanitize_text_field($_REQUEST [self::getFieldIdDataSet($this)]);
                // get container data
                $container = $this->getComponent('options',
                    ProjectCategory::ALL)->getContainer($containerId);
                // check if container is for data set
                if (($container ['type'] === ContainerType::DATA_SET_SITE) || ($container ['type'] === ContainerType::DATA_SET_NETWORK)) {
                    // get action
                    $action = sanitize_text_field($_REQUEST ['action']);
                    if (!$this->checkDataSetController($action)) {
                        $action = sanitize_text_field($_REQUEST ['action2']);
                    }
                    if ($this->checkDataSetController($action)) {
                        // get data set controller data
                        $dataSetController = $this->getDataSetController($action);
                        // optionally check nonce
                        $bulkAction = false;
                        if ((isset ($_REQUEST ['id'])) && (is_array($_REQUEST ['id']))) {
                            $bulkAction = true;
                            if ((!isset ($_REQUEST ['_wpnonce'])) || ((isset ($_REQUEST ['_wpnonce'])) && (!wp_verify_nonce(sanitize_text_field($_REQUEST ['_wpnonce']),
                                        'bulk-' . $containerId)))) {
                                wp_die($this->getStrings('settings-form',
                                    ProjectCategory::BACKEND)->getString('ACTION_CONTROLLER_SECURITY_ERROR'));
                            }
                        }
                        if (((!$bulkAction) && ((!$dataSetController ['noncerequired']) || (($dataSetController ['noncerequired']) && (isset ($_REQUEST ['_wpnonce'])) && (wp_verify_nonce(sanitize_text_field($_REQUEST ['_wpnonce']),
                                            self::getDataSetNonceId($this,
                                                $containerId)))))) || (($bulkAction) && ($dataSetController ['bulkavailable'] === BulkAvailable::YES))) {
                            // start database transaction
                            DbDataHelper::getInstance()->databaseTransactionStart();
                            // process elements
                            if ($bulkAction) {
                                $ids = sanitize_text_field($_REQUEST ['id']);
                            } else {
                                $ids = array(
                                    sanitize_text_field($_REQUEST ['id'])
                                );
                            }
                            // process elements
                            if (!empty ($ids)) {
                                foreach ($ids as $id) {
                                    // execute controller
                                    if (($this->getComponent('options',
                                            ProjectCategory::ALL)->checkDataSetElementId($containerId,
                                            $id)) && ($dataSetController ['callback'] !== null)) {
                                        $outputText = '';
                                        $output = call_user_func_array($dataSetController ['callback'], array(
                                            $this,
                                            $containerId,
                                            $id,
                                            &$outputText
                                        ));
                                        if (isset ($outputText [0]) /* strlen($outputText) > 0 */) {
                                            if (!$output) {
                                                $controllerStatus = false;
                                            }
                                            if (isset ($controllerText [0]) /* strlen($controllerText) > 0 */) {
                                                $controllerText .= '<br /><br />';
                                            }
                                            $controllerText .= $outputText;
                                        }
                                    }
                                }
                            }
                            // end database transaction
                            DbDataHelper::getInstance()->databaseTransactionEnd();
                            // optionally redirect
                            if ($dataSetController ['redirectafter'] === RedirectAfter::YES) {
                                wp_redirect((isset ($_REQUEST ['_wp_http_referer'])) ? sanitize_text_field($_REQUEST ['_wp_http_referer']) : admin_url('/'));
                                die ();
                            }
                        } else {
                            wp_die($this->getStrings('settings-form',
                                ProjectCategory::BACKEND)->getString('ACTION_CONTROLLER_SECURITY_ERROR'));
                        }
                    }
                } else {
                    wp_die($this->getStrings('settings-form',
                        ProjectCategory::BACKEND)->getString('ACTION_CONTROLLER_DATA_DOES_NOT_EXIST'));
                }
            }
            // optionally execute controller
            if ((isset ($_REQUEST [self::getFieldNameAction($this)])) && (isset ($this->controllers [$_REQUEST [self::getFieldNameAction($this)]])) && (isset ($this->forms [$_REQUEST [self::getFieldNameFormId($this)]]))) {
                // check nonce
                if ((isset ($_REQUEST ['_wpnonce'])) && (isset ($_REQUEST [self::getFieldNameFormId($this)])) && (wp_verify_nonce(sanitize_text_field($_REQUEST ['_wpnonce']),
                        self::getNonceId($this, sanitize_text_field($_REQUEST [self::getFieldNameFormId($this)]))))) {
                    // get form data
                    $form = $this->getForm(sanitize_text_field($_REQUEST [self::getFieldNameFormId($this)]));
                    // get container identifier
                    $containerId = $this->forms [sanitize_text_field($_REQUEST [self::getFieldNameFormId($this)])] ['optionscontainerid'];
                    // get container
                    $container = $this->getComponent('options',
                        ProjectCategory::ALL)->getContainer($containerId);
                    // check if it is adding new element to data set
                    $addElementToDataSet = ((($container ['type'] === ContainerType::DATA_SET_SITE) || ($container ['type'] === ContainerType::DATA_SET_NETWORK)) && (self::checkDataSetActionAddNew()));
                    // start database transaction
                    DbDataHelper::getInstance()->databaseTransactionStart();
                    // optionally get element identifier for data set
                    $dataSetElementId = 0;
                    if (($container ['type'] === ContainerType::DATA_SET_SITE) || ($container ['type'] === ContainerType::DATA_SET_NETWORK)) {
                        if ((($container ['type'] === ContainerType::DATA_SET_SITE) || ($container ['type'] === ContainerType::DATA_SET_NETWORK)) && (isset ($form ['additional'] ['global_isdatasetadd'])) && ($form ['additional'] ['global_isdatasetadd']) && (self::checkDataSetActionAddNew())) {
                            $dataSetElementId = $this->getComponent('options',
                                ProjectCategory::ALL)->addDataSetElement($containerId);
                        } else {
                            if (isset ($_REQUEST [self::getFieldNameDataSetElementId($this)])) {
                                $dataSetElementId = sanitize_text_field($_REQUEST [self::getFieldNameDataSetElementId($this)]);
                            }
                            if ((!isset ($_REQUEST ['id'])) || (!$this->getComponent('options',
                                    ProjectCategory::ALL)->checkDataSetElementId($containerId,
                                    sanitize_text_field($_REQUEST ['id'])))) {
                                wp_die($this->getStrings('settings-form',
                                    ProjectCategory::BACKEND)->getString('ACTION_CONTROLLER_ELEMENT_DOES_NOT_EXIST'));
                            }
                        }
                    }
                    // execute controller
                    foreach ($this->controllers [sanitize_text_field($_REQUEST [self::getFieldNameAction($this)])] as $callback) {
                        $outputText = '';
                        $output = call_user_func_array($callback, array(
                            $this,
                            sanitize_text_field($_REQUEST [self::getFieldNameFormId($this)]),
                            (($container ['type'] === ContainerType::DATA_SET_SITE) || ($container ['type'] === ContainerType::DATA_SET_NETWORK)),
                            $dataSetElementId,
                            &$outputText
                        ));
                        if (isset ($outputText [0]) /* strlen($outputText) > 0 */) {
                            if (!$output) {
                                $controllerStatus = false;
                            }
                            if (isset ($controllerText [0]) /* strlen($controllerText) > 0 */) {
                                $controllerText .= '<br /><br />';
                            }
                            $controllerText .= $outputText;
                        }
                    }
                    // optionally remove added table
                    if ((!$controllerStatus) && ($addElementToDataSet)) {
                        $this->getComponent('options',
                            ProjectCategory::ALL)->removeDataSetElement($containerId,
                            $dataSetElementId);
                    }
                    // end database transaction
                    DbDataHelper::getInstance()->databaseTransactionEnd();
                    // optionally redirect
                    if (($controllerStatus) && ($addElementToDataSet)) {
                        wp_redirect(((isset ($_REQUEST ['return_to'])) && (isset ($_REQUEST ['return_to'] [0]) /* strlen($_REQUEST['return_to']) > 0 */)) ? sanitize_text_field($_REQUEST ['return_to']) : ((isset ($_REQUEST ['_wp_http_referer'])) ? sanitize_text_field($_REQUEST ['_wp_http_referer']) : admin_url('/')));
                        die ();
                    }
                } else {
                    wp_die($this->getStrings('settings-form',
                        ProjectCategory::BACKEND)->getString('ACTION_CONTROLLER_SECURITY_ERROR'));
                }
            }
            // optionally show message with controller output text
            if (isset ($controllerText [0]) /* strlen($controllerText) > 0 */) {
                $this->getComponent('message',
                    ProjectCategory::BACKEND)->addMessage('settings_form_information',
                    $controllerText,
                    $controllerStatus ? Type::INFORMATION : Type::ERROR);
            }
        }
    }

    /**
     * Action for form header
     *
     * @return void
     * @todo add possibility to enable/disable selected columns of elements list
     * @access public
     */
    public function actionFormHeader()
    {
        // get forms list for current settings page and add header for each form
        $currentScreenId = CurrentScreenIdHelper::getInstance()->get();
        $forms = $this->getForms();
        if (!empty ($forms)) {
            foreach ($forms as $formId => $form) {
                if (!empty ($form ['settingsmenusids'])) {
                    foreach ($form ['settingsmenusids'] as $menuId) {
                        if ($currentScreenId ['alternative'] === 'admin_page_' . $this->getProjectObj()->getMainSettingInternalName() . '_' . $menuId) {
                            // set form to display
                            $this->formsToDisplayOnCurrentScreen [] = $formId;
                            // get container
                            $container = $this->getComponent('options',
                                ProjectCategory::ALL)->getContainer($form ['optionscontainerid']);
                            // check if it is adding new element to data set
                            $addElementToDataSet = ((($container ['type'] === ContainerType::DATA_SET_SITE) || ($container ['type'] === ContainerType::DATA_SET_NETWORK)) && (isset ($form ['additional'] ['global_isdatasetadd'])) && ($form ['additional'] ['global_isdatasetadd']) && (self::checkDataSetActionAddNew()));
                            // process forms
                            if ((($container ['type'] === ContainerType::DATA_SET_NETWORK) || ($container ['type'] === ContainerType::DATA_SET_SITE)) && (!self::checkDataSetActionAndId($this,
                                    $form ['optionscontainerid'], false)) && (!$addElementToDataSet)) {
                                // display screen options for element list
                                add_screen_option('per_page', array(
                                    'label' => $this->getStrings('settings-form',
                                        ProjectCategory::BACKEND)->getString('ACTION_FORM_HEADER_ELEMENTS'),
                                    'default' => 10,
                                    'option' => $this->getProjectObj()->getMainSettingInternalName() . '__' . $form ['optionscontainerid'] . '__per_page'
                                ));
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Action for tabs script
     *
     * @access public
     * @return void
     */
    public function actionPrintFooterScripts()
    {
        // initialize tabs script
        if (!empty ($this->formsTabs)) {
            ?>
            <script type="text/javascript">
                /* <![CDATA[ */
                (function ($) {
                    $(document).ready(function () {
                        kocujILV13aBackendSettingsFormTabs.addProject('<?php echo esc_js($this->getProjectObj()->getMainSettingInternalName()); ?>', '<?php echo esc_js($this->getProjectObj()->getMainSettingTitleOriginal()); ?>');
                        <?php
                        foreach ( $this->formsTabs as $formId => $tabsCount ) {
                        ?>
                        kocujILV13aBackendSettingsFormTabs.process('<?php echo esc_js($this->getProjectObj()->getMainSettingInternalName()); ?>', '<?php echo esc_js($formId); ?>', '<?php echo esc_js($tabsCount); ?>');
                        <?php
                        }
                        ?>
                    });
                }(jQuery));
                /* ]]> */
            </script>
            <?php
        }
        // initialize array script
        if ($this->libArrayScriptAdded) {
            ?>
            <script type="text/javascript">
                /* <![CDATA[ */
                (function ($) {
                    $(document).ready(function () {
                        kocujILV13aBackendSettingsFormArray.addProject('<?php echo esc_js($this->getProjectObj()->getMainSettingInternalName()); ?>', '<?php echo esc_js($this->getProjectObj()->getMainSettingTitleOriginal()); ?>');
                        <?php
                        $jsObjFields = array(
                            'addnewbutton',
                            'allowchangeorder',
                            'autoadddeleteifempty',
                            'deletebutton'
                        );
                        foreach ( $this->formsArrays as $formId => $fieldsData ) {
                        if (isset ($this->activeFormsArrays [$formId]) && !empty ($fieldsData)) {
                        foreach ( $fieldsData as $fieldId => $data ) {
                        $jsObj = '';
                        foreach ($jsObjFields as $objField) {
                            $jsObj .= $objField . ': ' . (((isset ($data ['arraysettings'] [$objField])) && ($data ['arraysettings'] [$objField])) ? 'true' : 'false') . ', ';
                        }
                        $jsObj = substr($jsObj, 0, -2);
                        ?>
                        kocujILV13aBackendSettingsFormArray.process('<?php echo esc_js($this->getProjectObj()->getMainSettingInternalName()); ?>', '<?php echo esc_js($formId); ?>', '<?php echo esc_js($fieldId); ?>', '<?php echo str_replace('<',
                            '<\' + \'', str_replace('\'', '\\\'',
                                $data['emptyfield'])); ?>', '<?php echo esc_js($data['count']); ?>', {<?php

                            echo $jsObj;
                            ?>});
                        <?php
                        }
                        }
                        }
                        ?>
                    });
                }(jQuery));
                /* ]]> */
            </script>
            <?php
        }
    }
}
