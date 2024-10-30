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
namespace KocujIL\V13a\Classes\Project\Components\All\Version;

// set namespaces aliases
use KocujIL\V13a\Classes\ComponentObject;
use KocujIL\V13a\Enums\Project\Components\Core\Meta\Type;

// security
if ((!defined('ABSPATH')) || ((isset ($_SERVER ['SCRIPT_FILENAME'])) && (basename($_SERVER ['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die ();
}

/**
 * Version class
 *
 * @access public
 */
class Component extends ComponentObject
{

    /**
     * Current version of project
     *
     * @access private
     * @var string
     */
    private $currentVersion = '';

    /**
     * Update callbacks
     *
     * @access private
     * @var array
     */
    private $updateCallbacks = array();

    /**
     * Get option name for version
     *
     * @access public
     * @return string Option name for version
     */
    public static function getOptionNameVersion()
    {
        // exit
        return 'version';
    }

    /**
     * Get option name for old version number
     *
     * @access public
     * @return string Option name for old version number
     */
    public static function getOptionNameOldVersion()
    {
        // exit
        return 'old_version';
    }

    /**
     * Get version number from database
     *
     * @access public
     * @return string Version number from database
     */
    public function getVersionOptionValue()
    {
        // exit
        return $this->getComponent('meta')->get(self::getOptionNameVersion(), false, Type::AUTO);
    }

    /**
     * Get old version number from database
     *
     * @access public
     * @return array Old version number from database
     */
    public function getOldVersionOptionValue()
    {
        // exit
        return $this->getComponent('meta')->get(self::getOptionNameOldVersion(), false,
            Type::AUTO);
    }

    /**
     * Get version number for site from database
     *
     * @access public
     * @return string Version number for site from database
     */
    public function getVersionSiteOptionValue()
    {
        // exit
        return $this->getComponent('meta')->get(self::getOptionNameVersion(), false,
            Type::SITE);
    }

    /**
     * Get old version number for site from database
     *
     * @access public
     * @return array Old version number for site from database
     */
    public function getOldVersionSiteOptionValue()
    {
        // exit
        return $this->getComponent('meta')->get(self::getOptionNameOldVersion(), false,
            Type::SITE);
    }

    /**
     * Set current version of project
     *
     * @access public
     * @param string $version
     *            Current version of project
     * @return void
     */
    public function setCurrentVersion($currentVersion)
    {
        // set current version of project
        $this->currentVersion = $currentVersion;
    }

    /**
     * Get current version of project
     *
     * @access public
     * @return string Current version of project
     */
    public function getCurrentVersion()
    {
        // exit
        return $this->currentVersion;
    }

    /**
     * Set update callbacks
     *
     * @access public
     * @param array|string $updateCallback
     *            Callback function or method name for update; can be global function or method from any class
     * @param array|string $siteUpdateCallback
     *            Callback function or method name for site update; can be global function or method from any class - default: NULL
     * @return void
     */
    public function setUpdateCallbacks($updateCallback, $siteUpdateCallback = null)
    {
        // set update callback
        $this->updateCallbacks = array(
            'updatecallback' => $updateCallback
        );
        if ($siteUpdateCallback !== null) {
            $this->updateCallbacks ['siteupdatecallback'] = $siteUpdateCallback;
        }
    }

    /**
     * Get update callbacks
     *
     * @access public
     * @return array Update callbacks
     */
    public function getUpdateCallbacks()
    {
        // prepare update callbacks
        $updateCallbacks = $this->updateCallbacks;
        if (!empty ($updateCallbacks)) {
            if (!isset ($updateCallbacks ['siteupdatecallback'])) {
                $updateCallbacks ['siteupdatecallback'] = null;
            }
        }
        // exit
        return $updateCallbacks;
    }

    /**
     * Update project version
     *
     * @access private
     * @param bool|string $version
     *            Version number or false if cannot be retrieved
     * @param string $updateCallbackType
     *            Update callback type
     * @param int $metaType
     *            Meta type; must be one of the following constants from \KocujIL\V13a\Enums\Project\Components\Core\Meta\Type: AUTO (for automatic meta type) or SITE (for site meta type)
     * @return void
     */
    private function update($version, $updateCallbackType, $metaType)
    {
        // check version of project
        if ($version === false) {
            // update version of project in database
            $this->getComponent('meta')->addOrUpdate(self::getOptionNameVersion(), $this->currentVersion, $metaType);
            $version = $this->currentVersion;
        }
        // optionally update version of project
        if (version_compare($version, $this->currentVersion, '<')) {
            // execute update callback
            $updateStatus = (isset ($this->updateCallbacks [$updateCallbackType])) ? call_user_func_array($this->updateCallbacks [$updateCallbackType],
                array(
                    $version,
                    $this->currentVersion
                )) : true;
            // optionally update version number
            if ($updateStatus) {
                // save information about this update
                $this->getComponent('meta')->addOrUpdate(self::getOptionNameOldVersion(), $version, $metaType);
                // update version of project in database
                $this->getComponent('meta')->addOrUpdate(self::getOptionNameVersion(), $this->currentVersion,
                    $metaType);
            }
        }
    }

    /**
     * Action for updating version number
     *
     * @access public
     * @return void
     */
    public function actionPluginsLoaded()
    {
        // check if current version of project is set
        if (!isset ($this->currentVersion [0]) /* strlen($this->currentVersion) === 0 */) {
            return;
        }
        // update project version
        $version = $this->getVersionOptionValue();
        $this->update($version, 'updatecallback', Type::AUTO);
        $versionSite = $this->getVersionSiteOptionValue();
        if ($versionSite === false) {
            $versionSite = $this->getOldVersionOptionValue();
        }
        if ($versionSite === false) {
            $versionSite = $version;
        }
        $this->update($versionSite, 'siteupdatecallback', Type::SITE);
    }
}
