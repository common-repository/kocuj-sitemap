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
namespace KocujIL\V13a\Classes\Project\Components\Backend\SettingsHelp;

// set namespaces aliases
use KocujIL\V13a\Classes\ComponentObject;
use KocujIL\V13a\Classes\Exception;
use KocujIL\V13a\Enums\Project\Components\Backend\SettingsHelp\ExceptionCode;
use KocujIL\V13a\Enums\ProjectCategory;

// security
if ((!defined('ABSPATH')) || ((isset ($_SERVER ['SCRIPT_FILENAME'])) && (basename($_SERVER ['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die ();
}

/**
 * Settings help class
 *
 * @access public
 */
class Component extends ComponentObject
{

    /**
     * Help topics
     *
     * @access private
     * @var array
     */
    private $helpTopics = array();

    /**
     * Action for help has been added (true) or not (false)
     *
     * @access private
     * @var bool
     */
    private $actionAdded = false;

    /**
     * Add help topic for settings menu
     *
     * @access public
     * @param string $settingsMenuId
     *            Settings menu identifier
     * @param string $helpTopicId
     *            Help data identifier; must be unique
     * @param string $title
     *            Help title
     * @param string $content
     *            Help content
     * @return void
     * @throws Exception
     */
    public function addHelpTopic($settingsMenuId, $helpTopicId, $title, $content)
    {
        // check if help topic does not exist already
        if (isset ($this->helpTopics [$settingsMenuId] [$helpTopicId])) {
            throw new Exception ($this, ExceptionCode::HELP_ID_EXISTS, __FILE__, __LINE__,
                $settingsMenuId . '/' . $helpTopicId);
        }
        // check if settings menu exists
        if (!$this->getComponent('settings-menu', ProjectCategory::BACKEND)->checkSettingsMenu($settingsMenuId)) {
            throw new Exception ($this, ExceptionCode::SETTINGS_MENU_ID_DOES_NOT_EXIST, __FILE__, __LINE__,
                $settingsMenuId);
        }
        // optionally create help topics data and action adding help
        if (!isset ($this->helpTopics [$settingsMenuId])) {
            $this->helpTopics [$settingsMenuId] = array();
            if (!$this->actionAdded) {
                add_action('admin_enqueue_scripts', array(
                    $this,
                    'actionAddHelp'
                ));
                $this->actionAdded = true;
            }
        }
        // add help topic
        $this->helpTopics [$settingsMenuId] [$helpTopicId] = array(
            'title' => $title,
            'content' => $content
        );
    }

    /**
     * Get help topics for settings menu data
     *
     * @access public
     * @param string $settingsMenuId
     *            Settings menu identifier
     * @return array Help topics for settings menu data; each help topic for settings menu data has the following fields: "content" (string type; help content), "title" (string type; help title)
     * @throws Exception
     */
    public function getHelpTopics($settingsMenuId)
    {
        // check if settings menu exists
        if (!$this->getComponent('settings-menu',
            ProjectCategory::BACKEND)->checkSettingsMenu($settingsMenuId)) {
            throw new Exception ($this, ExceptionCode::SETTINGS_MENU_ID_DOES_NOT_EXIST, __FILE__, __LINE__,
                $settingsMenuId);
        }
        // exit
        return $this->helpTopics [$settingsMenuId];
    }

    /**
     * Check if help topic for settings menu exists
     *
     * @access public
     * @param string $settingsMenuId
     *            Settings menu identifier
     * @param string $helpTopicId
     *            Help data identifier
     * @return bool Help topic for settings menu exists (true) or not (false)
     * @throws Exception
     */
    public function checkHelpTopic($settingsMenuId, $helpTopicId)
    {
        // check if settings menu exists
        if (!$this->getComponent('settings-menu',
            ProjectCategory::BACKEND)->checkSettingsMenu($settingsMenuId)) {
            throw new Exception ($this, ExceptionCode::SETTINGS_MENU_ID_DOES_NOT_EXIST, __FILE__, __LINE__,
                $settingsMenuId);
        }
        // exit
        return isset ($this->helpTopics [$settingsMenuId] [$helpTopicId]);
    }

    /**
     * Get help topic for settings menu data by id
     *
     * @access public
     * @param string $settingsMenuId
     *            Settings menu identifier
     * @param string $helpTopicId
     *            Help data identifier
     * @return array|bool Help topic for settings menu data or false if not exists; help topic for settings menu data have the following fields: "content" (string type; help content), "title" (string type; help title)
     * @throws Exception
     */
    public function getHelpTopic($settingsMenuId, $helpTopicId)
    {
        // check if settings menu exists
        if (!$this->getComponent('settings-menu',
            ProjectCategory::BACKEND)->checkSettingsMenu($settingsMenuId)) {
            throw new Exception ($this, ExceptionCode::SETTINGS_MENU_ID_DOES_NOT_EXIST, __FILE__, __LINE__,
                $settingsMenuId);
        }
        // exit
        return (isset ($this->helpTopics [$settingsMenuId] [$helpTopicId])) ? $this->helpTopics [$settingsMenuId] [$helpTopicId] : false;
    }

    /**
     * Remove help topic for settings menu
     *
     * @access public
     * @param string $settingsMenuId
     *            Settings menu identifier
     * @param string $helpTopicId
     *            Help data identifier
     * @return void
     * @throws Exception
     */
    public function removeHelpTopic($settingsMenuId, $helpTopicId)
    {
        // check if settings menu exists
        if (!$this->getComponent('settings-menu',
            ProjectCategory::BACKEND)->checkSettingsMenu($settingsMenuId)) {
            throw new Exception ($this, ExceptionCode::SETTINGS_MENU_ID_DOES_NOT_EXIST, __FILE__, __LINE__,
                $settingsMenuId);
        }
        // check if help topic exists
        if (!isset ($this->helpTopics [$settingsMenuId] [$helpTopicId])) {
            throw new Exception ($this, ExceptionCode::HELP_ID_DOES_NOT_EXIST, __FILE__, __LINE__,
                $settingsMenuId . '/' . $helpTopicId);
        }
        // remove help topic for settings menu
        unset ($this->helpTopics [$settingsMenuId] [$helpTopicId]);
    }

    /**
     * Action for adding help
     *
     * @access public
     * @return void
     */
    public function actionAddHelp()
    {
        // get current screen
        $screen = get_current_screen();
        if (empty ($screen)) {
            return;
        }
        // get settings menus
        $settingsMenus = $this->getComponent('settings-menu', ProjectCategory::BACKEND)->getSettingsMenus();
        // add help topics
        foreach ($settingsMenus as $key => $val) {
            if ($val ['pagescreenname'] === $screen->id) {
                if (isset ($this->helpTopics [$key])) {
                    foreach ($this->helpTopics [$key] as $key2 => $val2) {
                        $screen->add_help_tab(array(
                            'id' => $key2,
                            'title' => $val2 ['title'],
                            'content' => '<div style="width:100%;max-height:250px;">' . $val2 ['content'] . '</div>'
                        ));
                    }
                }
                break;
            }
        }
    }
}
