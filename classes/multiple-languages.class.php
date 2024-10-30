<?php

/**
 * multiple-languages.class.php
 *
 * @author Dominik Kocuj
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2 or later
 * @copyright Copyright (c) 2013-2020 kocuj.pl
 * @package kocuj_sitemap
 */

// set namespace
namespace KocujSitemapPlugin\Classes;

// security
if ((!defined('ABSPATH')) || ((isset($_SERVER['SCRIPT_FILENAME'])) && (basename($_SERVER['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die();
}

/**
 * Multiple languages plugin class
 *
 * @access public
 */
class MultipleLanguages
{

    /**
     * Singleton instance
     *
     * @access private
     * @var object
     */
    private static $instance = null;

    /**
     * Languages cache
     *
     * @access private
     * @var array
     */
    private $languagesCache = array();

    /**
     * Selected class
     *
     * @access private
     * @var string
     */
    private $selectedClass = '';

    /**
     * Class has been already selected (true) or not (false)
     *
     * @access private
     * @var bool
     */
    private $isSelectedClass = false;

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
     * Get selectedy class name
     *
     * @access private
     * @param string $forceMultilangOptionValue
     *            Force 'Multilang' option value if string is not empty - default: empty
     * @return string Selected class name
     */
    private function getSelectedClass($forceMultilangOptionValue = '')
    {
        // check if class has not been already selected
        if ((!isset($forceMultilangOptionValue[0]) /* strlen($forceMultilangOptionValue) === 0 */) && ($this->isSelectedClass)) {
            return $this->selectedClass;
        }
        // initialize
        $selectedClass = '';
        // get data
        $data = MultipleLanguagesData::getInstance()->getData();
        // select translation plugin
        $value = !isset($forceMultilangOptionValue[0]) /* strlen($forceMultilangOptionValue) === 0 */ ?
            Options::getInstance()->getOption('Multilang') : $forceMultilangOptionValue;
        if ((isset($value[0]) /* strlen($value) > 0 */) && ($value !== '0') && ($value !== '//donotuse//')) {
            $value = str_replace('/', '\\', $value);
            if (isset($data[$value])) {
                $selectedClass = $value;
            }
        } else {
            // search for the lowest priority
            if (($value !== '//donotuse//') && (!empty($data))) {
                $prior = -1;
                $pos = '';
                foreach ($data as $key => $val) {
                    if (($prior === -1) || ($val['prior'] < $prior)) {
                        $prior = $val['prior'];
                        $pos = $key;
                    }
                }
                $selectedClass = $pos;
            }
        }
        // set that class has been already selected
        if (!isset($forceMultilangOptionValue[0]) /* strlen($forceMultilangOptionValue) === 0 */) {
            $this->isSelectedClass = true;
            $this->selectedClass = $selectedClass;
        }
        // exit
        return $selectedClass;
    }

    /**
     * Process callback function for the selected translation plugin
     *
     * @access private
     * @param string $callback
     *            Callback function
     * @param array $parameters
     *            Callback parameters
     * @return any Returned callback data
     */
    private function processCallback($callback, array $parameters = array())
    {
        // get data
        $data = MultipleLanguagesData::getInstance()->getData();
        // execute callback
        $selectedClass = $this->getSelectedClass();
        if ((isset($selectedClass[0]) /* strlen($selectedClass) > 0 */) && (method_exists($data[$selectedClass]['instance'],
                $callback))) {
            if (method_exists($data[$selectedClass]['instance'], $callback)) {
                return call_user_func_array(array(
                    $data[$selectedClass]['instance'],
                    $callback
                ), $parameters);
            }
        }
        // optionally return first parameter
        if (isset($parameters[0])) {
            return $parameters[0];
        }
        // exit
        return null;
    }

    /**
     * Get selected translation plugin name
     *
     * @access public
     * @return string Selected translation plugin name or empty string if no plugin has been selected
     */
    public function getSelectedPluginName()
    {
        // get data
        $data = MultipleLanguagesData::getInstance()->getData();
        // get selected class
        $selectedClass = $this->getSelectedClass();
        if ((isset($selectedClass[0]) /* strlen($selectedClass) > 0 */) && (isset($data[$selectedClass]['admin_name']))) {
            return $data[$selectedClass]['admin_name'];
        }
        // exit
        return '';
    }

    /**
     * Get all languages list
     *
     * @access public
     * @param string $forceMultilangOptionValue
     *            Force 'Multilang' option value if string is not empty - default: empty
     * @return array Languages list
     */
    public function getLanguages($forceMultilangOptionValue = '')
    {
        // check if it is not in cache
        if ((!isset($forceMultilangOptionValue[0]) /* strlen($forceMultilangOptionValue) === 0 */) && (!empty($this->languagesCache))) {
            return $this->languagesCache;
        }
        // initialize
        $languages = array();
        // get languages
        $selectedClass = $this->getSelectedClass($forceMultilangOptionValue);
        if (isset($selectedClass[0]) /* strlen($selectedClass) > 0 */) {
            $data = MultipleLanguagesData::getInstance()->getData();
            $languages = array_keys($data[$selectedClass]['instance']->getLanguages());
        }
        // optionally set current language
        if (empty($languages)) {
            $languages[] = get_locale();
        }
        // set languages cache
        if (!isset($forceMultilangOptionValue[0]) /* strlen($forceMultilangOptionValue) === 0 */) {
            $this->languagesCache = $languages;
        }
        // exit
        return $languages;
    }

    /**
     * Get path to language flag
     *
     * @access public
     * @param string $locale
     *            Language locale
     * @return string Path to language flag
     */
    public function getLanguageFlag($locale)
    {
        // get languages
        $languages = $this->getLanguages();
        // get flag
        $selectedClass = $this->getSelectedClass();
        if ((isset($selectedClass[0]) /* strlen($selectedClass) > 0 */) && (in_array($locale, $languages))) {
            $data = MultipleLanguagesData::getInstance()->getData();
            return $data[$selectedClass]['instance']->getLanguageFlag($locale);
        }
        // exit
        return '';
    }

    /**
     * Something to do before or after some processing
     *
     * @access public
     * @param string $type
     *            Type of something to do
     * @param string $locale
     *            Language locale
     * @param int $itemId
     *            Item identifier - default: empty
     * @param string $additional
     *            Additional string - default: empty
     * @return void
     */
    public function doSomething($type, $locale, $itemId = 0, $additional = '')
    {
        // initialize
        $emptyCallbacks = array(
            'beforegetblogname' => 'beforeGetBlogName',
            'aftergetblogname' => 'afterGetBlogName'
        );
        $standardCallbacks = array(
            'beforeprocess' => 'beforeProcess',
            'afterprocess' => 'afterProcess',
            'beforegethomeurl' => 'beforeGetHomeURL',
            'aftergethomeurl' => 'afterGetHomeURL',
            'beforegetpages' => 'beforeGetPages',
            'aftergetpages' => 'afterGetPages',
            'beforegetposts' => 'beforeGetPosts',
            'aftergetposts' => 'afterGetPosts',
            'beforegetcategories' => 'beforeGetCategories',
            'aftergetcategories' => 'afterGetCategories',
            'beforegetmenu' => 'beforeGetMenu',
            'aftergetmenu' => 'afterGetMenu',
            'beforegetauthors' => 'beforeGetAuthors',
            'aftergetauthors' => 'afterGetAuthors',
            'beforegettags' => 'beforeGetTags',
            'aftergettags' => 'afterGetTags',
            'beforegetcustomposts' => 'beforeGetCustomPosts',
            'aftergetcustomposts' => 'afterGetCustomPosts'
        );
        $itemCallbacks = array(
            'beforegetpageitem' => 'beforeGetPageItem',
            'aftergetpageitem' => 'afterGetPageItem',
            'beforegetpostitem' => 'beforeGetPostItem',
            'aftergetpostitem' => 'afterGetPostItem',
            'beforegetcategoryitem' => 'beforeGetCategoryItem',
            'aftergetcategoryitem' => 'afterGetCategoryItem',
            'beforegettaxonomies' => 'beforeGetTaxonomies',
            'aftergettaxonomies' => 'afterGetTaxonomies',
            'beforegetmenuitem' => 'beforeGetMenuItem',
            'aftergetmenuitem' => 'afterGetMenuItem',
            'beforegetauthoritem' => 'beforeGetAuthorItem',
            'aftergetauthoritem' => 'afterGetAuthorItem',
            'beforegettagitem' => 'beforeGetTagItem',
            'aftergettagitem' => 'afterGetTagItem',
            'beforegetcustompostitem' => 'beforeGetCustomPostItem',
            'aftergetcustompostitem' => 'afterGetCustomPostItem'
        );
        $itemAdditionalCallbacks = array(
            'beforegettaxonomyitem' => 'beforeGetTaxonomyItem',
            'aftergettaxonomyitem' => 'afterGetTaxonomyItem'
        );
        // do something
        if (isset($emptyCallbacks[$type])) {
            $this->processCallback($emptyCallbacks[$type]);
        } else {
            if (isset($standardCallbacks[$type])) {
                $this->processCallback($standardCallbacks[$type], array(
                    $locale
                ));
            } else {
                if (isset($itemCallbacks[$type])) {
                    $this->processCallback($itemCallbacks[$type], array(
                        $itemId,
                        $locale
                    ));
                } else {
                    if (isset($itemAdditionalCallbacks[$type])) {
                        $this->processCallback($itemAdditionalCallbacks[$type], array(
                            $itemId,
                            $additional,
                            $locale
                        ));
                    }
                }
            }
        }
    }

    /**
     * Get item
     *
     * @access public
     * @param string $type
     *            Type of something to do
     * @param string $locale
     *            Language locale
     * @param string $origText
     *            Original text - default: empty
     * @param int $itemId
     *            Item identifier - default: empty
     * @param string $additional
     *            Additional string - default: empty
     * @return string Item
     */
    public function getItem($type, $locale, $origText = '', $itemId = 0, $additional = '')
    {
        // initialize
        $output = $origText;
        // get item
        switch ($type) {
            case 'blogname':
                // get original blog name
                $origText = get_bloginfo('name');
                // get translated blog name
                $name = $this->processCallback('getTranslatedBlogName', array(
                    $origText,
                    $locale
                ));
                // exit
                return (isset($name[0]) /* strlen($name) > 0 */) ? $name : $origText;
                break;
            default:
                $noItemCallbacks = array(
                    'homeurl' => 'getTranslatedHomeURL'
                );
                $itemCallbacks = array(
                    'pageurl' => 'getTranslatedPageURL',
                    'pagetitle' => 'getTranslatedPageTitle',
                    'posturl' => 'getTranslatedPostURL',
                    'posttitle' => 'getTranslatedPostTitle',
                    'categoryurl' => 'getTranslatedCategoryURL',
                    'categorytitle' => 'getTranslatedCategoryTitle',
                    'menuurl' => 'getTranslatedMenuURL',
                    'menutitle' => 'getTranslatedMenuTitle',
                    'authorurl' => 'getTranslatedAuthorURL',
                    'authorname' => 'getTranslatedAuthorName',
                    'tagurl' => 'getTranslatedTagURL',
                    'tagname' => 'getTranslatedTagName',
                    'customposturl' => 'getTranslatedCustomPostURL',
                    'customposttitle' => 'getTranslatedCustomPostTitle'
                );
                $itemAdditionalCallbacks = array(
                    'taxonomyurl' => 'getTranslatedTaxonomyURL',
                    'taxonomytitle' => 'getTranslatedTaxonomyTitle'
                );
                $checkText = false;
                if (isset($noItemCallbacks[$type])) {
                    $output = $this->processCallback($noItemCallbacks[$type], array(
                        $origText,
                        $locale
                    ));
                    $checkText = true;
                } else {
                    if (isset($itemCallbacks[$type])) {
                        $output = $this->processCallback($itemCallbacks[$type], array(
                            $origText,
                            $itemId,
                            $locale
                        ));
                        $checkText = true;
                    } else {
                        if (isset($itemAdditionalCallbacks[$type])) {
                            $output = $this->processCallback($itemAdditionalCallbacks[$type], array(
                                $origText,
                                $itemId,
                                $additional,
                                $locale
                            ));
                            $checkText = true;
                        }
                    }
                }
                if ($checkText) {
                    if (!isset($output[0]) /* strlen($output) === 0 */) {
                        $output = $origText;
                    }
                }
        }
        // exit
        return $output;
    }
}
