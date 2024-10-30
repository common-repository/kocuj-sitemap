<?php

/**
 * custom.class.php
 *
 * @author Dominik Kocuj
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2 or later
 * @copyright Copyright (c) 2013-2020 kocuj.pl
 * @package kocuj_sitemap
 */

// set namespace
namespace KocujSitemapPlugin\Classes\ElementType\Backend;

// set namespaces aliases
use KocujIL\V13a\Classes\HtmlHelper;
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
 * Custom post types administration panel class
 *
 * @access public
 */
class Custom extends ElementTypeBackendParent
{

    /**
     * Add custom posts by reccurence
     *
     * @access private
     * @param string $type
     *            Custom post type
     * @param string $taxonomy
     *            Taxonomy name
     * @param int $termId
     *            Taxonomy id
     * @param string $locale
     *            Language locale - default: empty
     * @return array Output array
     */
    private function addReccurence($type, $taxonomy, $termId, $locale = '')
    {
        // initialize
        $array = array();
        // check if display taxonomies
        $showTaxonomies = Options::getInstance()->getOption('DisplayCustomPostsTaxonomies');
        // get custom posts
        if (($termId !== 0) || ($showTaxonomies === '0')) {
            $value = Options::getInstance()->getOption('DisplayCustomPostsSort');
            $sortColumn = 'date';
            if ((isset($value[0]) /* strlen($value) > 0 */) && ($value !== 'date')) {
                $sortColumns = array(
                    'moddate' => 'modified',
                    'title' => 'title',
                    'id' => 'ID'
                );
                $sortColumn = $sortColumns[$value];
            }
            $value = Options::getInstance()->getOption('DisplayCustomPostsOrder');
            $sortOrder = 'DESC';
            if (isset($value[0]) /* strlen($value) > 0 */) {
                switch ($value) {
                    case 'asc':
                        $sortOrder = 'ASC';
                        break;
                }
            }
            MultipleLanguages::getInstance()->doSomething('beforegetcustomposts', $locale);
            $posts = get_posts(array(
                'post_parent' => 0,
                'orderby' => $sortColumn,
                'order' => $sortOrder,
                'post_type' => $type,
                'post_status' => 'publish',
                'numberposts' => -1,
                'offset' => 0,
                'tax_query' => $showTaxonomies ? array(
                    array(
                        'taxonomy' => $taxonomy,
                        'field' => 'term_id',
                        'terms' => $termId,
                        'operator' => 'IN'
                    )
                ) : array()
            ));
            MultipleLanguages::getInstance()->doSomething('aftergetcustomposts', $locale);
            foreach ($posts as $post) {
                MultipleLanguages::getInstance()->doSomething('beforegetcustompostitem',
                    $locale, $post->ID);
                $linkText = apply_filters('kocujsitemap_link_text', apply_filters('the_title',
                    MultipleLanguages::getInstance()->getItem('customposttitle', $locale,
                        $post->post_title, $post->ID), $post->ID), $post->ID, 'custom', $locale);
                if (!isset($linkText[0]) /* strlen($linkText) === 0 */) {
                    $linkText = '-';
                }
                $url = Url::getInstance()->removeProtocolLocal(MultipleLanguages::getInstance()->getItem('customposturl',
                    $locale, get_permalink($post->ID), $post->ID));
                $link = apply_filters('kocujsitemap_element', HtmlHelper::getInstance()->getLink($url, $linkText),
                    $post->ID, 'custom', $locale);
                $customTypeObj = get_post_type_object($type);
                $arrayAdd = array(
                    'id' => $post->ID,
                    'tp' => 'post',
                    'lk' => $link,
                    'ur' => $url,
                    'ad' => (isset($customTypeObj->labels->name)) ? array(
                        $type,
                        $customTypeObj->labels->name
                    ) : array(
                        $type,
                        $type
                    )
                );
                if ($sortColumn === 'title') {
                    $arrayAdd['sortname'] = $linkText;
                }
                $array[] = $arrayAdd;
                MultipleLanguages::getInstance()->doSomething('aftergetcustompostitem',
                    $locale, $post->ID);
            }
            if ($sortColumn === 'title') {
                $array = Sort::getInstance()->sortElements($array, $sortOrder);
            }
        }
        // get taxonomies
        if ($showTaxonomies) {
            $showEmptyTaxonomies = Options::getInstance()->getOption('DisplayEmptyCustomPostsTaxonomies');
            MultipleLanguages::getInstance()->doSomething('beforegettaxonomies', $locale,
                $taxonomy);
            $terms = get_categories(array(
                'type' => $type,
                'parent' => $termId,
                'orderby' => 'name',
                'order' => 'ASC',
                'hide_empty' => ($showEmptyTaxonomies === '1') ? 0 : 1,
                'hierarchical' => 1,
                'taxonomy' => $taxonomy
            ));
            MultipleLanguages::getInstance()->doSomething('aftergettaxonomies', $locale,
                $taxonomy);
            if (!empty($terms)) {
                $array2 = array();
                foreach ($terms as $term) {
                    MultipleLanguages::getInstance()->doSomething('beforegettaxonomyitem',
                        $locale, $term->term_id, $taxonomy);
                    $linkText = apply_filters('kocujsitemap_link_text',
                        MultipleLanguages::getInstance()->getItem('taxonomytitle', $locale,
                            $term->name, $term->term_id, $taxonomy), $term->term_id, 'taxonomy', $locale);
                    if (!isset($linkText[0]) /* strlen($linkText) === 0 */) {
                        $linkText = '-';
                    }
                    $url = Url::getInstance()->removeProtocolLocal(MultipleLanguages::getInstance()->getItem('taxonomyurl',
                        $locale, get_term_link($term), $term->term_id, $taxonomy));
                    $link = apply_filters('kocujsitemap_element',
                        HtmlHelper::getInstance()->getLink($url, $linkText), $term->term_id, 'taxonomy',
                        $locale);
                    $customTypeObj = get_post_type_object($type);
                    $pos = count($array2) + count($array);
                    $array2[$pos] = array(
                        'id' => $term->term_id,
                        'tp' => 'term',
                        'lk' => $link,
                        'ur' => $url,
                        'ad' => (isset($customTypeObj->labels->name)) ? array(
                            $type,
                            $customTypeObj->labels->name
                        ) : array(
                            $type,
                            $type
                        ),
                        'sortname' => $linkText
                    );
                    MultipleLanguages::getInstance()->doSomething('aftergettaxonomyitem',
                        $locale, $term->term_id, $taxonomy);
                    $array2[$pos]['ch'] = $this->addReccurence($type, $taxonomy, $term->term_id, $locale);
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
     * Add custom post types posts to sitemap
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
        // add posts to sitemap
        $value = Options::getInstance()->getOption('DisplayCustomPosts');
        if ($value === '1') {
            // get custom posts list
            $types = Options::getInstance()->getOption('CustomPosts');
            if (empty($types)) {
                $types = apply_filters('kocujsitemap_default_custom', array());
            }
            if (!empty($types)) {
                $showTaxonomies = Options::getInstance()->getOption('DisplayCustomPostsTaxonomies');
                foreach ($types as $type) {
                    $taxonomies = $showTaxonomies ? get_object_taxonomies($type) : array(
                        ''
                    );
                    foreach ($taxonomies as $taxonomy) {
                        $array2 = $this->addReccurence($type, $taxonomy, 0, $locale);
                        if (!empty($array2)) {
                            $array = array_merge($array, $array2);
                        }
                    }
                }
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
            'title' => __('Custom post types posts list options', 'kocuj-sitemap'),
            'id' => 'custom_options',
            'help' => array(
                'overview' => array(
                    'title' => __('Overview', 'kocuj-sitemap'),
                    'content' => __('This is the place where you can enable or disable displaying of custom post types posts in the sitemap. You can also change how they will be displayed.',
                            'kocuj-sitemap') . '</p>' . '<p>' . __('Options are divided into three tabs: `displaying custom post types posts`, `custom post types list` and `options`. Each tab can be selected by clicking on it.',
                            'kocuj-sitemap') . '</p>' . '<p>' . __('To save changed settings, click on the button `save custom post types posts list options`.',
                            'kocuj-sitemap')
                ),
                'displaying_custom_posts_type' => array(
                    'title' => __('`Displaying custom post types posts` tab', 'kocuj-sitemap'),
                    'content' => __('There are the following options in this tab:',
                            'kocuj-sitemap') . '</p>' . '<p><ul>' . '<li><em>`' . __('Display custom post types posts',
                            'kocuj-sitemap') . '`</em>: ' . __('If this option is checked, there will be custom post types posts list displayed in the sitemap.',
                            'kocuj-sitemap') . '</li>' . '</ul>'
                ),
                'custom_posts_types_list' => array(
                    'title' => __('`Custom post types list` tab', 'kocuj-sitemap'),
                    'content' => __('You can select multiple custom post types to be displayed in sitemap. To do this, click on the dropdown field and select one custom post type from the list. If you have done this step in the dropdown list which was empty, there will be added another empty dropdown list where you can select another custom post type from the list.',
                            'kocuj-sitemap') . '</p>' . '<p>' . __('If you want to change the order of the custom post types, you can click on one of the arrows next to the entry to move it up or down.',
                            'kocuj-sitemap') . '</p>' . '<p>' . __('Clicking on the `X` button deletes the entry.',
                            'kocuj-sitemap')
                ),
                'options' => array(
                    'title' => __('`Options` tab', 'kocuj-sitemap'),
                    'content' => __('There are the following options in this tab:',
                            'kocuj-sitemap') . '</p>' . '<p><ul>' . '<li><em>`' . __('Divide custom post types posts into taxonomies',
                            'kocuj-sitemap') . '`</em>: ' . __('If this option is checked, custom post type posts will be divided into taxonomies.',
                            'kocuj-sitemap') . '</li>' . '<li><em>`' . __('Display empty taxonomies',
                            'kocuj-sitemap') . '`</em>: ' . __('If this option is checked, empty taxonomies will be displayed. This option works only if option `divide custom post types posts into taxonomies` is checked.',
                            'kocuj-sitemap') . '</li>' . '<li><em>`' . __('Sort custom post types posts by',
                            'kocuj-sitemap') . '`</em>: ' . __('You can sort custom post types posts in the sitemap using the selected posts properties. There are the following custom post types posts properties to select:',
                            'kocuj-sitemap') . '<ul>' . '<li><em>`' . __('created date',
                            'kocuj-sitemap') . '`</em>: ' . __('Posts will be sorted by created date.',
                            'kocuj-sitemap') . '</li>' . '<li><em>`' . __('last modification date',
                            'kocuj-sitemap') . '`</em>: ' . __('Posts will be sorted by last modification date.',
                            'kocuj-sitemap') . '</li>' . '<li><em>`' . __('title',
                            'kocuj-sitemap') . '`</em>: ' . __('Posts will be sorted by title.',
                            'kocuj-sitemap') . '</li>' . '<li><em>`' . __('ID',
                            'kocuj-sitemap') . '`</em>: ' . __('Posts will be sorted by post identifier.',
                            'kocuj-sitemap') . '</li>' . '</ul>' . '</li>' . '<li><em>`' . __('Sort custom post types posts order',
                            'kocuj-sitemap') . '`</em>: ' . __('You can sort custom post types posts by ascending or descending order.',
                            'kocuj-sitemap') . '</li>' . '</ul>'
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
        // get custom posts options
        $types = get_post_types(array(
            'public' => true,
            '_builtin' => false
        ), 'object');
        $options = array();
        foreach ($types as $type => $val) {
            $options[$type] = $val->labels->name;
        }
        // exit
        return array(
            'tabs' => array(
                'custom_displaying' => array(
                    'title' => __('Displaying custom post types posts', 'kocuj-sitemap'),
                    'fields' => array(
                        array(
                            'checkbox',
                            'DisplayCustomPosts',
                            __('If this option is checked, there will be custom post types posts list displayed in the sitemap.',
                                'kocuj-sitemap'),
                            array(),
                            array()
                        )
                    )
                ),
                'custom_list' => array(
                    'title' => __('Custom post types list', 'kocuj-sitemap'),
                    'fields' => (!empty($types)) ? array(
                        array(
                            'select',
                            'CustomPosts',
                            '',
                            array(),
                            array(
                                'options' => $options
                            )
                        )
                    ) : array(
                        array(
                            '',
                            __('No custom post types. Options here will be activated as soon as you create any custom post types.',
                                'kocuj-sitemap')
                        )
                    )
                ),
                'custom_options' => array(
                    'title' => __('Options', 'kocuj-sitemap'),
                    'fields' => array(
                        array(
                            'checkbox',
                            'DisplayCustomPostsTaxonomies',
                            __('If this option is checked, custom post type posts will be divided into taxonomies.',
                                'kocuj-sitemap'),
                            array(),
                            array()
                        ),
                        array(
                            'checkbox',
                            'DisplayEmptyCustomPostsTaxonomies',
                            __('If this option is checked, empty taxonomies will be displayed.', 'kocuj-sitemap'),
                            array(),
                            array(
                                'global_addinfo' => __('This option works only if option `divide custom post types posts into taxonomies` is checked.',
                                    'kocuj-sitemap')
                            )
                        ),
                        array(
                            'select',
                            'DisplayCustomPostsSort',
                            __('You can sort custom post types posts in the sitemap using the selected posts properties.',
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
                            'DisplayCustomPostsOrder',
                            __('You can sort custom post types posts by ascending or descending order.',
                                'kocuj-sitemap'),
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
                'label' => __('Save custom post types posts list options', 'kocuj-sitemap'),
                'tooltip' => __('Save current custom post types posts list options', 'kocuj-sitemap')
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
        return __('Custom post types', 'kocuj-sitemap');
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
