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
namespace KocujIL\V13a\Classes\Project\Components\Backend\SettingsFields;

// set namespaces aliases
use KocujIL\V13a\Classes\ComponentObject;
use KocujIL\V13a\Classes\Exception;
use KocujIL\V13a\Classes\JsHelper;
use KocujIL\V13a\Classes\LibUrls;
use KocujIL\V13a\Enums\Project\Components\Backend\SettingsFields\AllowInWidget;
use KocujIL\V13a\Enums\Project\Components\Backend\SettingsFields\ExceptionCode;
use KocujIL\V13a\Enums\Project\Components\Backend\SettingsFields\FieldForWidget;
use KocujIL\V13a\Enums\Project\Components\Backend\SettingsFields\OptionArray;
use KocujIL\V13a\Enums\Project\Components\Backend\SettingsFields\OrderInWidget;
use KocujIL\V13a\Enums\ProjectCategory;

// security
if ((!defined('ABSPATH')) || ((isset ($_SERVER ['SCRIPT_FILENAME'])) && (basename($_SERVER ['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die ();
}

/**
 * Settings fields class
 *
 * @access public
 */
class Component extends ComponentObject
{

    /**
     * Fields types list
     *
     * @access private
     * @var array
     */
    private $types = array();

    /**
     * Fields types for current site
     *
     * @access private
     * @var array
     */
    private $fieldsTypesForCurrentSite = array();

    /**
     * Constructor
     *
     * @access public
     * @param object $projectObj
     *            \KocujIL\V13a\Classes\Project object for current project
     * @throws Exception
     */
    public function __construct($projectObj)
    {
        // execute parent constructor
        parent::__construct($projectObj);
        // add fields types
        $this->addType('hidden', array(
            __CLASS__,
            'fieldHidden'
        ));
        $this->addType('text', array(
            __CLASS__,
            'fieldText'
        ), null, AllowInWidget::YES);
        $this->addType('textdisabled', array(
            __CLASS__,
            'fieldTextDisabled'
        ));
        $this->addType('textarea', array(
            __CLASS__,
            'fieldTextarea'
        ), null, AllowInWidget::YES);
        $this->addType('checkbox', array(
            __CLASS__,
            'fieldCheckbox'
        ), null, AllowInWidget::YES,
            OrderInWidget::FIRST_ELEMENT);
        $this->addType('select', array(
            __CLASS__,
            'fieldSelect'
        ), null, AllowInWidget::YES);
        $this->addType('button', array(
            __CLASS__,
            'fieldButton'
        ));
        $this->addType('image', array(
            __CLASS__,
            'fieldImage'
        ), array(
            __CLASS__,
            'fieldHeaderImage'
        ));
    }

    /**
     * Add field type
     *
     * @access public
     * @param string $type
     *            Field type
     * @param array|string $callbackDisplay
     *            Callback function or method name for displaying field; can be global function or method from any class
     * @param array|string $callbackHeader
     *            Callback function or method name for header on site with field; can be global function or method from any class - default: NULL
     * @param int $allowInWidget
     *            Field type is allowed in widget or not; must be one of the following constants from \KocujIL\V13a\Enums\Project\Components\Backend\SettingsFields\AllowInWidget: NO (field type is not allowed in widget) or YES (field type is allowed in widget) - default: \KocujIL\V13a\Enums\Project\Components\Backend\SettingsFields\AllowInWidget::NO
     * @param int $orderInWidget
     *            Label and HTML element order in widget; must be one of the following constants from \KocujIL\V13a\Enums\Project\Components\Backend\SettingsFields\OrderInWidget: FIRST_LABEL (first label, then element) or FIRST_ELEMENT (first element, then label) - default: \KocujIL\V13a\Enums\Project\Components\Backend\SettingsFields\OrderInWidget::FIRST_LABEL
     * @return void
     * @throws Exception
     */
    public function addType(
        $type,
        $callbackDisplay,
        $callbackHeader = null,
        $allowInWidget = AllowInWidget::NO,
        $orderInWidget = OrderInWidget::FIRST_LABEL
    ) {
        // check if this field type identifier does not exist already
        if (isset ($this->types [$type])) {
            throw new Exception ($this, ExceptionCode::TYPE_ID_EXISTS, __FILE__, __LINE__, $type);
        }
        // add type
        $this->types [$type] = array(
            'callbackdisplay' => $callbackDisplay
        );
        if ($callbackHeader !== null) {
            $this->types [$type] ['callbackheader'] = $callbackHeader;
        }
        if ($allowInWidget !== AllowInWidget::NO) {
            $this->types [$type] ['allowinwidget'] = $allowInWidget;
        }
        if ($orderInWidget !== OrderInWidget::FIRST_LABEL) {
            $this->types [$type] ['orderinwidget'] = $orderInWidget;
        }
    }

    /**
     * Get field type data
     *
     * @access public
     * @return array Fields types data; each field type data has the following fields: "allowinwidget" (int type; field type is allowed in widget or not), "callbackdisplay" (array or string type; callback to function or method which will display this field), "callbackheader" (array or string type; callback to function or method which will add header for field), "orderinwidget" (int type; order of label and HTML element in widget)
     */
    public function getTypes()
    {
        // prepare types
        $types = $this->types;
        foreach ($types as $key => $val) {
            if (!isset ($val ['callbackheader'])) {
                $types [$key] ['callbackheader'] = null;
            }
            if (!isset ($val ['allowinwidget'])) {
                $types [$key] ['allowinwidget'] = AllowInWidget::NO;
            }
            if (!isset ($val ['orderinwidget'])) {
                $types [$key] ['orderinwidget'] = OrderInWidget::FIRST_LABEL;
            }
        }
        // exit
        return $types;
    }

    /**
     * Check if field type exists
     *
     * @access public
     * @param string $id
     *            Field type identifier
     * @return bool Field exists (true) or not (false)
     */
    public function checkType($id)
    {
        // exit
        return isset ($this->types [$id]);
    }

    /**
     * Get field type data by id
     *
     * @access public
     * @param string $id
     *            Field type identifier
     * @return array|bool Field type data or false if not exists; field type data have the following fields: "allowinwidget" (int type; field type is allowed in widget or not), "callbackdisplay" (array or string type; callback to function or method which will display this field), "callbackheader" (array or string type; callback to function or method which will add header for field), "orderinwidget" (int type; order of label and HTML element in widget)
     */
    public function getType($id)
    {
        // get types
        $types = $this->getTypes();
        // exit
        return (isset ($types [$id])) ? $types [$id] : false;
    }

    /**
     * Remove field type
     *
     * @access public
     * @param string $id
     *            Field type identifier
     * @return void
     * @throws Exception
     */
    public function removeType($id)
    {
        // check if this field type identifier exists
        if (!isset ($this->types [$id])) {
            throw new Exception ($this, ExceptionCode::TYPE_ID_DOES_NOT_EXIST, __FILE__, __LINE__, $id);
        }
        // remove field type
        unset ($this->types [$id]);
    }

    /**
     * Add field type for current site
     *
     * @access public
     * @param $fieldType
     * @return void
     * @throws Exception
     * @throws Exception
     * @todo throw an error \KocujIL\V13a\Enums\Project\Components\Backend\SettingsFields\ExceptionCode::WRONG_ACTION_FOR_METHOD when this method is executed after "current_screen" action
     */
    public function addFieldTypeForCurrentSite($fieldType)
    {
        // check parameters
        if (!isset ($this->types [$fieldType])) {
            throw new Exception ($this, ExceptionCode::TYPE_ID_DOES_NOT_EXIST, __FILE__, __LINE__, $fieldType);
        }
        // add field type to list
        if (!in_array($fieldType, $this->fieldsTypesForCurrentSite)) {
            $this->fieldsTypesForCurrentSite [] = $fieldType;
        }
    }

    /**
     * Get field
     *
     * @access public
     * @param string $fieldType
     *            Field type
     * @param string $fieldId
     *            Field identifier
     * @param string $fieldHtmlId
     *            Field HTML id; if empty, HTML id will be the same as $fieldId
     * @param string $fieldValue
     *            Field value
     * @param string $tipText
     *            Tooltip text - default: empty
     * @param array $classAndStyle
     *            Class and style for element; class and style data must have the following fields: "all" (string type; HTML classes with "class" tag and HTML styles with "style" tag and space before), "class" (string type; HTML classes), "classwithtag" (string type; HTML classes with "class" tag and space before), "style" (string type; HTML styles), "stylewithtag" (string type; HTML styles with "style" tag and space before) - default: empty
     * @param int $fieldForWidget
     *            This field is for widget or not; must be one of the following constants from \KocujIL\V13a\Enums\Project\Components\Backend\SettingsFields\FieldForWidget: NO (when field is not for widget) or YES (when field is for widget) - default: \KocujIL\V13a\Enums\Project\Components\Backend\SettingsFields\FieldForWidget::NO
     * @param int $optionArray
     *            Option is array or standard; must be one of the following constants from \KocujIL\V13a\Enums\Project\Components\Backend\SettingsFields\OptionArray: NO (when it is standard option) or YES (when it is array option) - default: \KocujIL\V13a\Enums\Project\Components\Backend\SettingsFields\OptionArray::NO
     * @param array $events
     *            HTML events - default: empty
     * @param array $additional
     *            Additional settings; each field type can use different additional settings; there are the following additional settings which can be always used: "global_addinfo" (string type; text to add below field), "global_widgetobj" (object type; it must be set to widget object, when $fieldForWidget is set to \KocujIL\V13a\Enums\Project\Components\Backend\SettingsFields\FieldForWidget::YES)
     * @return string Field
     * @throws Exception
     */
    public function getField(
        $fieldType,
        $fieldId,
        $fieldHtmlId,
        $fieldValue,
        $tipText = '',
        array $classAndStyle = array(),
        $fieldForWidget = FieldForWidget::NO,
        $optionArray = OptionArray::NO,
        array $events = array(),
        array $additional = array()
    ) {
        // check parameters
        if (!isset ($this->types [$fieldType])) {
            throw new Exception ($this, ExceptionCode::TYPE_ID_DOES_NOT_EXIST, __FILE__, __LINE__, $fieldType);
        }
        // check if this field type is available for current site
        if (!in_array($fieldType, $this->fieldsTypesForCurrentSite)) {
            throw new Exception ($this, ExceptionCode::TYPE_ID_NOT_DECLARED_FOR_CURRENT_SITE, __FILE__, __LINE__,
                $fieldType);
        }
        // check if this field type is allowed in widget
        if (($fieldForWidget === FieldForWidget::YES) && ((!isset ($this->types [$fieldType] ['allowinwidget'])) || ((isset ($this->types [$fieldType] ['allowinwidget'])) && ($this->types [$fieldType] ['allowinwidget'] === AllowInWidget::NO)))) {
            throw new Exception ($this, ExceptionCode::CANNOT_USE_TYPE_ID_IN_WIDGET, __FILE__, __LINE__, $fieldType);
        }
        // check if it is widget and array
        if (($fieldForWidget === FieldForWidget::YES) && ($optionArray === OptionArray::YES)) {
            throw new Exception ($this, ExceptionCode::CANNOT_USE_ARRAY_OPTION_IN_WIDGET, __FILE__, __LINE__);
        }
        // check if there is a widget object
        if (($fieldForWidget === FieldForWidget::YES) && (!isset ($additional ['global_widgetobj']))) {
            throw new Exception ($this, ExceptionCode::NO_WIDGET_OBJECT, __FILE__, __LINE__);
        }
        // prepare HTML attributes
        $fieldHtmlName = $fieldId;
        if ($optionArray === OptionArray::YES) {
            $fieldHtmlName .= '[]';
        }
        $fieldHtmlId = (isset ($fieldHtmlId [0]) /* strlen($fieldHtmlId) > 0 */) ? $fieldHtmlId : $fieldId;
        if ($fieldForWidget === FieldForWidget::YES) {
            $fieldHtmlId = $additional ['global_widgetobj']->get_field_id($fieldHtmlId);
            $fieldHtmlName = $additional ['global_widgetobj']->get_field_name($fieldHtmlName);
        }
        $attr = ' data-type="field-value" id="' . esc_attr($fieldHtmlId) . '" name="' . esc_attr($fieldHtmlName) . '"';
        if (isset ($tipText [0]) /* strlen($tipText) > 0 */) {
            $attr .= ' title="' . esc_attr($tipText) . '"';
        }
        // prepare events
        foreach ($events as $eventId => $eventVal) {
            $events [$eventId] = 'var eventId = \'' . esc_js($eventId) . '\';' . PHP_EOL . $eventVal;
        }
        // optionally add events to HTML element
        if ($fieldForWidget === FieldForWidget::YES) {
            foreach ($events as $eventId => $eventVal) {
                $attr .= ' on' . $eventId . '="' . esc_attr('(function($) {' . str_replace(PHP_EOL, ' ',
                            $eventVal) . '}(jQuery));') . '"';
            }
        }
        // call method
        $output = call_user_func_array($this->types [$fieldType] ['callbackdisplay'], array(
            $this,
            $fieldHtmlId,
            $fieldHtmlName,
            $fieldValue,
            $attr,
            $tipText,
            $classAndStyle,
            $fieldForWidget,
            $optionArray,
            isset ($this->types [$fieldType] ['callbackdisplay']) ? $this->types [$fieldType] ['callbackdisplay'] : OrderInWidget::FIRST_LABEL,
            $additional
        ));
        // optionally add information below field
        if ((isset ($additional ['global_addinfo'])) && (isset ($additional ['global_addinfo'] [0]) /* strlen($additional['global_addinfo']) > 0 */)) {
            $output .= '<p class="description">' . $additional ['global_addinfo'] . '</p>';
        }
        // optionally add events to JavaScript
        if ((!empty ($events)) && ($fieldForWidget === FieldForWidget::NO) && ($optionArray === OptionArray::NO)) {
            $output .= '<script type="text/javascript">' . PHP_EOL . '/* <![CDATA[ */' . PHP_EOL . '(function($) {' . PHP_EOL . '$(document).ready(function() {' . PHP_EOL;
            foreach ($events as $eventId => $eventVal) {
                $output .= '$(\'' . esc_js('#' . $fieldId) . '\').on(\'' . esc_js($eventId) . '\', function(event) {' . PHP_EOL . 'event.preventDefault();' . PHP_EOL . $eventVal . PHP_EOL . '});' . PHP_EOL;
            }
            $output .= '});' . PHP_EOL . '}(jQuery));' . PHP_EOL . '/* ]]> */' . PHP_EOL . '</script>' . PHP_EOL;
        }
        // exit
        return $output;
    }

    /**
     * Get field - hidden
     *
     * @access private
     * @param object $componentObj
     *            Component object
     * @param string $fieldHtmlId
     *            Field HTML id
     * @param string $fieldHtmlName
     *            Field HTML name
     * @param string $fieldValue
     *            Field value
     * @param string $fieldAttrs
     *            Prepared text with field attributes "id", "name" and "title" and with space at beginning
     * @param string $tipText
     *            Tooltip text
     * @param array $classAndStyle
     *            Class and style for element; class and style data must have the following fields: "all" (string type; HTML classes with "class" tag and HTML styles with "style" tag and space before), "class" (string type; HTML classes), "classwithtag" (string type; HTML classes with "class" tag and space before), "style" (string type; HTML styles), "stylewithtag" (string type; HTML styles with "style" tag and space before)
     * @param int $fieldForWidget
     *            This field is for widget or not; must be one of the following constants from \KocujIL\V13a\Enums\Project\Components\Backend\SettingsFields\FieldForWidget: NO (when field is not for widget) or YES (when field is for widget)
     * @param int $optionArray
     *            Option is array or standard; must be one of the following constants from \KocujIL\V13a\Enums\Project\Components\Backend\SettingsFields\OptionArray: NO (when it is standard option) or YES (when it is array option)
     * @param int $orderInWidget
     *            Label and HTML element order in widget; must be one of the following constants from \KocujIL\V13a\Enums\Project\Components\Backend\SettingsFields\OrderInWidget: FIRST_LABEL (first label, then element), FIRST_ELEMENT (first element, then label)
     * @param array $additional
     *            Additional settings
     * @return string Field
     */
    private static function fieldHidden(
        $componentObj,
        $fieldHtmlId,
        $fieldHtmlName,
        $fieldValue,
        $fieldAttrs,
        $tipText,
        array $classAndStyle,
        $fieldForWidget,
        $optionArray,
        $orderInWidget,
        array $additional
    ) {
        // exit
        return '<input' . $fieldAttrs . ' type="hidden" value="' . esc_attr($fieldValue) . '" />';
    }

    /**
     * Get field - text
     *
     * @access private
     * @param object $componentObj
     *            Component object
     * @param string $fieldHtmlId
     *            Field HTML id
     * @param string $fieldHtmlName
     *            Field HTML name
     * @param string $fieldValue
     *            Field value
     * @param string $fieldAttrs
     *            Prepared text with field attributes "id", "name" and "title" and with space at beginning
     * @param string $tipText
     *            Tooltip text
     * @param array $classAndStyle
     *            Class and style for element; class and style data must have the following fields: "all" (string type; HTML classes with "class" tag and HTML styles with "style" tag and space before), "class" (string type; HTML classes), "classwithtag" (string type; HTML classes with "class" tag and space before), "style" (string type; HTML styles), "stylewithtag" (string type; HTML styles with "style" tag and space before)
     * @param int $fieldForWidget
     *            This field is for widget or not; must be one of the following constants from \KocujIL\V13a\Enums\Project\Components\Backend\SettingsFields\FieldForWidget: NO (when field is not for widget) or YES (when field is for widget)
     * @param int $optionArray
     *            Option is array or standard; must be one of the following constants from \KocujIL\V13a\Enums\Project\Components\Backend\SettingsFields\OptionArray: NO (when it is standard option) or YES (when it is array option)
     * @param int $orderInWidget
     *            Label and HTML element order in widget; must be one of the following constants from \KocujIL\V13a\Enums\Project\Components\Backend\SettingsFields\OrderInWidget: FIRST_LABEL (first label, then element), FIRST_ELEMENT (first element, then label)
     * @param array $additional
     *            Additional settings
     * @return string Field
     */
    private static function fieldText(
        $componentObj,
        $fieldHtmlId,
        $fieldHtmlName,
        $fieldValue,
        $fieldAttrs,
        $tipText,
        array $classAndStyle,
        $fieldForWidget,
        $optionArray,
        $orderInWidget,
        array $additional
    ) {
        // exit
        return '<input' . $fieldAttrs . ' type="text" value="' . esc_attr($fieldValue) . '"' . $classAndStyle ['all'] . ' />';
    }

    /**
     * Get field - text disabled
     *
     * @access private
     * @param object $componentObj
     *            Component object
     * @param string $fieldHtmlId
     *            Field HTML id
     * @param string $fieldHtmlName
     *            Field HTML name
     * @param string $fieldValue
     *            Field value
     * @param string $fieldAttrs
     *            Prepared text with field attributes "id", "name" and "title" and with space at beginning
     * @param string $tipText
     *            Tooltip text
     * @param array $classAndStyle
     *            Class and style for element; class and style data must have the following fields: "all" (string type; HTML classes with "class" tag and HTML styles with "style" tag and space before), "class" (string type; HTML classes), "classwithtag" (string type; HTML classes with "class" tag and space before), "style" (string type; HTML styles), "stylewithtag" (string type; HTML styles with "style" tag and space before)
     * @param int $fieldForWidget
     *            This field is for widget or not; must be one of the following constants from \KocujIL\V13a\Enums\Project\Components\Backend\SettingsFields\FieldForWidget: NO (when field is not for widget) or YES (when field is for widget)
     * @param int $optionArray
     *            Option is array or standard; must be one of the following constants from \KocujIL\V13a\Enums\Project\Components\Backend\SettingsFields\OptionArray: NO (when it is standard option) or YES (when it is array option)
     * @param int $orderInWidget
     *            Label and HTML element order in widget; must be one of the following constants from \KocujIL\V13a\Enums\Project\Components\Backend\SettingsFields\OrderInWidget: FIRST_LABEL (first label, then element), FIRST_ELEMENT (first element, then label)
     * @param array $additional
     *            Additional settings
     * @return string Field
     */
    private static function fieldTextDisabled(
        $componentObj,
        $fieldHtmlId,
        $fieldHtmlName,
        $fieldValue,
        $fieldAttrs,
        $tipText,
        array $classAndStyle,
        $fieldForWidget,
        $optionArray,
        $orderInWidget,
        array $additional
    ) {
        // exit
        return '<input' . $fieldAttrs . ' type="hidden" value="' . esc_attr($fieldValue) . '" style="width:1px;height:1px;margin:0;padding:0;position:absolute;" /><div' . $classAndStyle ['all'] . '>' . $fieldValue . '</div>';
    }

    /**
     * Get field - textarea
     *
     * @access private
     * @param object $componentObj
     *            Component object
     * @param string $fieldHtmlId
     *            Field HTML id
     * @param string $fieldHtmlName
     *            Field HTML name
     * @param string $fieldValue
     *            Field value
     * @param string $fieldAttrs
     *            Prepared text with field attributes "id", "name" and "title" and with space at beginning
     * @param string $tipText
     *            Tooltip text
     * @param array $classAndStyle
     *            Class and style for element; class and style data must have the following fields: "all" (string type; HTML classes with "class" tag and HTML styles with "style" tag and space before), "class" (string type; HTML classes), "classwithtag" (string type; HTML classes with "class" tag and space before), "style" (string type; HTML styles), "stylewithtag" (string type; HTML styles with "style" tag and space before)
     * @param int $fieldForWidget
     *            This field is for widget or not; must be one of the following constants from \KocujIL\V13a\Enums\Project\Components\Backend\SettingsFields\FieldForWidget: NO (when field is not for widget) or YES (when field is for widget)
     * @param int $optionArray
     *            Option is array or standard; must be one of the following constants from \KocujIL\V13a\Enums\Project\Components\Backend\SettingsFields\OptionArray: NO (when it is standard option) or YES (when it is array option)
     * @param int $orderInWidget
     *            Label and HTML element order in widget; must be one of the following constants from \KocujIL\V13a\Enums\Project\Components\Backend\SettingsFields\OrderInWidget: FIRST_LABEL (first label, then element), FIRST_ELEMENT (first element, then label)
     * @param array $additional
     *            Additional settings
     * @return string Field
     */
    private static function fieldTextarea(
        $componentObj,
        $fieldHtmlId,
        $fieldHtmlName,
        $fieldValue,
        $fieldAttrs,
        $tipText,
        array $classAndStyle,
        $fieldForWidget,
        $optionArray,
        $orderInWidget,
        array $additional
    ) {
        // exit
        return '<textarea' . $fieldAttrs . ' rows="15" cols="" style="' . esc_attr($classAndStyle ['style'] . 'resize:none;') . $classAndStyle ['classwithtag'] . '">' . htmlspecialchars($fieldValue) . '</textarea>';
    }

    /**
     * Get field - checkbox
     *
     * @access private
     * @param object $componentObj
     *            Component object
     * @param string $fieldHtmlId
     *            Field HTML id
     * @param string $fieldHtmlName
     *            Field HTML name
     * @param string $fieldValue
     *            Field value
     * @param string $fieldAttrs
     *            Prepared text with field attributes "id", "name" and "title" and with space at beginning
     * @param string $tipText
     *            Tooltip text
     * @param array $classAndStyle
     *            Class and style for element; class and style data must have the following fields: "all" (string type; HTML classes with "class" tag and HTML styles with "style" tag and space before), "class" (string type; HTML classes), "classwithtag" (string type; HTML classes with "class" tag and space before), "style" (string type; HTML styles), "stylewithtag" (string type; HTML styles with "style" tag and space before)
     * @param int $fieldForWidget
     *            This field is for widget or not; must be one of the following constants from \KocujIL\V13a\Enums\Project\Components\Backend\SettingsFields\FieldForWidget: NO (when field is not for widget) or YES (when field is for widget)
     * @param int $optionArray
     *            Option is array or standard; must be one of the following constants from \KocujIL\V13a\Enums\Project\Components\Backend\SettingsFields\OptionArray: NO (when it is standard option) or YES (when it is array option)
     * @param int $orderInWidget
     *            Label and HTML element order in widget; must be one of the following constants from \KocujIL\V13a\Enums\Project\Components\Backend\SettingsFields\OrderInWidget: FIRST_LABEL (first label, then element), FIRST_ELEMENT (first element, then label)
     * @param array $additional
     *            Additional settings; there are the following additional settings which can be used: "checkedvalue" (string type; value which will be used for checked checkbox; if empty or does not exist, "1" will be used)
     * @return string Field
     */
    private static function fieldCheckbox(
        $componentObj,
        $fieldHtmlId,
        $fieldHtmlName,
        $fieldValue,
        $fieldAttrs,
        $tipText,
        array $classAndStyle,
        $fieldForWidget,
        $optionArray,
        $orderInWidget,
        array $additional
    ) {
        // get checked value
        $checkedValue = (isset ($additional ['checkedvalue'])) ? $additional ['checkedvalue'] : '1';
        // prepare class and style
        $class = $classAndStyle ['class'];
        if ($fieldForWidget === FieldForWidget::YES) {
            if (isset ($class [0]) /* strlen($class) > 0 */) {
                $class .= ' ';
            }
            $class .= 'checkbox';
        }
        $style = $classAndStyle ['style'] . 'border-width:0;width:auto;';
        // exit
        return '<input type="hidden" id="' . esc_attr($fieldHtmlId . '__checkbox_hidden') . '" name="' . esc_attr($fieldHtmlName) . '" value="0" /><input' . $fieldAttrs . ' type="checkbox" value="' . esc_attr($checkedValue) . '"' . ((isset ($class [0]) /* strlen($class) > 0 */) ? ' class="' . esc_attr($class) . '"' : '') . ' style="' . esc_attr($style) . '"' . (($fieldValue === $checkedValue) ? ' checked="checked"' : '') . ' />';
    }

    /**
     * Get field - select
     *
     * @access private
     * @param object $componentObj
     *            Component object
     * @param string $fieldHtmlId
     *            Field HTML id
     * @param string $fieldHtmlName
     *            Field HTML name
     * @param string $fieldValue
     *            Field value
     * @param string $fieldAttrs
     *            Prepared text with field attributes "id", "name" and "title" and with space at beginning
     * @param string $tipText
     *            Tooltip text
     * @param array $classAndStyle
     *            Class and style for element; class and style data must have the following fields: "all" (string type; HTML classes with "class" tag and HTML styles with "style" tag and space before), "class" (string type; HTML classes), "classwithtag" (string type; HTML classes with "class" tag and space before), "style" (string type; HTML styles), "stylewithtag" (string type; HTML styles with "style" tag and space before)
     * @param int $fieldForWidget
     *            This field is for widget or not; must be one of the following constants from \KocujIL\V13a\Enums\Project\Components\Backend\SettingsFields\FieldForWidget: NO (when field is not for widget) or YES (when field is for widget)
     * @param int $optionArray
     *            Option is array or standard; must be one of the following constants from \KocujIL\V13a\Enums\Project\Components\Backend\SettingsFields\OptionArray: NO (when it is standard option) or YES (when it is array option)
     * @param int $orderInWidget
     *            Label and HTML element order in widget; must be one of the following constants from \KocujIL\V13a\Enums\Project\Components\Backend\SettingsFields\OrderInWidget: FIRST_LABEL (first label, then element), FIRST_ELEMENT (first element, then label)
     * @param array $additional
     *            Additional settings; there are the following additional settings which can be used: "emptyoptionatbegin" (bool type; there will be an empty option at the beginning of options list), "options" (array type; options list; each array key will be an option value and each array value will be displayed text for option)
     * @return string Field
     */
    private static function fieldSelect(
        $componentObj,
        $fieldHtmlId,
        $fieldHtmlName,
        $fieldValue,
        $fieldAttrs,
        $tipText,
        array $classAndStyle,
        $fieldForWidget,
        $optionArray,
        $orderInWidget,
        array $additional
    ) {
        // set options
        $options = '';
        if ((isset ($additional ['options'])) && (!empty ($additional ['options']))) {
            if (($optionArray === OptionArray::YES) || ((isset ($additional ['emptyoptionatbegin'])) && ($additional ['emptyoptionatbegin']))) {
                $additional ['options'] = array(
                        '' => '----'
                    ) + $additional ['options'];
            }
            foreach ($additional ['options'] as $key => $val) {
                $options .= '<option value="' . esc_attr($key) . '"' . ((( string )$fieldValue === ( string )$key) ? ' selected="selected"' : '') . '>' . $val . '</option>';
            }
        }
        // exit
        return '<select' . $fieldAttrs . ' ' . $classAndStyle ['all'] . ' >' . $options . '</select>';
    }

    /**
     * Get field - button
     *
     * @access private
     * @param object $componentObj
     *            Component object
     * @param string $fieldHtmlId
     *            Field HTML id
     * @param string $fieldHtmlName
     *            Field HTML name
     * @param string $fieldValue
     *            Field value
     * @param string $fieldAttrs
     *            Prepared text with field attributes "id", "name" and "title" and with space at beginning
     * @param string $tipText
     *            Tooltip text
     * @param array $classAndStyle
     *            Class and style for element; class and style data must have the following fields: "all" (string type; HTML classes with "class" tag and HTML styles with "style" tag and space before), "class" (string type; HTML classes), "classwithtag" (string type; HTML classes with "class" tag and space before), "style" (string type; HTML styles), "stylewithtag" (string type; HTML styles with "style" tag and space before)
     * @param int $fieldForWidget
     *            This field is for widget or not; must be one of the following constants from \KocujIL\V13a\Enums\Project\Components\Backend\SettingsFields\FieldForWidget: NO (when field is not for widget) or YES (when field is for widget)
     * @param int $optionArray
     *            Option is array or standard; must be one of the following constants from \KocujIL\V13a\Enums\Project\Components\Backend\SettingsFields\OptionArray: NO (when it is standard option) or YES (when it is array option)
     * @param int $orderInWidget
     *            Label and HTML element order in widget; must be one of the following constants from \KocujIL\V13a\Enums\Project\Components\Backend\SettingsFields\OrderInWidget: FIRST_LABEL (first label, then element), FIRST_ELEMENT (first element, then label)
     * @param array $additional
     *            Additional settings; there are the following additional settings which can be used: "buttonlabel" (string type; label for button), "buttonprimary" (bool type; if it is set to true, it will be a primary button with different appearance than others)
     * @return string Field
     */
    private static function fieldButton(
        $componentObj,
        $fieldHtmlId,
        $fieldHtmlName,
        $fieldValue,
        $fieldAttrs,
        $tipText,
        array $classAndStyle,
        $fieldForWidget,
        $optionArray,
        $orderInWidget,
        array $additional
    ) {
        // prepare class
        $class = $classAndStyle ['class'];
        if (isset ($class [0]) /* strlen($class) > 0 */) {
            $class .= ' ';
        }
        $class .= ((isset ($additional ['buttonprimary']) && ($additional ['buttonprimary'])) ? 'button-primary' : 'button');
        // exit
        return '<input' . $fieldAttrs . ' type="button"' . ((isset ($class [0]) /* strlen($class) > 0 */) ? ' class="' . esc_attr($class) . '"' : '') . $classAndStyle ['stylewithtag'] . ' value="' . esc_attr(isset ($additional ['buttonlabel']) ? $additional ['buttonlabel'] : '') . '" />';
    }

    /**
     * Execute field header - image
     *
     * @access private
     * @param object $componentObj
     *            Component object
     * @return void
     */
    private static function fieldHeaderImage($componentObj)
    {
        // add script for adding media window
        wp_enqueue_media();
        // add uploading script
        JsHelper::getInstance()->addLibScript('backend-settings-fields-image',
            'project/components/backend/settings-fields', 'image', array(
                'helper'
            ), array(), 'kocujILV13aBackendSettingsFieldsImageVals', array(
                'textWindowTitle' => $componentObj->getStrings('settings-fields',
                    ProjectCategory::BACKEND)->getString('FIELD_HEADER_IMAGE_WINDOW_TITLE'),
                'textWindowButtonLabel' => $componentObj->getStrings('settings-fields',
                    ProjectCategory::BACKEND)->getString('FIELD_HEADER_IMAGE_WINDOW_BUTTON_LABEL')
            ));
    }

    /**
     * Get field - image
     *
     * @access private
     * @param object $componentObj
     *            Component object
     * @param string $fieldHtmlId
     *            Field HTML id
     * @param string $fieldHtmlName
     *            Field HTML name
     * @param string $fieldValue
     *            Field value
     * @param string $fieldAttrs
     *            Prepared text with field attributes "id", "name" and "title" and with space at beginning
     * @param string $tipText
     *            Tooltip text
     * @param array $classAndStyle
     *            Class and style for element; class and style data must have the following fields: "all" (string type; HTML classes with "class" tag and HTML styles with "style" tag and space before), "class" (string type; HTML classes), "classwithtag" (string type; HTML classes with "class" tag and space before), "style" (string type; HTML styles), "stylewithtag" (string type; HTML styles with "style" tag and space before)
     * @param int $fieldForWidget
     *            This field is for widget or not; must be one of the following constants from \KocujIL\V13a\Enums\Project\Components\Backend\SettingsFields\FieldForWidget: NO (when field is not for widget) or YES (when field is for widget)
     * @param int $optionArray
     *            Option is array or standard; must be one of the following constants from \KocujIL\V13a\Enums\Project\Components\Backend\SettingsFields\OptionArray: NO (when it is standard option) or YES (when it is array option)
     * @param int $orderInWidget
     *            Label and HTML element order in widget; must be one of the following constants from \KocujIL\V13a\Enums\Project\Components\Backend\SettingsFields\OrderInWidget: FIRST_LABEL (first label, then element), FIRST_ELEMENT (first element, then label)
     * @param array $additional
     *            Additional settings; there are the following additional settings which can be used: "buttonlabel" (string type; label for button), "buttonprimary" (bool type; if it is set to true, it will be a primary button with different appearance than others)
     * @return string Field
     */
    private static function fieldImage(
        $componentObj,
        $fieldHtmlId,
        $fieldHtmlName,
        $fieldValue,
        $fieldAttrs,
        $tipText,
        array $classAndStyle,
        $fieldForWidget,
        $optionArray,
        $orderInWidget,
        array $additional
    ) {
        // prepare class
        $class = $classAndStyle ['class'];
        if (isset ($class [0]) /* strlen($class) > 0 */) {
            $class .= ' ';
        }
        $class .= ((isset ($additional ['buttonprimary']) && ($additional ['buttonprimary'])) ? 'button-primary' : 'button');
        // exit
        return '<input type="hidden" id="' . esc_attr($fieldHtmlId . '__image_hidden') . '" name="' . esc_attr($fieldHtmlName) . '" value="' . esc_attr($fieldValue) . '" /><div class="image-preview-wrapper"><img id="' . esc_attr($fieldHtmlId . '__image_preview') . '" src="' . esc_url(($fieldValue === '0') ? LibUrls::getInstance()->get('images') . '/project/components/backend/settings-fields/empty.png' : wp_get_attachment_url($fieldValue)) . '" width="100" height="100" style="width:100px;max-height:100px;" /></div><input id="' . esc_attr($fieldHtmlId . '__image_upload') . '" type="button"' . ((isset ($class [0]) /* strlen($class) > 0 */) ? ' class="' . esc_attr($class) . '"' : '') . $classAndStyle ['stylewithtag'] . ' value="' . esc_attr(isset ($additional ['buttonlabel']) ? $additional ['buttonlabel'] : '') . '" />' . '<script type="text/javascript">' . PHP_EOL . '/* <![CDATA[ */' . PHP_EOL . '(function($) {' . PHP_EOL . '$(document).ready(function() {' . PHP_EOL . 'kocujILV13aBackendSettingsFieldsImage.addProjectIfNotExists(\'' . esc_js($componentObj->getProjectObj()->getMainSettingInternalName()) . '\', \'\');' . PHP_EOL . 'kocujILV13aBackendSettingsFieldsImage.process(\'' . esc_js($componentObj->getProjectObj()->getMainSettingInternalName()) . '\', \'' . esc_js($fieldHtmlId) . '\');' . PHP_EOL . '});' . PHP_EOL . '}(jQuery));' . PHP_EOL . '/* ]]> */' . PHP_EOL . '</script>' . PHP_EOL;
    }

    /**
     * Action for fields headers callbacks
     *
     * @access public
     * @return void
     */
    public function actionFieldsHeaders()
    {
        // execute fields headers callbacks
        foreach ($this->fieldsTypesForCurrentSite as $fieldType) {
            if ((isset ($this->types [$fieldType] ['callbackheader'])) && ($this->types [$fieldType] ['callbackheader'] !== null)) {
                call_user_func_array($this->types [$fieldType] ['callbackheader'], array(
                    $this
                ));
            }
        }
    }
}
