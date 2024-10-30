<?php

/**
 * base.class.php
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
use KocujPlLib\V13a\Classes\Project;

// security
if ((!defined('ABSPATH')) || ((isset($_SERVER['SCRIPT_FILENAME'])) && (basename($_SERVER['SCRIPT_FILENAME']) === basename(__FILE__)))) {
    header('HTTP/1.1 404 Not Found');
    die();
}

/**
 * Base class
 *
 * @access public
 */
class Base
{

    /**
     * Singleton instance
     *
     * @access private
     * @var object
     */
    private static $instance = null;

    /**
     * Plugin URL
     *
     * @access private
     * @var string
     */
    private $pluginUrl;

    /**
     * Plugin directory
     *
     * @access private
     * @var string
     */
    private $pluginDir;

    /**
     * Plugin base name
     *
     * @access private
     * @var string
     */
    private $pluginBaseName;

    /**
     * Plugin filename
     *
     * @access private
     * @var string
     */
    private $pluginFilename;

    /**
     * Kocuj Internal Lib object instance
     *
     * @access private
     * @var object
     */
    private $kocujILObj = null;

    /**
     * Kocuj.pl Lib object instance
     *
     * @access private
     * @var object
     */
    private $kocujPlLibObj = null;

    /**
     * Constructor
     *
     * @access private
     * @param string $pluginFile
     *            Plugin file path
     * @return void
     */
    private function __construct($pluginFile)
    {
        // get plugin URL
        $this->pluginUrl = plugins_url('', $pluginFile);
        // get plugin directory
        $this->pluginDir = dirname($pluginFile);
        // get plugin base name
        $this->pluginBaseName = plugin_basename($pluginFile);
        // get plugin filename
        $this->pluginFilename = $pluginFile;
        // add initialize
        add_action('plugins_loaded', array(
            $this,
            'init'
        ), 10);
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
     * @param string $pluginFile
     *            Plugin file path; first execution of "getInstance()" method should set this to plugin file path, but all others executions can ommit this parameter - default: empty
     * @return object Singleton instance
     */
    public static function getInstance($pluginFile = '')
    {
        // optionally create new instance
        if (!self::$instance) {
            self::$instance = new self($pluginFile);
        }
        // exit
        return self::$instance;
    }

    /**
     * Initialize
     *
     * @access public
     * @return void
     * @throws KocujIL\Classes\Exception
     * @throws \KocujPlLib\V13a\Classes\Exception
     */
    public function init()
    {
        // load translations
        $domain = 'kocuj-sitemap';
        $locale = apply_filters('plugin_locale', get_locale(), $domain);
        load_textdomain($domain,
            WP_LANG_DIR . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . $domain . '-' . $locale . '.mo');
        load_plugin_textdomain($domain, false, dirname($this->pluginBaseName) . DIRECTORY_SEPARATOR . 'languages');
        // initialize Kocuj Internal Lib
        include $this->pluginDir . DIRECTORY_SEPARATOR . 'libs' . DIRECTORY_SEPARATOR . 'kocuj-internal-lib' . DIRECTORY_SEPARATOR . 'kocuj-internal-lib.php';
        $this->kocujILObj = new KocujIL\Classes\Project(array(
            KocujIL\Enums\ProjectCategory::ALL => array(
                'js-ajax',
                'options',
                'widget',
                'window',
                'version'
            ),
            KocujIL\Enums\ProjectCategory::BACKEND => array(
                'editor-text-buttons',
                'editor-visual-buttons',
                'installation-date',
                'license',
                'message',
                'page-about',
                'page-restore',
                'page-uninstall',
                'plugin-uninstall',
                'review-message',
                'settings-fields',
                'settings-form',
                'settings-help',
                'settings-menu',
                'settings-meta-boxes',
                'update-message'
            )
        ), array(
            'type' => KocujIL\Enums\ProjectType::PLUGIN,
            'mainfilename' => $this->pluginFilename,
            'licensename' => 'GPL v2',
            'title' => __('Sitemap', 'kocuj-sitemap'),
            'name' => 'Kocuj Sitemap plugin',
            'version' => $this->getVersion(),
            'url' => 'http://kocujsitemap.wpplugin.kocuj.pl/',
            'titleoriginal' => 'Kocuj Sitemap'
        ), array(
            'author' => array(
                'name' => 'Dominik Kocuj',
                'url' => 'http://kocuj.pl/'
            ),
            'contactform' => 'http://kocujsitemap.wpplugin.kocuj.pl/contact/',
            'repository' => array(
                'main' => 'https://wordpress.org/plugins/kocuj-sitemap/',
                'translation' => 'https://translate.wordpress.org/projects/wp-plugins/kocuj-sitemap',
                'support' => 'https://wordpress.org/support/plugin/kocuj-sitemap'
            ),
            'newschannels' => array(
                'facebook' => 'http://www.facebook.com/kocujsitemap/',
                'rss' => 'http://kocujsitemap.wpplugin.kocuj.pl/en/feed/'
            ),
            'tellothers' => array(
                'facebook' => 'http://www.facebook.com/sharer/sharer.php?u=' . urlencode('http://kocujsitemap.wpplugin.kocuj.pl/'),
                'twitter' => 'http://twitter.com/share?url=' . urlencode('http://kocujsitemap.wpplugin.kocuj.pl/') . '&text=' . urlencode(__('Very good #wordpress plugin for creating a #sitemap with #multilingualism and #cache.',
                        'kocuj-sitemap'))
            )
        ), array(
            KocujIL\Enums\ProjectCategory::ALL => array(
                'options' => '\\KocujSitemapPlugin\\Classes\\KocujILStrings\\All\\Options',
                'widget' => '\\KocujSitemapPlugin\\Classes\\KocujILStrings\\All\\Widget',
                'window' => '\\KocujSitemapPlugin\\Classes\\KocujILStrings\\All\\Window'
            ),
            KocujIL\Enums\ProjectCategory::BACKEND => array(
                'license' => '\\KocujSitemapPlugin\\Classes\\KocujILStrings\\Backend\\License',
                'page-about' => '\\KocujSitemapPlugin\\Classes\\KocujILStrings\\Backend\\PageAbout',
                'page-restore' => '\\KocujSitemapPlugin\\Classes\\KocujILStrings\\Backend\\PageRestore',
                'page-uninstall' => '\\KocujSitemapPlugin\\Classes\\KocujILStrings\\Backend\\PageUninstall',
                'review-message' => '\\KocujSitemapPlugin\\Classes\\KocujILStrings\\Backend\\ReviewMessage',
                'settings-form' => '\\KocujSitemapPlugin\\Classes\\KocujILStrings\\Backend\\SettingsForm',
                'settings-menu' => '\\KocujSitemapPlugin\\Classes\\KocujILStrings\\Backend\\SettingsMenu',
                'update-message' => '\\KocujSitemapPlugin\\Classes\\KocujILStrings\\Backend\\UpdateMessage'
            )
        ));
        // initialize Kocuj.pl Lib
        include $this->pluginDir . DIRECTORY_SEPARATOR . 'libs' . DIRECTORY_SEPARATOR . 'kocuj-pl-lib' . DIRECTORY_SEPARATOR . 'kocuj-pl-lib.php';
        if ((is_admin()) || (is_network_admin())) {
            $this->kocujPlLibObj = new Project($this->kocujILObj, array(
                KocujIL\Enums\ProjectCategory::BACKEND => array(
                    'add-thanks',
                    'page-about-add-thanks',
                    'review-message-add-thanks',
                    'settings-adv-meta-boxes'
                )
            ), array(
                'api' => array(
                    'url' => 'http://api.kocuj.pl/',
                    'login' => 'kocujsitemapplugin',
                    'password' => 'K0cmDi_98XifA'
                ),
                'additionalwebsites' => array(
                    'kocujsitemap.wpplugin.kocuj.pl'
                )
            ), array(
                KocujIL\Enums\ProjectCategory::BACKEND => array(
                    'add-thanks' => '\\KocujSitemapPlugin\\Classes\\KocujPlLibStrings\\Backend\\AddThanks',
                    'add-thanks-ajax' => '\\KocujSitemapPlugin\\Classes\\KocujPlLibStrings\\Backend\\AddThanksAjax',
                    'settings-adv-meta-boxes' => '\\KocujSitemapPlugin\\Classes\\KocujPlLibStrings\\Backend\\SettingsAdvMetaBoxes',
                    'page-about-add-thanks' => '\\KocujSitemapPlugin\\Classes\\KocujPlLibStrings\\Backend\\PageAboutAddThanks',
                    'review-message-add-thanks' => '\\KocujSitemapPlugin\\Classes\\KocujPlLibStrings\\Backend\\ReviewMessageAddThanks'
                )
            ), array(
                'KocujIL' => $this->kocujILObj
            ));
        }
        // initialize widget
        $this->kocujILObj->get('widget',
            KocujIL\Enums\ProjectCategory::ALL)->addWidget('KocujSitemapPlugin\\Classes\\Widget');
        // initialize some classes
        Sitemap::getInstance();
        // set plugin version
        $this->kocujILObj->get('version', KocujIL\Enums\ProjectCategory::ALL)->setCurrentVersion($this->getVersion());
        // update version data
        $versions = array( // for compatibility with older versions
            'do_not_check_for_1_x_x' => 'kocujsitemap_version',
            // for compatibility with older versions
            'do_not_check_for_2_1_0' => $this->kocujILObj->getMainSettingInternalName() . '__version'
            // for compatibility with older versions
        ); // for compatibility with older versions
        foreach ($versions as $versionFlag => $versionOptionName) { // for compatibility with older versions
            $value = $this->kocujILObj->get('meta')->get($versionFlag); // for compatibility with older versions
            if ($value === false) { // for compatibility with older versions
                $value = get_option($versionOptionName); // for compatibility with older versions
                if ($value !== false) { // for compatibility with older versions
                    $this->kocujILObj->get('meta')->addOrUpdate(KocujIL\Classes\Project\Components\All\Version\Component::getOptionNameVersion(),
                        $value); // for compatibility with older versions
                    delete_option($versionOptionName); // for compatibility with older versions
                } // for compatibility with older versions
                $this->kocujILObj->get('meta')->addOrUpdate($versionFlag, '1'); // for compatibility with older versions
            } // for compatibility with older versions
        } // for compatibility with older versions
        // set plugin update callback
        $this->kocujILObj->get('version', KocujIL\Enums\ProjectCategory::ALL)->setUpdateCallbacks(array(
            $this,
            'updateCallback'
        ), array(
            $this,
            'siteUpdateCallback'
        ));
        // initialize backend and options
        if ((is_admin()) || (is_network_admin())) {
            // initialize backend
            Backend::getInstance();
        } else {
            // initialize options
            Options::getInstance();
        }
    }

    /**
     * Update callback
     *
     * @access public
     * @param string $fromVersion
     *            From version
     * @param string $toVersion
     *            To version
     * @return bool Update has been executed correctly (true) or not (false)
     */
    public function updateCallback($fromVersion, $toVersion)
    {
        // change to main site
        $siteSwitched = false;
        if (is_multisite()) {
            if (!is_main_site()) {
                global $current_site;
                switch_to_blog($current_site->blog_id);
                $siteSwitched = true;
            }
        }
        // update from 1.x.x
        if (version_compare($fromVersion, '2.0.0', '<')) { // for compatibility with 1.x.x
            // remove old cache directory
            $oldCacheDir = Base::getInstance()->getPluginDir() . DIRECTORY_SEPARATOR . 'cache'; // for compatibility with 1.x.x
            if (is_dir($oldCacheDir)) { // for compatibility with 1.x.x
                $filesList = array(); // for compatibility with 1.x.x
                KocujIL\Classes\FilesHelper::getInstance()->removeFiles($oldCacheDir,
                    $filesList); // for compatibility with 1.x.x
                @rmdir($oldCacheDir); // for compatibility with 1.x.x
            } // for compatibility with 1.x.x
            // remove cron schedule
            wp_clear_scheduled_hook('kocujsitemapcron'); // for compatibility with 1.x.x
            // remove database values for old plugin versions
            delete_option('kocujsitemap_2_0_0_get_ready'); // for compatibility with 1.x.x (from transition version 1.3.2 and newer 1.3.3)
            delete_option('kocujsitemap_plugin_data_version'); // for compatibility with 1.x.x
            delete_option('kocujsitemap_thanks_date'); // for compatibility with 1.x.x
            // update sending website address ("thanks") status
            $value = maybe_unserialize(KocujIL\Classes\DbDataHelper::getInstance()->getOption('kocujsitemap_thanks',
                false, KocujIL\Enums\Area::SITE)); // for compatibility with 1.x.x
            if ($value !== false) { // for compatibility with 1.x.x
                $this->kocujILObj->get('meta')->addOrUpdate('thanks_added', '1'); // for compatibility with 1.x.x
                delete_option('kocujsitemap_thanks'); // for compatibility with 1.x.x
            } // for compatibility with 1.x.x
        } // for compatibility with 1.x.x
        // update from 2.1.x
        if (version_compare($fromVersion, '2.2.1', '<')) { // for compatibility with 2.1.x
            $names = array( // for compatibility with 2.1.x
                $this->kocujILObj->getMainSettingInternalName() . '__version' => 'version',
                // for compatibility with 2.1.x
                $this->kocujILObj->getMainSettingInternalName() . '__req_update_info' => 'req_update_info',
                // for compatibility with 2.1.x
                $this->kocujILObj->getMainSettingInternalName() . '__installation_date' => 'install_date',
                // for compatibility with 2.1.x
                $this->kocujILObj->getMainSettingInternalName() . '__review_message_closed' => 'review_msg_closed',
                // for compatibility with 2.1.x
                $this->kocujILObj->getMainSettingInternalName() . '__license_accept' => 'license_accept',
                // for compatibility with 2.1.x
                $this->kocujILObj->getMainSettingInternalName() . '__thanks_added' => 'thanks_added'
                // for compatibility with 2.1.x
            ); // for compatibility with 2.1.x
            foreach ($names as $oldName => $newName) { // for compatibility with 2.1.x
                $value = maybe_unserialize(KocujIL\Classes\DbDataHelper::getInstance()->getOption($oldName)); // for compatibility with 2.1.x
                if ($value !== false) { // for compatibility with 2.1.x
                    KocujIL\Classes\DbDataHelper::getInstance()->deleteOption($oldName); // for compatibility with 2.1.x
                    $this->kocujILObj->get('meta')->addOrUpdate($newName, $value); // for compatibility with 2.1.x
                } // for compatibility with 2.1.x
            } // for compatibility with 2.1.x
            $this->kocujILObj->get('meta')->forceRealUpdateNow(); // for compatibility with 2.1.x
        } // for compatibility with 2.1.x
        // update from 2.2.x
        if (version_compare($fromVersion, '2.3.0', '<')) { // for compatibility with 2.2.x
            $value = $this->kocujILObj->get('meta')->get('review_msg_closed'); // for compatibility with 2.2.x
            if ($value !== false) { // for compatibility with 2.2.x
                $this->kocujILObj->get('meta')->delete('review_msg_closed'); // for compatibility with 2.2.x
                $this->kocujILObj->get('meta')->addOrUpdate('msg_closed__review',
                    $value); // for compatibility with 2.2.x
            } // for compatibility with 2.2.x
        } // for compatibility with 2.2.x
        // update from 2.4.x
        if (version_compare($fromVersion, '2.5.0', '<')) { // for compatibility with 2.4.x
            $this->kocujILObj->get('meta')->addOrUpdate('old_version', $fromVersion); // for compatibility with 2.4.x
            $this->kocujILObj->get('meta')->delete('req_update_info'); // for compatibility with 2.4.x
            if (is_multisite()) { // for compatibility with 2.4.x
                delete_site_option('kocujsitemap_options_mu'); // for compatibility with 2.4.x
            } // for compatibility with 2.4.x
        } // for compatibility with 2.4.x
        // change back to old site
        if ((is_multisite()) && ($siteSwitched)) {
            restore_current_blog();
        }
        // purge cache
        try {
            Cache::getInstance()->purgeCache();
        } catch (\Exception $e) {
        }
        // exit
        return true;
    }

    /**
     * Site update callback
     *
     * @access public
     * @param string $fromVersion
     *            From version
     * @param string $toVersion
     *            To version
     * @return bool Update has been executed correctly (true) or not (false)
     */
    public function siteUpdateCallback($fromVersion, $toVersion)
    {
        // update options name and automatic loading in database for 2.5.0
        if (version_compare($fromVersion, '2.5.0', '<')) { // for compatibility with versions older than 2.5.0
            $value = get_option('kocuj_sitemap_plugin__options'); // for compatibility with versions older than 2.5.0
            if ($value !== false) { // for compatibility with versions older than 2.5.0
                delete_option('kocuj_sitemap_plugin__options'); // for compatibility with versions older than 2.5.0
                add_option('kocuj_sitemap_plugin__options', $value, '',
                    true); // for compatibility with versions older than 2.5.0
            } // for compatibility with versions older than 2.5.0
            $value = maybe_unserialize(KocujIL\Classes\DbDataHelper::getInstance()->getOption('kocujsitemap_options',
                false, KocujIL\Enums\Area::SITE)); // for compatibility with versions older than 2.5.0
            if ($value !== false) { // for compatibility with versions older than 2.5.0
                if ((isset($value['OrderList'])) && ($value['OrderList'] === false)) { // for compatibility with versions older than 2.5.0
                    unset($value['OrderList']); // for compatibility with versions older than 2.5.0
                } // for compatibility with versions older than 2.5.0
                foreach ($value as $key => $val) { // for compatibility with versions older than 2.5.0
                    if (!is_array($val)) { // for compatibility with versions older than 2.5.0
                        $value[$key] = (string)$val; // for compatibility with versions older than 2.5.0
                    } // for compatibility with versions older than 2.5.0
                } // for compatibility with versions older than 2.5.0
                if (isset($value['MultiLang_kocujsitemap_qtranslate'])) { // for compatibility with versions older than 2.5.0
                    unset($value['MultiLang_kocujsitemap_qtranslate']); // for compatibility with versions older than 2.5.0
                } // for compatibility with versions older than 2.5.0
                delete_option('kocujsitemap_options'); // for compatibility with versions older than 2.5.0
                KocujIL\Classes\DbDataHelper::getInstance()->addOrUpdateOption('kocuj_sitemap_plugin__options', $value,
                    true, KocujIL\Enums\Area::SITE); // for compatibility with versions older than 2.5.0
            } // for compatibility with versions older than 2.5.0
        } // for compatibility with versions older than 2.5.0
        // update from 1.x.x
        if (version_compare($fromVersion, '2.0.0', '<')) { // for compatibility with 1.x.x
            // update old options
            $value = maybe_unserialize(KocujIL\Classes\DbDataHelper::getInstance()->getOption('kocuj_sitemap_plugin__options',
                false, KocujIL\Enums\Area::SITE)); // for compatibility with 1.x.x
            if ($value !== false) { // for compatibility with 1.x.x
                if (isset($value['Order'])) { // for compatibility with 1.x.x
                    $optionValue = $value['Order']; // for compatibility with 1.x.x
                    if (isset($optionValue[0]) /* strlen($optionValue) > 0 */) { // for compatibility with 1.x.x
                        $def = str_split($optionValue); // for compatibility with 1.x.x
                        if (!in_array('M', $def)) { // for compatibility with 1.x.x
                            array_unshift($def, 'M'); // for compatibility with 1.x.x
                        } // for compatibility with 1.x.x
                        $def[] = 'C'; // for compatibility with 1.x.x
                        $def[] = 'A'; // for compatibility with 1.x.x
                        $def[] = 'T'; // for compatibility with 1.x.x
                        unset($value['Order']); // for compatibility with 1.x.x
                        $value['OrderList'] = $def; // for compatibility with 1.x.x
                        KocujIL\Classes\DbDataHelper::getInstance()->addOrUpdateOption('kocuj_sitemap_plugin__options',
                            $value, true, KocujIL\Enums\Area::SITE); // for compatibility with 1.x.x
                    } // for compatibility with 1.x.x
                } // for compatibility with 1.x.x
                // disable displaying sections if it is an update from 1.x.x
                $value['DisplaySections'] = '0'; // for compatibility with 1.x.x
                KocujIL\Classes\DbDataHelper::getInstance()->addOrUpdateOption('kocuj_sitemap_plugin__options', $value,
                    true, KocujIL\Enums\Area::SITE); // for compatibility with 1.x.x
            } // for compatibility with 1.x.x
        } // for compatibility with 1.x.x
        // update from 2.0.x
        if (version_compare($fromVersion, '2.1.0', '<')) { // for compatibility with 2.0.x
            $value = maybe_unserialize(KocujIL\Classes\DbDataHelper::getInstance()->getOption('kocuj_sitemap_plugin__options',
                false, KocujIL\Enums\Area::SITE)); // for compatibility with 2.0.x
            if ($value !== false) { // for compatibility with 2.0.x
                if (isset($value['Multilang'])) { // for compatibility with 2.0.x
                    $value['Multilang'] = str_replace('____B_A_C_K_S_P_A_C_E____', '/',
                        $value['Multilang']); // for compatibility with 2.0.x
                    KocujIL\Classes\DbDataHelper::getInstance()->addOrUpdateOption('kocuj_sitemap_plugin__options',
                        $value, true, KocujIL\Enums\Area::SITE); // for compatibility with 2.0.x
                } // for compatibility with 2.0.x
            } // for compatibility with 2.0.x
        } // for compatibility with 2.0.x
        // update from 2.4.x
        if (version_compare($fromVersion, '2.5.0', '<')) { // for compatibility with 2.4.x
            $value = maybe_unserialize(KocujIL\Classes\DbDataHelper::getInstance()->getOption('kocujsitemap_widget_sitemap',
                false, KocujIL\Enums\Area::SITE)); // for compatibility with 2.4.x
            if ($value !== false) { // for compatibility with 2.4.x
                $valueWidgets = maybe_unserialize(KocujIL\Classes\DbDataHelper::getInstance()->getOption('sidebars_widgets',
                    false, KocujIL\Enums\Area::SITE)); // for compatibility with 2.4.x
                if ($valueWidgets !== false) { // for compatibility with 2.4.x
                    foreach ($valueWidgets as $sidebarKey => $sidebarData) { // for compatibility with 2.4.x
                        if ((!empty($sidebarData)) && (is_array($sidebarData))) { // for compatibility with 2.4.x
                            $newWidgetData = array(); // for compatibility with 2.4.x
                            foreach ($sidebarData as $widgetKey => $widgetData) { // for compatibility with 2.4.x
                                $oldName = 'kocujsitemap_widget_sitemap'; // for compatibility with 2.4.x
                                if (substr($widgetData, 0,
                                        strlen($oldName)) === $oldName) { // for compatibility with 2.4.x
                                    $newWidgetData[$widgetKey] = 'kocuj_sitemap_plugin__widget-2'; // for compatibility with 2.4.x
                                } else { // for compatibility with 2.4.x
                                    $newWidgetData[$widgetKey] = $widgetData; // for compatibility with 2.4.x
                                } // for compatibility with 2.4.x
                            } // for compatibility with 2.4.x
                            $valueWidgets[$sidebarKey] = $newWidgetData; // for compatibility with 2.4.x
                        } // for compatibility with 2.4.x
                    } // for compatibility with 2.4.x
                    KocujIL\Classes\DbDataHelper::getInstance()->addOrUpdateOption('sidebars_widgets', $valueWidgets,
                        true, KocujIL\Enums\Area::SITE); // for compatibility with 2.4.x
                    $names = array( // for compatibility with 2.4.x
                        'kocujsitemap_widget_sitemap_title' => 'title',
                        // for compatibility with 2.4.x
                        'kocujsitemap_widget_sitemap_displaytitle' => 'display_title',
                        // for compatibility with 2.4.x
                        'kocujsitemap_widget_sitemap_exclude_post' => 'exclude_post',
                        // for compatibility with 2.4.x
                        'kocujsitemap_widget_sitemap_exclude_category' => 'exclude_category',
                        // for compatibility with 2.4.x
                        'kocujsitemap_widget_sitemap_exclude_author' => 'exclude_author',
                        // for compatibility with 2.4.x
                        'kocujsitemap_widget_sitemap_exclude_term' => 'exclude_term'
                        // for compatibility with 2.4.x
                    ); // for compatibility with 2.4.x
                    foreach ($names as $oldName => $newName) { // for compatibility with 2.4.x
                        if (isset($value[$oldName])) { // for compatibility with 2.4.x
                            $value[$newName] = (string)$value[$oldName]; // for compatibility with 2.4.x
                            unset($value[$oldName]); // for compatibility with 2.4.x
                        } // for compatibility with 2.4.x
                    } // for compatibility with 2.4.x
                    $value = array( // for compatibility with 2.4.x
                        2 => $value, // for compatibility with 2.4.x
                        '_multiwidget' => 1 // for compatibility with 2.4.x
                    ); // for compatibility with 2.4.x
                    delete_option('kocujsitemap_widget_sitemap'); // for compatibility with 2.4.x
                    KocujIL\Classes\DbDataHelper::getInstance()->addOrUpdateOption('widget_kocuj_sitemap_plugin__widget',
                        $value, true, KocujIL\Enums\Area::SITE); // for compatibility with 2.4.x
                } // for compatibility with 2.4.x
            } // for compatibility with 2.4.x
        } // for compatibility with 2.4.x
        // clear cache
        try {
            Cache::getInstance()->clearCache();
        } catch (\Exception $e) {
        }
        // exit
        return true;
    }

    /**
     * Get major version
     *
     * @access public
     * @return int Major version
     */
    public function getVersionMajor()
    {
        // get major version
        return 2;
    }

    /**
     * Get minor version
     *
     * @access public
     * @return int Minor version
     */
    public function getVersionMinor()
    {
        // get minor version
        return 7;
    }

    /**
     * Get revision version
     *
     * @access public
     * @return int Revision version
     */
    public function getVersionRevision()
    {
        // get revision version
        return 0;
    }

    /**
     * Get version
     *
     * @access public
     * @return string Version
     */
    public function getVersion()
    {
        // get version
        return $this->getVersionMajor() . '.' . $this->getVersionMinor() . '.' . $this->getVersionRevision();
    }

    /**
     * Get plugin URL
     *
     * @access public
     * @return string Plugin URL
     */
    public function getPluginUrl()
    {
        // get plugin URL
        return $this->pluginUrl;
    }

    /**
     * Get plugin directory
     *
     * @access public
     * @return string Plugin directory
     */
    public function getPluginDir()
    {
        // get plugin directory
        return $this->pluginDir;
    }

    /**
     * Get plugin base name
     *
     * @access public
     * @return string Plugin base name
     */
    public function getPluginBaseName()
    {
        // get plugin base name
        return $this->pluginBaseName;
    }

    /**
     * Get plugin filename
     *
     * @access public
     * @return string Plugin filename
     */
    public function getPluginFilename()
    {
        // get plugin filename
        return $this->pluginFilename;
    }

    /**
     * Get Kocuj Internal Lib object
     *
     * @access public
     * @return object Kocuj Internal Lib object
     */
    public function getKocujILObj()
    {
        // get Kocuj Internal Lib object
        return $this->kocujILObj;
    }

    /**
     * Get Kocuj.pl Lib object
     *
     * @access public
     * @return object Kocuj.pl Lib object
     */
    public function getKocujPlLibObj()
    {
        // get Kocuj.pl Lib object
        return $this->kocujPlLibObj;
    }
}
