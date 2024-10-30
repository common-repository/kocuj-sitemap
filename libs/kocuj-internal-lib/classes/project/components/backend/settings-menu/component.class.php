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
namespace KocujIL\V13a\Classes\Project\Components\Backend\SettingsMenu;

// set namespaces aliases
use KocujIL\V13a\Classes\ComponentObject;
use KocujIL\V13a\Classes\CurrentScreenIdHelper;
use KocujIL\V13a\Classes\Exception;
use KocujIL\V13a\Classes\HtmlHelper;
use KocujIL\V13a\Enums\Project\Components\Backend\SettingsMenu\ExceptionCode;
use KocujIL\V13a\Enums\Project\Components\Backend\SettingsMenu\ParentType;
use KocujIL\V13a\Enums\Project\Components\Backend\SettingsMenu\Type;
use KocujIL\V13a\Enums\ProjectCategory;
use KocujIL\V13a\Enums\ProjectType;

// security
if ((!defined('ABSPATH')) || ((isset ($_SERVER ['SCRIPT_FILENAME'])) && (basename($_SERVER ['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die ();
}

/**
 * Settings menu class
 *
 * @access public
 */
class Component extends ComponentObject
{

    /**
     * Settings menus list
     *
     * @access private
     * @var array
     */
    private $settingsMenu = array();

    /**
     * Settings menus hooks suffixes
     *
     * @access private
     * @var array
     */
    private $settingsMenuHookSuffix = array();

    /**
     * Settings pages screen names
     *
     * @access private
     * @var array
     */
    private $pageScreenNames = array();

    /**
     * Settings alternative pages screen names
     *
     * @access private
     * @var array
     */
    private $alternativePageScreenNames = array();

    /**
     * Show title button
     *
     * @access private
     * @param string $id
     *            Button identifier
     * @param string $link
     *            Button link
     * @param string $text
     *            Button text
     * @return void
     */
    private function showTitleButton($id, $link, $text)
    {
        // show title button
        echo '<div class="' . esc_attr($this->getComponent('project-helper')->getPrefix() . '__settings_menu_title_buttons_button_inside') . '"></div>';
        echo '<div class="' . esc_attr($this->getComponent('project-helper')->getPrefix() . '__settings_menu_title_buttons_button_inside') . '" style="position:relative;"><input type="button" class="button button-small" id="' . esc_attr($id) . '" value="' . esc_attr($text) . '" /></div>';
        echo '<script type="text/javascript">' . PHP_EOL;
        echo '/* <![CDATA[ */' . PHP_EOL;
        echo '(function($) {' . PHP_EOL;
        echo '$(document).ready(function() {' . PHP_EOL;
        echo '$(\'#' . esc_js($id) . '\').click(function(event) {' . PHP_EOL;
        echo 'event.preventDefault();' . PHP_EOL;
        echo 'window.open(\'' . esc_js($link) . '\', \'_blank\');' . PHP_EOL;
        echo '});' . PHP_EOL;
        echo '});' . PHP_EOL;
        echo '}(jQuery));' . PHP_EOL;
        echo '/* ]]> */' . PHP_EOL;
        echo '</script>' . PHP_EOL;
    }

    /**
     * Call handler for non-existing methods
     *
     * @access public
     * @param string $name
     *            Method name
     * @param array $argument
     *            Method arguments
     * @return array|bool|float|int|string|void Value returned by called method
     */
    public function __call($name, array $arguments)
    {
        // get type and identifier based on method name
        $div = explode('_', $name);
        if (count($div) > 1) {
            $type = $div [0];
            unset ($div [0]);
            $id = implode('_', $div);
            // check type and id
            if (($type === 'settingsMenu') && (isset ($this->settingsMenu [$id]))) {
                // check user permission
                if (!current_user_can($this->settingsMenu [$id] ['capability'])) {
                    wp_die($this->getStrings('settings-menu',
                        ProjectCategory::BACKEND)->getString('CALL_NO_PERMISSION'));
                }
                // show header
                $this->getComponent('project-helper')->doAction('before_wrap_div');
                $this->getComponent('project-helper')->doAction('before_wrap_div__' . $id);
                echo '<div id="' . esc_attr($this->getComponent('project-helper')->getPrefix() . '__div_wrap') . '" ';
                echo $this->getComponent('project-helper')->applyFiltersForHTMLStyleAndClass('wrap_div',
                    'wrap_div__' . $id . '_', array(
                        'defaultclass' => 'wrap'
                    ));
                echo '>' . PHP_EOL;
                echo '<h1>' . $this->getProjectObj()->getMainSettingTitle() . '</h1>';
                if ((!isset ($this->settingsMenu [$id] ['parentid'])) && (isset ($this->settingsMenu [$id] ['firstoptiontitle']))) {
                    $title = $this->settingsMenu [$id] ['firstoptiontitle'];
                } else {
                    $title = (isset ($this->settingsMenu [$id] ['fulltitle'])) ? $this->settingsMenu [$id] ['fulltitle'] : $this->settingsMenu [$id] ['title'];
                }
                $supportLink = $this->getProjectObj()->getSettingArray('repository', 'support');
                $translationLink = $this->getProjectObj()->getSettingArray('repository', 'translation');
                $contactFormLink = $this->getProjectObj()->getSettingString('contactform');
                if ((isset ($supportLink [0]) /* strlen($supportLink) > 0 */) || (isset ($translationLink [0]) /* strlen($translationLink) > 0 */) || (isset ($contactFormLink [0]) /* strlen($contactFormLink) > 0 */)) {
                    echo '<style scoped="scoped" id="' . esc_attr($this->getComponent('project-helper')->getPrefix() . '__settings_menu_title_buttons_style') . '" type="text/css" media="all">' . '.' . $this->getComponent('project-helper')->getPrefix() . '__settings_menu_title_buttons_header' . PHP_EOL . '{' . PHP_EOL . 'float: left;' . PHP_EOL . 'height: 60px;' . PHP_EOL . 'position: relative;' . PHP_EOL . '}' . PHP_EOL . '.' . $this->getComponent('project-helper')->getPrefix() . '__settings_menu_title_buttons_button_inside' . PHP_EOL . '{' . PHP_EOL . 'position: absolute;' . PHP_EOL . 'height: 60px;' . PHP_EOL . 'margin-top: -16px;' . PHP_EOL . 'top: 50%;' . PHP_EOL . 'margin-left: 10px;' . PHP_EOL . '}' . PHP_EOL . '@media screen and (max-width: 782px) {' . PHP_EOL . '.' . $this->getComponent('project-helper')->getPrefix() . '__settings_menu_title_buttons_header' . PHP_EOL . '{' . PHP_EOL . 'float: none;' . PHP_EOL . 'height: auto;' . PHP_EOL . '}' . PHP_EOL . '.' . $this->getComponent('project-helper')->getPrefix() . '__settings_menu_title_buttons_button_inside' . PHP_EOL . '{' . PHP_EOL . 'position: relative;' . PHP_EOL . 'margin-top: 5px;' . PHP_EOL . 'margin-bottom: 5px;' . PHP_EOL . 'height: auto;' . PHP_EOL . 'margin-left: 0px;' . PHP_EOL . '}' . PHP_EOL . '}' . PHP_EOL . '</style>' . PHP_EOL;
                    echo '<div class="' . esc_attr($this->getComponent('project-helper')->getPrefix() . '__settings_menu_title_buttons_header') . '">';
                }
                echo '<h2>' . $title . '</h2>';
                if (isset ($supportLink [0]) /* strlen($supportLink) > 0 */) {
                    echo '</div>';
                    echo '<div class="' . esc_attr($this->getComponent('project-helper')->getPrefix() . '__settings_menu_title_buttons_header') . '">';
                    $this->showTitleButton($this->getComponent('project-helper')->getPrefix() . '__settings_menu_support',
                        $supportLink, $this->getStrings('settings-menu',
                            ProjectCategory::BACKEND)->getString('CALL_BUTTON_SUPPORT'), false);
                    echo '</div>';
                }
                if (isset ($translationLink [0]) /* strlen($translationLink) > 0 */) {
                    if (!isset ($supportLink [0]) /* strlen($supportLink) === 0 */) {
                        echo '</div>';
                    }
                    echo '<div class="' . esc_attr($this->getComponent('project-helper')->getPrefix() . '__settings_menu_title_buttons_header') . '">';
                    $this->showTitleButton($this->getComponent('project-helper')->getPrefix() . '__settings_menu_translation',
                        $translationLink, $this->getStrings('settings-menu',
                            ProjectCategory::BACKEND)->getString('CALL_BUTTON_TRANSLATION'));
                    echo '</div>';
                }
                if (isset ($contactFormLink [0]) /* strlen($contactFormLink) > 0 */) {
                    if (!isset ($translationLink [0]) /* strlen($translationLink) === 0 */) {
                        echo '</div>';
                    }
                    echo '<div class="' . esc_attr($this->getComponent('project-helper')->getPrefix() . '__settings_menu_title_buttons_header') . '">';
                    $this->showTitleButton($this->getComponent('project-helper')->getPrefix() . '__settings_menu_contact',
                        $contactFormLink, $this->getStrings('settings-menu',
                            ProjectCategory::BACKEND)->getString('CALL_BUTTON_CONTACT'));
                    echo '</div>';
                }
                if ((isset ($supportLink [0]) /* strlen($supportLink) > 0 */) || (isset ($translationLink [0]) /* strlen($translationLink) > 0 */) || (isset ($contactFormLink [0]) /* strlen($contactFormLink) > 0 */)) {
                    echo '<div style="clear:both;"></div>';
                }
                $this->getComponent('project-helper')->doAction('before_wrapinside_div');
                $this->getComponent('project-helper')->doAction('before_wrapinside_div__' . $id);
                echo '<div id="' . esc_attr($this->getComponent('project-helper')->getPrefix() . '__div_wrapinside') . '" ';
                echo $this->getComponent('project-helper')->applyFiltersForHTMLStyleAndClass('wrapinside_div',
                    'wrapinside_div__' . $id . '_', array());
                echo '>' . PHP_EOL;
                // show page content
                $this->getComponent('project-helper')->doAction('before_page_content');
                $this->getComponent('project-helper')->doAction('before_page_content__' . $id);
                call_user_func($this->settingsMenu [$id] ['function']);
                $this->getComponent('project-helper')->doAction('after_page_content');
                $this->getComponent('project-helper')->doAction('after_page_content__' . $id);
                // show footer
                echo '</div>' . PHP_EOL;
                $this->getComponent('project-helper')->doAction('after_wrapinside_div');
                $this->getComponent('project-helper')->doAction('after_wrapinside_div__' . $id);
                echo '</div>' . PHP_EOL;
                $this->getComponent('project-helper')->doAction('after_wrap_div');
                $this->getComponent('project-helper')->doAction('after_wrap_div__' . $id);
            }
        }
    }

    /**
     * Add settings menu, submenu or submenu to built-in menu
     *
     * @access private
     * @param string $title
     *            Menu title
     * @param string $capability
     *            Capability required for access to this menu
     * @param string $id
     *            Menu id
     * @param array|string $function
     *            Callback function or method name; can be global function or method from any class
     * @param bool $parentIsBuiltInMenu
     *            Parent is built-in menu (true) or is settings menu for this project if exists (false)
     * @param string $parentId
     *            Parent menu identifier (if $parentIsBuiltInMenu=false) or parent menu type (if $parentIsBuiltInMenu=true); for parent menu type it must be one of the following constants from \KocujIL\V13a\Enums\Project\Components\Backend\SettingsMenu\ParentType: DASHBOARD, POSTS, MEDIA, LINKS, PAGES, COMMENTS, THEMES, PLUGINS, USERS, TOOLS, NETWORK_DASHBOARD, NETWORK_SITES, NETWORK_USERS, NETWORK_THEMES, NETWORK_PLUGINS or NETWORK_OPTIONS - default: \KocujIL\V13a\Enums\Project\Components\Backend\SettingsMenu\ParentType::DASHBOARD
     * @param int $type
     *            Menu type - site or network; must be one of the following constants from \KocujIL\V13a\Enums\Project\Components\Backend\SettingsMenu\Type: SITE or NETWORK
     * @param array $attr
     *            Additional attributes; there are available the following attributes: "firstoptiontitle" (title for first option if current option menu is without parent; only for menu - not submenu), "fulltitle" (string type; full title of page), "icon" (string type; icon name for settings option; only for menu - not submenu), "onpluginslist" (bool type; there will be link to this settings on this plugin information in plugins list with title from this option or from "pluginslisttitle" attribute if exists), "pluginslisttitle" (string type; title of plugin if "onpluginslist" is set to true)
     * @return void
     */
    private function addSettingsMenuOrSubmenu(
        $title,
        $capability,
        $id,
        $function,
        $parentIsBuiltInMenu,
        $parentId,
        $type,
        array $attr
    ) {
        // check if settings menu does not exist already
        if (isset ($this->settingsMenu [$id])) {
            throw new Exception ($this, ExceptionCode::SETTINGS_MENU_ID_EXISTS, __FILE__, __LINE__, $id);
        }
        // check if settings menu is correctly placed
        if (($this->getProjectObj()->getMainSettingType() !== ProjectType::PLUGIN) && ((($parentIsBuiltInMenu) && ($parentId !== ParentType::THEMES)) || (!$parentIsBuiltInMenu))) {
            throw new Exception ($this, ExceptionCode::SETTINGS_MENU_INCORRECTLY_PLACED, __FILE__, __LINE__);
        }
        // set page screen name
        $pageScreenSuffix = '';
        $alternativePageScreenSuffix = '';
        if (!$parentIsBuiltInMenu) {
            $parentTitle = ((isset ($parentId [0]) /* strlen($parentId) > 0 */) && (isset ($this->settingsMenu [$parentId]))) ? $this->settingsMenu [$parentId] ['title'] : '';
            $pageScreenName = (((!isset ($parentId [0]) /* strlen($parentId) === 0 */) || (!isset ($parentTitle [0]) /* strlen($parentTitle) === 0 */)) ? 'toplevel' : sanitize_title($parentTitle));
            $alternativePageScreenName = 'admin';
            if ($type === Type::NETWORK) {
                $alternativePageScreenSuffix = '-network';
            }
        } else {
            $enumMenuParentType = ParentType;
            switch ($parentId) {
                case $enumMenuParentType::NETWORK_DASHBOARD :
                case $enumMenuParentType::NETWORK_SITES :
                case $enumMenuParentType::NETWORK_USERS :
                case $enumMenuParentType::NETWORK_THEMES :
                case $enumMenuParentType::NETWORK_PLUGINS :
                case $enumMenuParentType::NETWORK_OPTIONS :
                    $type = Type::NETWORK;
                    break;
                default :
                    $type = Type::SITE;
            }
            $pageScreenName = 'admin';
            $pageScreenNames = array(
                $enumMenuParentType::DASHBOARD => 'dashboard',
                $enumMenuParentType::POSTS => 'posts',
                $enumMenuParentType::MEDIA => 'media',
                $enumMenuParentType::LINKS => 'admin',
                $enumMenuParentType::PAGES => 'pages',
                $enumMenuParentType::COMMENTS => 'comments',
                $enumMenuParentType::THEMES => 'appearance',
                $enumMenuParentType::PLUGINS => 'plugins',
                $enumMenuParentType::USERS => 'users',
                $enumMenuParentType::TOOLS => 'tools',
                $enumMenuParentType::OPTIONS => 'settings',
                $enumMenuParentType::NETWORK_DASHBOARD => 'index',
                $enumMenuParentType::NETWORK_SITES => 'sites',
                $enumMenuParentType::NETWORK_USERS => 'users',
                $enumMenuParentType::NETWORK_THEMES => 'themes',
                $enumMenuParentType::NETWORK_PLUGINS => 'plugins',
                $enumMenuParentType::NETWORK_OPTIONS => 'settings'
            );
            if (isset ($pageScreenNames [$parentId])) {
                $pageScreenName = $pageScreenNames [$parentId];
            }
            $suffix = ($type === Type::NETWORK) ? '-network' : '';
            $pageScreenSuffix = $suffix;
            $alternativePageScreenName = $pageScreenName;
            $alternativePageScreenSuffix = $suffix;
        }
        $toAdd = '_page_' . $this->getProjectObj()->getMainSettingInternalName() . '_' . $id;
        $pageScreenName .= $toAdd . $pageScreenSuffix;
        $alternativePageScreenName .= $toAdd . $alternativePageScreenSuffix;
        // add settings menu
        $this->settingsMenu [$id] = array(
            'title' => $title,
            'capability' => $capability,
            'function' => $function,
            'menutype' => $type,
            'pagescreenname' => $pageScreenName,
            'alternativepagescreenname' => $alternativePageScreenName
        );
        if (isset ($attr ['fulltitle'])) {
            $this->settingsMenu [$id] ['fulltitle'] = $attr ['fulltitle'];
        }
        if (!$parentIsBuiltInMenu) {
            if (isset ($parentId [0]) /* strlen($parentId) > 0 */) {
                $this->settingsMenu [$id] ['parentid'] = $parentId;
            }
        } else {
            $this->settingsMenu [$id] ['parentid'] = $parentId;
            $this->settingsMenu [$id] ['parentisbuiltinmenu'] = true;
        }
        if ((!$parentIsBuiltInMenu) && (isset ($attr ['icon']))) {
            $this->settingsMenu [$id] ['icon'] = $attr ['icon'];
        }
        if (isset ($attr ['onpluginslist'])) {
            $this->settingsMenu [$id] ['onpluginslist'] = $attr ['onpluginslist'];
            if (isset ($attr ['pluginslisttitle'])) {
                $this->settingsMenu [$id] ['pluginslisttitle'] = $attr ['pluginslisttitle'];
            }
        }
        if (!$parentIsBuiltInMenu) {
            if ((!isset ($parentId [0]) /* strlen($parentId) === 0 */) && (isset ($attr ['firstoptiontitle']))) {
                $this->settingsMenu [$id] ['firstoptiontitle'] = $attr ['firstoptiontitle'];
            }
        }
        // add page internal name
        if (!in_array($pageScreenName, $this->pageScreenNames)) {
            $this->pageScreenNames [] = $pageScreenName;
        }
        if (!in_array($alternativePageScreenName, $this->alternativePageScreenNames)) {
            $this->alternativePageScreenNames [] = $alternativePageScreenName;
        }
    }

    /**
     * Add settings menu
     *
     * @access public
     * @param string $title
     *            Menu title
     * @param string $capability
     *            Capability required for access to this menu
     * @param string $id
     *            Menu id
     * @param array|string $function
     *            Callback function or method name; can be global function or method from any class
     * @param string $parentId
     *            Parent menu identifier - default: empty
     * @param int $type
     *            Menu type - site or network; must be one of the following constants from \KocujIL\V13a\Enums\Project\Components\Backend\SettingsMenu\Type: SITE or NETWORK - default: \KocujIL\V13a\Enums\Project\Components\Backend\SettingsMenu\Type::SITE
     * @param array $attr
     *            Additional attributes; there are available the following attributes: "firstoptiontitle" (title for first option if current option menu is without parent; only for menu - not submenu), "fulltitle" (string type; full title of page), "icon" (string type; icon name for settings option; only for menu - not submenu), "onpluginslist" (bool type; there will be link to this settings on this plugin information in plugins list with title from this option or from "pluginslisttitle" attribute if exists), "pluginslisttitle" (string type; title of plugin if "onpluginslist" is set to true) - default: empty
     * @return void
     * @throws Exception
     */
    public function addSettingsMenu(
        $title,
        $capability,
        $id,
        $function,
        $parentId = '',
        $type = Type::SITE,
        array $attr = array()
    ) {
        // add settings menu
        $this->addSettingsMenuOrSubmenu($title, $capability, $id, $function, false, $parentId, $type, $attr);
    }

    /**
     * Add settings menu to built in menu
     *
     * @access public
     * @param string $title
     *            Menu title
     * @param string $capability
     *            Capability required for access to this menu
     * @param string $id
     *            Menu id
     * @param array|string $function
     *            Callback function or method name; can be global function or method from any class
     * @param int $parentType
     *            Parent menu type; must be one of the following constants from \KocujIL\V13a\Enums\Project\Components\Backend\SettingsMenu\ParentType: DASHBOARD, POSTS, MEDIA, LINKS, PAGES, COMMENTS, THEMES, PLUGINS, USERS, TOOLS, NETWORK_DASHBOARD, NETWORK_SITES, NETWORK_USERS, NETWORK_THEMES, NETWORK_PLUGINS or NETWORK_OPTIONS - default: \KocujIL\V13a\Enums\Project\Components\Backend\SettingsMenu\ParentType::DASHBOARD
     * @param array $attr
     *            Additional attributes; there are available the following attributes: "firstoptiontitle" (title for first option if current option menu is without parent; only for menu - not submenu), "fulltitle" (string type; full title of page), "icon" (string type; icon name for settings option; only for menu - not submenu), "onpluginslist" (bool type; there will be link to this settings on this plugin information in plugins list with title from this option or from "pluginslisttitle" attribute if exists), "pluginslisttitle" (string type; title of plugin if "onpluginslist" is set to true) - default: empty
     * @return void
     * @throws Exception
     */
    public function addSettingsMenuBuiltIn(
        $title,
        $capability,
        $id,
        $function,
        $parentType = ParentType::DASHBOARD,
        array $attr = array()
    ) {
        // add settings menu
        $this->addSettingsMenuOrSubmenu($title, $capability, $id, $function, true, $parentType, 0, $attr);
    }

    /**
     * Get settings menus data
     *
     * @access public
     * @return array Settings menus data; each settings menu data has the following fields: "alternativepagescreenname" (string type; alternative screen name for this page), "capability" (string type; capability required for access to this menu), "firstoptiontitle" (string type; title for first option if current option menu is without parent; only for menu - not submenu), "function" (array or string type; callback function or method name), "icon" (string type; icon name for settings option; only for menu - not submenu), "menutype" (int type; it is one of the following constants from \KocujIL\V13a\Enums\Project\Components\Backend\SettingsMenu\Type: SITE or NETWORK), "onpluginslist" (bool type; there will be link to this settings on this plugin information in plugins list with title from this option or from "pluginslisttitle" attribute if exists), "pagescreenname" (string type; screen name for this page), "pluginslisttitle" (string type; title of plugin if "onpluginslist" is set to true), "title" (string type; menu title)
     */
    public function getSettingsMenus()
    {
        // prepare settings menus
        $settingsMenu = $this->settingsMenu;
        foreach ($settingsMenu as $key => $val) {
            if (!isset ($val ['fulltitle'])) {
                $settingsMenu [$key] ['fulltitle'] = '';
            }
            if (!isset ($val ['parentid'])) {
                $settingsMenu [$key] ['parentid'] = 0;
            }
            if (!isset ($val ['parentisbuiltinmenu'])) {
                $settingsMenu [$key] ['parentisbuiltinmenu'] = false;
            }
            if (!isset ($val ['icon'])) {
                $settingsMenu [$key] ['icon'] = '';
            }
            if (!isset ($val ['onpluginslist'])) {
                $settingsMenu [$key] ['onpluginslist'] = false;
            }
            if (!isset ($val ['pluginslisttitle'])) {
                $settingsMenu [$key] ['pluginslisttitle'] = '';
            }
            if (!isset ($val ['firstoptiontitle'])) {
                $settingsMenu [$key] ['firstoptiontitle'] = '';
            }
        }
        // exit
        return $settingsMenu;
    }

    /**
     * Check if settings menu exists
     *
     * @access public
     * @param string $id
     *            Settings menu identifier
     * @return bool Settings menu exists (true) or not (false)
     */
    public function checkSettingsMenu($id)
    {
        // exit
        return isset ($this->settingsMenu [$id]);
    }

    /**
     * Get the selected settings menu data
     *
     * @access public
     * @param string $id
     *            Settings menu identifier
     * @return array|bool Selected setting menu data or false if not exists; each settings menu data has the following fields: "alternativepagescreenname" (string type; alternative screen name for this page), "capability" (string type; capability required for access to this menu), "firstoptiontitle" (string type; title for first option if current option menu is without parent; only for menu - not submenu), "function" (array or string type; callback function or method name), "icon" (string type; icon name for settings option; only for menu - not submenu), "menutype" (int type; it is one of the following constants from \KocujIL\V13a\Enums\Project\Components\Backend\SettingsMenu\Type: SITE or NETWORK), "onpluginslist" (bool type; there will be link to this settings on this plugin information in plugins list with title from this option or from "pluginslisttitle" attribute if exists), "pagescreenname" (string type; screen name for this page), "pluginslisttitle" (string type; title of plugin if "onpluginslist" is set to true), "title" (string type; menu title)
     */
    public function getSettingsMenu($id)
    {
        // get settings menus
        $settingsMenu = $this->getSettingsMenus();
        // exit
        return (isset ($settingsMenu [$id])) ? $settingsMenu [$id] : false;
    }

    /**
     * Remove settings menu
     *
     * @access public
     * @param string $id
     *            Settings menu identifier
     * @return void
     * @throws Exception
     */
    public function removeSettingsMenu($id)
    {
        // check if this settings menu identifier exists
        if (!isset ($this->settingsMenu [$id])) {
            throw new Exception ($this,
                ExceptionCode::SETTINGS_MENU_ID_DOES_NOT_EXIST,
                __FILE__, __LINE__, $id);
        }
        // remove settings menu
        unset ($this->settingsMenu [$id]);
    }

    /**
     * Get hook suffix for the selected settings menu
     *
     * @access public
     * @param string $id
     *            Settings menu identifier
     * @return bool|string Hook suffix for the selected settings menu or false if not exists
     */
    public function getSettingsMenuHookSuffix($id)
    {
        // exit
        return (isset ($this->settingsMenuHookSuffix [$id])) ? $this->settingsMenuHookSuffix [$id] : false;
    }

    /**
     * Check if current page is for settings for current project
     *
     * @access public
     * @return bool Current page is for settings for current project (true) or not (false)
     */
    public function checkCurrentPageIsSettingsForProject()
    {
        // get screen identifier
        $screenId = CurrentScreenIdHelper::getInstance()->get();
        // exit
        return ((!empty ($screenId)) && (((!empty ($this->pageScreenNames)) && (in_array($screenId ['original'],
                        $this->pageScreenNames))) || ((!empty ($this->alternativePageScreenNames)) && (in_array($screenId ['alternative'],
                        $this->alternativePageScreenNames)))));
    }

    /**
     * Get current settings menu page
     *
     * @access public
     * @return bool|string Current settings menu page or false if it is not page for current project
     */
    public function getCurrentSettingsMenu()
    {
        // get current settings menu
        if (!empty ($this->settingsMenu)) {
            // get screen identifier
            $screenId = CurrentScreenIdHelper::getInstance()->get();
            if (!empty ($screenId)) {
                // get current settings menu
                foreach ($this->settingsMenu as $key => $val) {
                    if (($val ['pagescreenname'] === $screenId ['original']) || ($val ['alternativepagescreenname'] === $screenId ['alternative'])) {
                        return $key;
                    }
                }
            }
        }
        // exit
        return false;
    }

    /**
     * Action for adding menu
     *
     * @access public
     * @return void
     */
    public function actionAdminMenu()
    {
        // add settings menu
        if (!empty ($this->settingsMenu)) {
            // set required value of menu type
            $reqMenuType = (is_network_admin()) ? Type::NETWORK : Type::SITE;
            // add settings main menus
            foreach ($this->settingsMenu as $key => $menu) {
                if (($menu ['menutype'] === $reqMenuType) && (!isset ($menu ['parentid']))) {
                    $hookSuffix = add_menu_page($menu ['title'], $menu ['title'], $menu ['capability'],
                        $this->getProjectObj()->getMainSettingInternalName() . '_' . $key, array(
                            $this,
                            'settingsMenu_' . $key
                        ), (isset ($menu ['icon'])) ? $menu ['icon'] : '');
                    if ($hookSuffix !== false) {
                        $this->settingsMenuHookSuffix [$key] = $hookSuffix;
                    }
                    if (isset ($menu ['firstoptiontitle'])) {
                        $hookSuffix = add_submenu_page($this->getProjectObj()->getMainSettingInternalName() . '_' . $key,
                            $menu ['firstoptiontitle'], $menu ['firstoptiontitle'], $menu ['capability'],
                            $this->getProjectObj()->getMainSettingInternalName() . '_' . $key, array(
                                $this,
                                'settingsMenu_' . $key
                            ));
                        if ($hookSuffix !== false) {
                            $this->settingsMenuHookSuffix [$key] = $hookSuffix;
                        }
                    }
                }
            }
            // add settings submenus
            foreach ($this->settingsMenu as $key => $menu) {
                if (($menu ['menutype'] === $reqMenuType) && (isset ($menu ['parentid']))) {
                    $id = $this->getProjectObj()->getMainSettingInternalName() . '_' . $key;
                    $callback = array(
                        $this,
                        'settingsMenu_' . $key
                    );
                    if ((!isset ($menu ['parentisbuiltinmenu'])) || ((isset ($menu ['parentisbuiltinmenu'])) && (!$menu ['parentisbuiltinmenu']))) {
                        $hookSuffix = add_submenu_page($this->getProjectObj()->getMainSettingInternalName() . '_' . $menu ['parentid'],
                            $menu ['title'], $menu ['title'], $menu ['capability'], $id, $callback);
                    } else {
                        $enumMenuParentType = ParentType;
                        switch ($menu ['parentid']) {
                            case $enumMenuParentType::DASHBOARD :
                            case $enumMenuParentType::NETWORK_DASHBOARD :
                                $hookSuffix = add_dashboard_page($menu ['title'], $menu ['title'], $menu ['capability'],
                                    $id, $callback);
                                break;
                            case $enumMenuParentType::POSTS :
                                $hookSuffix = add_posts_page($menu ['title'], $menu ['title'], $menu ['capability'],
                                    $id, $callback);
                                break;
                            case $enumMenuParentType::MEDIA :
                                $hookSuffix = add_media_page($menu ['title'], $menu ['title'], $menu ['capability'],
                                    $id, $callback);
                                break;
                            case $enumMenuParentType::LINKS :
                                $hookSuffix = add_links_page($menu ['title'], $menu ['title'], $menu ['capability'],
                                    $id, $callback);
                                break;
                            case $enumMenuParentType::PAGES :
                                $hookSuffix = add_pages_page($menu ['title'], $menu ['title'], $menu ['capability'],
                                    $id, $callback);
                                break;
                            case $enumMenuParentType::COMMENTS :
                                $hookSuffix = add_comments_page($menu ['title'], $menu ['title'], $menu ['capability'],
                                    $id, $callback);
                                break;
                            case $enumMenuParentType::THEMES :
                            case $enumMenuParentType::NETWORK_THEMES :
                                $hookSuffix = add_theme_page($menu ['title'], $menu ['title'], $menu ['capability'],
                                    $id, $callback);
                                break;
                            case $enumMenuParentType::PLUGINS :
                            case $enumMenuParentType::NETWORK_PLUGINS :
                                $hookSuffix = add_plugins_page($menu ['title'], $menu ['title'], $menu ['capability'],
                                    $id, $callback);
                                break;
                            case $enumMenuParentType::USERS :
                            case $enumMenuParentType::NETWORK_USERS :
                                $hookSuffix = add_users_page($menu ['title'], $menu ['title'], $menu ['capability'],
                                    $id, $callback);
                                break;
                            case $enumMenuParentType::TOOLS :
                                $hookSuffix = add_management_page($menu ['title'], $menu ['title'],
                                    $menu ['capability'], $id, $callback);
                                break;
                            case $enumMenuParentType::OPTIONS :
                                $hookSuffix = add_options_page($menu ['title'], $menu ['title'], $menu ['capability'],
                                    $id, $callback);
                                break;
                            case $enumMenuParentType::NETWORK_SITES :
                                $hookSuffix = add_submenu_page('sites.php', $menu ['title'], $menu ['title'],
                                    $menu ['capability'], $id, $callback);
                                break;
                            case $enumMenuParentType::NETWORK_OPTIONS :
                                $hookSuffix = add_submenu_page('settings.php', $menu ['title'], $menu ['title'],
                                    $menu ['capability'], $id, $callback);
                                break;
                        }
                    }
                    if ($hookSuffix !== false) {
                        $this->settingsMenuHookSuffix [$key] = $hookSuffix;
                    }
                }
            }
        }
    }

    /**
     * Filter for actions links on plugins list
     *
     * @access public
     * @param string $links
     *            Actions links list
     * @param string $filename
     *            Plugin filename
     * @return string Actions links list
     */
    public function filterPluginActionLinks($links, $filename)
    {
        // show settings on plugin list
        if ((plugin_basename($this->getProjectObj()->getMainSettingMainFilename()) === $filename) && (!empty ($this->settingsMenu))) {
            // set required value of menu type
            $reqMenuType = (is_network_admin()) ? Type::NETWORK : Type::SITE;
            // show settings on plugin list
            foreach (array_reverse($this->settingsMenu) as $id => $settingsMenu) {
                if (($settingsMenu ['menutype'] === $reqMenuType) && (isset ($settingsMenu ['onpluginslist']))) {
                    array_unshift($links,
                        HtmlHelper::getInstance()->getLink(network_admin_url('admin.php?page=' . $this->getProjectObj()->getMainSettingInternalName() . '_' . $id),
                            (((isset ($settingsMenu ['pluginslisttitle']) && (isset ($settingsMenu ['pluginslisttitle'] [0]) /* strlen($settingsMenu['pluginslisttitle']) > 0 */))) ? $settingsMenu ['pluginslisttitle'] : $settingsMenu ['title'])));
                }
            }
        }
        // exit
        return $links;
    }
}
