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
namespace KocujIL\V13a\Classes\Project\Components\Core\Urls;

// set namespaces aliases
use KocujIL\V13a\Classes\ComponentObject;
use KocujIL\V13a\Enums\ProjectType;

// security
if ((!defined('ABSPATH')) || ((isset ($_SERVER ['SCRIPT_FILENAME'])) && (basename($_SERVER ['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die ();
}

/**
 * URLS-s class
 *
 * @access public
 */
class Component extends ComponentObject
{

    /**
     * Project URL
     *
     * @access private
     * @var string
     */
    private $projectUrl;

    /**
     * URL-s
     *
     * @access private
     * @var array
     */
    private $urls;

    /**
     * Directories
     *
     * @access private
     * @var array
     */
    private $dirs = array();

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
        // set URL-s
        $this->projectUrl = ($this->getProjectObj()->getMainSettingType() === ProjectType::PLUGIN) ? plugin_dir_url($this->getProjectObj()->getMainSettingMainFilename()) : get_template_directory_uri();
        $this->urls = array(
            'customimages' => 'images',
            'tinymcebuttonsjs' => 'js/tiny-mce',
            'tinymcebuttonsphp' => 'php/tiny-mce'
        );
    }

    /**
     * Get project URL
     *
     * @access public
     * @return string Project URL
     */
    public function getProjectUrl()
    {
        // get project URL
        return $this->projectUrl;
    }

    /**
     * Get project URL with the selected type
     *
     * @access public
     * @param string $type
     *            Type
     * @return string Project URL with the selected type
     */
    public function get($type)
    {
        // check if type exists
        if (!isset ($this->urls [$type])) {
            return '';
        }
        // optionally get new URL
        $dir = $this->getComponent('dirs')->getSubDir($type);
        if ((!isset ($this->dirs [$type])) || ($this->dirs [$type] !== $dir)) {
            // set new URL
            $this->urls [$type] = str_replace(DIRECTORY_SEPARATOR, '/',
                substr($dir, strlen($this->getComponent('dirs')->getProjectDir())));
            // remember new subdirectory
            $this->dirs [$type] = $dir;
        }
        // exit
        return $this->projectUrl . '/' . $this->urls [$type];
    }
}
