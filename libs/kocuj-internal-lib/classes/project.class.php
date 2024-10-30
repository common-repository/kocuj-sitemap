<?php

/**
 * project.class.php
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
final class Project extends ProjectParent
{

    /**
     * Namespace prefix
     *
     * @access protected
     * @var string
     */
    protected $namespacePrefix = '\\KocujIL\\V13a';

    /**
     * Project main settings
     *
     * @access private
     * @var array
     */
    private $settingsMain = array();

    /**
     * Constructor
     *
     * @access public
     * @param array $components
     *            Components to use
     * @param array $settingsMain
     *            Project main settings; project settings have the following fields: "licensename" (string type; name of license, for example, "GPL"), "mainfilename" (string type; main filename in project), "title" (string type; title of project), "type" (int type; type of project; it should be one of the constants from \KocujIL\V13a\Enums\ProjectType), "version" (string type; version of the project); there are also the following fields which can exists or not: "name" (string type; name of the project), "titleoriginal" (string type; original title of project; should not be translated), "url" (string type; URL to website from the project)
     * @param array $settings
     *            Project settings
     * @param array $stringsClasses
     *            Classes list for classes with strings which implement \KocujIL\V13a\Interfaces\Strings interface; if some keys are empty or does not exist, the default classes which returns only empty strings will be used for these keys - default: empty
     * @param array $libClasses
     *            Classes list for Kocuj Internal Lib which has been replaced by child classed; if some keys are empty or does not exist, the default classes will be used for these keys - default: empty
     * @return void
     * @throws Exception
     */
    public function __construct(
        array $components,
        array $settingsMain,
        array $settings,
        array $stringsClasses = array(),
        array $libClasses = array()
    ) {
        // check if there are all required main settings for project
        $reqSettings = array(
            'type',
            'mainfilename',
            'licensename',
            'title',
            'version'
        );
        foreach ($reqSettings as $setting) {
            if (!isset ($settingsMain [$setting])) {
                throw new Exception (null, ExceptionCode::NO_REQUIRED_SETTING_DATA, __FILE__,
                    __LINE__, 'main: ' . $setting);
            }
        }
        // remember settings
        $this->settingsMain = array(
            'type' => $settingsMain ['type'],
            'mainfilename' => $settingsMain ['mainfilename'],
            'licensename' => (isset ($settingsMain ['licensename'])) ? $settingsMain ['licensename'] : '',
            'title' => $settingsMain ['title'],
            'name' => (isset ($settingsMain ['name'])) ? $settingsMain ['name'] : $settingsMain ['title'],
            'version' => $settingsMain ['version'],
            'url' => (isset ($settingsMain ['url'])) ? $settingsMain ['url'] : '',
            'titleoriginal' => (isset ($settingsMain ['titleoriginal'])) ? $settingsMain ['titleoriginal'] : $settingsMain ['title']
        );
        $this->settingsMain ['internalname'] = str_replace(array(
            '-',
            '__'
        ), '_', sanitize_title($this->settingsMain ['name']));
        if (isset ($this->settingsMain ['internalname'] [30]) /* strlen($this->settingsMain['internalname']) > 30 */) {
            $this->settingsMain ['internalname'] = substr($this->settingsMain ['internalname'], 0, 30);
        }
        // execute parent constructor
        parent::__construct($components, $settings, $stringsClasses, $libClasses);
    }

    /**
     * Get project main setting with type
     *
     * @access public
     * @return string Project main setting with type
     */
    public function getMainSettingType()
    {
        // exit
        return $this->settingsMain ['type'];
    }

    /**
     * Get project main setting with main filename
     *
     * @access public
     * @return string Project main setting with main filename
     */
    public function getMainSettingMainFilename()
    {
        // exit
        return $this->settingsMain ['mainfilename'];
    }

    /**
     * Get project main setting with license name
     *
     * @access public
     * @return string Project main setting with license name
     */
    public function getMainSettingLicenseName()
    {
        // exit
        return $this->settingsMain ['licensename'];
    }

    /**
     * Get project main setting with title
     *
     * @access public
     * @return string Project main setting with title
     */
    public function getMainSettingTitle()
    {
        // exit
        return $this->settingsMain ['title'];
    }

    /**
     * Get project main setting with name
     *
     * @access public
     * @return string Project main setting with name
     */
    public function getMainSettingName()
    {
        // exit
        return $this->settingsMain ['name'];
    }

    /**
     * Get project main setting with internal name
     *
     * @access public
     * @return string Project main setting with internal name
     */
    public function getMainSettingInternalName()
    {
        // exit
        return $this->settingsMain ['internalname'];
    }

    /**
     * Get project main setting with version
     *
     * @access public
     * @return string Project main setting with version
     */
    public function getMainSettingVersion()
    {
        // exit
        return $this->settingsMain ['version'];
    }

    /**
     * Get project main setting with URL
     *
     * @access public
     * @return string Project main setting with URL
     */
    public function getMainSettingUrl()
    {
        // exit
        return $this->settingsMain ['url'];
    }

    /**
     * Get project main setting with original title
     *
     * @access public
     * @return string Project main setting with original title
     */
    public function getMainSettingTitleOriginal()
    {
        // exit
        return $this->settingsMain ['titleoriginal'];
    }
}
