<?php

/**
 * widget.class.php
 *
 * @author Dominik Kocuj
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2 or later
 * @copyright Copyright (c) 2016-2020 kocuj.pl
 * @package kocuj_internal_lib
 */

// set namespace
namespace KocujIL\V13a\Classes\Project\Components\All\Widget;

// set namespaces aliases
use KocujIL\V13a\Enums\Project\Components\All\Options\ContainerType;
use KocujIL\V13a\Enums\ProjectCategory;
use WP_Widget;

// security
if ((!defined('ABSPATH')) || ((isset ($_SERVER ['SCRIPT_FILENAME'])) && (basename($_SERVER ['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die ();
}

/**
 * Widget class
 *
 * @access public
 */
class Widget extends WP_Widget
{

    /**
     * Project object
     *
     * @access private
     * @var object
     */
    private $projectObj = null;

    /**
     * Container identifier
     *
     * @access private
     * @var string
     */
    private $containerId = '';

    /**
     * Additional settings
     *
     * @access private
     * @var array
     */
    private $additional = array();

    /**
     * Get project object
     *
     * @access protected
     * @return object Project object
     */
    protected function getProjectObj()
    {
        // exit
        return $this->projectObj;
    }

    /**
     * Get container identifier
     *
     * @access protected
     * @return string Container identifier
     */
    protected function getContainerId()
    {
        // exit
        return $this->containerId;
    }

    /**
     * Get additional settings
     *
     * @access protected
     * @return array Additional settings
     */
    protected function getAdditional()
    {
        // exit
        return $this->additional;
    }

    /**
     * Constructor
     *
     * @access public
     * @param string $widgetId
     *            Widget identifier
     * @param string $title
     *            Widget title
     * @param array $settings
     *            Settings; there are the following settings which must be used: "global_additional" (array type; additional settings; there are the following additional settings which can be used: "defaultitle" (string type; default title which will be used if title is empty), "optiondisplaytitle" (string type; option name for displaying or not displaying the title), "optiondisplaytitlelabel" (string type; label for option for displaying or not displaying the title), "optiondisplaytitletooltip" (string type; tooltip for option for displaying or not displaying the title), "optiontitle" (string type; option name for title), "optiontitlelabel" (string type; label for option for title), "optiontitletooltip" (string type; tooltip for option for title)), "global_containerid" (string type; container identifier), "global_description" (string type; widget description), "global_projectobj" (object type; project object)
     * @return void
     */
    public function __construct($widgetId, $title, $settings)
    {
        // get settings
        $projectObj = $settings ['global_projectobj'];
        $containerId = $settings ['global_containerid'];
        $description = $settings ['global_description'];
        $additional = $settings ['global_additional'];
        // remember project object
        $this->projectObj = $projectObj;
        // remember container identifier
        $this->containerId = $containerId;
        // remember additional settings
        $this->additional = $additional;
        // execute parent constructor
        parent::__construct($projectObj->getMainSettingInternalName() . '__' . $widgetId, $title, array(
            'description' => $description
        ));
        // add options container
        $projectObj->get('options', ProjectCategory::ALL)->addContainer($containerId, false, ContainerType::WIDGET);
        // optionally add some options
        if (isset ($additional ['optiontitle'])) {
            $projectObj->get('options', ProjectCategory::ALL)->addDefinition($containerId,
                $additional ['optiontitle'], 'text', '',
                isset ($additional ['optiontitlelabel']) ? $additional ['optiontitlelabel'] : $projectObj->getStringsObj('widget',
                    ProjectCategory::ALL)->getString('WIDGET_CONSTRUCT_OPTION_TITLE_LABEL'));
        }
        if (isset ($additional ['optiondisplaytitle'])) {
            $projectObj->get('options', ProjectCategory::ALL)->addDefinition($containerId,
                $additional ['optiondisplaytitle'], 'checkbox', '1',
                isset ($additional ['optiondisplaytitlelabel']) ? $additional ['optiondisplaytitlelabel'] : $projectObj->getStringsObj('widget',
                    ProjectCategory::ALL)->getString('WIDGET_CONSTRUCT_OPTION_DISPLAY_TITLE_LABEL'));
        }
    }

    /**
     * Display widget content; should be used inside "widget" method to display widget content
     *
     * @access protected
     * @param array $args
     *            Widget arguments
     * @param string $widgetOutput
     *            Widget output to display
     * @return void
     */
    protected function displayWidgetContent($args, $widgetOutput)
    {
        // display widget beginning
        echo $args ['before_widget'];
        // display widget title
        $displayTitle = (isset ($this->additional ['optiondisplaytitle'])) ? ($this->projectObj->get('options',
                ProjectCategory::ALL)->getOption($this->containerId,
                $this->additional ['optiondisplaytitle']) === '1') : true;
        if ($displayTitle) {
            // get title
            $title = (isset ($this->additional ['optiontitle'])) ? $this->projectObj->get('options',
                ProjectCategory::ALL)->getOption($this->containerId,
                $this->additional ['optiontitle']) : '';
            if ((isset ($this->additional ['defaulttitle'])) && (!isset ($title [0]) /* strlen($title) === 0 */)) {
                $title = $this->additional ['defaulttitle'];
            }
            // display title
            echo $args ['before_title'] . apply_filters('widget_title', $title) . $args ['after_title'];
        }
        // display widget
        echo $widgetOutput;
        // display widget ending
        echo $args ['after_widget'];
    }

    /**
     * Display widget
     *
     * @access public
     * @param array $args
     *            Widget arguments
     * @param array $options
     *            Widget options
     * @return void
     */
    public function widget($args, $options)
    {
        // load container with options
        $this->projectObj->get('options',
            ProjectCategory::ALL)->loadContainerForWidget($this->containerId, $options);
    }

    /**
     * Update widget options
     *
     * @access public
     * @param array $newOptions
     *            New options
     * @param array $oldOptions
     *            Old options
     * @return array Parsed values
     */
    public function update($newOptions, $oldOptions)
    {
        // load container with old options
        $this->projectObj->get('options',
            ProjectCategory::ALL)->loadContainerForWidget($this->containerId, $oldOptions);
        // update options
        foreach ($newOptions as $key => $val) {
            $this->projectObj->get('options', ProjectCategory::ALL)->setOption($this->containerId,
                $key, $val);
        }
        // prepare output
        $output = array();
        $definitions = $this->projectObj->get('options',
            ProjectCategory::ALL)->getDefinitions($this->containerId);
        foreach ($definitions as $key => $val) {
            $output [$key] = $this->projectObj->get('options',
                ProjectCategory::ALL)->getOption($this->containerId, $key);
        }
        // exit
        return $output;
    }

    /**
     * Display form; should be used inside "form" method to display form
     *
     * @access protected
     * @return void
     */
    protected function displayForm()
    {
        // display form
        $this->projectObj->get('settings-form',
            ProjectCategory::BACKEND)->showForm($this->containerId);
    }

    /**
     * Display widget form
     *
     * @access public
     * @param array $options
     *            Widget options
     * @return void
     */
    public function form($options)
    {
        // load container with options
        $this->projectObj->get('options',
            ProjectCategory::ALL)->loadContainerForWidget($this->containerId, $options);
        // add form
        $form = $this->projectObj->get('settings-form',
            ProjectCategory::BACKEND)->getForm($this->containerId);
        if ($form !== false) {
            $this->projectObj->get('settings-form',
                ProjectCategory::BACKEND)->removeTab($this->containerId, $this->containerId);
            $this->projectObj->get('settings-form',
                ProjectCategory::BACKEND)->removeForm($this->containerId);
        }
        $this->projectObj->get('settings-form',
            ProjectCategory::BACKEND)->addForm($this->containerId, $this->containerId);
        $this->projectObj->get('settings-form',
            ProjectCategory::BACKEND)->addTab($this->containerId, $this->containerId, '');
        if (isset ($this->additional ['optiontitle'])) {
            $this->projectObj->get('settings-form',
                ProjectCategory::BACKEND)->addOptionFieldToTab($this->containerId,
                $this->containerId, 'text', $this->additional ['optiontitle'],
                isset ($this->additional ['optiontitletooltip']) ? $this->additional ['optiontitletooltip'] : $this->projectObj->getStringsObj('widget',
                    ProjectCategory::ALL)->getString('WIDGET_CONSTRUCT_OPTION_TITLE_TOOLTIP'),
                array(), array(
                    'global_widgetobj' => $this
                ));
        }
        if (isset ($this->additional ['optiondisplaytitle'])) {
            $this->projectObj->get('settings-form',
                ProjectCategory::BACKEND)->addOptionFieldToTab($this->containerId,
                $this->containerId, 'checkbox', $this->additional ['optiondisplaytitle'],
                isset ($this->additional ['optiondisplaytitletooltip']) ? $this->additional ['optiondisplaytitletooltip'] : $this->projectObj->getStringsObj('widget',
                    ProjectCategory::ALL)->getString('WIDGET_CONSTRUCT_OPTION_DISPLAY_TITLE_TOOLTIP'),
                array(), array(
                    'global_widgetobj' => $this
                ));
        }
    }
}
