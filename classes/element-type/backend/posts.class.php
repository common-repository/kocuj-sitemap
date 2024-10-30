<?php

/**
 * posts.class.php
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
 * Posts type administration panel class
 *
 * @access public
 */
class Posts extends ElementTypeBackendParent
{

    /**
     * Add posts and category by reccurence
     *
     * @access private
     * @param int $parentId
     *            Category parent id
     * @param string $locale
     *            Language locale - default: empty
     * @return array Output array
     */
    private function addReccurence($parentId, $locale = '')
    {
        // initialize
        $array = array();
        // check if display categories
        $showCategories = Options::getInstance()->getOption('DisplayPostsCategories');
        // get posts
        if (($parentId !== 0) || (!$showCategories)) {
            $value = Options::getInstance()->getOption('DisplayPostsSort');
            $sortColumn = 'date';
            if ((isset($value[0]) /* strlen($value) > 0 */) && ($value !== 'date')) {
                $sortColumns = array(
                    'moddate' => 'modified',
                    'title' => 'title',
                    'id' => 'ID'
                );
                $sortColumn = $sortColumns[$value];
            }
            $value = Options::getInstance()->getOption('DisplayPostsOrder');
            $sortOrder = 'DESC';
            if (isset($value[0]) /* strlen($value) > 0 */) {
                switch ($value) {
                    case 'asc':
                        $sortOrder = 'ASC';
                        break;
                }
            }
            MultipleLanguages::getInstance()->doSomething('beforegetposts', $locale);
            $posts = get_posts(array(
                'category' => $parentId,
                'orderby' => $sortColumn,
                'order' => $sortOrder,
                'post_type' => 'post',
                'post_status' => 'publish',
                'offset' => 0,
                'numberposts' => -1
            ));
            MultipleLanguages::getInstance()->doSomething('aftergetposts', $locale);
            foreach ($posts as $post) {
                MultipleLanguages::getInstance()->doSomething('beforegetpostitem', $locale,
                    $post->ID);
                $linkText = apply_filters('kocujsitemap_link_text', apply_filters('the_title',
                    MultipleLanguages::getInstance()->getItem('posttitle', $locale,
                        $post->post_title, $post->ID), $post->ID), $post->ID, 'post', $locale);
                if (!isset($linkText[0]) /* strlen($linkText) === 0 */) {
                    $linkText = '-';
                }
                $url = Url::getInstance()->removeProtocolLocal(MultipleLanguages::getInstance()->getItem('posturl',
                    $locale, get_permalink($post->ID), $post->ID));
                $link = apply_filters('kocujsitemap_element',
                    KocujIL\Classes\HtmlHelper::getInstance()->getLink($url, $linkText), $post->ID, 'post', $locale);
                $arrayAdd = array(
                    'id' => $post->ID,
                    'tp' => 'post',
                    'lk' => $link,
                    'ur' => $url
                );
                if ($sortColumn === 'title') {
                    $arrayAdd['sortname'] = $linkText;
                }
                $array[] = $arrayAdd;
                MultipleLanguages::getInstance()->doSomething('aftergetpostitem', $locale,
                    $post->ID);
            }
            if ($sortColumn === 'title') {
                $array = Sort::getInstance()->sortElements($array, $sortOrder);
            }
        }
        // get categories
        if ($showCategories) {
            $showEmptyCategories = Options::getInstance()->getOption('DisplayEmptyPostsCategories');
            MultipleLanguages::getInstance()->doSomething('beforegetcategories', $locale);
            $categories = get_categories(array(
                'type' => 'post',
                'parent' => $parentId,
                'orderby' => 'name',
                'order' => 'ASC',
                'hide_empty' => ($showEmptyCategories === '1') ? 0 : 1,
                'hierarchical' => 1,
                'taxonomy' => 'category'
            ));
            MultipleLanguages::getInstance()->doSomething('aftergetcategories', $locale);
            if (!empty($categories)) {
                $array2 = array();
                foreach ($categories as $category) {
                    MultipleLanguages::getInstance()->doSomething('beforegetcategoryitem',
                        $locale, $category->cat_ID);
                    $linkText = apply_filters('kocujsitemap_link_text',
                        MultipleLanguages::getInstance()->getItem('categorytitle', $locale,
                            $category->name, $category->cat_ID), $category->cat_ID, 'category', $locale);
                    if (!isset($linkText[0]) /* strlen($linkText) === 0 */) {
                        $linkText = '-';
                    }
                    $url = Url::getInstance()->removeProtocolLocal(MultipleLanguages::getInstance()->getItem('categoryurl',
                        $locale, get_category_link($category->cat_ID), $category->cat_ID));
                    $link = apply_filters('kocujsitemap_element',
                        KocujIL\Classes\HtmlHelper::getInstance()->getLink($url, $linkText), $category->cat_ID,
                        'category', $locale);
                    $pos = count($array2) + count($array);
                    $array2[$pos] = array(
                        'id' => $category->cat_ID,
                        'tp' => 'category',
                        'lk' => $link,
                        'ur' => $url,
                        'sortname' => $linkText
                    );
                    MultipleLanguages::getInstance()->doSomething('aftergetcategoryitem',
                        $locale, $category->cat_ID);
                    $array2[$pos]['ch'] = $this->addReccurence($category->cat_ID, $locale);
                    if (empty($array2[$pos]['ch'])) {
                        unset($array2[$pos]['ch']);
                    }
                }
                $array2 = Sort::getInstance()->sortElements($array2, 'ASC');
                $array = array_merge($array, $array2);
            }
        }
        // exit
        return $array;
    }

