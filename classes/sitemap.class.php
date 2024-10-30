<?php

/**
 * sitemap.class.php
 *
 * @author Dominik Kocuj
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2 or later
 * @copyright Copyright (c) 2013-2020 kocuj.pl
 * @package kocuj_sitemap
 */

// set namespace
namespace KocujSitemapPlugin\Classes;

// set namespaces aliases
use KocujIL\V13a as KocujIL;
use KocujSitemapPlugin\Enums\DisplayType;
use KocujSitemapPlugin\Enums\OnlyWithConfigurableOrder;

// security
if ((!defined('ABSPATH')) || ((isset($_SERVER['SCRIPT_FILENAME'])) && (basename($_SERVER['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die();
}

/**
 * Plugin sitemap class
 *
 * @access public
 */
class Sitemap
{

    /**
     * Minimal length of additional serialization meta data for each link after making it shorter
     *
     * @access public
     */
    const LINK_SHORTER_META_LENGTH = 15;

    /**
     * Standard position of website root URL when link is in format '<a href="'; if link format is other than that, this constants should be also changed
     *
     * @access public
     */
    const LINK_ROOT_URL_STANDARD_POS = 9;

    /**
     * Singleton instance
     *
     * @access private
     * @var object
     */
    private static $instance = null;

    /**
     * Home link text position
     *
     * @access private
     * @var int
     */
    private $homeLinkTextPos = -1;

    /**
     * Elements types
     *
     * @access private
     * @var array
     */
    private $elementsTypes;

    /**
     * Constructor
     *
     * @access private
     * @return void
     */
    private function __construct()
    {
        // initialize data types in default order
        $types = array(
            'home',
            'menus',
            'posts',
            'pages',
            'authors',
            'tags',
            'custom'
        );
        // initialize data types letters
        $this->elementsTypes = array();
        foreach ($types as $type) {
            $typeFirstCapital = ucfirst($type);
            $classTemp = '\\KocujSitemapPlugin\\Classes\\ElementType\\Frontend\\' . $typeFirstCapital;
            $obj = new $classTemp();
            $element = array(
                'type' => $type,
                'object' => $obj,
                'configurableorder' => $obj->checkConfigurableOrder(),
                'letter' => $obj->getTypeLetter(),
                'classnamebackend' => '\\KocujSitemapPlugin\\Classes\\ElementType\\Backend\\' . $typeFirstCapital
            );
            $letter = $element['object']->getTypeLetter();
            if (!isset($this->elementsTypes[$letter])) {
                $this->elementsTypes[$letter] = $element;
            }
        }
        // set cache actions and filters for administration panel
        $actions = array();
        $filters = array();
        if ((is_admin()) || (is_network_admin())) {
            foreach ($this->elementsTypes as $type => $element) {
                $object = $this->getElementTypeObject($type);
                $adminobject = $this->getElementTypeAdminObject($type);
                if ($object->checkExists()) {
                    $actions = array_merge($actions, $adminobject->getAdminCacheActions());
                    $filters = array_merge($filters, $adminobject->getAdminCacheFilters());
                }
            }
        }
        // initialize cache class
        Cache::getInstance($actions, $filters);
        // add shortcode
        add_shortcode('KocujSitemap', array(
            $this,
            'shortcode'
        ));
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
     * Get home link text position
     *
     * @access public
     * @return int Home link text position
     */
    public function getHomeLinkTextPos()
    {
        // get home link text position
        return $this->homeLinkTextPos;
    }

    /**
     * Set home link text position
     *
     * @access public
     * @param int $homeLinkTextPos
     *            Home link text position
     * @return void
     */
    public function setHomeLinkTextPos($homeLinkTextPos)
    {
        // set home link text position
        $this->homeLinkTextPos = $homeLinkTextPos;
    }

    /**
     * Get elements types
     *
     * @access public
     * @param int $onlyWithConfigurableOrder
     *            Get only elements types with configurable order or all of them; must be one of the following constants from \KocujSitemapPlugin\Enums\OnlyWithConfigurableOrder: NO (to get all elements types) or YES (to get elements types only with configurable order) - default: \KocujSitemapPlugin\Enums\OnlyWithConfigurableOrder::NO
     * @return array Elements types
     */
    public function getElementsTypes(
        $onlyWithConfigurableOrder = OnlyWithConfigurableOrder::NO
    ) {
        // get elements types
        if ($onlyWithConfigurableOrder === OnlyWithConfigurableOrder::NO) {
            return $this->elementsTypes;
        } else {
            $output = array();
            foreach ($this->elementsTypes as $key => $type) {
                if ($type['configurableorder']) {
                    $output[$key] = $type;
                }
            }
            return $output;
        }
    }

    /**
     * Get element type
     *
     * @access public
     * @param string $type
     *            Element type
     * @return array Element type
     */
    public function getElementType($type)
    {
        // get element type
        if ((isset($this->elementsTypes[$type])) && ($this->elementsTypes[$type]['object']->checkExists())) {
            return $this->elementsTypes[$type];
        }
        // exit
        return null;
    }

    /**
     * Get element type class object
     *
     * @access public
     * @param string $type
     *            Element type
     * @return object Element type class
     */
    public function getElementTypeObject($type)
    {
        // get element type
        if ((isset($this->elementsTypes[$type])) && ($this->elementsTypes[$type]['object']->checkExists())) {
            return $this->elementsTypes[$type]['object'];
        }
        // exit
        return null;
    }

    /**
     * Get element type administration panel class object
     *
     * @access public
     * @param string $type
     *            Element type
     * @return object Element type administration panel class
     */
    public function getElementTypeAdminObject($type)
    {
        // get element type
        if (isset($this->elementsTypes[$type])) {
            // set object
            $this->elementsTypes[$type]['objectadmin'] = new $this->elementsTypes[$type]['classnamebackend']();
            // get element type
            return $this->elementsTypes[$type]['objectadmin'];
        }
        // exit
        return null;
    }

    /**
     * Get all exclude defaults
     *
     * @access public
     * @param array $labels
     *            Output with labels for each suffix
     * @param string $keyPrefix
     *            Prefix for keys in output array - default: exclude
     * @param string $filterPrefix
     *            Prefix for filter with default value; if empty, there will be not filter - default: kocujsitemap_default_exclude_
     * @return array All exclude defaults
     */
    public function getExcludeDefaults(
        array &$labels,
        $keyPrefix = 'exclude',
        $filterPrefix = 'kocujsitemap_default_exclude_'
    ) {
        // initialize
        $output = array();
        $labels = array();
        // get all exclude defaults
        $elements = $this->getElementsTypes();
        foreach ($elements as $element) {
            if ($element['object']->checkExcludeParameters()) {
                $suffixes = $element['object']->getExcludeParametersSuffixes();
                foreach ($suffixes as $suffix => $text) {
                    if (!isset($output[$keyPrefix . $suffix])) {
                        $output[$keyPrefix . $suffix] = (isset($filterPrefix[0]) /* strlen($filterPrefix) > 0 */) ? apply_filters($filterPrefix . $suffix,
                            '') : '';
                    }
                    if (!isset($labels[$keyPrefix . $suffix])) {
                        $labels[$keyPrefix . $suffix] = array();
                    }
                    $labels[$keyPrefix . $suffix][] = $text;
                }
            }
        }
        // exit
        return $output;
    }

    /**
     * Get hide types list
     *
     * @access public
     * @return array Hide types list
     */
    public function getHideTypesList()
    {
        // initialize
        $output = array();
        // get hide types list
        $elements = $this->getElementsTypes();
        foreach ($elements as $element) {
            $output[$element['object']->getHideTypeParameterValue()] = $element['object']->getHideTypeParameterDescription();
        }
        // exit
        return $output;
    }

    /**
     * Compress text
     *
     * @access private
     * @param string $text
     *            Text to compress
     * @return string Compressed text
     */
    private function compressText($text)
    {
        // compress text
        if (isset($text[0]) /* strlen($text) > 0 */) {
            $textCompressed = chr(255) . gzdeflate($text, 9);
            if (strlen($textCompressed) < strlen($text)) {
                $text = $textCompressed;
            }
        }
        // exit
        return $text;
    }

    /**
     * Decompress text
     *
     * @access private
     * @param string $text
     *            Text to decompress
     * @return string Decompressed text
     */
    private function decompressText($text)
    {
        // exit
        return ((isset($text[0])) && ($text[0] === chr(255))) ? gzinflate(substr($text, 1)) : $text;
    }

    /**
     * Compress sitemap elements
     *
     * @access private
     * @param
     *            array &$data Data to change and its output
     * @param
     *            array &$index Data index to change and its output
     * @param array $attr
     *            Attributes
     * @return void
     */
    private function compressElements(&$data, &$index, array $attr)
    {
        // compress sitemap items
        if (!empty($data)) {
            // check if make links shorter
            $makeShorter = ($attr['rooturlsize'] > self::LINK_SHORTER_META_LENGTH);
            // process data
            foreach ($data as $key => $val) {
                // make link shorter
                if ($makeShorter) {
                    $pos = strpos($val['lk'], $attr['rooturl']);
                    if ($pos !== false) {
                        $data[$key]['lk'] = array(
                            'lk' => substr($val['lk'], 0, $pos) . substr($val['lk'], $pos + $attr['rooturlsize'])
                        );
                        if ($pos !== self::LINK_ROOT_URL_STANDARD_POS) {
                            $data[$key]['rp'] = $pos;
                        }
                        $val = $data[$key];
                    }
                }
                // compress text
                if ($attr['additionalcompression']) {
                    $text = $this->compressText((is_array($val['lk'])) ? $val['lk']['lk'] : $val['lk']);
                    if (is_array($val['lk'])) {
                        $data[$key]['lk']['lk'] = $text;
                    } else {
                        $data[$key]['lk'] = $text;
                    }
                    if (isset($val['ac'])) {
                        foreach ($val['ac'] as $key2 => $val2) {
                            $data[$key]['ac'][$key2] = $this->compressText($val2);
                        }
                    }
                    if (isset($val['ad'])) {
                        foreach ($val['ad'] as $key2 => $val2) {
                            $data[$key]['ad'][$key2] = $this->compressText($val2);
                        }
                    }
                    $val = $data[$key];
                }
                // change type to value
                if (!isset($index['tp'])) {
                    $index['tp'] = array();
                }
                $type = $val['tp'];
                $pos = array_search($type, $index['tp']);
                if ($pos === false) {
                    $pos = count($index['tp']);
                    $index['tp'][] = $type;
                }
                $data[$key]['tp'] = $pos;
                // optionally make reccurence
                if ((isset($val['ch'])) && (!empty($val['ch']))) {
                    $this->compressElements($data[$key]['ch'], $index, $attr);
                }
            }
        }
    }

    /**
     * Decompress sitemap element
     *
     * @access private
     * @param array $element
     *            Sitemap element to change
     * @param array $index
     *            Sitemap data index
     * @param array $attr
     *            Attributes
     * @return array Decompressed sitemap element
     */
    private function decompressElement($element, array $index, array $attr)
    {
        // change type to name
        if (isset($index['tp'][$element['tp']])) {
            $element['tp'] = $index['tp'][$element['tp']];
        }
        // decompress text
        if ($attr['additionalcompression']) {
            $text = $this->decompressText((is_array($element['lk'])) ? $element['lk']['lk'] : $element['lk']);
            if (is_array($element['lk'])) {
                $element['lk']['lk'] = $text;
            } else {
                $element['lk'] = $text;
            }
            if (isset($element['ac'])) {
                foreach ($element['ac'] as $key => $val) {
                    $element['ac'][$key] = $this->decompressText($val);
                }
            }
            if (isset($element['ad'])) {
                foreach ($element['ad'] as $key => $val) {
                    $element['ad'][$key] = $this->decompressText($val);
                }
            }
        }
        // change link if is shortened
        if (is_array($element['lk'])) {
            $pos = (isset($element['lk']['rp'])) ? $element['lk']['rp'] : self::LINK_ROOT_URL_STANDARD_POS;
            $element['lk'] = substr($element['lk']['lk'], 0, $pos) . $attr['rooturl'] . substr($element['lk']['lk'],
                    $pos);
        }
        // exit
        return $element;
    }

    /**
     * Create sitemap
     *
     * @access public
     * @param string $locale
     *            Language locale - default: empty
     * @return array Sitemap data
     */
    public function create($locale = '')
    {
        // prepare locale
        if (!isset($locale[0]) /* strlen($locale) === 0 */) {
            $locale = get_locale();
        }
        // check if make additional compression
        if (Options::getInstance()->getOption('Cache') === '1') {
            $additionalCompression = (Options::getInstance()->getOption('CacheAdditionalCompress') === '1');
            if (($additionalCompression) && (!function_exists('gzdeflate'))) {
                $additionalCompression = false;
            }
        } else {
            $additionalCompression = false;
        }
        // set elements order
        $value = Options::getInstance()->getOption('OrderList');
        if (!isset($value[0]) /* strlen($value) === 0 */) {
            $value = Base::getInstance()->getKocujILObj()
                ->get('options', KocujIL\Enums\ProjectCategory::ALL)
                ->getDefinition('options', 'OrderList');
            $value = $value['defaultvalue'];
        }
        $order = 'H' . implode('', $value);
        // delete WordPress cache
        global $wp_object_cache;
        $wpCache = $wp_object_cache;
        wp_cache_flush();
        // begin multi-lingual processing
        MultipleLanguages::getInstance()->doSomething('beforeprocess', $locale);
        // get blog name
        MultipleLanguages::getInstance()->doSomething('beforegetblogname', $locale);
        $defaultHomeLinkText = apply_filters('kocujsitemap_default_home_link_text',
            MultipleLanguages::getInstance()->getItem('blogname', $locale), $locale);
        MultipleLanguages::getInstance()->doSomething('aftergetblogname', $locale);
        // clear home link text position
        $this->setHomeLinkTextPos(-1);
        // get elements
        $rootURL = Helpers\Url::getInstance()->getRootUrlWithoutProtocol();
        $attr = array(
            'rooturl' => $rootURL,
            'rooturlsize' => strlen($rootURL),
            'additionalcompression' => $additionalCompression
        );
        $index = array(
            'ru' => $rootURL,
            'hl' => $defaultHomeLinkText
        );
        $sectionsNames = array();
        $array = array();
        $loopLength = strlen($order);
        for ($z = 0; $z < $loopLength; $z++) {
            // get element type
            $elementType = $this->getElementTypeAdminObject($order[$z]);
            if (!empty($elementType)) {
                // get elements for current type
                $array[$order[$z]] = $elementType->getElementArray($locale);
                // compress elements
                if (!empty($array[$order[$z]])) {
                    $this->compressElements($array[$order[$z]], $index, $attr);
                }
                // get sections names
                $options = Options::getInstance()->getSitemapTypeLanguagesOptionsNames($order[$z]);
                foreach ($options as $option) {
                    $optionValue = Options::getInstance()->getOption($option['option']);
                    if (isset($optionValue[0]) /* strlen($optionValue) > 0 */) {
                        $sectionsNames[$option['option']] = $optionValue;
                    }
                }
            }
        }
        // end multi-lingual processing
        MultipleLanguages::getInstance()->doSomething('afterprocess', $locale);
        // prepare array
        if (!empty($sectionsNames)) {
            $index['sn'] = $sectionsNames;
        }
        $index['hp'] = $this->getHomeLinkTextPos();
        $array = array(
            'dt' => $array,
            'ix' => $index
        );
        if ($additionalCompression) {
            $array['ac'] = true;
        }
        // return WordPress cache
        $wp_object_cache = $wpCache;
        // exit
        return $array;
    }

    /**
     * Sitemap shortcode
     *
     * @access public
     * @param array|string $args
     *            Arguments
     * @return string Parsed output
     */
    public function shortcode($args)
    {
        // get arguments
        $defaultMainCSSClass = apply_filters('kocujsitemap_default_main_css_class', '');
        $shortcodes = array(
            'homelinktext' => '',
            'title' => '', // for compatibility with 1.x.x
            'class' => $defaultMainCSSClass,
            'hidetypes' => ''
        );
        $labels = array();
        $shortcodeAttr = shortcode_atts(array_merge($shortcodes, $this->getExcludeDefaults($labels)), $args,
            'kocujsitemap');
        $homeLinkText = (isset($shortcodeAttr['homelinktext'])) ? $shortcodeAttr['homelinktext'] : '';
        $title = (isset($shortcodeAttr['title'])) ? $shortcodeAttr['title'] : '';
        if ((isset($title[0]) /* strlen($title) > 0 */) && ($title !== $homeLinkText)) { // for compatibility with 1.x.x
            $homeLinkText = $title; // for compatibility with 1.x.x
        } // for compatibility with 1.x.x
        $excludeData = array();
        $elements = $this->getElementsTypes();
        foreach ($elements as $element) {
            if ($element['object']->checkExcludeParameters()) {
                $suffixes = $element['object']->getExcludeParametersSuffixes();
                foreach ($suffixes as $suffix => $text) {
                    if (!isset($excludeData[$suffix])) {
                        $excludeData[$suffix] = (isset($shortcodeAttr['exclude' . $suffix][0]) /* strlen($shortcodeAttr['exclude'.$suffix]) > 0 */) ? explode(',',
                            trim($shortcodeAttr['exclude' . $suffix])) : array();
                    }
                }
            }
        }
        $hideTypes = (isset($shortcodeAttr['hidetypes']) /* strlen($shortcodeAttr['hidetypes']) > 0 */) ? explode(',',
            trim($shortcodeAttr['hidetypes'])) : array();
        // exit
        return $this->get($homeLinkText, (isset($shortcodeAttr['class'])) ? $shortcodeAttr['class'] : '', $excludeData,
            $hideTypes);
    }

    /**
     * Prepare sitemap data for text by reccurence when there are any elements excluded
     *
     * @access private
     * @param string $type
     *            Sitemap data type
     * @param
     *            array &$data Sitemap data and its output
     * @param array $index
     *            Sitemap data index
     * @param array $attr
     *            Attributes
     * @param array $exclude
     *            List of elements to exclude - it is divided into sections: "post", "category", "author", "term"
     * @return void
     */
    private function addSitemapTextReccurencePrepareForExclude($type, &$data, array $index, array $attr, array $exclude)
    {
        // prepare data for exclude
        if (!empty($data)) {
            // get element type object
            $elementType = $this->getElementTypeObject($type);
            // process data
            $requiresChildren = $elementType->getTypesRequiresChildren();
            $loopCount = count($data);
            for ($z = 0; $z < $loopCount; $z++) {
                // decompress element
                $data[$z] = $this->decompressElement($data[$z], $index, $attr);
                // get element
                $element = $data[$z];
                // process children
                if ((isset($element['ch'])) && (!empty($element['ch']))) {
                    $this->addSitemapTextReccurencePrepareForExclude($type, $data[$z]['ch'], $index, $attr, $exclude);
                }
                // check if children list after excludes is empty and should be removed
                if ((in_array($element['tp'],
                        $requiresChildren)) && ((!isset($data[$z]['ch'])) || (isset($data[$z]['ch']) && (empty($data[$z]['ch']))))) {
                    unset($data[$z]);
                } else {
                    // check if exclude this element
                    $sectionsCheck = $elementType->getSectionsToCheck($element);
                    foreach ($sectionsCheck as $oneSection) {
                        if ((isset($exclude[$oneSection])) && (!empty($exclude[$oneSection]))) {
                            $idCheck = $elementType->getIDToCheck($element, $oneSection);
                            if ($idCheck >= 0) {
                                foreach ($exclude[$oneSection] as $id) {
                                    if ((isset($id[0]) /* strlen($id) > 0 */) && (is_numeric($id)) && ($id > 0) && ($id === (string)$idCheck)) {
                                        unset($data[$z]);
                                        break;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Add sitemap text by reccurence
     *
     * @access private
     * @param string $type
     *            Sitemap data type
     * @param array $data
     *            Sitemap data
     * @param array $index
     *            Sitemap data index
     * @param array $attr
     *            Attributes
     * @param bool $firstLevel
     *            It is first level on list (true) or not (false)
     * @param
     *            bool &$first Output flag indicate that it is first element (true) or not (false)
     * @param array $exclude
     *            List of elements to exclude - it is divided into sections: "post", "category", "author", "term"
     * @param bool $isWidget
     *            Sitemap for widget (true) or not (false)
     * @param int $display
     *            Display type; must be one of the following constants from \KocujSitemapPlugin\Enums\DisplayType: STANDARD (when it is standard display) or DROPDOWN (when it is drop-down list display)
     * @param int $depth
     *            Depth
     * @param
     *            array &$urls URL-s list for each link with key generated automatically
     * @return string Sitemap text
     */
    private function addSitemapTextReccurence(
        $type,
        array $data,
        array $index,
        array $attr,
        $firstLevel,
        &$first,
        array $exclude,
        $isWidget,
        $display,
        $depth,
        &$urls
    ) {
        // initialize
        $output = '';
        switch ($display) {
            case DisplayType::DROPDOWN:
                $beforeTagBegin = '<option value="####URLID####"';
                $beforeTagEnd = str_repeat('&nbsp;&nbsp;', $depth);
                $afterTag = '</option>';
                break;
            default:
                $beforeTagBegin = '<li';
                $beforeTagEnd = '';
                $afterTag = '</li>';
        }
        // set output text
        if (!empty($data)) {
            $elementType = $this->getElementTypeObject($type);
            $checkElementDisplay = $elementType->checkForDisplay($display);
            if ($checkElementDisplay) {
                $showUl = $display === DisplayType::STANDARD ? $elementType->checkListTag() : false;
                $elementKeys = array_keys($data);
                foreach ($elementKeys as $elementKey) {
                    // set element id for output URL-s array
                    $urlId = str_replace('.', '', uniqid('kocuj_sitemap_url', true));
                    // get element
                    $element = $data[$elementKey];
                    // get text before element
                    $output .= $elementType->getBeforeElement($element,
                        $isWidget ? \KocujSitemapPlugin\Enums\Widget::YES : \KocujSitemapPlugin\Enums\Widget::NO,
                        get_locale());
                    // decompress element if there are no elements excluded
                    if (empty($exclude)) {
                        $element = $this->decompressElement($element, $index, $attr);
                    }
                    // add title to home element
                    if (($element['tp'] === 'home') && ($index['hp'] > -1)) {
                        $element['lk'] = substr($element['lk'], 0,
                                $index['hp']) . $attr['homelinktext'] . substr($element['lk'], $index['hp']);
                    }
                    // get element data
                    $style = '';
                    if (!$firstLevel) {
                        $marginLeft = Options::getInstance()->getOption('MarginLeft');
                        if (!empty($marginLeft)) {
                            $style = 'margin-left:' . $marginLeft . 'px';
                        }
                    }
                    if ((isset($element['sc'])) && ($element['sc'])) {
                        $elementData = $element['lk'];
                        $containerClassPos = $element['cp'];
                    } else {
                        $elementData = str_replace('####URLID####', esc_attr($urlId), $beforeTagBegin);
                        if (isset($style[0]) /* strlen($style) > 0 */) {
                            $elementData .= ' style="' . esc_attr($style) . '"';
                        }
                        $containerClassPos = strlen($elementData);
                        $elementData .= '>' . $beforeTagEnd . $element['lk'];
                    }
                    // add additional classes
                    $addClass = '';
                    if ((isset($element['ac'])) && (isset($element['ac'][0]) /* strlen($element['ac']) > 0 */)) {
                        $addClass = implode(' ', $element['ac']);
                    }
                    // check if element is first
                    if ($first) {
                        $addClass .= ' kocujsitemapfirst';
                        $firstClass = apply_filters('kocujsitemap_first_element_css_class', '');
                        if (isset($firstClass[0]) /* strlen($firstClass) > 0 */) {
                            $addClass .= ' ' . $firstClass;
                        }
                        $first = false;
                    }
                    // add CSS class based on type
                    if (isset($addClass[0]) /* strlen($addClass) > 0 */) {
                        $addClass .= ' ';
                    }
                    $addClass .= 'kocujsitemap-' . $element['tp'];
                    // add CSS classes
                    $classText = ' class="' . esc_attr(trim($addClass)) . '"';
                    // create output
                    $output .= substr($elementData, 0, $containerClassPos) . $classText . substr($elementData,
                            $containerClassPos);
                    // add children
                    if ((isset($element['ch'])) && (!empty($element['ch']))) {
                        $output .= $this->addSitemapTextReccurence($type, $element['ch'], $index, $attr, false, $first,
                            $exclude, $isWidget, $display, $depth + 1, $urls);
                    }
                    // end tag
                    if ($showUl) {
                        $output .= $afterTag;
                    }
                    // get text after element
                    $output .= $elementType->getAfterElement($element,
                        $isWidget ? \KocujSitemapPlugin\Enums\Widget::YES : \KocujSitemapPlugin\Enums\Widget::NO,
                        get_locale());
                    // add to URL-s list
                    if (isset($element['ur'])) {
                        $urls[$urlId] = $element['ur'];
                    }
                    // remove element from memory
                    unset($data[$elementKey]);
                }
                if ((isset($output[0]) /* strlen($output) > 0 */) && ($showUl)) {
                    $output = '<ul>' . $output . '</ul>';
                }
            }
        }
        // exit
        return $output;
    }

    /**
     * Add sitemap text
     *
     * @access private
     * @param string $type
     *            Sitemap data type
     * @param array $attr
     *            Attributes
     * @param array $data
     *            Sitemap data
     * @param array $index
     *            Sitemap data index
     * @param
     *            bool &$first Output flag indicate that it is first element (true) or not (false)
     * @param array $exclude
     *            List of elements to exclude - it is divided into sections: "post", "category", "author", "term"
     * @param bool $isWidget
     *            Sitemap for widget (true) or not (false)
     * @param int $display
     *            Display type; must be one of the following constants from \KocujSitemapPlugin\Enums\DisplayType: STANDARD (when it is standard display) or DROPDOWN (when it is drop-down list display)
     * @return string Sitemap text
     */
    private function addSitemapText(
        $type,
        array $attr,
        array $data,
        array $index,
        &$first,
        array $exclude,
        $isWidget,
        $display
    ) {
        // add sitemap text
        if (!empty($exclude)) {
            $this->addSitemapTextReccurencePrepareForExclude($type, $data, $index, $attr, $exclude);
        }
        $urls = array();
        $output = $this->addSitemapTextReccurence($type, $data, $index, $attr, true, $first, $exclude, $isWidget,
            $display, 0, $urls);
        if ($display === DisplayType::DROPDOWN) {
            if (isset($output[0]) /* strlen($output) > 0 */) {
                $selectId = str_replace('.', '', uniqid('kocuj_sitemap_select', true));
                $output = '<p><select name="' . esc_attr($selectId) . '" id="' . esc_attr($selectId) . '"><option value="">-- ' . __('select',
                        'kocuj-sitemap') . ' --</option>' . $output . '</select></p>' . PHP_EOL;
                wp_enqueue_script('jquery', '', array(), false, true);
                $output .= '<script type="text/javascript">' . PHP_EOL;
                $output .= '/* <![CDATA[ */' . PHP_EOL;
                $output .= '(function($) {' . PHP_EOL;
                $output .= '"use strict";' . PHP_EOL;
                $output .= '$(document).ready(function($) {' . PHP_EOL;
                $output .= '$("#' . esc_js($selectId) . '").change(function() {' . PHP_EOL;
                foreach ($urls as $urlId => $url) {
                    $output .= 'if ($(this).val() === "' . esc_js($urlId) . '") {' . PHP_EOL;
                    $output .= 'window.location.href = "' . esc_js($url) . '";' . PHP_EOL;
                    $output .= '}' . PHP_EOL;
                }
                $output .= '});' . PHP_EOL;
                $output .= '});' . PHP_EOL;
                $output .= '}(jQuery));' . PHP_EOL;
                $output .= '/* ]]> */' . PHP_EOL;
                $output .= '</script>' . PHP_EOL;
            } else {
                $output = '';
            }
        }
        return $output;
    }

    /**
     * Get sitemap to display
     *
     * @access public
     * @param string $homeLinkText
     *            Home link text in the sitemap - default: empty
     * @param string $class
     *            Sitemap class - default: empty
     * @param array $exclude
     *            List of elements to exclude - it is divided into sections: "post", "category", "author", "term" - default: empty
     * @param array $hideTypes
     *            List of elements types to hide; there are the following types allowed: "authors", "custom", "home", "menus", "pages", "posts", "tags" - default: empty
     * @param int $widget
     *            It is widget or not; must be one of the following constants from \KocujSitemapPlugin\Enums\Widget: NO (when it is not widget) or YES (when it is widget) - default: \KocujSitemapPlugin\Enums\Widget::NO
     * @param int $display
     *            Display type; must be one of the following constants from \KocujSitemapPlugin\Enums\DisplayType: STANDARD (when it is standard display) or DROPDOWN (when it is drop-down list display) - default: \KocujSitemapPlugin\Enums\DisplayType::STANDARD
     * @return string Sitemap to display
     */
    public function get(
        $homeLinkText = '',
        $class = '',
        array $exclude = array(),
        array $hideTypes = array(),
        $widget = \KocujSitemapPlugin\Enums\Widget::NO,
        $display = DisplayType::STANDARD
    ) {
        // load cache
        try {
            $data = Cache::getInstance()->loadCache();
        } catch (\Exception $e) {
            return '';
        }
        if ((isset($data['ac'])) && ($data['ac']) && (!function_exists('gzdeflate'))) {
            try {
                Cache::getInstance()->clearCache();
                Cache::getInstance()->createCache();
                $data = Cache::getInstance()->loadCache();
            } catch (\Exception $e) {
                return '';
            }
        }
        $index = (isset($data['ix'])) ? $data['ix'] : array();
        // clear excludes array if all exclude children arrays are empty
        $excludeTemp = array_filter($exclude);
        if (empty($excludeTemp)) {
            $exclude = $excludeTemp;
        }
        // get arguments
        if (!isset($homeLinkText[0]) /* strlen($homeLinkText) === 0 */) {
            $homeLinkText = $index['hl'];
        }
        if (!isset($class[0]) /* strlen($class) === 0 */) {
            $class = apply_filters('kocujsitemap_default_main_css_class', '');
        }
        $homeLinkText = apply_filters('kocujsitemap_link_text', $homeLinkText, 0, 'home', get_locale());
        // integrate data from cache
        $first = true;
        $output = '';
        $sections = (Options::getInstance()->getOption('DisplaySections') === '1');
        if ((isset($data['dt'])) && (!empty($data['dt']))) {
            $rootURL = $index['ru'];
            $attr = array(
                'rooturl' => $rootURL,
                'rooturlsize' => strlen($rootURL),
                'homelinktext' => $homeLinkText,
                'additionalcompression' => (isset($data['ac'])) ? $data['ac'] : false
            );
            $hLevel = ($widget === \KocujSitemapPlugin\Enums\Widget::NO) ? Options::getInstance()->getOption('HLevelMain') : Options::getInstance()->getOption('HLevelWidget');
            $types = array_keys($data['dt']);
            foreach ($types as $type) {
                $elementType = $this->getElementTypeObject($type);
                $hideParameterValue = $elementType->getHideTypeParameterValue();
                if (!in_array($hideParameterValue, $hideTypes)) {
                    $oneData = $data['dt'][$type];
                    $outputSection = '';
                    if ($sections) {
                        $sectionTitle = '';
                        if ((!empty($elementType)) && ($elementType->checkSectionName())) {
                            $key = 'SectionName_' . $type . '_' . get_locale();
                            if ((isset($index['sn'][$key])) && (isset($index['sn'][$key][0]) /* strlen($index['sn'][$key]) > 0 */)) {
                                $sectionTitle = $index['sn'][$key];
                            }
                            if (!isset($sectionTitle[0]) /* strlen($sectionTitle) === 0 */) {
                                $key = 'SectionName_' . $type . '_en_US';
                                if ((isset($index['sn'][$key])) && (isset($index['sn'][$key][0]) /* strlen($index['sn'][$key]) > 0 */)) {
                                    $sectionTitle = $index['sn'][$key];
                                }
                            }
                            if (!isset($sectionTitle[0]) /* strlen($sectionTitle) === 0 */) {
                                $sectionTitle = $elementType->getSectionName();
                            }
                        }
                        if (isset($sectionTitle[0]) /* strlen($sectionTitle) > 0 */) {
                            $outputSection = '<h' . $hLevel . '>' . $sectionTitle . '</h' . $hLevel . '>';
                        }
                    }
                    $outputText = $this->addSitemapText($type, $attr, $oneData, $index, $first, $exclude,
                        $widget === \KocujSitemapPlugin\Enums\Widget::YES, $display);
                    if (isset($outputText[0]) /* strlen($outputText) > 0 */) {
                        $output .= $outputSection . $outputText;
                    }
                    unset($data['dt'][$type]);
                }
            }
        }
        // clean empty lists, integrate lists and remove EOL in output
        $output = str_replace(array(
            "\r\n",
            "\n",
            "\r"
        ), '', preg_replace('/<\/([u|U])([l|L])><([u|U])([l|L])(.*?)>/s', '',
            preg_replace('/<([u|U])([l|L])><([u|U])([l|L])>/s', '', $output)));
        // show begin and end
        if (isset($output[0]) /* strlen($output) > 0 */) {
            $classText = '';
            if (isset($class[0]) /* strlen($class) > 0 */) {
                $classText = ' ' . $class;
            }
            $html5 = (Options::getInstance()->getOption('UseHTML5') === '1');
            $footer = '';
            $value = ($widget === \KocujSitemapPlugin\Enums\Widget::YES) ? (Options::getInstance()->getOption('PoweredByInWidget') === '1') : (Options::getInstance()->getOption('PoweredBy') === '1');
            if ($value) {
                $footer = (($html5) ? Helpers\Html5::getInstance()->getTagBegin('footer', '',
                        'kocujsitemapfooter') : '<div class="kocujsitemapfooter">') .
                    /* translators: 1: link (HTML anchor) to plugin website, 2: link (HTML anchor) to plugin's author website */
                    sprintf(__('Powered by %1$s plugin created by %2$s', 'kocuj-sitemap'),
                        KocujIL\Classes\HtmlHelper::getInstance()->getLink('http://kocujsitemap.wpplugin.kocuj.pl/',
                            'Kocuj Sitemap', array(
                                'externalwithouttarget' => true
                            )),
                        KocujIL\Classes\HtmlHelper::getInstance()->getLink('http://kocuj.pl/', 'kocuj.pl', array(
                            'externalwithouttarget' => true
                        ))) . (($html5) ? Helpers\Html5::getInstance()->getTagEnd('footer') : '</div>');
            }
            $output = (($html5) ? Helpers\Html5::getInstance()->getTagBegin('nav', '',
                    'kocujsitemap' . $classText) : '<div class="' . esc_attr('kocujsitemap' . $classText) . '">') . $output . (($html5) ? Helpers\Html5::getInstance()->getTagEnd('nav') : '</div>') . $footer;
        }
        // exit
        return $output;
    }
}
