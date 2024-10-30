<?php

/**
 * authors.class.php
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
 * Authors type administration panel class
 *
 * @access public
 */
class Authors extends ElementTypeBackendParent
{

    /**
     * Add authors to sitemap
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
        // add authors to sitemap
        $value = Options::getInstance()->getOption('DisplayAuthors');
        if ($value === '1') {
            // get authors
            $value = Options::getInstance()->getOption('DisplayAuthorsSort');
            $sortColumn = 'display_name';
            if ((isset($value[0]) /* strlen($value) > 0 */) && ($value !== 'name')) {
                $sortColumns = array(
                    'login' => 'login',
                    'id' => 'ID',
                    'date' => 'registered',
                    'posts' => 'post_count'
                );
                $sortColumn = $sortColumns[$value];
            }
            $value = Options::getInstance()->getOption('DisplayAuthorsOrder');
            $sortOrder = 'ASC';
            if (isset($value[0]) /* strlen($value) > 0 */) {
                switch ($value) {
                    case 'desc':
                        $sortOrder = 'DESC';
                        break;
                }
            }
            MultipleLanguages::getInstance()->doSomething('beforegetauthors', $locale);
            $authors = get_users(array(
                'orderby' => $sortColumn,
                'order' => $sortOrder,
                'who' => 'authors',
                'number' => '',
                'offset' => ''
            ));
            MultipleLanguages::getInstance()->doSomething('aftergetauthors', $locale);
            if (!empty($authors)) {
                $check = Options::getInstance()->getOption('DisplayOnlyAuthorsWithArticles');
                global $wpdb;
                if ($check === '1') {
                    $types = get_post_types(array(
                        'public' => true,
                        '_builtin' => false
                    ), 'names');
                    $typeString = '';
                    if (!empty($types)) {
                        foreach ($types as $type) {
                            $typeString .= $wpdb->prepare('"%s"', $type) . ',';
                        }
                        $typeString = substr($typeString, 0, -1);
                    }
                }
                foreach ($authors as $author) {
                    $ok = true;
                    if ($check) {
                        $postsCount = $wpdb->get_var($wpdb->prepare('SELECT COUNT(*) FROM ' . $wpdb->posts . ' WHERE post_author = "%d" AND post_type IN ("post", "page"' . $typeString . ') AND post_status = "publish"',
                            $author->ID));
                        if (empty($postsCount)) {
                            $ok = false;
                        }
                    }
                    if ($ok) {
                        MultipleLanguages::getInstance()->doSomething('beforegetauthoritem', $locale, $author->ID);
                        $linkText = apply_filters('kocujsitemap_link_text',
                            MultipleLanguages::getInstance()->getItem('authorname', $locale, $author->display_name,
                                $author->ID), $author->ID, 'author', $locale);
                        if (!isset($linkText[0]) /* strlen($linkText) === 0 */) {
                            $linkText = '-';
                        }
                        $url = Url::getInstance()->removeProtocolLocal(MultipleLanguages::getInstance()->getItem('authorurl',
                            $locale, get_author_posts_url($author->ID), $author->ID));
                        $link = apply_filters('kocujsitemap_element', HtmlHelper::getInstance()->getLink('', $linkText),
                            $author->ID, 'author',
                            $locale);
                        $arrayAdd = array(
                            'id' => $author->ID,
                            'tp' => 'author',
                            'lk' => $link,
                            'ur' => $url
                        );
                        if ($sortColumn === 'login') {
                            $arrayAdd['sortname'] = $linkText;
                        }
                        $array[] = $arrayAdd;
                        MultipleLanguages::getInstance()->doSomething('aftergetauthoritem', $locale, $author->ID);
                    }
                }
                if ($sortColumn === 'login') {
                    $array = Sort::getInstance()->sortElements($array, $sortOrder);
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
            'title' => __('Authors list options', 'kocuj-sitemap'),
            'id' => 'authors_options',
            'help' => array(
                'overview' => array(
                    'title' => __('Overview', 'kocuj-sitemap'),
                    'content' => __('This is the place where you can enable or disable displaying of authors in the sitemap. You can also change how they will be displayed.',
                            'kocuj-sitemap') . '</p>' . '<p>' . __('Options are divided into three tabs: `displaying authors`, `options` and `section title`. Each tab can be selected by clicking on it.',
                            'kocuj-sitemap') . '</p>' . '<p>' . __('To save changed settings, click on the button `save authors list options`.',
                            'kocuj-sitemap')
                ),
                'displaying_authors' => array(
                    'title' => __('`Displaying authors` tab', 'kocuj-sitemap'),
                    'content' => __('There are the following options in this tab:',
                            'kocuj-sitemap') . '</p>' . '<p><ul>' . '<li><em>`' . __('Display authors',
                            'kocuj-sitemap') . '`</em>: ' . __('If this option is checked, there will be authors list displayed in the sitemap.',
                            'kocuj-sitemap') . '</li>' . '</ul>'
                ),
                'options' => array(
                    'title' => __('`Options` tab', 'kocuj-sitemap'),
                    'content' => __('There are the following options in this tab:',
                            'kocuj-sitemap') . '</p>' . '<p><ul>' . '<li><em>`' . __('Display only authors with at least one article',
                            'kocuj-sitemap') . '`</em>: ' . __('If this option is checked, only authors with at least one article will be displayed.',
                            'kocuj-sitemap') . '<li><em>`' . __('Sort authors by',
                            'kocuj-sitemap') . '`</em>: ' . __('You can sort authors in the sitemap using the selected authors properties. There are the following authors properties to select:',
                            'kocuj-sitemap') . '<ul>' . '<li><em>`' . __('name',
                            'kocuj-sitemap') . '`</em>: ' . __('Authors will be sorted by name.',
                            'kocuj-sitemap') . '</li>' . '<li><em>`' . __('login',
                            'kocuj-sitemap') . '`</em>: ' . __('Authors will be sorted by login.',
                            'kocuj-sitemap') . '</li>' . '<li><em>`' . __('ID',
                            'kocuj-sitemap') . '`</em>: ' . __('Authors will be sorted by author identifier.',
                            'kocuj-sitemap') . '</li>' . '<li><em>`' . __('registered date',
                            'kocuj-sitemap') . '`</em>: ' . __('Authors will be sorted by registered date.',
                            'kocuj-sitemap') . '</li>' . '<li><em>`' . __('posts count',
                            'kocuj-sitemap') . '`</em>: ' . __('Authors will be sorted by posts count created by each author.',
                            'kocuj-sitemap') . '</li>' . '</ul>' . '</li>' . '<li><em>`' . __('Sort authors order',
                            'kocuj-sitemap') . '`</em>: ' . __('You can sort authors by ascending or descending order.',
                            'kocuj-sitemap') . '</li>' . '</ul>'
                ),
                'section_title' => array(
                    'title' => __('`Section title` tab', 'kocuj-sitemap'),
                    'content' => sprintf(__('In this tab you can set the title of the section with authors. It is used if option `divide display into sections` in the main settings of the %s plugin is checked.',
                            'kocuj-sitemap'),
                            'Kocuj Sitemap') . '</p>' . '<p>' . __('There are fields to enter the section title for each language that is available in your WordPress installation. If you have not activated any of the supported plugins for multilingualism, there will be visible only two fields to enter the title - for current WordPress language and for default section title in English language.',
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
                'authors_displaying' => array(
                    'title' => __('Displaying authors', 'kocuj-sitemap'),
                    'fields' => array(
                        array(
                            'checkbox',
                            'DisplayAuthors',
                            __('If this option is checked, there will be authors list displayed in the sitemap.',
                                'kocuj-sitemap'),
                            array(),
                            array()
                        )
                    )
                ),
                'authors_options' => array(
                    'title' => __('Options', 'kocuj-sitemap'),
                    'fields' => array(
                        array(
                            'checkbox',
                            'DisplayOnlyAuthorsWithArticles',
                            __('If this option is checked, only authors with at least one article will be displayed.',
                                'kocuj-sitemap'),
                            array(),
                            array()
                        ),
                        array(
                            'select',
                            'DisplayAuthorsSort',
                            __('You can sort authors in the sitemap using the selected authors properties.',
                                'kocuj-sitemap'),
                            array(),
                            array(
                                'options' => array(
                                    'name' => __('name', 'kocuj-sitemap'),
                                    'login' => __('login', 'kocuj-sitemap'),
                                    'id' => __('ID', 'kocuj-sitemap'),
                                    'date' => __('registered date', 'kocuj-sitemap'),
                                    'posts' => __('posts count', 'kocuj-sitemap')
                                )
                            )
                        ),
                        array(
                            'select',
                            'DisplayAuthorsOrder',
                            __('You can sort authors by ascending or descending order.', 'kocuj-sitemap'),
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
                'label' => __('Save authors list options', 'kocuj-sitemap'),
                'tooltip' => __('Save current authors list options.', 'kocuj-sitemap')
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
        return __('Authors', 'kocuj-sitemap');
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
            'user_register',
            'edit_user_profile_update',
            'deleted_user'
        );
    }
}
