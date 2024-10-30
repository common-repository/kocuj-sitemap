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
namespace KocujIL\V13a\Classes\Project\Components\Backend\EditorVisualButtons;

// set namespaces aliases
use KocujIL\V13a\Classes\ComponentObject;
use KocujIL\V13a\Classes\Exception;
use KocujIL\V13a\Enums\Project\Components\Backend\EditorVisualButtons\ExceptionCode;

// security
if ((!defined('ABSPATH')) || ((isset ($_SERVER ['SCRIPT_FILENAME'])) && (basename($_SERVER ['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die ();
}

/**
 * Visual editor buttons class
 *
 * @access public
 */
class Component extends ComponentObject
{

    /**
     * Buttons
     *
     * @access private
     * @var array
     */
    private $buttons = array();

    /**
     * Add button for visual editor
     *
     * @access public
     * @param string $id
     *            Button id; must be unique for this project
     * @param string $filenameJS
     *            JavaScript filename
     * @param string $filenamePhp
     *            PHP filename - default: empty
     * @return void
     * @throws Exception
     */
    public function addButton($id, $filenameJS, $filenamePhp = '')
    {
        // check if button does not exist already
        if (isset ($this->buttons [$id])) {
            throw new Exception ($this, ExceptionCode::BUTTON_ID_EXISTS, __FILE__, __LINE__, $id);
        }
        // add button
        $this->buttons [$id] = array(
            'filenamejs' => $filenameJS
        );
        if (isset ($filenamePhp [0]) /* strlen($filenamePhp) > 0 */) {
            $this->buttons [$id] ['filenamephp'] = $filenamePhp;
        }
    }

    /**
     * Get buttons for visual editor data
     *
     * @access public
     * @return array Buttons for visual editor data; each button for visual editor data has the following fields: "filenamejs" (string type; JavaScript filename), "filenamephp" (string type; PHP filename)
     */
    public function getButtons()
    {
        // prepare buttons
        $buttons = $this->buttons;
        foreach ($buttons as $key => $val) {
            if (!isset ($val ['filenamephp'])) {
                $buttons [$key] ['filenamephp'] = '';
            }
        }
        // exit
        return $buttons;
    }

    /**
     * Check if button for visual editor exists
     *
     * @access public
     * @param string $id
     *            Button for visual editor identifier
     * @return bool Button for visual editor exists (true) or not (false)
     */
    public function checkButton($id)
    {
        // exit
        return isset ($this->buttons [$id]);
    }

    /**
     * Get button for visual editor data by id
     *
     * @access public
     * @param string $id
     *            Button for visual editor identifier
     * @return array|bool Button for visual editor data or false if not exists; button for visual editor data have the following fields: "filenamejs" (string type; JavaScript filename), "filenamephp" (string type; PHP filename)
     */
    public function getButton($id)
    {
        // get buttons
        $buttons = $this->getButtons();
        // exit
        return (isset ($buttons [$id])) ? $buttons [$id] : false;
    }

    /**
     * Remove button for visual editor
     *
     * @access public
     * @param string $id
     *            Button for visual editor identifier
     * @return void
     * @throws Exception
     */
    public function removeButton($id)
    {
        // check if this button for visual editor identifier exists
        if (!isset ($this->buttons [$id])) {
            throw new Exception ($this, ExceptionCode::BUTTON_ID_DOES_NOT_EXIST, __FILE__, __LINE__, $id);
        }
        // remove button for visual editor
        unset ($this->buttons [$id]);
    }

    /**
     * Filter for TinyMCE plugins
     *
     * @access public
     * @param array $plugins
     *            Plugins list
     * @return array Plugins list
     */
    public function filterMceExternalPlugins(array $plugins)
    {
        // filter TinyMCE plugins
        foreach ($this->buttons as $id => $button) {
            if (is_file($this->getComponent('dirs')->getSubDir('tinymcebuttonsjs') . DIRECTORY_SEPARATOR . $button ['filenamejs'])) {
                $plugins [$id] = $this->getComponent('urls')->get('tinymcebuttonsjs') . '/' . $button ['filenamejs'];
            }
        }
        // exit
        return $plugins;
    }

    /**
     * Filter for TinyMCE plugins languages
     *
     * @access public
     * @param array $languages
     *            Plugins languages list
     * @return array Plugins languages list
     */
    public function filterMceExternalLanguages(array $languages)
    {
        // filter TinyMCE languages
        foreach ($this->buttons as $button) {
            if (isset ($button ['filenamephp'])) {
                $filename = $this->getComponent('dirs')->getSubDir('tinymcebuttonsphp') . DIRECTORY_SEPARATOR . $button ['filenamephp'];
                if (is_file($filename)) {
                    $languages [] = $filename;
                }
            }
        }
        // exit
        return $languages;
    }

    /**
     * Filter for TinyMCE buttons
     *
     * @access public
     * @param array $buttons
     *            Buttons list
     * @return array Buttons list
     */
    public function filterMceButtons(array $buttons)
    {
        // filter TinyMCE buttons
        foreach ($this->buttons as $id => $button) {
            $buttons [] = $id;
        }
        // exit
        return $buttons;
    }
}
