<?php

/**
 * component-object.class.php
 *
 * @author Dominik Kocuj
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2 or later
 * @copyright Copyright (c) 2016-2020 kocuj.pl
 * @package kocuj_internal_lib
 */

// set namespace
namespace KocujIL\V13a\Classes;

// set namespaces aliases
use KocujIL\V13a\Enums\ProjectCategory;

// security
if ((!defined('ABSPATH')) || ((isset ($_SERVER ['SCRIPT_FILENAME'])) && (basename($_SERVER ['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die ();
}

/**
 * Component object class
 *
 * @access public
 */
class ComponentObject
{

    /**
     * \KocujIL\V13a\Classes\Project object for current project
     *
     * @access private
     * @var object
     */
    private $projectObj;

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
        // remember project object
        $this->projectObj = $projectObj;
    }

    /**
     * Get object of type \KocujIL\V13a\Classes\Project
     *
     * @access public
     * @return object Object of type \KocujIL\V13a\Classes\Project
     */
    public function getProjectObj()
    {
        // get object of type \KocujIL\V13a\Classes\Project
        return $this->projectObj;
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
     */
    public function getComponent($type, $projectCategory = ProjectCategory::CORE, $fragment = '')
    {
        // exit
        return $this->projectObj->get($type, $projectCategory, $fragment);
    }

    /**
     * Get object of class type for strings from component
     *
     * @access public
     * @param string $type
     *            Component type
     * @param int $projectCategory
     *            Component category; must be one of the following constants from \KocujIL\V13a\Enums\ProjectCategory: CORE (for "core" category), ALL (for "all" category), FRONTEND (for "frontend" category) or BACKEND (for "backend" category) - default: \KocujIL\V13a\Enums\ProjectCategory::CORE
     * @return object Object of class type for strings from component
     */
    public function getStrings($type, $projectCategory = ProjectCategory::CORE)
    {
        // exit
        return $this->projectObj->getStringsObj($type, $projectCategory);
    }
}
