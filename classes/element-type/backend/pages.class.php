<?php

/**
 * pages.class.php
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
use KocujSitemapPlugin\Classes\Helpers\Sort;
use KocujSitemapPlugin\Classes\Helpers\Url;
use KocujSitemapPlugin\Classes\MultipleLanguages;
use KocujSitemapPlugin\Classes\Options;

// security
if ((!defined('ABSPATH')) || ((isset($_SERVER['SCRIPT_FILENAME'])) && (basename($_SERVER['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die();
}

/**
 * Pages type administration panel class
 *
 * @access public
 */
class Pages extends ElementTypeBackendParent
{

    /**
     * Add pages by reccurence
     *
     * @access private
     * @param int $parentId
     *            Page parent id
     * @param string $locale
     *            Language locale - default: empty
     * @return array Output array
     */
    private function addReccurence($parentId, $locale = '')
    {
        // initialize
        $array = array();
        // get pages
        $value = Options::getInstance()->getOption('DisplayPagesSort');
        $sortColumn = 'menu_order';
        if ((isset($value[0]) /* strlen($value) > 0 */) && ($value !== 'default')) {
            $sortColumns = array(
                'title' => 'post_title',
                'id' => 'ID',
                'date' => 'post_date',
                'moddate' => 'post_modified'
            );
            $sortColumn = $sortColumns[$value];
        }
        $value = Options::getInstance()->getOption('DisplayPagesOrder');
        $sortOrder = 'ASC';
        if (isset($value[0]) /* strlen($value) > 0 */) {
            switch ($value) {
                case 'desc':
                    $sortOrder = 'DESC';
                    break;
            }
        }
        MultipleLanguages::getInstance()->doSomething('beforegetpages', $locale);
        $pages = get_pages(array(
            'parent' => $parentId,
            'sort_column' => $sortColumn,
            'sort_order' => $sortOrder,
            'post_type' => 'page',
            'post_status' => 'publish',
            'hierarchical' => 0,
            'offset' => 0,
            'number' => ''
        ));
        MultipleLanguages::getInstance()->doSomething('aftergetpages', $locale);
        foreach ($pages as $page) {
            MultipleLanguages::getInstance()->doSomething('beforegetpageitem', $locale,
                $page->ID);
            $linkText = apply_filters('kocujsitemap_link_text', apply_filters('the_title',
                MultipleLanguages::getInstance()->getItem('pagetitle', $locale,
                    $page->post_title, $page->ID), $page->ID), $page->ID, 'page', $locale);
            if (!isset($linkText[0]) /* strlen($linkText) === 0 */) {
                $linkText = '-';
            }
            $url = Url::getInstance()->removeProtocolLocal(MultipleLanguages::getInstance()->getItem('pageurl',
                $locale, get_permalink($page->ID), $page->ID));
            $link = apply_filters('kocujsitemap_element',
                KocujIL\Classes\HtmlHelper::getInstance()->getLink($url, $linkText), $page->ID, 'page', $locale);
            $pos = count($array);
            $arrayAdd = array(
                'id' => $page->ID,
                'tp' => 'page',
                'lk' => $link,
                'ur' => $url
            );
            if ($sortColumn === 'post_title') {
                $arrayAdd['sortname'] = $linkText;
            }
            $array[$pos] = $arrayAdd;
            MultipleLanguages::getInstance()->doSomething('aftergetpageitem', $locale,
                $page->ID);
            $array[$pos]['ch'] = $this->addReccurence($page->ID, $locale);
            if (empty($array[$pos]['ch'])) {
                unset($array[$pos]['ch']);
            }
        }
        if ($sortColumn === 'post_title') {
            $array = Sort::getInstance()->sortElements($array, $sortOrder);
        }
        // exit
        return $array;
    }

