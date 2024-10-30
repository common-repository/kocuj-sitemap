<?php

/**
 * project-parent.class.php
 *
 * @author Dominik Kocuj
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2 or later
 * @copyright Copyright (c) 2016-2020 kocuj.pl
 * @package kocuj_internal_lib
 */

// set namespace
namespace KocujIL\V13a\Classes;

// set namespaces aliases
use KocujIL\V13a\Enums\ExceptionCode;
use KocujIL\V13a\Enums\ProjectCategory;

// security
if ((!defined('ABSPATH')) || ((isset ($_SERVER ['SCRIPT_FILENAME'])) && (basename($_SERVER ['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die ();
}

/**
 * Project parent class
 *
 * @access public
 */
class ProjectParent
{

    /**
     * Namespace prefix
     *
     * @access protected
     * @var string
     */
    protected $namespacePrefix = '';

    /**
     * Project components
     *
     * @access private
     * @var array
     */
    private $components;

    /**
     * Project components errors
     *
     * @access private
     * @var array
     */
    private $componentsErrors = array();

    /**
     * Project settings
     *
     * @access private
     * @var array
     */
    private $settings;

    /**
     * Classes list for classes with strings which implement \KocujIL\V13a\Interfaces\Strings interface
     *
     * @access private
     * @var array
     */
    private $stringsClassesList;

    /**
     * Objects with instances of classes for components
     *
     * @access private
     * @var array
     */
    private $libComponentsObjects = array();

    /**
     * Shutdown flag
     *
     * @access private
     * @var bool
     */
    private $shutdown = false;

    /**
     * Initialization classes to remove
     *
     * @access private
     * @var array
     */
    private $initsToRemove = array();

    /**
     * Constructor
     *
     * @access public
     * @param array $components
     *            Components to use
     * @param array $settings
     *            Project settings
     * @param array $stringsClasses
     *            Classes list for classes with strings which implement \KocujIL\V13a\Interfaces\Strings interface; if some keys are empty or does not exist, the default classes which returns only empty strings will be used for these keys - default: empty
     * @param array $additionalProjectsForRequirements
     *            Additional projects for checking requirements; each element should be objec of class derived from this class (ProjectParent) and its key should be the name of class derived from this class (ProjectParent) - default: empty
     * @return void
     * @throws Exception
     */
    public function __construct(
        array $components,
        array $settings,
        array $stringsClasses = array(),
        array $additionalProjectsForRequirements = array()
    ) {
        // remember settings
        $this->settings = $settings;
        // set strings classes
        $this->stringsClassesList = $stringsClasses;
        // set components
        $this->components = $components;
        // initialize components
        foreach ($this->components as $category => $data) {
            if ($category !== ProjectCategory::CORE) {
                foreach ($data as $component) {
                    $classInit = $this->get($component, $category, 'init');
                    if ($classInit !== null) {
                        if (!isset ($this->initsToRemove [$category])) {
                            $this->initsToRemove [$category] = array();
                        }
                        $this->initsToRemove [$category] [$component] = true;
                        $errors = $classInit->getErrors();
                        if (!empty ($errors)) {
                            if (!isset ($this->componentsErrors [$category])) {
                                $this->componentsErrors [$category] = array();
                            }
                            $this->componentsErrors [$category] [$component] = $errors;
                        }
                        $requiredComponents = $classInit->getRequiredComponents();
                        foreach ($requiredComponents as $requiredLibrary => $requiredLibraryData) {
                            if (!empty ($requiredLibraryData)) {
                                $thisReq = true;
                                if (!isset ($requiredLibrary [0]) /* strlen($requiredLibrary) === 0 */) {
                                    $obj = $this;
                                } else {
                                    if (isset ($additionalProjectsForRequirements [$requiredLibrary])) {
                                        $thisReq = false;
                                        $obj = $additionalProjectsForRequirements [$requiredLibrary];
                                    } else {
                                        throw new Exception (null,
                                            ExceptionCode::NO_REQUIRED_LIBRARY, __FILE__, __LINE__,
                                            $requiredLibrary);
                                    }
                                }
                                foreach ($requiredLibraryData as $requiredCategory => $requiredCategoryData) {
                                    if (!empty ($requiredCategoryData)) {
                                        foreach ($requiredCategoryData as $requiredComponent) {
                                            if (($requiredCategory === ProjectCategory::CORE) || ($requiredCategory === ProjectCategory::ALL) || ($requiredCategory === $category)) {
                                                $classInit = $obj->get($requiredComponent, $requiredCategory, 'init');
                                                if ($thisReq) {
                                                    if (!isset ($this->initsToRemove [$requiredCategory])) {
                                                        $this->initsToRemove [$requiredCategory] = array();
                                                    }
                                                    $this->initsToRemove [$requiredCategory] [$requiredComponent] = true;
                                                }
                                                if ($classInit === null) {
                                                    throw new Exception (null,
                                                        ExceptionCode::NO_REQUIRED_COMPONENT,
                                                        __FILE__, __LINE__,
                                                        'library: ' . $requiredLibrary . ',category: ' . $requiredCategory . ', component: ' . $requiredComponent);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        // add action for removing all initialization classes
        add_action('plugins_loaded', array(
            $this,
            'actionRemoveInits'
        ), 999);
        // add shutdown action
        add_action('shutdown', array(
            $this,
            'actionShutdown'
        ), 1);
    }

    /**
     * Get project setting with string type
     *
     * @access public
     * @param string $type
     *            Data type
     * @return string Project setting
     */
    public function getSettingString($type)
    {
        // exit
        return (isset ($this->settings [$type])) ? $this->settings [$type] : '';
    }

    /**
     * Get project setting with array type
     *
     * @access public
     * @param string $type
     *            Data type
     * @param string $index
     *            Data index
     * @return array|string Project setting
     */
    public function getSettingArray($type, $index = '')
    {
        // optionally return entire array
        if (!isset ($index [0]) /* strlen($index) === 0 */) {
            return ((isset ($this->settings [$type])) && (is_array($this->settings [$type]))) ? $this->settings [$type] : array();
        }
        // exit
        return ((isset ($this->settings [$type] [$index])) && (is_array($this->settings [$type]))) ? $this->settings [$type] [$index] : '';
    }

    /**
     * Get object of class type which implements \KocujIL\V13a\Interfaces\Strings interface
     *
     * @access public
     * @param string $type
     *            Component type
     * @param int $projectCategory
     *            Component category; must be one of the following constants from \KocujIL\V13a\Enums\ProjectCategory: CORE (for "core" category), ALL (for "all" category), FRONTEND (for "frontend" category) or BACKEND (for "backend" category) - default: \KocujIL\V13a\Enums\ProjectCategory::CORE
     * @return object Object of class type which implements \KocujIL\V13a\Interfaces\Strings interface
     * @throws Exception
     */
    public function getStringsObj($type, $projectCategory = ProjectCategory::CORE)
    {
        // check if there was not a shutdown
        if ($this->shutdown) {
            throw new Exception (null, ExceptionCode::CANNOT_USE_COMPONENTS_AFTER_SHUTDOWN,
                __FILE__, __LINE__);
        }
        // optionally create instance of class
        if (isset ($this->stringsClassesList [$projectCategory] [$type])) {
            $interfaces = class_implements($this->stringsClassesList [$projectCategory] [$type]);
            if (in_array('KocujIL\\V13a\\Interfaces\\Strings', $interfaces)) {
                return call_user_func(array(
                    $this->stringsClassesList [$projectCategory] [$type],
                    'getInstance'
                ));
            }
        }
        // exit
        return StringsEmpty::getInstance();
    }

    /**
     * Load class for namespace prefix
     *
     * @access private
     * @param string $namespacePrefix
     *            Namespace prefix
     * @param string $className
     *            Class name
     * @param int $projectCategory
     *            Component category; must be one of the following constants from \KocujIL\V13a\Enums\ProjectCategory: CORE (for "core" category), ALL (for "all" category), FRONTEND (for "frontend" category) or BACKEND (for "backend" category)
     * @param string $type
     *            Component type
     * @param string $fragmentIndex
     *            Component fragment index
     * @return bool Class has been loaded (true) or not (false)
     */
    private function loadClassForNamespacePrefix($namespacePrefix, $className, $projectCategory, $type, $fragmentIndex)
    {
        // load class
        $className = $namespacePrefix . '\\' . $className;
        if (!isset ($this->libComponentsObjects [$projectCategory])) {
            $this->libComponentsObjects [$projectCategory] = array();
        }
        if (!isset ($this->libComponentsObjects [$projectCategory] [$type])) {
            $this->libComponentsObjects [$projectCategory] [$type] = array();
        }
        if (is_subclass_of($className, '\\KocujIL\\V13a\\Classes\\ComponentObject')) {
            $this->libComponentsObjects [$projectCategory] [$type] [$fragmentIndex] = new $className ($this);
            return true;
        } else {
            $this->libComponentsObjects [$projectCategory] [$type] [$fragmentIndex] = null;
            return false;
        }
    }

    /**
     * Get object of class type from component
     *
     * @access public
     * @param string $type
     *            Component type
     * @param int $projectCategory
     *            Component category; must be one of the following constants from \KocujIL\V13a\Enums\ProjectCategory: CORE (for "core" category), ALL (for "all" category), FRONTEND (for "frontend" category) or BACKEND (for "backend" category) - default: \KocujIL\V13a\Enums\ProjectCategory::CORE
     * @param string $fragment
     *            Component fragment - default: empty
     * @return object Object of class type from component
     * @throws Exception
     */
    public function get($type, $projectCategory = ProjectCategory::CORE, $fragment = '')
    {
        // check if there was not a shutdown
        if ($this->shutdown) {
            throw new Exception (null, ExceptionCode::CANNOT_USE_COMPONENTS_AFTER_SHUTDOWN,
                __FILE__, __LINE__);
        }
        // check if component has been declared as used by project
        if (($projectCategory !== ProjectCategory::CORE) && (isset ($this->components [$projectCategory])) && (!in_array($type,
                $this->components [$projectCategory]))) {
            return null;
        }
        // check if this component can be used
        if ((($projectCategory === ProjectCategory::FRONTEND) && ((is_admin()) || (is_network_admin()))) || (($projectCategory === ProjectCategory::BACKEND) && (!is_admin()) && (!is_network_admin()))) {
            return null;
        }
        // set fragment value
        $fragmentIndex = (isset ($fragment [0]) /* strlen($fragment) === 0 */) ? $fragment : '_';
        // optionally initialize object
        if (!isset ($this->libComponentsObjects [$projectCategory] [$type] [$fragmentIndex])) {
            // prepare category name for fragments
            $categories = array(
                ProjectCategory::ALL => 'all',
                ProjectCategory::FRONTEND => 'frontend',
                ProjectCategory::BACKEND => 'backend'
            );
            $categoryName = isset ($categories [$projectCategory]) ? $categories [$projectCategory] : 'core';
            // prepare fragments
            $fragments = array(
                $categoryName,
                $type,
                (isset ($fragment [0]) /* strlen($fragment) > 0 */) ? $fragment : 'component'
            );
            // get class name
            foreach ($fragments as $key => $val) {
                $fragments [$key] = preg_replace_callback('/-([a-z])/', function ($matches) {
                    return strtoupper($matches [1]);
                }, $val);
                if (isset ($fragments [$key] [1]) /* strlen($fragments[$key]) > 1 */) {
                    $fragments [$key] = strtoupper($fragments [$key] [0]) . substr($fragments [$key], 1);
                }
            }
            // initialize object
            $className = 'Classes\\Project\\Components\\' . implode('\\', $fragments);
            $classLoaded = $this->loadClassForNamespacePrefix($this->namespacePrefix, $className, $projectCategory,
                $type, $fragmentIndex);
            if ((!$classLoaded) && ($projectCategory === ProjectCategory::CORE)) {
                $classLoaded = $this->loadClassForNamespacePrefix('\\KocujIL\\V13a', $className, $projectCategory,
                    $type, $fragmentIndex);
            }
            // if fragment is "init", make additional initialization
            if (($classLoaded) && ($fragment === 'init')) {
                // check if current script is "customizer"
                $screenId = CurrentScreenIdHelper::getInstance()->get();
                $isCustomizer = ($screenId ['scriptname'] === 'customize.php');
                // optionally initialize actions and filters
                if ((!$isCustomizer) || (($isCustomizer) && ($this->libComponentsObjects [$projectCategory] [$type] [$fragmentIndex]->getAllowActionsAndFiltersInCustomizer()))) {
                    $this->libComponentsObjects [$projectCategory] [$type] [$fragmentIndex]->actionsAndFilters();
                }
            }
        }
        // exit
        return $this->libComponentsObjects [$projectCategory] [$type] [$fragmentIndex];
    }

    /**
     * Get component errors
     *
     * @access public
     * @param int $projectCategory
     *            Component category; must be one of the following constants from \KocujIL\V13a\Enums\ProjectCategory: CORE (for "core" category), ALL (for "all" category), FRONTEND (for "frontend" category) or BACKEND (for "backend" category)
     * @param string $type
     *            Component type
     * @return array Component errors
     */
    public function getComponentErrors($projectCategory, $type)
    {
        // exit
        return (isset ($this->componentsErrors [$projectCategory] [$type])) ? $this->componentsErrors [$projectCategory] [$type] : array();
    }

    /**
     * Action for removing all initialization classes
     *
     * @access public
     * @return void
     */
    public function actionRemoveInits()
    {
        // remove all initialization classes
        if (!empty ($this->initsToRemove)) {
            foreach ($this->initsToRemove as $initCategory => $initVal) {
                foreach ($initVal as $initComponent => $initVal2) {
                    $this->libComponentsObjects [$initCategory] [$initComponent] ['init'] = null;
                    unset ($this->libComponentsObjects [$initCategory] [$initComponent] ['init']);
                }
            }
            $this->initsToRemove = array();
        }
    }

    /**
     * Shutdown action
     *
     * @access public
     * @return void
     */
    public function actionShutdown()
    {
        // shutdown all components
        unset ($this->libComponentsObjects);
        // set shutdown flag
        $this->shutdown = true;
    }
}