    /**
     * Add posts to sitemap
     *
     * @access public
     * @param string $locale
     *            Language locale - default: empty
     * @return array Output array
     */
    public function getElementArray($locale = '')
    {
        // add posts to sitemap
        $value = Options::getInstance()->getOption('DisplayPosts');
        if ($value === '1') {
            return $this->addReccurence(0, $locale);
        }
        // exit
        return array();
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
            'title' => __('Posts list options', 'kocuj-sitemap'),
            'id' => 'posts_options',
            'help' => array(
                'overview' => array(
                    'title' => __('Overview', 'kocuj-sitemap'),
                    'content' => __('This is the place where you can enable or disable displaying of posts in the sitemap. You can also change how they will be displayed.',
                            'kocuj-sitemap') . '</p>' . '<p>' . __('Options are divided into three tabs: `displaying posts`, `options` and `section title`. Each tab can be selected by clicking on it.',
                            'kocuj-sitemap') . '</p>' . '<p>' . __('To save changed settings, click on the button `save posts list options`.',
                            'kocuj-sitemap')
                ),
                'displaying_posts' => array(
                    'title' => __('`Displaying posts` tab', 'kocuj-sitemap'),
                    'content' => __('There are the following options in this tab:',
                            'kocuj-sitemap') . '</p>' . '<p><ul>' . '<li><em>`' . __('Display posts',
                            'kocuj-sitemap') . '`</em>: ' . __('If this option is checked, there will be posts list displayed in the sitemap.',
                            'kocuj-sitemap') . '</li>' . '</ul>'
                ),
                'options' => array(
                    'title' => __('`Options` tab', 'kocuj-sitemap'),
                    'content' => __('There are the following options in this tab:',
                            'kocuj-sitemap') . '</p>' . '<p><ul>' . '<li><em>`' . __('Divide posts into categories',
                            'kocuj-sitemap') . '`</em>: ' . __('If this option is checked, posts will be divided into categories.',
                            'kocuj-sitemap') . '</li>' . '<li><em>`' . __('Display empty categories',
                            'kocuj-sitemap') . '`</em>: ' . __('If this option is checked, empty categories will be displayed. This option works only if option `divide posts into categories` is checked.',
                            'kocuj-sitemap') . '</li>' . '<li><em>`' . __('Sort posts by',
                            'kocuj-sitemap') . '`</em>: ' . __('You can sort posts in the sitemap using the selected posts properties. There are the following posts properties to select:',
                            'kocuj-sitemap') . '<ul>' . '<li><em>`' . __('created date',
                            'kocuj-sitemap') . '`</em>: ' . __('Posts will be sorted by created date.',
                            'kocuj-sitemap') . '</li>' . '<li><em>`' . __('last modification date',
                            'kocuj-sitemap') . '`</em>: ' . __('Posts will be sorted by last modification date.',
                            'kocuj-sitemap') . '</li>' . '<li><em>`' . __('title',
                            'kocuj-sitemap') . '`</em>: ' . __('Posts will be sorted by title.',
                            'kocuj-sitemap') . '</li>' . '<li><em>`' . __('ID',
                            'kocuj-sitemap') . '`</em>: ' . __('Posts will be sorted by post identifier.',
                            'kocuj-sitemap') . '</li>' . '</ul>' . '</li>' . '<li><em>`' . __('Sort posts order',
                            'kocuj-sitemap') . '`</em>: ' . __('You can sort posts by ascending or descending order.',
                            'kocuj-sitemap') . '</li>' . '</ul>'
                ),
                'section_title' => array(
                    'title' => __('`Section title` tab', 'kocuj-sitemap'),
                    'content' => __('In this tab you can set the title of the section with posts. It is used if option `divide display into sections` in the main settings of the Kocuj Sitemap plugin is checked.',
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
                'posts_displaying' => array(
                    'title' => __('Displaying posts', 'kocuj-sitemap'),
                    'fields' => array(
                        array(
                            'checkbox',
                            'DisplayPosts',
                            __('If this option is checked, there will be posts list displayed in the sitemap.',
                                'kocuj-sitemap'),
                            array(),
                            array()
                        )
                    )
                ),
                'posts_options' => array(
                    'title' => __('Options', 'kocuj-sitemap'),
                    'fields' => array(
                        array(
                            'checkbox',
                            'DisplayPostsCategories',
                            __('If this option is checked, posts will be divided into categories.', 'kocuj-sitemap'),
                            array(),
                            array()
                        ),
                        array(
                            'checkbox',
                            'DisplayEmptyPostsCategories',
                            __('If this option is checked, empty categories will be displayed.', 'kocuj-sitemap'),
                            array(),
                            array(
                                'global_addinfo' => __('This option works only if option `divide posts into categories` is checked.',
                                    'kocuj-sitemap')
                            )
                        ),
                        array(
                            'select',
                            'DisplayPostsSort',
                            __('You can sort posts in the sitemap using the selected posts properties.',
                                'kocuj-sitemap'),
                            array(),
                            array(
                                'options' => array(
                                    'date' => __('created date', 'kocuj-sitemap'),
                                    'moddate' => __('last modification date', 'kocuj-sitemap'),
                                    'title' => __('title', 'kocuj-sitemap'),
                                    'id' => __('ID', 'kocuj-sitemap')
                                )
                            )
                        ),
                        array(
                            'select',
                            'DisplayPostsOrder',
                            __('You can sort posts by ascending or descending order.', 'kocuj-sitemap'),
                            array(),
                            array(
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
                'label' => __('Save posts list options', 'kocuj-sitemap'),
                'tooltip' => __('Save current posts list options', 'kocuj-sitemap')
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
        return __('Posts', 'kocuj-sitemap');
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
