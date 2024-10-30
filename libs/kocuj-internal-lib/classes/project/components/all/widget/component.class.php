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
namespace KocujIL\V13a\Classes\Project\Components\All\Widget;

// set namespace
use KocujIL\V13a\Classes\ComponentObject;
use KocujIL\V13a\Classes\Exception;
use KocujIL\V13a\Enums\Project\Components\All\Widget\ExceptionCode;

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
class Component extends ComponentObject
{

    /**
     * Widgets
     *
     * @access private
     * @var array
     */
    private $widgets = array();

    /**
     * Add widget
     *
     * @access public
     * @param string $class
     *            Widget class name
     * @return void
     * @throws Exception
     */
    public function addWidget($class)
    {
        // check if this widget class does not already exists
        if (in_array($class, $this->widgets)) {
            throw new Exception ($this, ExceptionCode::WIDGET_EXISTS, __FILE__, __LINE__, $class);
        }
        // add widget
        $this->widgets [] = $class;
    }

    /**
     * Get widgets data
     *
     * @access public
     * @return array Widgets data; each widget data contains widget class name
     */
    public function getWidgets()
    {
        // exit
        return $this->widgets;
    }

    /**
     * Check if widget exists
     *
     * @access public
     * @param string $class
     *            Widget class name
     * @return bool Widget exists (true) or not (false)
     */
    public function checkWidget($class)
    {
        // exit
        return in_array($class, $this->widgets);
    }

    /**
     * Remove widget
     *
     * @access public
     * @param string $class
     *            Widget class name
     * @return void
     * @throws Exception
     */
    public function removeWidget($class)
    {
        // check if this widget class exists
        if (!in_array($class, $this->widgets)) {
            throw new Exception ($this,
                ExceptionCode::WIDGET_DOES_NOT_EXIST, __FILE__,
                __LINE__, $class);
        }
        // remove widget
        $pos = array_search($class, $this->widgets);
        unset ($this->widgets [$pos]);
    }

    /**
     * Action for widgets initialization
     *
     * @access public
     * @return void
     * @throws Exception
     */
    public function actionWidgetsInit()
    {
        // initialize widgets
        foreach ($this->widgets as $className) {
            // check if class exists
            if (!class_exists($className)) {
                throw new Exception ($this,
                    ExceptionCode::CLASS_DOES_NOT_EXIST, __FILE__,
                    __LINE__, $className);
            }
            // check if class is child of Widget class
            if (!is_subclass_of($className, '\\KocujIL\\V13a\\Classes\\Project\\Components\\All\\Widget\\Widget')) {
                throw new Exception ($this,
                    ExceptionCode::WRONG_CLASS_PARENT, __FILE__,
                    __LINE__, $className);
            }
            // register widget
            register_widget($className);
        }
    }
}
