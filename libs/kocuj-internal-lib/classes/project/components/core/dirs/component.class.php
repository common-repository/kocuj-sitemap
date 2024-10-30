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
namespace KocujIL\V13a\Classes\Project\Components\Core\Dirs;

// set namespaces aliases
use KocujIL\V13a\Classes\ComponentObject;

// security
if ((!defined('ABSPATH')) || ((isset ($_SERVER ['SCRIPT_FILENAME'])) && (basename($_SERVER ['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die ();
}

/**
 * Directories class
 *
 * @access public
 */
class Component extends ComponentObject
{

    /**
     * Project directory
     *
     * @access private
     * @var string
     */
    private $projectDir;

    /**
     * Directories
     *
     * @access private
     * @var array
     */
    private $dirs;

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
        // set directories
        $this->projectDir = dirname($this->getProjectObj()->getMainSettingMainFilename());
        $this->dirs = array(
            'customimages' => 'images',
            'tinymcebuttonsjs' => 'js' . DIRECTORY_SEPARATOR . 'tiny-mce',
            'tinymcebuttonsphp' => 'php' . DIRECTORY_SEPARATOR . 'tiny-mce'
        );
    }

    /**
     * Get project directory
     *
     * @access public
     * @return string Project directory
     */
    public function getProjectDir()
    {
        // get project directory
        return $this->projectDir;
    }

    /**
     * Set project subdirectory with the selected type
     *
     * @access public
     * @param string $type
     *            Type
     * @param string $subDir
     *            Project subdirectory with the selected type
     * @return void
     */
    public function setSubDir($type, $subDir)
    {
        // set subdirectory
        if (isset ($this->dirs [$type])) {
            $this->dirs [$type] = $subDir;
        }
    }

    /**
     * Get project subdirectory with the selected type
     *
     * @access public
     * @param string $type
     *            Type
     * @return string Project subdirectory with the selected type
     */
    public function getSubDir($type)
    {
        // exit
        return (isset ($this->dirs [$type])) ? $this->projectDir . DIRECTORY_SEPARATOR . $this->dirs [$type] : '';
    }
}
