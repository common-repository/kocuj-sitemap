<?php

/**
 * js-helper.class.php
 *
 * @author Dominik Kocuj
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2 or later
 * @copyright Copyright (c) 2016-2020 kocuj.pl
 * @package kocuj_internal_lib
 */

// set namespace
namespace KocujIL\V13a\Classes;

// set namespaces aliases
use KocujIL\V13a\Enums\CheckJavascript;

// security
if ((!defined('ABSPATH')) || ((isset ($_SERVER ['SCRIPT_FILENAME'])) && (basename($_SERVER ['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die ();
}

/**
 * JavaScript helper class
 *
 * @access public
 */
final class JsHelper
{

    /**
     * Singleton instance
     *
     * @access private
     * @var object
     */
    private static $instance = null;

    /**
     * Action for adding scripts has been already executed (true) or not (false)
     *
     * @access private
     * @var bool
     */
    private $actionExecuted = false;

    /**
     * Scripts to add in footer in adding scripts action
     *
     * @access private
     * @var array
     */
    private $addJs = array();

    /**
     * Scripts which has been added to queue
     *
     * @access private
     * @var array
     */
    private $addedJs = array();

    /**
     * Constructor
     *
     * @access private
     * @return void
     */
    private function __construct()
    {
        // set scripts actions
        if ((is_admin()) || (is_network_admin())) {
            add_action('admin_enqueue_scripts', array(
                $this,
                'actionAddScripts'
            ), 1);
        } else {
            add_action('wp_enqueue_scripts', array(
                $this,
                'actionAddScripts'
            ), 1);
        }
    }

    /**
     * Disable cloning of object
     *
     * @access private
     * @return void
     */
    private function __clone()
    {
    }

    /**
     * Get singleton instance
     *
     * @access public
     * @return object Singleton instance
     */
    public static function getInstance()
    {
        // optionally create new instance
        if (!self::$instance) {
            self::$instance = new self ();
        }
        // exit
        return self::$instance;
    }

    /**
     * Check if JavaScript scripts are in debug mode
     *
     * @access public
     * @return bool JavaScript scripts are in debug mode (true) or not (false)
     */
    public function checkScriptDebug()
    {
        // exit
        return ((defined('SCRIPT_DEBUG')) && (SCRIPT_DEBUG));
    }

    /**
     * Get minified JavaScript script filename if WordPress allows for it
     *
     * @access public
     * @param string $filename
     *            Filename of script without extension
     * @return string Minified JavaScript script filename if WordPress allows for it
     */
    public function getMinJsFilename($filename)
    {
        // exit
        return $filename . '.' . (($this->checkScriptDebug() || ((!is_admin()) && (!is_network_admin()))) ? 'js' : 'min.js');
    }

    /**
     * Add script
     *
     * @access public
     * @param string $handle
     *            Script handle
     * @param string $urlToDirectory
     *            URL to script directory
     * @param string $filenameWithoutExt
     *            Script filename without extension
     * @param array $mainLibDeps
     *            Script dependencies with main scripts from Kocuj Internal Lib - default: empty
     * @param array $deps
     *            Script dependencies - default: empty
     * @param string $ver
     *            Script version - default: empty
     * @param bool $inFooter
     *            Show script in footer (true) or in header (false) - default: false
     * @param bool $useMin
     *            Use minimized script (true) or standard (false) - default: true
     * @param string $localizeVar
     *            Variable for localization; if empty, there will be no localization - default: empty
     * @param array $localizeData
     *            Localization data; used only if $localizeVar is not empty - default: empty
     * @param bool $addThrowToLocalizeData
     *            Add settings "throwErrors" to localization data with value indicated if script should throw errors (true) or does not add anything (false) - default: false
     * @return void
     */
    public function addScript(
        $handle,
        $urlToDirectory,
        $filenameWithoutExt,
        array $mainLibDeps = array(),
        array $deps = array(),
        $ver = '',
        $inFooter = false,
        $useMin = true,
        $localizeVar = '',
        array $localizeData = array(),
        $addThrowToLocalizeData = false
    ) {
        // integrate dependencies
        if (!empty ($mainLibDeps)) {
            if ((Helper::getInstance()->checkDebug(CheckJavascript::YES)) && (!in_array('exception',
                    $mainLibDeps))) {
                $mainLibDeps [] = 'exception';
            }
            $methods = array(
                'exception' => 'addExceptionJs',
                'helper' => 'addHelperJs',
                'data-helper' => 'addDataHelperJs'
            );
            $addJquery = false;
            foreach ($mainLibDeps as $key => $val) {
                if (isset ($methods [$val])) {
                    call_user_func_array(array(
                        $this,
                        $methods [$val]
                    ), array());
                    $mainLibDeps [$key] = Helper::getInstance()->getPrefix() . '-' . $val;
                    if ($val !== 'helper') {
                        $addJquery = true;
                    }
                }
            }
            if ($addJquery) {
                $mainLibDeps [] = 'jquery';
            }
            $deps = array_merge($mainLibDeps, $deps);
        }
        // add script
        wp_enqueue_script($handle,
            $urlToDirectory . '/' . (($useMin) ? $this->getMinJsFilename($filenameWithoutExt) : $filenameWithoutExt . '.js'),
            $deps, $ver, $inFooter);
        // add script localization
        if (isset ($localizeVar [0]) /* strlen($localizeVar) > 0 */) {
            if ($addThrowToLocalizeData) {
                $localizeData ['throwErrors'] = (Helper::getInstance()->checkDebug(CheckJavascript::YES)) ? '1' : '0';
            }
            wp_localize_script($handle, $localizeVar, $localizeData);
        }
    }

    /**
     * Add Kocuj Internal Lib script
     *
     * @access public
     * @param string $handle
     *            Script handle; prefix will be added automatically
     * @param string $urlToDirectory
     *            URL to script directory; main URL to library will be added automatically
     * @param string $filenameWithoutExt
     *            Script filename without extension
     * @param array $mainLibDeps
     *            Script dependencies with main scripts from Kocuj Internal Lib - default: empty
     * @param array $deps
     *            Script dependencies - default: empty
     * @param string $localizeVar
     *            Variable for localization; if empty, there will be no localization - default: empty
     * @param array $localizeData
     *            Localization data; used only if $localizeVar is not empty - default: empty
     * @return void
     */
    public function addLibScript(
        $handle,
        $urlToDirectory,
        $filenameWithoutExt,
        array $mainLibDeps = array(),
        array $deps = array(),
        $localizeVar = '',
        array $localizeData = array()
    ) {
        // add script
        $this->addScript(Helper::getInstance()->getPrefix() . '-' . $handle,
            LibUrls::getInstance()->get('js') . '/' . $urlToDirectory, $filenameWithoutExt, $mainLibDeps, $deps,
            ($this->checkScriptDebug()) ? date('YmdHis') : Version::getInstance()->getVersion(), true, true,
            $localizeVar, $localizeData, true);
    }

    /**
     * Add Kocuj Internal Lib script from vendor
     *
     * @access public
     * @param string $handle
     *            Script handle; prefix will be added automatically
     * @param string $urlToDirectory
     *            URL to script directory; main URL to library will be added automatically
     * @param string $filenameWithoutExt
     *            Script filename without extension
     * @param array $mainLibDeps
     *            Script dependencies with main scripts from Kocuj Internal Lib - default: empty
     * @param array $deps
     *            Script dependencies - default: empty
     * @param string $localizeVar
     *            Variable for localization; if empty, there will be no localization - default: empty
     * @param array $localizeData
     *            Localization data; used only if $localizeVar is not empty - default: empty
     * @return void
     */
    public function addLibVendorScript(
        $handle,
        $urlToDirectory,
        $filenameWithoutExt,
        array $mainLibDeps = array(),
        array $deps = array(),
        $localizeVar = '',
        array $localizeData = array()
    ) {
        // add script
        $this->addScript(Helper::getInstance()->getPrefix() . '-' . $handle,
            LibUrls::getInstance()->get('vendors') . '/' . $urlToDirectory, $filenameWithoutExt, $mainLibDeps, $deps,
            ($this->checkScriptDebug()) ? date('YmdHis') : Version::getInstance()->getVersion(), true, true,
            $localizeVar, $localizeData, true);
    }

    /**
     * Add "exception.js" and "exception-code.js" scripts
     *
     * @access public
     * @return void
     */
    public function addExceptionJs()
    {
        // add script
        if (!isset ($this->addedJs ['exception'])) {
            if (!$this->actionExecuted) {
                $this->addJs ['exception'] = true;
            } else {
                $this->addExceptionJsNow();
            }
        }
    }

    /**
     * Add "exception.js" and "exception-code.js" scripts now
     *
     * @access private
     * @return void
     */
    private function addExceptionJsNow()
    {
        // add script now
        $this->addHelperJsNow();
        if (!isset ($this->addedJs ['exception'])) {
            if (Helper::getInstance()->checkDebug(CheckJavascript::YES)) {
                $this->addScript(Helper::getInstance()->getPrefix() . '-exception-code',
                    LibUrls::getInstance()->get('js'), 'exception-code', array(), array(),
                    Version::getInstance()->getVersion(), true);
                $this->addScript(Helper::getInstance()->getPrefix() . '-exception', LibUrls::getInstance()->get('js'),
                    'exception', array(), array(
                        Helper::getInstance()->getPrefix() . '-helper',
                        Helper::getInstance()->getPrefix() . '-exception-code'
                    ), Version::getInstance()->getVersion(), true);
            }
            $this->addedJs ['exception'] = true;
        }
    }

    /**
     * Add "helper.js" script
     *
     * @access public
     * @return void
     */
    public function addHelperJs()
    {
        // add script
        if (!isset ($this->addedJs ['helper'])) {
            if (!$this->actionExecuted) {
                $this->addJs ['helper'] = true;
            } else {
                $this->addHelperJsNow();
            }
        }
    }

    /**
     * Add "helper.js" script now
     *
     * @access private
     * @return void
     */
    private function addHelperJsNow()
    {
        // add script now
        if (!isset ($this->addedJs ['helper'])) {
            $this->addLibScript('helper', '', 'helper');
            $this->addedJs ['helper'] = true;
        }
    }

    /**
     * Add "data-helper.js" script
     *
     * @access public
     * @return void
     */
    public function addDataHelperJs()
    {
        // add script
        if (!isset ($this->addedJs ['data-helper'])) {
            if (!$this->actionExecuted) {
                $this->addJs ['data-helper'] = true;
            } else {
                $this->addDataHelperJsNow();
            }
        }
    }

    /**
     * Add "data-helper.js" script now
     *
     * @access private
     * @return void
     */
    private function addDataHelperJsNow()
    {
        // add script now
        $this->addHelperJsNow();
        if (!isset ($this->addedJs ['data-helper'])) {
            $this->addLibScript('data-helper', '', 'data-helper', array(), array(
                Helper::getInstance()->getPrefix() . '-helper'
            ));
            $this->addedJs ['data-helper'] = true;
        }
    }

    /**
     * Action for adding scripts
     *
     * @access public
     * @return void
     */
    public function actionAddScripts()
    {
        // add scripts
        if (isset ($this->addJs ['helper'])) {
            $this->addHelperJsNow();
        }
        if (isset ($this->addJs ['exception'])) {
            $this->addExceptionJsNow();
        }
        if (isset ($this->addJs ['data-helper'])) {
            $this->addDataHelperJsNow();
        }
        // set action as executed
        $this->actionExecuted = true;
    }
}
