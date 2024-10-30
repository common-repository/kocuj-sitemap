<?php

/**
 * q-translate-x.class.php
 *
 * @author Dominik Kocuj
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2 or later
 * @copyright Copyright (c) 2013-2020 kocuj.pl
 * @package kocuj_sitemap
 */

// set namespace
namespace KocujSitemapPlugin\Classes\MultipleLanguages;

// set namespaces aliases
use KocujSitemapPlugin\Classes\Helpers\FiltersRemover;
use KocujSitemapPlugin\Interfaces\Language;
use QTX_Translator;
use QTX_Translator_Admin;

// security
if ((!defined('ABSPATH')) || ((isset($_SERVER['SCRIPT_FILENAME'])) && (basename($_SERVER['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die();
}

/**
 * Multiple languages plugin class - qTranslate X
 *
 * @access public
 */
class QTranslateX implements Language
{

    /**
     * Identifier for set of filters from file "qtranslate_frontend.php" in qTranslate X plugin
     *
     * @access public
     */
    const FILTERS_ID_QTRANSLATE_FRONTEND = 'multiple-languages-qtranslate-x-qtranslate_frontend';

    /**
     * Identifier for set of filters from file "qtranslate_hooks.php" in qTranslate X plugin
     *
     * @access public
     */
    const FILTERS_ID_QTRANSLATE_HOOKS = 'multiple-languages-qtranslate-x-qtranslate_hooks';

    /**
     * Identifier for set of filters from file "qtranslate_utils.php" in qTranslate X plugin
     *
     * @access public
     */
    const FILTERS_ID_QTRANSLATE_UTILS = 'multiple-languages-qtranslate-x-qtranslate_utils';

    /**
     * Identifier for set of filters from file "admin/qtx_admin.php" in qTranslate X plugin
     *
     * @access public
     */
    const FILTERS_ID_ADMIN_QTX_ADMIN = 'multiple-languages-qtranslate-x-admin-qtx_admin';

    /**
     * Identifier for set of filters from file "admin/qtx_admin_class_translator.php" in qTranslate X plugin
     *
     * @access public
     */
    const FILTERS_ID_ADMIN_QTX_ADMIN_CLASS_TRANSLATOR = 'multiple-languages-qtranslate-x-admin-qtx_admin_class_translator';

    /**
     * Identifier for set of filters from file "admin/qtx_admin_utils.php" in qTranslate X plugin
     *
     * @access public
     */
    const FILTERS_ID_ADMIN_QTX_ADMIN_UTILS = 'multiple-languages-qtranslate-x-admin-qtx_admin_utils';

    /**
     * Identifier for set of filters from file "inc/qtx_class_translator.php" in qTranslate X plugin
     *
     * @access public
     */
    const FILTERS_ID_INC_QTX_CLASS_TRANSLATOR = 'multiple-languages-qtranslate-x-inc-qtx_class_translator';

    /**
     * Singleton instance
     *
     * @access private
     * @var object
     */
    private static $instance = null;

    /**
     * Languages list prepared (true) or not (false)
     *
     * @access private
     * @var array
     */
    private $languagesPrepared = false;

    /**
     * Languages list
     *
     * @access private
     * @var array
     */
    private $languages = array();

    /**
     * Locale
     *
     * @access private
     * @var string
     */
    private $locale = '';

    /**
     * Constructor
     *
     * @access private
     * @return void
     */
    private function __construct()
    {
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
            self::$instance = new self();
        }
        // exit
        return self::$instance;
    }

    /**
     * Prepare languages list if any
     *
     * @access private
     * @return void
     */
    private function prepareLanguages()
    {
        // check if languages list not prepared already
        if ($this->languagesPrepared) {
            return;
        }
        // get languages
        $langs = call_user_func('qtranxf_getSortedLanguages');
        global $q_config;
        foreach ($langs as $val) {
            if (isset($q_config['locale'][$val])) {
                $this->languages[$q_config['locale'][$val]] = $val;
            }
        }
        // set languages list as prepared
        $this->languagesPrepared = true;
    }

    /**
     * Get translated data
     *
     * @access private
     * @param string $origData
     *            Original data
     * @param string $type
     *            Data type; can be "text", "url" or "term"
     * @param string $locale
     *            Language locale
     * @param string $termType
     *            Term type; used only when $type="term"; default: empty
     * @param int $termId
     *            Term id; used only when $type="term"; default: empty
     * @return string Translated data
     */
    private function getTranslatedData($origData, $type, $locale, $termType = '', $termId = 0)
    {
        // prepare languages list
        $this->prepareLanguages();
        // initialize
        $data = $origData;
        // translate data
        if (isset($this->languages[$locale])) {
            switch ($type) {
                case 'text':
                    $data = call_user_func_array('qtranxf_use', array(
                        $this->languages[$locale],
                        $origData,
                        false
                    ));
                    break;
                case 'url':
                    $data = call_user_func_array('qtranxf_convertURL', array(
                        $origData,
                        $this->languages[$locale],
                        true
                    ));
                    break;
                case 'term':
                    if ((is_admin()) || (is_network_admin())) {
                        $data = call_user_func_array('qtranxf_get_term_joined', array(
                            get_term($termId, $termType)
                        ));
                        $origData = (isset($data->name)) ? $data->name : $origData;
                    }
                    $data = call_user_func_array('qtranxf_use', array(
                        $this->languages[$locale],
                        $origData,
                        false
                    ));
                    break;
            }
        }
        // exit
        return $data;
    }

    /**
     * Get translated text
     *
     * @access private
     * @param string $origText
     *            Original text
     * @param string $locale
     *            Language locale
     * @return string Translated text
     */
    private function getTranslatedText($origText, $locale)
    {
        // get translated text
        return $this->getTranslatedData($origText, 'text', $locale);
    }

    /**
     * Get translated URL
     *
     * @access private
     * @param string $origURL
     *            Original URL
     * @param string $locale
     *            Language locale
     * @return string Translated URL
     */
    private function getTranslatedURL($origURL, $locale)
    {
        // get translated URL
        return $this->getTranslatedData($origURL, 'url', $locale);
    }

    /**
     * Get translated term
     *
     * @access private
     * @param string $origText
     *            Original term name
     * @param string $termType
     *            Term type
     * @param int $termId
     *            Term id
     * @param string $locale
     *            Language locale
     * @return string Translated term name
     */
    private function getTranslatedTerm($origText, $termType, $termId, $locale)
    {
        // get translated URL
        return $this->getTranslatedData($origText, 'term', $locale, $termType, $termId);
    }

    /**
     * Get translation plugin name; it is used in administration panel during the selection of multi-lingual plugin
     *
     * @access public
     * @return string Plugin name
     */
    public function getName()
    {
        // get plugin name
        return 'qTranslate X';
    }

    /**
     * Get plugin priority; it is used to sort translation plugins usage order; the lowest number is the first plugin in order, the highest number is the last plugin in order
     *
     * @access public
     * @return string Plugin priority
     */
    public function getPriority()
    {
        // get plugin priority
        return 10;
    }

    /**
     * Get languages list
     *
     * @access public
     * @return array Languages list
     */
    public function getLanguages()
    {
        // prepare languages list
        $this->prepareLanguages();
        // get languages list
        return $this->languages;
    }

    /**
     * Get full URL to language flag
     *
     * @access public
     * @param string $locale
     *            Language locale
     * @return string URL to language flag
     */
    public function getLanguageFlag($locale)
    {
        // prepare languages list
        $this->prepareLanguages();
        // get locale
        if (isset($this->languages[$locale])) {
            $locale = $this->languages[$locale];
        } else {
            return '';
        }
        // declare global
        global $q_config;
        // check for locale
        if ((!isset($q_config['locale'])) || (!isset($q_config['locale'][$locale]))) {
            return '';
        }
        // get flag image path
        $output = '';
        if ((isset($locale[0]) /* strlen($locale) > 0 */) && (isset($q_config['flag'][$locale])) && (isset($q_config['flag_location']))) {
            $flagLocation = $q_config['flag_location'] . DIRECTORY_SEPARATOR . $q_config['flag'][$locale];
            if (is_file(WP_CONTENT_DIR . DIRECTORY_SEPARATOR . $flagLocation)) {
                $output = content_url() . '/' . str_replace(DIRECTORY_SEPARATOR, '/', $flagLocation);
            }
        }
        // exit
        return $output;
    }

    /**
     * Check if translation plugin exists; this method checks if all functions, classes, methods, properties and global variables, which are used from translation plugin, exists
     *
     * @access public
     * @return bool Translation plugin exists (true) or not (false)
     */
    public function checkPlugin()
    {
        // initialize
        global $q_config;
        // exit
        return ((isset($q_config)) && (is_array($q_config)) && (function_exists('qtranxf_getSortedLanguages')) && (function_exists('qtranxf_use')) && (function_exists('qtranxf_convertURL')) && (function_exists('qtranxf_use_term')) && (((is_admin()) || (is_network_admin())) && (function_exists('qtranxf_get_term_joined')) || ((!is_admin()) && (!is_network_admin()))));
    }

    /**
     * Get translation plugin filename with its directory
     *
     * @access public
     * @return string Translation plugin filename
     */
    public function getPluginFile()
    {
        // get plugin name
        return 'qtranslate-x/qtranslate.php';
    }

    /**
     * Something to do before processing
     *
     * @access public
     * @param string $locale
     *            Language locale
     * @return void
     */
    public function beforeProcess($locale)
    {
        // remember locale
        $this->locale = (isset($this->languages[$locale])) ? $this->languages[$locale] : 'en';
        // disable filters
        if ((!is_admin()) && (!is_network_admin())) {
            FiltersRemover::getInstance()->setFilters(self::FILTERS_ID_QTRANSLATE_FRONTEND,
                array(
                    'wp_translator' => array(
                        'QTX_Translator::get_translator',
                        10,
                        1
                    ),
                    'wp_get_nav_menu_items' => array(
                        'qtranxf_wp_get_nav_menu_items',
                        20,
                        3
                    ),
                    'the_posts' => array(
                        'qtranxf_postsFilter',
                        5,
                        2
                    ),
                    'wp_get_attachment_image_attributes' => array(
                        'qtranxf_get_attachment_image_attributes',
                        5,
                        3
                    ),
                    'esc_html' => array(
                        'qtranxf_esc_html',
                        0,
                        1
                    ),
                    'get_post_metadata' => array(
                        'qtranxf_filter_postmeta',
                        5,
                        4
                    ),
                    'get_user_metadata' => array(
                        'qtranxf_filter_usermeta',
                        5,
                        4
                    ),
                    'get_pagenum_link' => array(
                        'qtranxf_pagenum_link',
                        10,
                        1
                    ),
                    'wp_list_pages_excludes' => array(
                        'qtranxf_excludePages',
                        10,
                        1
                    ),
                    'posts_where_request' => array(
                        'qtranxf_excludeUntranslatedPosts',
                        10,
                        2
                    ),
                    'get_previous_post_where' => array(
                        'qtranxf_excludeUntranslatedAdjacentPosts',
                        10,
                        1
                    ),
                    'get_next_post_where' => array(
                        'qtranxf_excludeUntranslatedAdjacentPosts',
                        10,
                        1
                    ),
                    'bloginfo_url' => array(
                        'qtranxf_convertBlogInfoURL',
                        10,
                        2
                    ),
                    'home_url' => array(
                        'qtranxf_home_url',
                        0,
                        4
                    ),
                    'gettext' => array(
                        'qtranxf_gettext',
                        0,
                        1
                    ),
                    'gettext_with_context' => array(
                        'qtranxf_gettext_with_context',
                        0,
                        1
                    ),
                    'ngettext' => array(
                        'qtranxf_ngettext',
                        0,
                        1
                    )
                ));
        }
        FiltersRemover::getInstance()->setFilters(self::FILTERS_ID_QTRANSLATE_HOOKS,
            array(
                'wp_trim_words' => array(
                    'qtranxf_trim_words',
                    0,
                    4
                ),
                'sanitize_title' => array(
                    'qtranxf_useRawTitle',
                    0,
                    3
                ),
                'the_content' => array(
                    'qtranxf_useCurrentLanguageIfNotFoundShowAvailable',
                    100,
                    1
                ),
                'the_excerpt' => array(
                    'qtranxf_useCurrentLanguageIfNotFoundShowAvailable',
                    0,
                    1
                ),
                'get_post_modified_time' => array(
                    'qtranxf_timeModifiedFromPostForCurrentLanguage',
                    0,
                    3
                ),
                'get_the_time' => array(
                    'qtranxf_timeFromPostForCurrentLanguage',
                    0,
                    3
                ),
                'get_the_date' => array(
                    'qtranxf_dateFromPostForCurrentLanguage',
                    0,
                    3
                ),
                'get_the_modified_date' => array(
                    'qtranxf_dateModifiedFromPostForCurrentLanguage',
                    0,
                    2
                ),
                'locale' => array(
                    'qtranxf_localeForCurrentLanguage',
                    99,
                    1
                ),
                'post_title' => array(
                    'qtranxf_useCurrentLanguageIfNotFoundUseDefaultLanguage',
                    0,
                    1
                ),
                'tag_rows' => array(
                    'qtranxf_useCurrentLanguageIfNotFoundUseDefaultLanguage',
                    0,
                    1
                ),
                'wp_list_categories' => array(
                    'qtranxf_useCurrentLanguageIfNotFoundUseDefaultLanguage',
                    0,
                    1
                ),
                'wp_title' => array(
                    'qtranxf_useCurrentLanguageIfNotFoundUseDefaultLanguage',
                    0,
                    1
                ),
                'single_post_title' => array(
                    'qtranxf_useCurrentLanguageIfNotFoundUseDefaultLanguage',
                    0,
                    1
                ),
                'bloginfo' => array(
                    'qtranxf_useCurrentLanguageIfNotFoundUseDefaultLanguage',
                    0,
                    1
                ),
                'get_others_drafts' => array(
                    'qtranxf_useCurrentLanguageIfNotFoundUseDefaultLanguage',
                    0,
                    1
                ),
                'get_pages' => array(
                    'qtranxf_useCurrentLanguageIfNotFoundUseDefaultLanguage',
                    0,
                    1
                ),
                'term_links-post_tag' => array(
                    'qtranxf_useCurrentLanguageIfNotFoundUseDefaultLanguage',
                    0,
                    1
                ),
                'link_name' => array(
                    'qtranxf_useCurrentLanguageIfNotFoundUseDefaultLanguage',
                    0,
                    1
                ),
                'link_description' => array(
                    'qtranxf_useCurrentLanguageIfNotFoundUseDefaultLanguage',
                    0,
                    1
                ),
                'the_author' => array(
                    'qtranxf_useCurrentLanguageIfNotFoundUseDefaultLanguage',
                    0,
                    1
                ),
                '_wp_post_revision_field_post_title' => array(
                    'qtranxf_showAllSeparated',
                    0,
                    1
                ),
                '_wp_post_revision_field_post_content' => array(
                    'qtranxf_showAllSeparated',
                    0,
                    1
                ),
                '_wp_post_revision_field_post_excerpt' => array(
                    'qtranxf_showAllSeparated',
                    0,
                    1
                )
            ));
        FiltersRemover::getInstance()->setFilters(self::FILTERS_ID_QTRANSLATE_UTILS,
            array(
                'the_title' => array(
                    'qtranxf_useCurrentLanguageIfNotFoundUseDefaultLanguage',
                    20,
                    1
                ),
                'category_description' => array(
                    'qtranxf_useCurrentLanguageIfNotFoundUseDefaultLanguage',
                    20,
                    1
                ),
                'list_cats' => array(
                    'qtranxf_useCurrentLanguageIfNotFoundUseDefaultLanguage',
                    20,
                    1
                ),
                'wp_dropdown_cats' => array(
                    'qtranxf_useCurrentLanguageIfNotFoundUseDefaultLanguage',
                    20,
                    1
                ),
                'term_name' => array(
                    'qtranxf_useCurrentLanguageIfNotFoundUseDefaultLanguage',
                    20,
                    1
                ),
                'the_author' => array(
                    'qtranxf_useCurrentLanguageIfNotFoundUseDefaultLanguage',
                    20,
                    1
                ),
                'tml_title' => array(
                    'qtranxf_useCurrentLanguageIfNotFoundUseDefaultLanguage',
                    20,
                    1
                ),
                'term_description' => array(
                    'qtranxf_useCurrentLanguageIfNotFoundUseDefaultLanguage',
                    20,
                    1
                ),
                'author_link' => array(
                    'qtranxf_convertURL',
                    10,
                    1
                ),
                'day_link' => array(
                    'qtranxf_convertURL',
                    10,
                    1
                ),
                'month_link' => array(
                    'qtranxf_convertURL',
                    10,
                    1
                ),
                'year_link' => array(
                    'qtranxf_convertURL',
                    10,
                    1
                ),
                'page_link' => array(
                    'qtranxf_convertURL',
                    10,
                    1
                ),
                'post_link' => array(
                    'qtranxf_convertURL',
                    10,
                    1
                ),
                'category_link' => array(
                    'qtranxf_convertURL',
                    10,
                    1
                ),
                'tag_link' => array(
                    'qtranxf_convertURL',
                    10,
                    1
                ),
                'term_link' => array(
                    'qtranxf_convertURL',
                    10,
                    1
                ),
                'the_permalink' => array(
                    'qtranxf_convertURL',
                    10,
                    1
                ),
                'cat_row' => array(
                    'qtranxf_useTermLib',
                    0,
                    1
                ),
                'cat_rows' => array(
                    'qtranxf_useTermLib',
                    0,
                    1
                ),
                'wp_get_object_terms' => array(
                    'qtranxf_useTermLib',
                    0,
                    1
                ),
                'single_cat_title' => array(
                    'qtranxf_useTermLib',
                    0,
                    1
                ),
                'single_tag_title' => array(
                    'qtranxf_useTermLib',
                    0,
                    1
                ),
                'single_term_title' => array(
                    'qtranxf_useTermLib',
                    0,
                    1
                ),
                'the_category' => array(
                    'qtranxf_useTermLib',
                    0,
                    1
                ),
                'get_term' => array(
                    'qtranxf_useTermLib',
                    0,
                    1
                ),
                'get_terms' => array(
                    'qtranxf_useTermLib',
                    0,
                    1
                ),
                'get_category' => array(
                    'qtranxf_useTermLib',
                    0,
                    1
                )
            ));
        if ((is_admin()) || (is_network_admin())) {
            FiltersRemover::getInstance()->setFilters(self::FILTERS_ID_ADMIN_QTX_ADMIN,
                array(
                    'qtranslate_init_language' => array(
                        'qtranxf_load_admin_page_config',
                        20,
                        1
                    ),
                    'customize_allowed_urls' => array(
                        'qtranxf_customize_allowed_urls',
                        10,
                        1
                    ),
                    'get_terms_args' => array(
                        'qtranxf_get_terms_args',
                        5,
                        2
                    ),
                    'home_url' => array(
                        'qtranxf_admin_home_url',
                        5,
                        4
                    )
                ));
            FiltersRemover::getInstance()->setFilters(self::FILTERS_ID_ADMIN_QTX_ADMIN_CLASS_TRANSLATOR,
                array(
                    'multilingual_term' => array(
                        ((class_exists('QTX_Translator_Admin')) && (method_exists('QTX_Translator_Admin',
                                'get_translator'))) ? array(
                            QTX_Translator_Admin::get_translator(),
                            'multilingual_term'
                        ) : 'qtranxf_dummy',
                        10,
                        3
                    ),
                    'wp_translator' => array(
                        'QTX_Translator_Admin::get_translator',
                        10,
                        1
                    )
                ));
            FiltersRemover::getInstance()->setFilters(self::FILTERS_ID_ADMIN_QTX_ADMIN_UTILS,
                array(
                    'get_term' => array(
                        'qtranxf_useAdminTermLibJoin',
                        5,
                        2
                    ),
                    'get_terms' => array(
                        'qtranxf_useAdminTermLibJoin',
                        5,
                        3
                    ),
                    'term_name' => array(
                        'qtranxf_admin_term_name',
                        5,
                        4
                    ),
                    'list_cats' => array(
                        'qtranxf_admin_list_cats',
                        0,
                        1
                    ),
                    'wp_dropdown_cats' => array(
                        'qtranxf_admin_dropdown_cats',
                        0,
                        1
                    ),
                    'category_description' => array(
                        'qtranxf_admin_category_description',
                        0,
                        1
                    ),
                    'the_title' => array(
                        'qtranxf_admin_the_title',
                        0,
                        1
                    ),
                    'gettext' => array(
                        'qtranxf_gettext',
                        0,
                        1
                    ),
                    'gettext_with_context' => array(
                        'qtranxf_gettext_with_context',
                        0,
                        1
                    ),
                    'ngettext' => array(
                        'qtranxf_ngettext',
                        0,
                        1
                    )
                ));
        }
        FiltersRemover::getInstance()->setFilters(self::FILTERS_ID_INC_QTX_CLASS_TRANSLATOR,
            array(
                'translate_text' => array(
                    ((class_exists('QTX_Translator')) && (method_exists('QTX_Translator', 'get_translator'))) ? array(
                        QTX_Translator::get_translator(),
                        'translate_text'
                    ) : 'qtranxf_dummy',
                    10,
                    3
                ),
                'translate_term' => array(
                    ((class_exists('QTX_Translator')) && (method_exists('QTX_Translator', 'get_translator'))) ? array(
                        QTX_Translator::get_translator(),
                        'translate_term'
                    ) : 'qtranxf_dummy',
                    10,
                    3
                ),
                'translate_url' => array(
                    ((class_exists('QTX_Translator')) && (method_exists('QTX_Translator', 'get_translator'))) ? array(
                        QTX_Translator::get_translator(),
                        'translate_url'
                    ) : 'qtranxf_dummy',
                    10,
                    2
                )
            ));
        if ((!is_admin()) && (!is_network_admin())) {
            FiltersRemover::getInstance()->disableFilters(self::FILTERS_ID_QTRANSLATE_FRONTEND);
        }
        FiltersRemover::getInstance()->disableFilters(self::FILTERS_ID_QTRANSLATE_HOOKS);
        FiltersRemover::getInstance()->disableFilters(self::FILTERS_ID_QTRANSLATE_UTILS);
        if ((is_admin()) || (is_network_admin())) {
            FiltersRemover::getInstance()->disableFilters(self::FILTERS_ID_ADMIN_QTX_ADMIN);
            FiltersRemover::getInstance()->disableFilters(self::FILTERS_ID_ADMIN_QTX_ADMIN_CLASS_TRANSLATOR);
            FiltersRemover::getInstance()->disableFilters(self::FILTERS_ID_ADMIN_QTX_ADMIN_UTILS);
        }
        FiltersRemover::getInstance()->disableFilters(self::FILTERS_ID_INC_QTX_CLASS_TRANSLATOR);
        // add terms filter
        if ((!is_admin()) && (!is_network_admin())) {
            add_filter('get_terms', array(
                $this,
                'filterGetTerms'
            ), 1);
        }
    }

    /**
     * Something to do after processing
     *
     * @access public
     * @param string $locale
     *            Language locale
     * @return void
     */
    public function afterProcess($locale)
    {
        // enable filters
        if ((!is_admin()) && (!is_network_admin())) {
            FiltersRemover::getInstance()->enableFilters(self::FILTERS_ID_QTRANSLATE_FRONTEND);
        }
        FiltersRemover::getInstance()->enableFilters(self::FILTERS_ID_QTRANSLATE_HOOKS);
        FiltersRemover::getInstance()->enableFilters(self::FILTERS_ID_QTRANSLATE_UTILS);
        if ((is_admin()) || (is_network_admin())) {
            FiltersRemover::getInstance()->enableFilters(self::FILTERS_ID_ADMIN_QTX_ADMIN);
            FiltersRemover::getInstance()->enableFilters(self::FILTERS_ID_ADMIN_QTX_ADMIN_CLASS_TRANSLATOR);
            FiltersRemover::getInstance()->enableFilters(self::FILTERS_ID_ADMIN_QTX_ADMIN_UTILS);
        }
        FiltersRemover::getInstance()->enableFilters(self::FILTERS_ID_INC_QTX_CLASS_TRANSLATOR);
        // remove terms filter
        if ((!is_admin()) && (!is_network_admin())) {
            remove_filter('get_terms', array(
                $this,
                'filterGetTerms'
            ), 1);
        }
    }

    /**
     * Filter for get terms
     *
     * @access public
     * @param array $terms
     *            Terms
     * @return array Modified terms
     */
    public function filterGetTerms($terms)
    {
        // translate terms
        foreach ($terms as $id => $term) {
            $terms[$id] = qtranxf_use_term($this->locale, $term, null);
        }
        // exit
        return $terms;
    }

    /**
     * Something to do before get blog name
     *
     * @access public
     * @return void
     */
    public function beforeGetBlogName()
    {
    }

    /**
     * Something to do after get blog name
     *
     * @access public
     * @return void
     */
    public function afterGetBlogName()
    {
    }

    /**
     * Get translated blog name with locale
     *
     * @access public
     * @param string $origText
     *            Original text
     * @param string $locale
     *            Language locale
     * @return string Translated text
     */
    public function getTranslatedBlogName($origText, $locale)
    {
        // get translated blog name
        return $this->getTranslatedText($origText, $locale);
    }

    /**
     * Something to do before get home URL
     *
     * @access public
     * @param string $locale
     *            Language locale
     * @return void
     */
    public function beforeGetHomeURL($locale)
    {
    }

    /**
     * Something to do after get home URL
     *
     * @access public
     * @param string $locale
     *            Language locale
     * @return void
     */
    public function afterGetHomeURL($locale)
    {
    }

    /**
     * Get translated home URL
     *
     * @access public
     * @param string $origURL
     *            Original home URL
     * @param string $locale
     *            Language locale
     * @return string Translated home URL
     */
    public function getTranslatedHomeURL($origURL, $locale)
    {
        // get translated home URL
        return $this->getTranslatedURL($origURL, $locale);
    }

    /**
     * Something to do before get pages
     *
     * @access public
     * @param string $locale
     *            Language locale
     * @return void
     */
    public function beforeGetPages($locale)
    {
    }

    /**
     * Something to do after get pages
     *
     * @access public
     * @param string $locale
     *            Language locale
     * @return void
     */
    public function afterGetPages($locale)
    {
    }

    /**
     * Something to do before get page item
     *
     * @access public
     * @param int $pageId
     *            Page id
     * @param string $locale
     *            Language locale
     * @return void
     */
    public function beforeGetPageItem($pageId, $locale)
    {
    }

    /**
     * Something to do after get page item
     *
     * @access public
     * @param int $pageId
     *            Page id
     * @param string $locale
     *            Language locale
     * @return void
     */
    public function afterGetPageItem($pageId, $locale)
    {
    }

    /**
     * Get translated page URL
     *
     * @access public
     * @param string $origURL
     *            Original page URL
     * @param int $pageId
     *            Page id
     * @param string $locale
     *            Language locale
     * @return string Translated page URL
     */
    public function getTranslatedPageURL($origURL, $pageId, $locale)
    {
        // get translated page URL
        return $this->getTranslatedURL($origURL, $locale);
    }

    /**
     * Get translated page title
     *
     * @access public
     * @param string $origTitle
     *            Original page title
     * @param int $pageId
     *            Page id
     * @param string $locale
     *            Language locale
     * @return string Translated page title
     */
    public function getTranslatedPageTitle($origTitle, $pageId, $locale)
    {
        // get translated page title
        return $this->getTranslatedText($origTitle, $locale);
    }

    /**
     * Something to do before get posts
     *
     * @access public
     * @param string $locale
     *            Language locale
     * @return void
     */
    public function beforeGetPosts($locale)
    {
    }

    /**
     * Something to do after get posts
     *
     * @access public
     * @param string $locale
     *            Language locale
     * @return void
     */
    public function afterGetPosts($locale)
    {
    }

    /**
     * Something to do before get post item
     *
     * @access public
     * @param int $postId
     *            Post id
     * @param string $locale
     *            Language locale
     * @return void
     */
    public function beforeGetPostItem($postId, $locale)
    {
    }

    /**
     * Something to do after get post item
     *
     * @access public
     * @param int $postId
     *            Post id
     * @param string $locale
     *            Language locale
     * @return void
     */
    public function afterGetPostItem($postId, $locale)
    {
    }

    /**
     * Get translated post URL
     *
     * @access public
     * @param string $origURL
     *            Original post URL
     * @param int $postId
     *            Post id
     * @param string $locale
     *            Language locale
     * @return string Translated post URL
     */
    public function getTranslatedPostURL($origURL, $postId, $locale)
    {
        // get translated post URL
        return $this->getTranslatedURL($origURL, $locale);
    }

    /**
     * Get translated post title
     *
     * @access public
     * @param string $origTitle
     *            Original post title
     * @param int $postId
     *            Post id
     * @param string $locale
     *            Language locale
     * @return string Translated post title
     */
    public function getTranslatedPostTitle($origTitle, $postId, $locale)
    {
        // get translated post title
        return $this->getTranslatedText($origTitle, $locale);
    }

    /**
     * Something to do before get categories
     *
     * @access public
     * @param string $locale
     *            Language locale
     * @return void
     */
    public function beforeGetCategories($locale)
    {
    }

    /**
     * Something to do after get categories
     *
     * @access public
     * @param string $locale
     *            Language locale
     * @return void
     */
    public function afterGetCategories($locale)
    {
    }

    /**
     * Something to do before get category item
     *
     * @access public
     * @param int $categoryId
     *            Category id
     * @param string $locale
     *            Language locale
     * @return void
     */
    public function beforeGetCategoryItem($categoryId, $locale)
    {
    }

    /**
     * Something to do after get category item
     *
     * @access public
     * @param int $categoryId
     *            Category id
     * @param string $locale
     *            Language locale
     * @return void
     */
    public function afterGetCategoryItem($categoryId, $locale)
    {
    }

    /**
     * Get translated category URL
     *
     * @access public
     * @param string $origURL
     *            Original category URL
     * @param int $categoryId
     *            Category id
     * @param string $locale
     *            Language locale
     * @return string Translated category URL
     */
    public function getTranslatedCategoryURL($origURL, $categoryId, $locale)
    {
        // get translated category URL
        return $this->getTranslatedURL($origURL, $locale);
    }

    /**
     * Get translated category title
     *
     * @access public
     * @param string $origTitle
     *            Original category title
     * @param int $categoryId
     *            Category id
     * @param string $locale
     *            Language locale
     * @return string Translated category title
     */
    public function getTranslatedCategoryTitle($origTitle, $categoryId, $locale)
    {
        // get translated category title
        return $this->getTranslatedTerm($origTitle, 'category', $categoryId, $locale);
    }

    /**
     * Something to do before get taxonomies
     *
     * @access public
     * @param string $taxonomy
     *            Taxonomy type
     * @param string $locale
     *            Language locale
     * @return void
     */
    public function beforeGetTaxonomies($taxonomy, $locale)
    {
    }

    /**
     * Something to do after get taxonomies
     *
     * @access public
     * @param string $taxonomy
     *            Taxonomy type
     * @param string $locale
     *            Language locale
     * @return void
     */
    public function afterGetTaxonomies($taxonomy, $locale)
    {
    }

    /**
     * Something to do before get taxonomy item
     *
     * @access public
     * @param int $termId
     *            Term id
     * @param string $taxonomy
     *            Taxonomy type
     * @param string $locale
     *            Language locale
     * @return void
     */
    public function beforeGetTaxonomyItem($termId, $taxonomy, $locale)
    {
    }

    /**
     * Something to do after get taxonomy item
     *
     * @access public
     * @param int $termId
     *            Taxonomy id
     * @param string $taxonomy
     *            Taxonomy type
     * @param string $locale
     *            Language locale
     * @return void
     */
    public function afterGetTaxonomyItem($termId, $taxonomy, $locale)
    {
    }

    /**
     * Get translated taxonomy URL
     *
     * @access public
     * @param string $origURL
     *            Original taxonomy URL
     * @param int $termId
     *            Taxonomy id
     * @param string $taxonomy
     *            Taxonomy type
     * @param string $locale
     *            Language locale
     * @return string Translated taxonomy URL
     */
    public function getTranslatedTaxonomyURL($origURL, $termId, $taxonomy, $locale)
    {
        // get translated taxonomy URL
        return $this->getTranslatedURL($origURL, $locale);
    }

    /**
     * Get translated taxonomy title
     *
     * @access public
     * @param string $origTitle
     *            Original taxonomy title
     * @param int $termId
     *            Taxonomy id
     * @param string $taxonomy
     *            Taxonomy type
     * @param string $locale
     *            Language locale
     * @return string Translated taxonomy title
     */
    public function getTranslatedTaxonomyTitle($origTitle, $termId, $taxonomy, $locale)
    {
        // get translated taxonomy title
        return $this->getTranslatedTerm($origTitle, $taxonomy, $termId, $locale);
    }

    /**
     * Something to do before get menu
     *
     * @access public
     * @param string $locale
     *            Language locale
     * @return void
     */
    public function beforeGetMenu($locale)
    {
    }

    /**
     * Something to do after get menu
     *
     * @access public
     * @param string $locale
     *            Language locale
     * @return void
     */
    public function afterGetMenu($locale)
    {
    }

    /**
     * Something to do before get menu item
     *
     * @access public
     * @param int $itemId
     *            Menu item id
     * @param string $locale
     *            Language locale
     * @return void
     */
    public function beforeGetMenuItem($itemId, $locale)
    {
    }

    /**
     * Something to do after get menu item
     *
     * @access public
     * @param int $itemId
     *            Menu item id
     * @param string $locale
     *            Language locale
     * @return void
     */
    public function afterGetMenuItem($itemId, $locale)
    {
    }

    /**
     * Get translated menu URL
     *
     * @access public
     * @param string $origURL
     *            Original menu URL
     * @param int $menuId
     *            Menu id
     * @param string $locale
     *            Language locale
     * @return string Translated menu URL
     */
    public function getTranslatedMenuURL($origURL, $menuId, $locale)
    {
        // get translated menu URL
        return $this->getTranslatedURL($origURL, $locale);
    }

    /**
     * Get translated menu title
     *
     * @access public
     * @param string $origTitle
     *            Original menu title
     * @param int $menuId
     *            Menu id
     * @param string $locale
     *            Language locale
     * @return string Translated menu title
     */
    public function getTranslatedMenuTitle($origTitle, $menuId, $locale)
    {
        // get translated menu title
        return $this->getTranslatedText($origTitle, $locale);
    }

    /**
     * Something to do before get authors
     *
     * @access public
     * @param string $locale
     *            Language locale
     * @return void
     */
    public function beforeGetAuthors($locale)
    {
    }

    /**
     * Something to do after get authors
     *
     * @access public
     * @param string $locale
     *            Language locale
     * @return void
     */
    public function afterGetAuthors($locale)
    {
    }

    /**
     * Something to do before get author item
     *
     * @access public
     * @param int $authorId
     *            Author id
     * @param string $locale
     *            Language locale
     * @return void
     */
    public function beforeGetAuthorItem($authorId, $locale)
    {
    }

    /**
     * Something to do after get author item
     *
     * @access public
     * @param int $authorId
     *            Author id
     * @param string $locale
     *            Language locale
     * @return void
     */
    public function afterGetAuthorItem($authorId, $locale)
    {
    }

    /**
     * Get translated author URL
     *
     * @access public
     * @param string $origURL
     *            Original author URL
     * @param int $authorId
     *            Author id
     * @param string $locale
     *            Language locale
     * @return string Translated author URL
     */
    public function getTranslatedAuthorURL($origURL, $authorId, $locale)
    {
        // get translated author URL
        return $this->getTranslatedURL($origURL, $locale);
    }

    /**
     * Get translated author name
     *
     * @access public
     * @param string $origName
     *            Original author name
     * @param int $authorId
     *            Author id
     * @param string $locale
     *            Language locale
     * @return string Translated author name
     */
    public function getTranslatedAuthorName($origName, $authorId, $locale)
    {
        // get translated author name
        return $this->getTranslatedText($origName, $locale);
    }

    /**
     * Something to do before get tags
     *
     * @access public
     * @param string $locale
     *            Language locale
     * @return void
     */
    public function beforeGetTags($locale)
    {
    }

    /**
     * Something to do after get tags
     *
     * @access public
     * @param string $locale
     *            Language locale
     * @return void
     */
    public function afterGetTags($locale)
    {
    }

    /**
     * Something to do before get tag item
     *
     * @access public
     * @param int $tagId
     *            Tag id
     * @param string $locale
     *            Language locale
     * @return void
     */
    public function beforeGetTagItem($tagId, $locale)
    {
    }

    /**
     * Something to do after get tag item
     *
     * @access public
     * @param int $tagId
     *            Tag id
     * @param string $locale
     *            Language locale
     * @return void
     */
    public function afterGetTagItem($tagId, $locale)
    {
    }

    /**
     * Get translated tag URL
     *
     * @access public
     * @param string $origURL
     *            Original tag URL
     * @param int $tagId
     *            Tag id
     * @param string $locale
     *            Language locale
     * @return string Translated tag URL
     */
    public function getTranslatedTagURL($origURL, $tagId, $locale)
    {
        // get translated tag URL
        return $this->getTranslatedURL($origURL, $locale);
    }

    /**
     * Get translated tag name
     *
     * @access public
     * @param string $origName
     *            Original tag name
     * @param int $tagId
     *            Tag id
     * @param string $locale
     *            Language locale
     * @return string Translated tag name
     */
    public function getTranslatedTagName($origName, $tagId, $locale)
    {
        // get translated tag name
        return $this->getTranslatedTerm($origName, 'post_tag', $tagId, $locale);
    }

    /**
     * Something to do before get custom posts
     *
     * @access public
     * @param string $locale
     *            Language locale
     * @return void
     */
    public function beforeGetCustomPosts($locale)
    {
    }

    /**
     * Something to do after get custom posts
     *
     * @access public
     * @param string $locale
     *            Language locale
     * @return void
     */
    public function afterGetCustomPosts($locale)
    {
    }

    /**
     * Something to do before get custom post item
     *
     * @access public
     * @param int $customPostId
     *            Custom post id
     * @param string $locale
     *            Language locale
     * @return void
     */
    public function beforeGetCustomPostItem($customPostId, $locale)
    {
    }

    /**
     * Something to do after get custom post item
     *
     * @access public
     * @param int $customPostId
     *            Custom post id
     * @param string $locale
     *            Language locale
     * @return void
     */
    public function afterGetCustomPostItem($customPostId, $locale)
    {
    }

    /**
     * Get translated custom post URL
     *
     * @access public
     * @param string $origURL
     *            Original custom post URL
     * @param int $customPostId
     *            Custom post id
     * @param string $locale
     *            Language locale
     * @return string Translated custom post URL
     */
    public function getTranslatedCustomPostURL($origURL, $customPostId, $locale)
    {
        // get translated custom post URL
        return $this->getTranslatedURL($origURL, $locale);
    }

    /**
     * Get translated custom post title
     *
     * @access public
     * @param string $origTitle
     *            Original custom post title
     * @param int $customPostId
     *            Custom post id
     * @param string $locale
     *            Language locale
     * @return string Translated custom post title
     */
    public function getTranslatedCustomPostTitle($origTitle, $customPostId, $locale)
    {
        // get translated custom post title
        return $this->getTranslatedText($origTitle, $locale);
    }
}