    /**
     * Add page to sitemap
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
        // add pages to sitemap
        $value = Options::getInstance()->getOption('DisplayPages');
        if ($value === '1') {
            $array = $this->addReccurence(0, $locale);
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
            'title' => __('Pages list options', 'kocuj-sitemap'),
            'id' => 'pages_options',
            'help' => array(
                'overview' => array(
                    'title' => __('Overview', 'kocuj-sitemap'),
                    'content' => __('This is the place where you can enable or disable displaying of pages in the sitemap. You can also change how they will be displayed.',
                            'kocuj-sitemap') . '</p>' . '<p>' . __('Options are divided into three tabs: `displaying pages`, `options` and `section title`. Each tab can be selected by clicking on it.',
                            'kocuj-sitemap') . '</p>' . '<p>' . __('To save changed settings, click on the button `save pages list options`.',
                            'kocuj-sitemap')
                ),
                'displaying_pages' => array(
                    'title' => __('`Displaying pages` tab', 'kocuj-sitemap'),
                    'content' => __('There are the following options in this tab:',
                            'kocuj-sitemap') . '</p>' . '<p><ul>' . '<li><em>`' . __('Display pages',
                            'kocuj-sitemap') . '`</em>: ' . __('If this option is checked, there will be pages list displayed in the sitemap.',
                            'kocuj-sitemap') . '</li>' . '</ul>'
                ),
                'options' => array(
                    'title' => __('`Options` tab', 'kocuj-sitemap'),
                    'content' => __('There are the following options in this tab:',
                            'kocuj-sitemap') . '</p>' . '<p><ul>' . '<li><em>`' . __('Sort pages by',
                            'kocuj-sitemap') . '`</em>: ' . __('You can sort pages in the sitemap using the selected pages properties. There are the following pages properties to select:',
                            'kocuj-sitemap') . '<ul>' . '<li><em>`' . __('default',
                            'kocuj-sitemap') . '`</em>: ' . __('Pages will be sorted by default order.',
                            'kocuj-sitemap') . '</li>' . '<li><em>`' . __('created date',
                            'kocuj-sitemap') . '`</em>: ' . __('Pages will be sorted by created date.',
                            'kocuj-sitemap') . '</li>' . '<li><em>`' . __('last modification date',
                            'kocuj-sitemap') . '`</em>: ' . __('Pages will be sorted by last modification date.',
                            'kocuj-sitemap') . '</li>' . '<li><em>`' . __('title',
                            'kocuj-sitemap') . '`</em>: ' . __('Pages will be sorted by title.',
                            'kocuj-sitemap') . '</li>' . '<li><em>`' . __('ID',
                            'kocuj-sitemap') . '`</em>: ' . __('Pages will be sorted by post identifier.',
                            'kocuj-sitemap') . '</li>' . '</ul>' . '</li>' . '<li><em>`' . __('Sort pages order',
                            'kocuj-sitemap') . '`</em>: ' . __('You can sort pages by ascending or descending order. It works only if option `sort pages by` is not set to `default`.',
                            'kocuj-sitemap') . '</li>' . '</ul>'
                ),
                'section_title' => array(
                    'title' => __('`Section title` tab', 'kocuj-sitemap'),
                    'content' => __('In this tab you can set the title of the section with pages. It is used if option `divide display into sections` in the main settings of the Kocuj Sitemap plugin is checked.',
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
        // exit
        return array(
            'tabs' => array(
                'pages_displaying' => array(
                    'title' => __('Displaying pages', 'kocuj-sitemap'),
                    'fields' => array(
                        array(
                            'checkbox',
                            'DisplayPages',
                            __('If this option is checked, there will be pages list displayed in the sitemap.',
                                'kocuj-sitemap'),
                            array(),
                            array()
                        )
                    )
                ),
                'pages_options' => array(
                    'title' => __('Options', 'kocuj-sitemap'),
                    'fields' => array(
                        array(
                            'select',
                            'DisplayPagesSort',
                            __('You can sort pages in the sitemap using the selected pages properties.',
                                'kocuj-sitemap'),
                            array(),
                            array(
                                'options' => array(
                                    'default' => __('default', 'kocuj-sitemap'),
                                    'date' => __('created date', 'kocuj-sitemap'),
                                    'moddate' => __('last modification date', 'kocuj-sitemap'),
                                    'title' => __('title', 'kocuj-sitemap'),
                                    'id' => __('ID', 'kocuj-sitemap')
                                )
                            )
                        ),
                        array(
                            'select',
                            'DisplayPagesOrder',
                            __('You can sort pages by ascending or descending order.', 'kocuj-sitemap'),
                            array(),
                            array(
                                'global_addinfo' => __('This option works only if option `sort pages by` is not set to `default`.',
                                    'kocuj-sitemap'),
                                'options' => array(
                                    'asc' => __('ascending', 'kocuj-sitemap'),
                                    'desc' => __('descending', 'kocuj-sitemap')
                                )
                            )
                        )
                    )
                )
            ),
            'submit' => array(
                'label' => __('Save pages list options', 'kocuj-sitemap'),
                'tooltip' => __('Save current pages list options', 'kocuj-sitemap')
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
        return __('Pages', 'kocuj-sitemap');
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
            'wp_insert_post',
            'post_updated',
            'after_delete_post'
        );
    }
}
