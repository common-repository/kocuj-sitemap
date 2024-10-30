<?php

/**
 * multiple-languages-data.class.php
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
class MultipleLanguagesData
{

    /**
     * Singleton instance
     *
     * @access private
     * @var object
     */
    private static $instance = null;

    /**
     * Classes data for multi-languages plugins
     *
     * @access private
     * @var array
     */
    private $data = array();

    /**
     * Multi-language plugins filenames
     *
     * @access private
     * @var array
     */
    private $pluginsFiles = array();

    /**
     * Constructor
     *
     * @access private
     * @return void
     */
    private function __construct()
    {
        // set list of all multi-languages plugins classes
        $classes = array(
            '\\KocujSitemapPlugin\\Classes\\MultipleLanguages\\QTranslateX' => array(
                'original' => true
            )
        );
        // get list of additional multi-languages plugins classes
        $additionalMultiLangClasses = apply_filters('kocujsitemap_additional_multiple_languages_php_classes', array());
        if (!empty($additionalMultiLangClasses)) {
            $additional = array();
            foreach ($additionalMultiLangClasses as $key => $val) {
                $additional[$val['class']] = $val;
                if (isset($classes[$val['class']])) {
                    unset($additional[$val['class']]);
                } else {
                    unset($additional[$val['class']]['class']);
                }
            }
            $classes = $classes + $additional;
        }
        // load all classes
        if (!empty($classes)) {
            // load all classes
            $keys = array_keys($classes);
            foreach ($keys as $key) {
                $class = $classes[$key];
                $ok = false;
                if ((isset($class['original'])) || ((isset($class['filename'])) && (is_file($class['filename']))) || ((isset($class['dir'])) && (is_file($class['dir'])))) {
                    if (!isset($class['original'])) {
                        if (isset($class['filename'])) {
                            include $class['filename'];
                        }
                    }
                    if ((isset($class['original'])) || (class_exists($key))) {
                        $ok = true;
                        $instance = call_user_func($key . '::getInstance');
                        if (!isset($class['original'])) {
                            $interfaces = class_implements($instance);
                            if (!in_array('KocujSitemapPlugin\\Interfaces\\Language', $interfaces)) {
                                $ok = false;
                            }
                        }
                        if ($ok) {
                            $this->pluginsFiles[] = $instance->getPluginFile();
                            $active = $instance->checkPlugin();
                            if (!$active) {
                                $ok = false;
                            } else {
                                $this->data[$key] = array(
                                    'instance' => $instance,
                                    'prior' => $instance->getPriority()
                                );
                                if ((is_admin()) || (is_network_admin())) {
                                    $this->data[$key]['admin_id'] = $key;
                                    $this->data[$key]['admin_name'] = $instance->getName();
                                }
                            }
                        }
                    }
                }
                if (!$ok) {
                    unset($classes[$key]);
                } else {
                    unset($classes[$key]['original']);
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
     * Get multi-language plugins data
     *
     * @access public
     * @return array Get multi-language plugins data
     */
    public function getData()
    {
        // get multi-language plugins data
        return $this->data;
    }

    /**
     * Get multi-language plugins filename
     *
     * @access public
     * @return array Get multi-language plugins filenames
     */
    public function getPluginsFiles()
    {
        // exit
        return $this->pluginsFiles;
    }
}
