<?php

/**
 * project.class.php
 *
 * @author Dominik Kocuj
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2 or later
 * @copyright Copyright (c) 2016-2020 kocuj.pl
 * @package kocuj_internal_lib\kocuj_pl_lib
 */

// set namespace
namespace KocujPlLib\V13a\Classes;

// set namespaces aliases
use KocujIL\V13a as KocujIL;
use KocujPlLib\V13a\Enums\ExceptionCode;

// security
if ((!defined('ABSPATH')) || ((isset ($_SERVER ['SCRIPT_FILENAME'])) && (basename($_SERVER ['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die ();
}

/**
 * Project class
 *
 * @access public
 */
final class Project extends KocujIL\Classes\ProjectParent
{

    /**
     * Namespace prefix
     *
     * @access protected
     * @var string
     */
    protected $namespacePrefix = '\\KocujPlLib\\V13a';

    /**
     * Project object for Kocuj Internal Lib
     *
     * @access private
     * @var object
     */
    private $projectKocujInternaLibObj = null;

    /**
     * Constructor
     *
     * @access public
     * @param object $projectKocujInternaLibObj
     *            Project object for Kocuj Internal Lib
     * @param array $components
     *            Components to use
     * @param array $settings
     *            Project settings
     * @param array $stringsClasses
     *            Classes list for classes with strings which implement \KocujPlLib\V13a\Interfaces\Strings interface; if some keys are empty or does not exist, the default classes which returns only empty strings will be used for these keys - default: empty
     * @param array $additionalProjectsForRequirements
     *            Additional projects for checking requirements; each element should be objec of class derived from this class (ProjectParent) and its key should be the name of class derived from this class (ProjectParent) - default: empty
     * @throws Exception
     * @throws KocujIL\Classes\Exception
     */
    public function __construct(
        $projectKocujInternaLibObj,
        array $components,
        array $settings,
        array $stringsClasses = array(),
        $additionalProjectsForRequirements = array()
    ) {
        // set Kocuj Internal Lib project object
        if (!($projectKocujInternaLibObj instanceof KocujIL\Classes\Project)) {
            throw new Exception (null, ExceptionCode::OBJECT_IS_NOT_KOCUJ_INTERNAL_LIB_PROJECT,
                __FILE__, __LINE__);
        }
        $this->projectKocujInternaLibObj = $projectKocujInternaLibObj;
        // execute parent constructor
        parent::__construct($components, $settings, $stringsClasses, $additionalProjectsForRequirements);
    }

    /**
     * Get project object for Kocuj Internal Lib
     *
     * @access public
     * @return object Project object for Kocuj Internal Lib
     */
    public function getProjectKocujILObj()
    {
        // exit
        return $this->projectKocujInternaLibObj;
    }
}
