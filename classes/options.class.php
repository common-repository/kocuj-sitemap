<?php

/**
 * options.class.php
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

// security
if ((!defined('ABSPATH')) || ((isset($_SERVER['SCRIPT_FILENAME'])) && (basename($_SERVER['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die();
}

/**
 * Options class
 *
 * @access public
 */
class Options
{

    /**
     * Singleton instance
     *
     * @access private
     * @var object
     */
    private static $instance = null;

    /**
     * Constructor
     *
     * @access private
     * @return void
     */
    private function __construct()
    {
        // add options containers
        Base::getInstance()->getKocujILObj()
            ->get('options', KocujIL\Enums\ProjectCategory::ALL)
            ->addContainer('options');
        // add options
        Base::getInstance()->getKocujILObj()
            ->get('options', KocujIL\Enums\ProjectCategory::ALL)
            ->addDefinition('options', 'PoweredBy', 'checkbox', '0',
                __('Display `powered by` information below sitemap', 'kocuj-sitemap'));
        Base::getInstance()->getKocujILObj()
            ->get('options', KocujIL\Enums\ProjectCategory::ALL)
            ->addDefinition('options', 'PoweredByInWidget', 'checkbox', '0',
                __('Display `powered by` information below sitemap in widget', 'kocuj-sitemap'));
        Base::getInstance()->getKocujILObj()
            ->get('options', KocujIL\Enums\ProjectCategory::ALL)
            ->addDefinition('options', 'UseHTML5', 'checkbox', '1', __('Use HTML 5 tags', 'kocuj-sitemap'));
        Base::getInstance()->getKocujILObj()
            ->get('options', KocujIL\Enums\ProjectCategory::ALL)
            ->addDefinition('options', 'LinkToMainSite', 'checkbox', '1',
                __('Display link to main site', 'kocuj-sitemap'));
        Base::getInstance()->getKocujILObj()
            ->get('options', KocujIL\Enums\ProjectCategory::ALL)
            ->addDefinition('options', 'DisplaySections', 'checkbox', '1',
                __('Divide display into sections', 'kocuj-sitemap'));
        Base::getInstance()->getKocujILObj()
            ->get('options', KocujIL\Enums\ProjectCategory::ALL)
            ->addDefinition('options', 'HLevelMain', 'integer', '2',
                __('Header text HTML level value for each section', 'kocuj-sitemap'),
                KocujIL\Enums\Project\Components\All\Options\OptionArray::NO, array(), array(
                    'minvalue' => 1,
                    'maxvalue' => 6
                ));
        Base::getInstance()->getKocujILObj()
            ->get('options', KocujIL\Enums\ProjectCategory::ALL)
            ->addDefinition('options', 'HLevelWidget', 'integer', '4',
                __('Header text HTML level value for each section in widget', 'kocuj-sitemap'),
                KocujIL\Enums\Project\Components\All\Options\OptionArray::NO, array(), array(
                    'minvalue' => 1,
                    'maxvalue' => 6
                ));
        Base::getInstance()->getKocujILObj()
            ->get('options', KocujIL\Enums\ProjectCategory::ALL)
            ->addDefinition('options', 'ButtonEditor', 'checkbox', '1',
                __('Enable shortcode button for sitemap in visual and HTML editor', 'kocuj-sitemap'));
        Base::getInstance()->getKocujILObj()
            ->get('options', KocujIL\Enums\ProjectCategory::ALL)
            ->addDefinition('options', 'Multilang', 'text', '0',
                __('Use plugin for multiple languages', 'kocuj-sitemap'));
        Base::getInstance()->getKocujILObj()
            ->get('options', KocujIL\Enums\ProjectCategory::ALL)
            ->addDefinition('options', 'MarginLeft', 'integer', '0',
                __('Force left margin in pixels for each level on multi-level list', 'kocuj-sitemap'),
                KocujIL\Enums\Project\Components\All\Options\OptionArray::NO, array(), array(
                    'minvalue' => 0
                ));
        Base::getInstance()->getKocujILObj()
            ->get('options', KocujIL\Enums\ProjectCategory::ALL)
            ->addDefinition('options', 'Cache', 'checkbox', '1', __('Enable cache', 'kocuj-sitemap'));
        Base::getInstance()->getKocujILObj()
            ->get('options', KocujIL\Enums\ProjectCategory::ALL)
            ->addDefinition('options', 'CacheFrontend', 'checkbox', '0',
                __('Generate cache on the website instead of in the administration panel', 'kocuj-sitemap'));
        Base::getInstance()->getKocujILObj()
            ->get('options', KocujIL\Enums\ProjectCategory::ALL)
            ->addDefinition('options', 'CacheAdditionalCompress', 'checkbox', '0',
                __('Enable additional compression of cache', 'kocuj-sitemap'));
        $def = array();
        $types = Sitemap::getInstance()->getElementsTypes(true);
        if (!empty($types)) {
            $def = array_keys($types);
        }
        Base::getInstance()->getKocujILObj()
            ->get('options', KocujIL\Enums\ProjectCategory::ALL)
            ->addDefinition('options', 'OrderList', 'text', $def, __('Order of elements', 'kocuj-sitemap'),
                KocujIL\Enums\Project\Components\All\Options\OptionArray::YES, array(
                    'allowchangeorder' => true,
                    'deletebutton' => false,
                    'addnewbutton' => false,
                    'autoadddeleteifempty' => false
                ));
        $types = Sitemap::getInstance()->getElementsTypes();
        foreach ($types as $key => $val) {
            if ($val['object']->checkExists()) {
                $options = $val['object']->getConfigOptions();
                if (!empty($options)) {
                    foreach ($options as $option) {
                        Base::getInstance()->getKocujILObj()
                            ->get('options', KocujIL\Enums\ProjectCategory::ALL)
                            ->addDefinition('options', $option[0], $option[1], $option[2], $option[3], $option[4],
                                $option[5], $option[6]);
                    }
                }
            }
        }
        // add options for section names
        $types = Sitemap::getInstance()->getElementsTypes();
        $languages = MultipleLanguages::getInstance()->getLanguages($this->getOption('Multilang'));
        if ((!empty($types)) && (!empty($languages))) {
            foreach ($types as $key => $type) {
                $options = $this->getSitemapTypeLanguagesOptionsNames($key);
                foreach ($options as $option) {
                    Base::getInstance()->getKocujILObj()
                        ->get('options', KocujIL\Enums\ProjectCategory::ALL)
                        ->addDefinition('options', $option['option'], 'text', '', __('Section title', 'kocuj-sitemap'));
                }
            }
        }
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
     * Get option value
     *
     * @access public
     * @param string $optionId
     *            Option identifier
     * @return string Option value
     */
    public function getOption($optionId)
    {
        // exit
        return Base::getInstance()->getKocujILObj()
            ->get('options', KocujIL\Enums\ProjectCategory::ALL)
            ->getOption('options', $optionId);
    }

    /**
     * Get sitemap type languages options names
     *
     * @access public
     * @param string $type
     *            Sitemap type
     * @return array Sitemap type languages options names
     */
    public function getSitemapTypeLanguagesOptionsNames($type)
    {
        // initialize
        $options = array();
        // get languages options names for type
        $object = Sitemap::getInstance()->getElementTypeObject($type);
        if ($object->checkSectionName()) {
            $data = Sitemap::getInstance()->getElementType($type);
            if (!empty($data)) {
                $options[] = array(
                    'option' => 'SectionName_' . $type . '_en_US',
                    'language' => 'en_US'
                );
                $languages = MultipleLanguages::getInstance()->getLanguages($this->getOption('Multilang'));
                foreach ($languages as $language) {
                    if (($language !== 'en_US') && ($language !== 'en')) {
                        $options[] = array(
                            'option' => 'SectionName_' . $type . '_' . $language,
                            'language' => $language
                        );
                    }
                }
            }
        }
        // exit
        return $options;
    }
}
