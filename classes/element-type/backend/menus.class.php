<?php

/**
 * menus.class.php
 *
 * @author Dominik Kocuj
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2 or later
 * @copyright Copyright (c) 2013-2020 kocuj.pl
 * @package kocuj_sitemap
 */

// set namespace
namespace KocujSitemapPlugin\Classes\ElementType\Backend;

// set namespaces aliases
use KocujIL\V13a as KocujIL;
use KocujSitemapPlugin\Classes\ElementTypeBackendParent;
use KocujSitemapPlugin\Classes\Helpers\Url;
use KocujSitemapPlugin\Classes\MultipleLanguages;
use KocujSitemapPlugin\Classes\Options;

// security
if ((!defined('ABSPATH')) || ((isset($_SERVER['SCRIPT_FILENAME'])) && (basename($_SERVER['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die();
}

/**
 * Menus type administration panel class
 *
 * @access public
 */
class Menus extends ElementTypeBackendParent
{

    /**
     * Menu name
     *
     * @access private
     * @var string
     */
    private $menuName = '';

    /**
     * Menu data
     *
     * @access private
     * @var array
     */
    private $menuData = null;

    /**
     * Add menus by reccurence
     *
     * @access private
     * @param int $parentId
     *            Menu parent id
     * @param bool $addUrl
     *            Add URL to array (true) or not (false)
     * @param string $locale
     *            Language locale
     * @return array Output array
     */
    private function addReccurence($parentId, $addUrl, $locale)
    {
        // initialize
        $array = array();
        // get menu
        if ($this->menuData === null) {
            MultipleLanguages::getInstance()->doSomething('beforegetmenu', $locale);
            $this->menuData = wp_get_nav_menu_items($this->menuName, array(
                'order' => 'ASC',
                'orderby' => 'menu_order',
                'post_type' => 'nav_menu_item',
                'post_status' => 'publish',
                'output' => ARRAY_A,
                'output_key' => 'menu_order',
                'nopaging' => true,
                'update_post_term_cache' => false
            ));
            MultipleLanguages::getInstance()->doSomething('aftergetmenu', $locale);
        }
        // get data
        foreach ($this->menuData as $element) {
            if ((int)$element->menu_item_parent === $parentId) {
                MultipleLanguages::getInstance()->doSomething('beforegetmenuitem', $locale,
                    $element->ID);
                $type = 'unknown';
                if ($element->type === 'post_type') {
                    $typeTemp = get_post_type($element->object_id);
                    $type = ($typeTemp !== false) ? 'post' : 'custom';
                }
                if ($element->type === 'taxonomy') {
                    $termTemp = get_term_by('id', $element->object_id, 'category');
                    $type = ($termTemp !== false) ? 'category' : 'term';
                }
                if ($element->type !== 'taxonomy') {
                    $title = apply_filters('the_title',
                        MultipleLanguages::getInstance()->getItem('menutitle', $locale,
                            $element->title, $element->ID), $element->object_id);
                } else {
                    $title = MultipleLanguages::getInstance()->getItem('menuurl', $locale,
                        $element->title, $element->ID);
                }
                $linkText = apply_filters('kocujsitemap_link_text', $title, $element->ID, 'menu', $locale);
                if (!isset($linkText[0]) /* strlen($linkText) === 0 */) {
                    $linkText = '-';
                }
                $url = Url::getInstance()->removeProtocolLocal(MultipleLanguages::getInstance()->getItem('menuurl',
                    $locale, $element->url, $element->ID));
                $link = apply_filters('kocujsitemap_element',
                    KocujIL\Classes\HtmlHelper::getInstance()->getLink($url, $linkText), $element->ID, 'menu', $locale);
                $pos = count($array);
                $array[$pos] = array(
                    'id' => $element->object_id,
                    'tp' => $type,
                    'lk' => $link,
                    'ur' => $url
                );
                MultipleLanguages::getInstance()->doSomething('aftergetmenuitem', $locale,
                    $element->ID);
                $array[$pos]['ch'] = $this->addReccurence($element->ID, $addUrl, $locale);
                if (empty($array[$pos]['ch'])) {
                    unset($array[$pos]['ch']);
                }
            }
        }
        // exit
        return $array;
    }

    /**
     * Remove menus elements duplicates by reccurence
     *
     * @access private
     * @param array $array
     *            Input array
     * @param bool $allowRemoveUrls
     *            Allow remove URL-s from array (true) or not (false) - default: true
     * @return array Output array
     */
    private function removeDuplicatedReccurence(array $array, $allowRemoveUrls = true)
    {
        // check if array is not empty
        if (empty($array)) {
            return $array;
        }
        // initialize
        $newArray = array();
        $changed = false;
        $index = array();
        $urls = array();
        // remove duplicates
        foreach ($array as $key => $val) {
            if ((isset($index[$val['tp']][$val['id']])) || ((isset($urls[$val['ur']])) && ((substr($val['ur'], 0,
                                7) === 'http://') || (substr($val['ur'], 0, 8) === 'https://')))) {
                if ((isset($val['ch'])) && (!empty($val['ch']))) {
                    $pos = (isset($index[$val['tp']][$val['id']])) ? $index[$val['tp']][$val['id']] : $urls[$val['ur']];
                    if (!isset($newArray[$pos]['ch'])) {
                        $newArray[$pos]['ch'] = array();
                    }
                    $newArray[$pos]['ch'] = array_merge($newArray[$pos]['ch'], $val['ch']);
                }
                $changed = true;
            } else {
                if (!isset($index[$val['tp']])) {
                    $index[$val['tp']] = array();
                }
                if ($val['tp'] === 'unknown') {
                    $urls[$val['ur']] = $key;
                } else {
                    $index[$val['tp']][$val['id']] = $key;
                }
                if ((isset($val['ch'])) && (!empty($val['ch']))) {
                    $val['ch'] = $this->removeDuplicatedReccurence($val['ch']);
                }
                $newArray[$key] = $val;
            }
        }
        // correct array keys
        $newArray = array_values($newArray);
        // optionally execute this once again
        if ($changed) {
            $newArray = $this->removeDuplicatedReccurence($newArray, false);
        }
        // exit
        return $newArray;
    }

    /**
     * Add menus to sitemap
     *
     * @access public
     * @param string $locale
     *            Language locale - default: empty
     * @return array Output array
     */
    public function getElementArray($locale = '')
    {
        // initialize
        $array = array();
        // add menus to sitemap
        $value = Options::getInstance()->getOption('DisplayMenus');
        if ($value === '1') {
            // get option for removing duplicates
            $value = Options::getInstance()->getOption('HideMenusDuplicates');
            // add menus to sitemap
            $menus = Options::getInstance()->getOption('Menus');
            if (empty($menus)) {
                $menus = apply_filters('kocujsitemap_default_menus', array());
            }
            foreach ($menus as $menu) {
                $exists = wp_get_nav_menu_object($menu);
                if ($exists !== false) {
                    $this->menuData = null;
                    $this->menuName = $menu;
                    $array2 = $this->addReccurence(0, $value, $locale);
                    $array = array_merge($array, $array2);
                }
            }
            // optionally remove elements duplicates
            if (($value === '1') && (!empty($array))) {
                $array = $this->removeDuplicatedReccurence($array);
            }
        }
        // exit
        return $array;
    }

    /**
     * Get administration page settings
     *
     * @access public
     * @return array Administration page settings
     */
    public function getAdminPageSettings()
    {
        // exit
        return array(
            'title' => __('Menus list options', 'kocuj-sitemap'),
            'id' => 'menus_options',
            'help' => array(
                'overview' => array(
                    'title' => __('Overview', 'kocuj-sitemap'),
                    'content' => __('This is the place where you can enable or disable displaying of menu items in the sitemap. You can also change how they will be displayed.',
                            'kocuj-sitemap') . '</p>' . '<p>' . __('Options are divided into four tabs: `displaying menu items`, `menus list`, `options` and `section title`. Each tab can be selected by clicking on it.',
                            'kocuj-sitemap') . '</p>' . '<p>' . __('To save changed settings, click on the button `save menus list options`.',
                            'kocuj-sitemap')
                ),
                'displaying_menu_items' => array(
                    'title' => __('`Displaying menu items` tab', 'kocuj-sitemap'),
                    'content' => __('There are the following options in this tab:',
                            'kocuj-sitemap') . '</p>' . '<p><ul>' . '<li><em>`' . __('Display menus',
                            'kocuj-sitemap') . '`</em>: ' . __('If this option is checked, there will be menu items list displayed in the sitemap.',
                            'kocuj-sitemap') . '</li>' . '</ul>'
                ),
                'menus_list' => array(
                    'title' => __('`Menus list` tab', 'kocuj-sitemap'),
                    'content' => __('You can select multiple menus to be displayed in sitemap. To do this, click on the dropdown field and select one menu from the list. If you have done this step in the dropdown list which was empty, there will be added another empty dropdown list where you can select another menu from the list.',
                            'kocuj-sitemap') . '</p>' . '<p>' . __('If you want to change the order of the menu, you can click on one of the arrow next to the entry to move it up or down.',
                            'kocuj-sitemap') . '</p>' . '<p>' . __('Clicking on the `X` button deletes the entry.',
                            'kocuj-sitemap')
                ),
                'options' => array(
                    'title' => __('`Options` tab', 'kocuj-sitemap'),
                    'content' => __('There are the following options in this tab:',
                            'kocuj-sitemap') . '</p>' . '<p><ul>' . '<li><em>`' . __('Remove menu items duplicates from sitemap',
                            'kocuj-sitemap') . '`</em>: ' . __('If this option is checked, the duplicated menu items (posts, pages, etc.) on the same level will be displayed only once.',
                            'kocuj-sitemap') . '</li>' . '</ul>'
                ),
                'section_title' => array(
                    'title' => __('`Section title` tab', 'kocuj-sitemap'),
                    'content' => __('In this tab you can set the title of the section with menu items. It is used if option `divide display into sections` in the main settings of the Kocuj Sitemap plugin is checked.',
                            'kocuj-sitemap') . '</p>' . '<p>' . __('There are fields to enter the section title for each language that is available in your WordPress installation. If you have not activated any of the supported plugins for multilingualism, there will be visible only two fields to enter the title - for current WordPress language and for default section title in English language.',
                            'kocuj-sitemap') . '</p>' . '<p>' . __('It is not necessary to enter section titles here. However, the place is here in order to be able to display the title of the section in your chosen language if there is no translation for it. If you leave this empty and if you will not have a translation of section title for current language and for default language (English language), there will be displayed the standard section title in English language.',
                            'kocuj-sitemap')
                )
            )
        );
    }

    /**
     * Get administration page data
     *
     * @access public
     * @return array Administration page data
     */
    public function getAdminPageData()
    {
        // set menu options
        $options = array();
        $menus = get_terms('nav_menu');
        foreach ($menus as $menu) {
            $options[$menu->term_id] = $menu->name;
        }
        // exit
        return array(
            'tabs' => array(
                'menus_displaying' => array(
                    'title' => __('Displaying menu items', 'kocuj-sitemap'),
                    'fields' => array(
                        array(
                            'checkbox',
                            'DisplayMenus',
                            __('If this option is checked, there will be menu items list displayed in the sitemap.',
                                'kocuj-sitemap'),
                            array(),
                            array()
                        )
                    )
                ),
                'menus_list' => array(
                    'title' => __('Menus list', 'kocuj-sitemap'),
                    'fields' => (!empty($menus)) ? array(
                        array(
                            'select',
                            'Menus',
                            '',
                            array(),
                            array(
                                'options' => $options
                            )
                        )
                    ) : array(
                        array(
                            '',
                            __('No menus. Options here will be activated as soon as you create any menu.',
                                'kocuj-sitemap')
                        )
                    )
                ),
                'menus_options' => array(
                    'title' => __('Options', 'kocuj-sitemap'),
                    'fields' => array(
                        array(
                            'checkbox',
                            'HideMenusDuplicates',
                            __('If this option is checked, the duplicated menu items (posts, pages, etc.) on the same level will be displayed only once.',
                                'kocuj-sitemap'),
                            array(),
                            array()
                        )
                    )
                )
            ),
            'submit' => array(
                'label' => __('Save menus list options', 'kocuj-sitemap'),
                'tooltip' => __('Save current menus list options', 'kocuj-sitemap')
            )
        );
    }

    /**
     * Get administration panel order name
     *
     * @access public
     * @return string Administration panel order name
     */
    public function getAdminOrderName()
    {
        // exit
        return __('Menus', 'kocuj-sitemap');
    }

    /**
     * Get administration cache actions
     *
     * @access public
     * @return array Administration cache actions
     */
    public function getAdminCacheActions()
    {
        // exit
        return array(
            'wp_update_nav_menu',
            'wp_delete_nav_menu'
        );
    }
}
