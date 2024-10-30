<?php

/**
 * backend.class.php
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
 * Backend class
 *
 * @access public
 */
class Backend
{

    /**
     * Singleton instance
     *
     * @access private
     * @var object
     */
    private static $instance = null;

    /**
     * Administration pages data
     *
     * @access private
     * @var array
     */
    private $adminPagesData = array();

    /**
     * Constructor
     *
     * @access private
     * @return void
     */
    private function __construct()
    {
        // initialize options
        Options::getInstance();
        // set data uninstallation option
        Base::getInstance()->getKocujILObj()
            ->get('page-uninstall', KocujIL\Enums\ProjectCategory::BACKEND)
            ->setOption('uninstall', 'UninstallRemoveData');
        // set review message
        Base::getInstance()->getKocujILObj()
            ->get('review-message', KocujIL\Enums\ProjectCategory::BACKEND)
            ->setDays(30);
        Base::getInstance()->getKocujILObj()
            ->get('review-message', KocujIL\Enums\ProjectCategory::BACKEND)
            ->setVoteUrl('https://wordpress.org/support/plugin/kocuj-sitemap/reviews/');
        // add action for initialization of settings
        add_action('init', array(
            $this,
            'actionInitSettingsPages'
        ), 9999);
        // optionally add buttons to editor
        $value = Options::getInstance()->getOption('ButtonEditor');
        if ($value === '1') {
            // add quick tags
            Base::getInstance()->getKocujILObj()
                ->get('editor-text-buttons', KocujIL\Enums\ProjectCategory::BACKEND)
                ->addButton('kocujsitemap', __('sitemap', 'kocuj-sitemap'),
                    __('Add sitemap shortcode', 'kocuj-sitemap'), '[KocujSitemap]', '');
            // add TinyMCE buttons
            Base::getInstance()->getKocujILObj()
                ->get('editor-visual-buttons', KocujIL\Enums\ProjectCategory::BACKEND)
                ->addButton('kocujsitemap',
                    KocujIL\Classes\JsHelper::getInstance()->getMinJsFilename('kocujsitemap-plugin'),
                    'kocujsitemap-plugin-langs.php');
        }
        // check if cache directory is writable
        $value = Options::getInstance()->getOption('Cache');
        if (($value === '1') && (!Cache::getInstance()->checkWritable())) {
            if (current_user_can('manage_options')) {
                /* translators: 1: name of this plugin ("Kocuj Sitemap"), 2: root directory name with cache for this plugin (usually "/wp-content/cache/kocuj-sitemap/"), 3: subdirectory name with cache for this plugin (for example, "/wp-content/cache/kocuj-sitemap/1/") */
                Base::getInstance()->getKocujILObj()
                    ->get('message', KocujIL\Enums\ProjectCategory::BACKEND)
                    ->addMessageForAllPages('cache_error',
                        '<strong>' . sprintf(__('Warning! Your cache directory for sitemap in %1$s plugin (placed in "%2$s") or its current subdirectory (placed in "%3$s") is not writable. Please, create this directory and change its permissions to allow the plugin to use cache for better performance.',
                            'kocuj-sitemap'), 'Kocuj Sitemap', Cache::getInstance()->getCacheRootDirectory(),
                            Cache::getInstance()->getCacheDirectory()) . '</strong>',
                        KocujIL\Enums\Project\Components\Backend\Message\Type::ERROR);
            } else {
                /* translators: 1: name of this plugin ("Kocuj Sitemap"), 2: root directory name with cache for this plugin (usually "/wp-content/cache/kocuj-sitemap/"), 3: subdirectory name with cache for this plugin (for example, "/wp-content/cache/kocuj-sitemap/1/") */
                Base::getInstance()->getKocujILObj()
                    ->get('message', KocujIL\Enums\ProjectCategory::BACKEND)
                    ->addMessageForAllPages('cache_error',
                        '<strong>' . sprintf(__('Warning! Your cache directory for sitemap in %1$s plugin (placed in "%2$s") or its current subdirectory (placed in "%3$s") is not writable. Please, contact with user who is managing permissions to allow the plugin to use cache for better performance.',
                            'kocuj-sitemap'), 'Kocuj Sitemap', Cache::getInstance()->getCacheRootDirectory(),
                            Cache::getInstance()->getCacheDirectory()) . '</strong>',
                        KocujIL\Enums\Project\Components\Backend\Message\Type::ERROR);
            }
        }
        // add beginning of information about version changes
        /* translators: 1: name of this plugin ("Kocuj Sitemap"), 2: old version of this plugin, 3: plugin version after update */
        $informationBegin = sprintf(__('Thank you for upgrading the %1$s plugin from version %2$s to version %3$s.',
            'kocuj-sitemap'), 'Kocuj Sitemap', '%1$s', '%2$s');
        $informationBegin .= '<br /><br />';
        /* translators: %s: New version number */
        $informationBegin .= sprintf(__('There are some new features in version %s which requires some information to read. You can read below a few information about these changes.',
            'kocuj-sitemap'), '2.6');
        $informationBegin .= '<br /><br /><br />';
        $informationBegin .= '<hr />';
        $informationBegin .= '<br />';
        // set styles for information about version changes
        $imageStyle = 'float:left;margin:0;padding:0;margin-right:5px;';
        $titleStyle = 'margin-top:8px;';
        // add information about version changes for 2.6.0
        $information = $informationBegin;
        $information .= '<br /><br />';
        $information26 = KocujIL\Classes\HtmlHelper::getInstance()->getImage(Base::getInstance()->getKocujILObj()
                    ->get('urls')
                    ->get('customimages') . '/changes2-6-hide.png', array(
                'style' => $imageStyle
            )) . '<h2 style="' . $titleStyle . '">' . __('Possibility to hide element types from the sitemap generated by shortcode',
                'kocuj-sitemap') . '</h2>';
        $information26 .= '<br />';
        /* translators: 1: name of this plugin ("Kocuj Sitemap"), 2: name of shortcode parameter */
        $information26 .= __('You can hide selected element types from the sitemap. This feature is useful when you want to have more than one sitemap and each should have different set of element types.',
                'kocuj-sitemap') . '<br /><br />' . sprintf(__('To hide selected element types simply use the shortcode %1$s with a parameter %2$s which contains the list of element types to hide. There are the following element types available:',
                'kocuj-sitemap'), '<em>[KocujSitemap]</em>',
                '<em>hidetypes</em>') . '<ul style="list-style:disc;margin-left:15px;"><li><em>author</em> - ' . __('to hide authors',
                'kocuj-sitemap') . ';</li><li><em>custom</em> - ' . __('to hide custom post types',
                'kocuj-sitemap') . ';</li><li><em>home</em> - ' . __('to hide link to homepage',
                'kocuj-sitemap') . ';</li><li><em>menu</em> - ' . __('to hide menus',
                'kocuj-sitemap') . ';</li><li><em>page</em> - ' . __('to hide pages',
                'kocuj-sitemap') . ';</li><li><em>post</em> - ' . __('to hide posts',
                'kocuj-sitemap') . ';</li><li><em>tag</em> - ' . __('to hide tags',
                'kocuj-sitemap') . '.</li></ul>' . __('For example:',
                'kocuj-sitemap') . '<br /><code>[KocujSitemap hidetypes="page"]</code><br />' . __('hides all pages.',
                'kocuj-sitemap') . '<br /><br />' . __('This feature is also available for widget in its settings.',
                'kocuj-sitemap');
        $information26 .= '<br /><br />';
        $information26 .= '<hr />';
        $information26 .= '<br />';
        $information26 .= KocujIL\Classes\HtmlHelper::getInstance()->getImage(Base::getInstance()->getKocujILObj()
                    ->get('urls')
                    ->get('customimages') . '/changes2-6-dropdown.png', array(
                'style' => $imageStyle
            )) . '<h2 style="' . $titleStyle . '">' . __('Sitemap as drop-down list in widget',
                'kocuj-sitemap') . '</h2>';
        $information26 .= '<br />';
        $information26 .= __('There is possibility to display the sitemap in widget as drop-down list.',
            'kocuj-sitemap');
        $information .= $information26;
        Base::getInstance()->getKocujILObj()
            ->get('update-message', KocujIL\Enums\ProjectCategory::BACKEND)
            ->addUpdateMessage('2.5.', $information,
                KocujIL\Enums\Project\Components\Backend\UpdateMessage\UseTopMessage::YES);
        // add information about version changes for 2.5.0
        $information = $informationBegin;
        $information .= '<br /><br />';
        $information25 = KocujIL\Classes\HtmlHelper::getInstance()->getImage(Base::getInstance()->getKocujILObj()
                    ->get('urls')
                    ->get('customimages') . '/changes2-5-duplicates.png', array(
                'style' => $imageStyle
            )) . '<h2 style="' . $titleStyle . '">' . __('Possibility to remove duplicates from the sitemap',
                'kocuj-sitemap') . '</h2>';
        $information25 .= '<br />';
        $information25 .= __('Duplicated entries in menus can be removed by checking corresponded option.',
            'kocuj-sitemap');
        $information .= $information25;
        Base::getInstance()->getKocujILObj()
            ->get('update-message', KocujIL\Enums\ProjectCategory::BACKEND)
            ->addUpdateMessage('2.3.', $information,
                KocujIL\Enums\Project\Components\Backend\UpdateMessage\UseTopMessage::YES);
        Base::getInstance()->getKocujILObj()
            ->get('update-message', KocujIL\Enums\ProjectCategory::BACKEND)
            ->addUpdateMessage('2.4.', $information,
                KocujIL\Enums\Project\Components\Backend\UpdateMessage\UseTopMessage::YES);
        // add information about version changes for 2.3.0
        $information = $informationBegin;
        $information .= '<br /><br />';
        $information23 = KocujIL\Classes\HtmlHelper::getInstance()->getImage(Base::getInstance()->getKocujILObj()
                    ->get('urls')
                    ->get('customimages') . '/changes2-3-qtranslate.png', array(
                'style' => $imageStyle
            )) . '<h2 style="' . $titleStyle . '">' . __('Removing of support for old multi-lingual plugin - qTranslate',
                'kocuj-sitemap') . '</h2>';
        $information23 .= '<br />';
        $information23 .= __('Support for multi-lingual plugin - qTranslate - has been removed. The qTranslate plugin is very old and has been not updated for a very long time. There is better version of qTranslate plugin which is available with name qTranslate X and this plugin is supported.',
            'kocuj-sitemap');
        $information .= $information23;
        $information .= '<br /><br />';
        $information .= '<hr />';
        $information .= '<br />';
        $information .= $information25;
        Base::getInstance()->getKocujILObj()
            ->get('update-message', KocujIL\Enums\ProjectCategory::BACKEND)
            ->addUpdateMessage('2.1.', $information,
                KocujIL\Enums\Project\Components\Backend\UpdateMessage\UseTopMessage::YES);
        Base::getInstance()->getKocujILObj()
            ->get('update-message', KocujIL\Enums\ProjectCategory::BACKEND)
            ->addUpdateMessage('2.2.', $information,
                KocujIL\Enums\Project\Components\Backend\UpdateMessage\UseTopMessage::YES);
        // add information about version changes for 2.1.0
        $information = $informationBegin;
        $information .= '<br /><br />';
        $information21 = KocujIL\Classes\HtmlHelper::getInstance()->getImage(Base::getInstance()->getKocujILObj()
                    ->get('urls')
                    ->get('customimages') . '/changes2-1-margin.png', array(
                'style' => $imageStyle
            )) . '<h2 style="' . $titleStyle . '">' . __('Forcing left margin for multi-level list',
                'kocuj-sitemap') . '</h2>';
        $information21 .= '<br />';
        $information21 .= __('Some themes can\'t display a multi-level list correctly because there are no left margins applied to some list elements. However, from now you can force left margin in pixels for each level on multi-level sitemap, if you have these problems.',
            'kocuj-sitemap');
        $information21 .= '<br /><br />';
        $information21 .= '<hr />';
        $information21 .= '<br />';
        $information21 .= KocujIL\Classes\HtmlHelper::getInstance()->getImage(Base::getInstance()->getKocujILObj()
                    ->get('urls')
                    ->get('customimages') . '/changes2-1-php7.png', array(
                'style' => $imageStyle
            )) . '<h2 style="' . $titleStyle . '">' . __('This plugin is fully compatible with PHP 7',
                'kocuj-sitemap') . '</h2>';
        $information21 .= '<br />';
        $information21 .= __('This plugin has been changed to be fully compatible with PHP 7 and newer versions.',
            'kocuj-sitemap');
        $information .= $information21;
        $information .= '<br /><br />';
        $information .= '<hr />';
        $information .= '<br />';
        $information .= $information23;
        $information .= '<br /><br />';
        $information .= '<hr />';
        $information .= '<br />';
        $information .= $information25;
        Base::getInstance()->getKocujILObj()
            ->get('update-message', KocujIL\Enums\ProjectCategory::BACKEND)
            ->addUpdateMessage('2.0.', $information,
                KocujIL\Enums\Project\Components\Backend\UpdateMessage\UseTopMessage::YES);
        // add information about version changes for 2.0.0
        $information = $informationBegin;
        $information .= '<br /><br />';
        $information .= KocujIL\Classes\HtmlHelper::getInstance()->getImage(Base::getInstance()->getKocujILObj()
                    ->get('urls')
                    ->get('customimages') . '/changes2-exclude.png', array(
                'style' => $imageStyle
            )) . '<h2 style="' . $titleStyle . '">' . __('Excluding selected posts, categories, pages, authors, tags or any other custom post types entries',
                'kocuj-sitemap') . '</h2>';
        $information .= '<br />';
        /* translators: %s: Shortcode name */
        $information .= __('You can exclude selected items from the sitemap. This feature is useful when you have articles that do not fit to the sitemap.',
                'kocuj-sitemap') . '<br /><br />' . sprintf(__('To exclude selected items simply use the shortcode %s with a parameter indicating the list of identifiers of elements to exclude. There are the following parameters:',
                'kocuj-sitemap'),
                '<em>[KocujSitemap]</em>') . '<ul style="list-style:disc;margin-left:15px;"><li><em>excludepost</em> - ' . __('for excluding posts of any type (posts, pages, custom post types entries)',
                'kocuj-sitemap') . ';</li><li><em>excludecategory</em> - ' . __('for excluding post categories',
                'kocuj-sitemap') . ';</li><li><em>excludeauthor</em> - ' . __('for excluding authors',
                'kocuj-sitemap') . ';</li><li><em>excludeterm</em> - ' . __('for excluding post tags and custom taxonomies',
                'kocuj-sitemap') . '.</li></ul>' . __('For example:',
                'kocuj-sitemap') . '<br /><code>[KocujSitemap excludepost="5,8" excludeterm="12"]</code><br />' . __('excludes posts with identifiers 5 and 8, and post tags and custom taxonomies with identifier 12.',
                'kocuj-sitemap') . '<br /><br />' . __('This feature is also available for widget in its settings.',
                'kocuj-sitemap');
        $information .= '<br /><br />';
        $information .= '<hr />';
        $information .= '<br />';
        $information .= KocujIL\Classes\HtmlHelper::getInstance()->getImage(Base::getInstance()->getKocujILObj()
                    ->get('urls')
                    ->get('customimages') . '/changes2-divide.png', array(
                'style' => $imageStyle
            )) . '<h2 style="' . $titleStyle . '">' . __('Dividing sitemap into sections', 'kocuj-sitemap') . '</h2>';
        $information .= '<br />';
        $information .= __('You can divide the sitemap into sections. Each section is then described with its title, for example, section with posts will have the title of `posts` and section with tags - `tags`.',
                'kocuj-sitemap') . '<br /><br />' . __('If the section title does not have a translation, you can manually enter its title in your language. Use the tab `section title`, which contains fields for entering the section titles for the individual languages available in your WordPress installation.',
                'kocuj-sitemap');
        $information .= '<br /><br />';
        $information .= '<hr />';
        $information .= '<br />';
        $information .= KocujIL\Classes\HtmlHelper::getInstance()->getImage(Base::getInstance()->getKocujILObj()
                    ->get('urls')
                    ->get('customimages') . '/changes2-entry.png', array(
                'style' => $imageStyle
            )) . '<h2 style="' . $titleStyle . '">' . __('Using all types of entries available in WordPress',
                'kocuj-sitemap') . '</h2>';
        $information .= '<br />';
        /* translators: %s: Name of this plugin ("Kocuj Sitemap") */
        $information .= sprintf(__('Currently %s plugin can use the following types of entries: menus, pages, posts, authors, tags and custom post types entries (with custom taxonomies).',
            'kocuj-sitemap'), 'Kocuj Sitemap');
        $information .= '<br /><br />';
        $information .= '<hr />';
        $information .= '<br />';
        $information .= KocujIL\Classes\HtmlHelper::getInstance()->getImage(Base::getInstance()->getKocujILObj()
                    ->get('urls')
                    ->get('customimages') . '/changes2-control.png', array(
                'style' => $imageStyle
            )) . '<h2 style="' . $titleStyle . '">' . __('More control of what will be displayed',
                'kocuj-sitemap') . '</h2>';
        $information .= '<br />';
        /* translators: %s: Name of this plugin ("Kocuj Sitemap") */
        $information .= sprintf(__('%s plugin now provides many options to change the way of what will be displayed on the sitemap. There is a large set of options for almost every type of entry that may be displayed on the sitemap.',
            'kocuj-sitemap'), 'Kocuj Sitemap');
        $information .= '<br /><br />';
        $information .= '<hr />';
        $information .= '<br />';
        $information .= KocujIL\Classes\HtmlHelper::getInstance()->getImage(Base::getInstance()->getKocujILObj()
                    ->get('urls')
                    ->get('customimages') . '/changes2-help.png', array(
                'style' => $imageStyle
            )) . '<h2 style="' . $titleStyle . '">' . __('Contextual help', 'kocuj-sitemap') . '</h2>';
        $information .= '<br />';
        $information .= __('This plugin has a large amount of help texts. In administration panel just click on the `help` button at the top of the screen to get help text about current screen with plugin settings.',
            'kocuj-sitemap');
        $information .= '<br /><br />';
        $information .= '<hr />';
        $information .= '<br />';
        $information .= KocujIL\Classes\HtmlHelper::getInstance()->getImage(Base::getInstance()->getKocujILObj()
                    ->get('urls')
                    ->get('customimages') . '/changes2-multisite.png', array(
                'style' => $imageStyle
            )) . '<h2 style="' . $titleStyle . '">' . __('Multisite support', 'kocuj-sitemap') . '</h2>';
        $information .= '<br />';
        $information .= __('This plugin can work also in multisite environment.', 'kocuj-sitemap');
        $information .= '<br /><br />';
        $information .= '<hr />';
        $information .= '<br />';
        $information .= $information21;
        $information .= '<br /><br />';
        $information .= '<hr />';
        $information .= '<br />';
        $information .= $information23;
        $information .= '<br /><br />';
        $information .= '<hr />';
        $information .= '<br />';
        $information .= $information25;
        Base::getInstance()->getKocujILObj()
            ->get('update-message', KocujIL\Enums\ProjectCategory::BACKEND)
            ->addUpdateMessage('1.', $information,
                KocujIL\Enums\Project\Components\Backend\UpdateMessage\UseTopMessage::YES);
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
     * Action for initialization of settings pages
     *
     * @access public
     * @return void
     */
    public function actionInitSettingsPages()
    {
        // add menu
        Base::getInstance()->getKocujILObj()
            ->get('settings-menu', KocujIL\Enums\ProjectCategory::BACKEND)
            ->addSettingsMenu(__('Sitemap', 'kocuj-sitemap'), 'manage_options', 'basic_settings', array(
                $this,
                'pluginForm'
            ), '', KocujIL\Enums\Project\Components\Backend\SettingsMenu\Type::SITE, array(
                'icon' => 'dashicons-search',
                'firstoptiontitle' => __('Basic settings', 'kocuj-sitemap'),
                'onpluginslist' => true,
                'pluginslisttitle' => __('Settings', 'kocuj-sitemap')
            ));
        Base::getInstance()->getKocujILObj()
            ->get('settings-menu', KocujIL\Enums\ProjectCategory::BACKEND)
            ->addSettingsMenu(__('Order of elements', 'kocuj-sitemap'), 'manage_options', 'order', array(
                $this,
                'pluginForm'
            ), 'basic_settings');
        $types = Sitemap::getInstance()->getElementsTypes();
        $addSettingsHelp = array();
        foreach ($types as $key => $val) {
            if ($val['object']->checkExists()) {
                $adminPageData = $val['objectadmin']->getAdminPageSettings();
                if (!empty($adminPageData)) {
                    $pos = count($this->adminPagesData);
                    $this->adminPagesData[$pos] = array(
                        'type' => $val,
                        'id' => $adminPageData['id'],
                        'issectionname' => $val['object']->checkSectionName()
                    );
                    Base::getInstance()->getKocujILObj()
                        ->get('settings-menu', KocujIL\Enums\ProjectCategory::BACKEND)
                        ->addSettingsMenu($adminPageData['title'], 'manage_options', $adminPageData['id'], array(
                            $this,
                            'pluginForm'
                        ), 'basic_settings');
                    if (!empty($adminPageData['help'])) {
                        foreach ($adminPageData['help'] as $key2 => $val2) {
                            $addSettingsHelp[] = array(
                                $adminPageData['id'],
                                $key2,
                                $val2['title'],
                                '<p>' . $val2['content'] . '</p>'
                            );
                        }
                    }
                }
            }
        }
        Base::getInstance()->getKocujILObj()
            ->get('settings-menu', KocujIL\Enums\ProjectCategory::BACKEND)
            ->addSettingsMenu(__('Restore default settings', 'kocuj-sitemap'), 'manage_options', 'restore', array(
                Base::getInstance()->getKocujILObj()
                    ->get('page-restore', KocujIL\Enums\ProjectCategory::BACKEND),
                'showPage'
            ), 'basic_settings');
        Base::getInstance()->getKocujILObj()
            ->get('settings-menu', KocujIL\Enums\ProjectCategory::BACKEND)
            ->addSettingsMenu(__('Plugin uninstallation options', 'kocuj-sitemap'), 'manage_options', 'uninstall',
                array(
                    Base::getInstance()->getKocujILObj()
                        ->get('page-uninstall', KocujIL\Enums\ProjectCategory::BACKEND),
                    'showPage'
                ), 'basic_settings');
        Base::getInstance()->getKocujILObj()
            ->get('page-about', KocujIL\Enums\ProjectCategory::BACKEND)
            ->setImageFilename('about2-5.jpg');
        /* translators: %s: Name of this plugin ("Kocuj Sitemap") */
        Base::getInstance()->getKocujILObj()
            ->get('settings-menu', KocujIL\Enums\ProjectCategory::BACKEND)
            ->addSettingsMenu(sprintf(__('Information about %s plugin', 'kocuj-sitemap'), 'Kocuj Sitemap'),
                'manage_options', 'about', array(
                    Base::getInstance()->getKocujILObj()
                        ->get('page-about', KocujIL\Enums\ProjectCategory::BACKEND),
                    'showPage'
                ), 'basic_settings');
        // check if current page is for current project
        $projectSettingsPage = Base::getInstance()->getKocujILObj()
            ->get('settings-menu', KocujIL\Enums\ProjectCategory::BACKEND)
            ->checkCurrentPageIsSettingsForProject();
        if ($projectSettingsPage) {
            // show information about author's website
            Base::getInstance()->getKocujPlLibObj()
                ->get('settings-adv-meta-boxes', KocujIL\Enums\ProjectCategory::BACKEND)
                ->setAuthorInfo(array(
                    'pl_PL'
                ));
            // add additional callback to controllers
            Base::getInstance()->getKocujILObj()
                ->get('settings-form', KocujIL\Enums\ProjectCategory::BACKEND)
                ->addController('save', array(
                    $this,
                    'controllerSaveOrRestore'
                ));
            Base::getInstance()->getKocujILObj()
                ->get('settings-form', KocujIL\Enums\ProjectCategory::BACKEND)
                ->addController('restore', array(
                    $this,
                    'controllerSaveOrRestore'
                ));
            // initialize forms
            Base::getInstance()->getKocujILObj()
                ->get('settings-form', KocujIL\Enums\ProjectCategory::BACKEND)
                ->addForm('settings', 'options', array(
                    'basic_settings'
                ), array(
                    'issubmit' => true,
                    'submitlabel' => __('Save settings', 'kocuj-sitemap'),
                    'submittooltip' => __('Save current settings', 'kocuj-sitemap')
                ));
            Base::getInstance()->getKocujILObj()
                ->get('settings-form', KocujIL\Enums\ProjectCategory::BACKEND)
                ->addForm('order', 'options', array(
                    'order'
                ), array(
                    'issubmit' => true,
                    'submitlabel' => __('Save order of elements', 'kocuj-sitemap'),
                    'submittooltip' => __('Save current order of elements', 'kocuj-sitemap')
                ));
            foreach ($this->adminPagesData as $val) {
                if (!empty($val['type']['objectadmin'])) {
                    $page = $val['type']['objectadmin']->getAdminPageData();
                    if ((!empty($page)) && (isset($page['tabs'])) && (!empty($page['tabs']))) {
                        $pageSettings = $val['type']['objectadmin']->getAdminPageSettings();
                        Base::getInstance()->getKocujILObj()
                            ->get('settings-form', KocujIL\Enums\ProjectCategory::BACKEND)
                            ->addForm($pageSettings['id'], 'options', array(
                                $pageSettings['id']
                            ), array(
                                'issubmit' => true,
                                'submitlabel' => $page['submit']['label'],
                                'submittooltip' => $page['submit']['tooltip']
                            ));
                    }
                }
            }
            // add forms options
            $multiLangOptions = array();
            $multiLangData = MultipleLanguagesData::getInstance()->getData();
            if (!empty($multiLangData)) {
                foreach ($multiLangData as $key => $val) {
                    $multiLangOptions[str_replace('\\', '/', $val['admin_id'])] = $val['admin_name'];
                }
                asort($multiLangOptions);
            }
            Base::getInstance()->getKocujILObj()
                ->get('settings-form', KocujIL\Enums\ProjectCategory::BACKEND)
                ->addTab('settings', 'format', __('Sitemap format', 'kocuj-sitemap'));
            Base::getInstance()->getKocujILObj()
                ->get('settings-form', KocujIL\Enums\ProjectCategory::BACKEND)
                ->addOptionFieldToTab('settings', 'format', 'checkbox', 'PoweredBy',
                    __('If this option is checked, there will be `powered by` information below the sitemap. It will also contains a link to the plugin\'s author website and link to the plugin\'s website. If you find this plugin useful, please check this option, because it is a very good solution for making an advertisement for its author.',
                        'kocuj-sitemap'));
            Base::getInstance()->getKocujILObj()
                ->get('settings-form', KocujIL\Enums\ProjectCategory::BACKEND)
                ->addOptionFieldToTab('settings', 'format', 'checkbox', 'PoweredByInWidget',
                    __('If this option is checked, there will be `powered by` information below the sitemap in widget. It will also contains a link to the plugin\'s author website and link to the plugin\'s website. If you find this plugin useful, please check this option, because it is a very good solution for making an advertisement for its author.',
                        'kocuj-sitemap'));
            Base::getInstance()->getKocujILObj()
                ->get('settings-form', KocujIL\Enums\ProjectCategory::BACKEND)
                ->addOptionFieldToTab('settings', 'format', 'checkbox', 'UseHTML5',
                    __('If this option is checked, the sitemap will display itself in HTML 5 navigation tag (`nav`) and the `powered by` information will display itself in HTML 5 footer tag (`footer`).',
                        'kocuj-sitemap'));
            Base::getInstance()->getKocujILObj()
                ->get('settings-form', KocujIL\Enums\ProjectCategory::BACKEND)
                ->addOptionFieldToTab('settings', 'format', 'checkbox', 'LinkToMainSite',
                    __('If this option is checked, there will be link to the main site as first entry in the sitemap.',
                        'kocuj-sitemap'));
            Base::getInstance()->getKocujILObj()
                ->get('settings-form', KocujIL\Enums\ProjectCategory::BACKEND)
                ->addOptionFieldToTab('settings', 'format', 'checkbox', 'DisplaySections',
                    __('If this option is checked, the sitemap will be divided into sections, for example, post, pages, etc.',
                        'kocuj-sitemap'));
            Base::getInstance()->getKocujILObj()
                ->get('settings-form', KocujIL\Enums\ProjectCategory::BACKEND)
                ->addOptionFieldToTab('settings', 'format', 'text', 'HLevelMain',
                    __('You can specify the header text level (HTML tag `H*`) for each section of the sitemap displayed by the shortcode or PHP function. The value which should be entered here can be different for different themes. If you are not sure what is the meaning of this tag in HTML, leave this value unchanged, because its default value should be correct for many WordPress themes.',
                        'kocuj-sitemap'));
            Base::getInstance()->getKocujILObj()
                ->get('settings-form', KocujIL\Enums\ProjectCategory::BACKEND)
                ->addOptionFieldToTab('settings', 'format', 'text', 'HLevelWidget',
                    __('You can specify the header text level (HTML tag `H*`) for each section of the sitemap displayed by the widget. The value which should be entered here can be different for different themes. If you are not sure what is the meaning of this tag in HTML, leave this value unchanged, because its default value should be correct for many WordPress themes.',
                        'kocuj-sitemap'));
            Base::getInstance()->getKocujILObj()
                ->get('settings-form', KocujIL\Enums\ProjectCategory::BACKEND)
                ->addTab('settings', 'adminpanel', __('Administration panel', 'kocuj-sitemap'));
            Base::getInstance()->getKocujILObj()
                ->get('settings-form', KocujIL\Enums\ProjectCategory::BACKEND)
                ->addOptionFieldToTab('settings', 'adminpanel', 'checkbox', 'ButtonEditor',
                    __('If this option is checked, there will be a button for adding a shortcode for sitemap in visual and HTML editor in administration panel.',
                        'kocuj-sitemap'));
            Base::getInstance()->getKocujILObj()
                ->get('settings-form', KocujIL\Enums\ProjectCategory::BACKEND)
                ->addTab('settings', 'multilang', __('Plugins for multilingualism', 'kocuj-sitemap'));
            $multiLangOptions = array_merge(array(
                '0' => __('Automatically select the plugin', 'kocuj-sitemap'),
                '//donotuse//' => __('Do not use any plugin for multiple languages', 'kocuj-sitemap')
            ), $multiLangOptions);
            $pluginName = MultipleLanguages::getInstance()->getSelectedPluginName();
            if (!isset($pluginName[0]) /* strlen($pluginName) === 0 */) {
                $pluginName = __('none', 'kocuj-sitemap');
            }
            Base::getInstance()->getKocujILObj()
                ->get('settings-form', KocujIL\Enums\ProjectCategory::BACKEND)
                ->addOptionFieldToTab('settings', 'multilang', 'select', 'Multilang',
                    __('You can select the plugin which you want to use for multi-lingual content on the website. You must also installed and activated the selected plugin to working it correctly. If you choose `automatically select the plugin`, the plugin for multi-lingual website will be selected automatically. If you choose `do not use any plugin for multiple languages`, any plugin for multi-lingual website will not be used.',
                        'kocuj-sitemap'), array(), array(
                        'global_addinfo' => __('Only installed and activated plugins are on this list. Automatically selecting the plugin may not always works correctly if you have installed and activated more than one plugin for multilingualism.',
                            'kocuj-sitemap'),
                        'options' => $multiLangOptions
                    ));
            Base::getInstance()->getKocujILObj()
                ->get('settings-form', KocujIL\Enums\ProjectCategory::BACKEND)
                ->addHtmlToTab('settings', 'multilang',
                    __('Currently used plugin:', 'kocuj-sitemap') . ' ' . $pluginName, '&nbsp;');
            Base::getInstance()->getKocujILObj()
                ->get('settings-form', KocujIL\Enums\ProjectCategory::BACKEND)
                ->addTab('settings', 'troubleshooting', __('Troubleshooting', 'kocuj-sitemap'));
            Base::getInstance()->getKocujILObj()
                ->get('settings-form', KocujIL\Enums\ProjectCategory::BACKEND)
                ->addOptionFieldToTab('settings', 'troubleshooting', 'text', 'MarginLeft',
                    __('You can force left margin in pixels for each level on multi-level list. `0` means that no left margin will be set by this plugin. Use this option only if you have problems in your theme with displaying a multi-level sitemap.',
                        'kocuj-sitemap'), array(), array(
                        'global_addinfo' => __('Value `0` means that left margin will not be forced. Use value greater than `0` if your theme does not support left margin for multi-level list.',
                            'kocuj-sitemap')
                    ));
            Base::getInstance()->getKocujILObj()
                ->get('settings-form', KocujIL\Enums\ProjectCategory::BACKEND)
                ->addOptionFieldToTab('settings', 'troubleshooting', 'checkbox', 'Cache',
                    __('If this option is checked, the sitemap caching is enabled. Do not disable this option unless you know what you are doing! Cache is an important element of this plugin and disabling it may lower performance of your website. Sometimes disabling of the cache may be needed for some reason, but you should be very careful with this.',
                        'kocuj-sitemap'), array(), array(
                        'global_addinfo' => __('Disabling this option is not recommended.', 'kocuj-sitemap')
                    ));
            Base::getInstance()->getKocujILObj()
                ->get('settings-form', KocujIL\Enums\ProjectCategory::BACKEND)
                ->addOptionFieldToTab('settings', 'troubleshooting', 'checkbox', 'CacheFrontend',
                    __('If this option is checked, cache is generated on the website, instead of in the administration panel. If this option is checked, sometimes displaying the sitemap will take more time due to generation of the cache.',
                        'kocuj-sitemap'), array(), array(
                        'global_addinfo' => __('Enabling this option is not recommended. This option works only if option `enable cache` is checked.',
                            'kocuj-sitemap')
                    ));
            Base::getInstance()->getKocujILObj()
                ->get('settings-form', KocujIL\Enums\ProjectCategory::BACKEND)
                ->addOptionFieldToTab('settings', 'troubleshooting', 'checkbox', 'CacheAdditionalCompress',
                    __('If this option is checked, there will be additional compression of the cache file. You should use this option if you have problems with disk space on your server.',
                        'kocuj-sitemap'), array(), array(
                        'global_addinfo' => __('This option works only if option `enable cache` is checked and Zlib extension for PHP is installed.',
                                'kocuj-sitemap') . '<br />' . (function_exists('gzinflate') ? __('You can use this option, because you have a Zlib extension.',
                                'kocuj-sitemap') : __('Warning: This option will not work, because you don\'t have a Zlib extension for PHP.',
                                'kocuj-sitemap'))
                    ));
            Base::getInstance()->getKocujILObj()
                ->get('settings-form', KocujIL\Enums\ProjectCategory::BACKEND)
                ->addTab('order', 'order', __('Order of elements', 'kocuj-sitemap'));
            Base::getInstance()->getKocujILObj()
                ->get('settings-fields', KocujIL\Enums\ProjectCategory::BACKEND)
                ->addType('sitemapelement', array(
                    $this,
                    'fieldSitemapElement'
                ));
            Base::getInstance()->getKocujILObj()
                ->get('settings-form', KocujIL\Enums\ProjectCategory::BACKEND)
                ->addOptionFieldToTab('order', 'order', 'sitemapelement', 'OrderList', '', array(), array(
                    'global_hidelabel' => true
                ));
            if (!empty($this->adminPagesData)) {
                foreach ($this->adminPagesData as $val) {
                    if (!empty($val['type']['objectadmin'])) {
                        // get administration page data
                        $page = $val['type']['objectadmin']->getAdminPageData();
                        if ((!empty($page)) && (isset($page['tabs'])) && (!empty($page['tabs']))) {
                            // add tab for section name
                            if (($val['issectionname']) && (!empty($page['tabs']))) {
                                $options = Options::getInstance()->getSitemapTypeLanguagesOptionsNames($val['type']['letter']);
                                if (!empty($options)) {
                                    $fields = array();
                                    foreach ($options as $option) {
                                        $addInfo = '';
                                        $flag = MultipleLanguages::getInstance()->getLanguageFlag($option['language']);
                                        if (isset($flag[0]) /* strlen($flag) > 0 */) {
                                            $addInfo .= KocujIL\Classes\HtmlHelper::getInstance()->getImage($flag,
                                                    array(
                                                        'style' => 'width:18px;height:12px;'
                                                    )) . '&nbsp;';
                                        }
                                        $addInfo .= '(' . $option['language'] . (($option['language'] === 'en_US') ? ' - ' . __('default language',
                                                    'kocuj-sitemap') : '') . ')';
                                        $fields[] = array(
                                            'text',
                                            $option['option'],
                                            __('You can set the title of the section for the selected language. If you leave this field empty and your current language will be the same as the language for this field, the default section title will be displayed or standard section title in current language (in English) if no translation is available.',
                                                'kocuj-sitemap'),
                                            array(),
                                            array(
                                                'global_addinfo' => $addInfo,
                                                'global_hidelabel' => true
                                            )
                                        );
                                    }
                                    $page['tabs']['section_name'] = array(
                                        'title' => __('Section title', 'kocuj-sitemap'),
                                        'fields' => $fields
                                    );
                                }
                            }
                            // add tabs and fields to form
                            foreach ($page['tabs'] as $key => $tab) {
                                Base::getInstance()->getKocujILObj()
                                    ->get('settings-form', KocujIL\Enums\ProjectCategory::BACKEND)
                                    ->addTab($val['id'], $key, $tab['title']);
                                foreach ($tab['fields'] as $field) {
                                    if (isset($field[0][0]) /* strlen($field[0]) > 0 */) {
                                        Base::getInstance()->getKocujILObj()
                                            ->get('settings-form', KocujIL\Enums\ProjectCategory::BACKEND)
                                            ->addOptionFieldToTab($val['id'], $key, $field[0], $field[1], $field[2],
                                                $field[3], $field[4]);
                                    } else {
                                        Base::getInstance()->getKocujILObj()
                                            ->get('settings-form', KocujIL\Enums\ProjectCategory::BACKEND)
                                            ->addHtmlToTab($val['id'], $key, $field[1]);
                                    }
                                }
                            }
                        }
                    }
                }
            }
            // set restore page
            Base::getInstance()->getKocujILObj()
                ->get('page-restore', KocujIL\Enums\ProjectCategory::BACKEND)
                ->setOptionsContainerId('options');
            // add settings help
            Base::getInstance()->getKocujILObj()
                ->get('settings-help', KocujIL\Enums\ProjectCategory::BACKEND)
                ->addHelpTopic('basic_settings', 'overview', __('Overview', 'kocuj-sitemap'),
                    '<p>' . __('This is the place where you can set the basic options for the sitemap.',
                        'kocuj-sitemap') . '</p>' . '<p>' . __('Options are divided into four tabs: `sitemap format`, `administration panel`, `plugins for multilingualism` and `troubleshooting`. Each tab can be selected by clicking on it.',
                        'kocuj-sitemap') . '</p>' . '<p>' . __('To save changed settings, click on the button `save settings`.',
                        'kocuj-sitemap') . '</p>');
            Base::getInstance()->getKocujILObj()
                ->get('settings-help', KocujIL\Enums\ProjectCategory::BACKEND)
                ->addHelpTopic('basic_settings', 'sitemap_format', __('`Sitemap format` tab', 'kocuj-sitemap'),
                    '<p>' . __('There are the following options in this tab:',
                        'kocuj-sitemap') . '</p>' . '<p><ul>' . '<li><em>`' . __('Display `powered by` information below sitemap',
                        'kocuj-sitemap') . '`</em>: ' . __('If this option is checked, there will be `powered by` information below the sitemap. It will also contains a link to the plugin\'s author website and link to the plugin\'s website. If you find this plugin useful, please check this option, because it is a very good solution for making an advertisement for its author.',
                        'kocuj-sitemap') . '</li>' . '<li><em>`' . __('Display `powered by` information below sitemap in widget',
                        'kocuj-sitemap') . '`</em>: ' . __('If this option is checked, there will be `powered by` information below the sitemap in widget. It will also contains a link to the plugin\'s author website and link to the plugin\'s website. If you find this plugin useful, please check this option, because it is a very good solution for making an advertisement for its author.',
                        'kocuj-sitemap') . '</li>' . '<li><em>`' . __('Use HTML 5 tags',
                        'kocuj-sitemap') . '`</em>: ' . __('If this option is checked, the sitemap will display itself in HTML 5 navigation tag (`nav`) and the `powered by` information will display itself in HTML 5 footer tag (`footer`).',
                        'kocuj-sitemap') . '</li>' . '<li><em>`' . __('Display link to main site',
                        'kocuj-sitemap') . '`</em>: ' . __('If this option is checked, there will be link to the main site as first entry in the sitemap.',
                        'kocuj-sitemap') . '</li>' . '<li><em>`' . __('Divide display into sections',
                        'kocuj-sitemap') . '`</em>: ' . __('If this option is checked, the sitemap will be divided into sections, for example, post, pages, etc.',
                        'kocuj-sitemap') . '</li>' . '<li><em>`' . __('Header text HTML level value for each section',
                        'kocuj-sitemap') . '`</em>: ' . __('You can specify the header text level (HTML tag `H*`) for each section of the sitemap displayed by the shortcode or PHP function. The value which should be entered here can be different for different themes. This option will not have any effect on sitemap displayed by widget. If you are not sure what is the meaning of this tag in HTML, leave this value unchanged, because its default value should be correct for many WordPress themes.',
                        'kocuj-sitemap') . '</li>' . '<li><em>`' . __('Header text HTML level value for each section in widget',
                        'kocuj-sitemap') . '`</em>: ' . __('You can specify the header text level (HTML tag `H*`) for each section of the sitemap displayed by the widget. The value which should be entered here can be different for different themes. This option will not have any effect on sitemap displayed by the shortcode or PHP function. If you are not sure what is the meaning of this tag in HTML, leave this value unchanged, because its default value should be correct for many WordPress themes.',
                        'kocuj-sitemap') . '</li>' . '</ul></p>');
            Base::getInstance()->getKocujILObj()
                ->get('settings-help', KocujIL\Enums\ProjectCategory::BACKEND)
                ->addHelpTopic('basic_settings', 'administration_panel_options',
                    __('`Administration panel` tab', 'kocuj-sitemap'),
                    '<p>' . __('There are the following options in this tab:',
                        'kocuj-sitemap') . '</p>' . '<p><ul>' . '<li><em>`' . __('Enable shortcode button for sitemap in visual and HTML editor',
                        'kocuj-sitemap') . '`</em>: ' . __('If this option is checked, there will be a button for adding a shortcode for sitemap in visual and HTML editor in administration panel.',
                        'kocuj-sitemap') . '</li>' . '</ul></p>');
            Base::getInstance()->getKocujILObj()
                ->get('settings-help', KocujIL\Enums\ProjectCategory::BACKEND)
                ->addHelpTopic('basic_settings', 'plugins_for_multilingualism',
                    __('`Plugins for multilingualism` tab', 'kocuj-sitemap'),
                    '<p>' . __('There are the following options in this tab:',
                        'kocuj-sitemap') . '</p>' . '<p><ul>' . '<li><em>`' . __('Use plugin for multiple languages',
                        'kocuj-sitemap') . '`</em>: ' . __('You can select the plugin which you want to use for multi-lingual content on the website. You must also installed and activated the selected plugin to working it correctly. If you choose `automatically select the plugin`, the plugin for multi-lingual website will be selected automatically. Remember, that automatically selecting the plugin may not always works correctly if you have installed and activated more than one plugin for multilingualism. If you choose `do not use any plugin for multiple languages`, any plugin for multi-lingual website will not be used.',
                        'kocuj-sitemap') . '</li>' . '</ul></p>');
            Base::getInstance()->getKocujILObj()
                ->get('settings-help', KocujIL\Enums\ProjectCategory::BACKEND)
                ->addHelpTopic('basic_settings', 'troubleshooting', __('`Troubleshooting` tab', 'kocuj-sitemap'),
                    '<p>' . __('There are the following options in this tab:', 'kocuj-sitemap') . '</p>' . '<p><ul>' .
                    /* translators: %s: Root directory name for cache for this plugin (usually "/wp-content/cache/kocuj-sitemap/") */
                    '<li><em>`' . __('Force left margin in pixels for each level on multi-level list',
                        'kocuj-sitemap') . '`</em>: ' . __('You can force left margin in pixels for each level on multi-level list. `0` means that no left margin will be set by this plugin. Use this option only if you have problems in your theme with displaying a multi-level sitemap.',
                        'kocuj-sitemap') . '</li>' . '<li><em>`' . __('Enable cache',
                        'kocuj-sitemap') . '`</em>: ' . sprintf(__('If this option is checked, the sitemap caching is enabled. Do not disable this option unless you know what you are doing! Cache is an important element of this plugin and disabling it may lower performance of your website. Sometimes disabling of the cache may be needed for some reason, but you should be very careful with this. Remember that cache can works correcly only if the cache directory (currently it is "%s") and all its subdirectories have permissions to write. Disabling this option is not recommended.',
                        'kocuj-sitemap'),
                        Cache::getInstance()->getCacheRootDirectory()) . '</li>' . '<li><em>`' . __('Generate cache on the website instead of in the administration panel',
                        'kocuj-sitemap') . '`</em>: ' . __('If this option is checked, cache is generated on the website, instead of in the administration panel. If this option is checked, sometimes displaying the sitemap will take more time due to generation of the cache. This option works only if option `enable cache` is checked. Enabling this option is not recommended.',
                        'kocuj-sitemap') . '</li>' . '<li><em>`' . __('Enable additional compression of cache',
                        'kocuj-sitemap') . '`</em>: ' . __('If this option is checked, there will be additional compression of the cache file. You should use this option if you have problems with disk space on your server. Remember that this option can lower the performance of your system, so use it only if you are sure that your website and server can provide enough resources to do so. This option works only if option `enable cache` is checked and Zlib extension for PHP installed.',
                        'kocuj-sitemap') . '</li>' . '</ul></p>');
            Base::getInstance()->getKocujILObj()
                ->get('settings-help', KocujIL\Enums\ProjectCategory::BACKEND)
                ->addHelpTopic('order', 'overview', __('Overview', 'kocuj-sitemap'),
                    '<p>' . __('This is the place where you can set the order of sections inside the sitemap.',
                        'kocuj-sitemap') . '</p>' . '<p>' . __('To save changed order, click on the button `save order of elements`.',
                        'kocuj-sitemap') . '</p>');
            Base::getInstance()->getKocujILObj()
                ->get('settings-help', KocujIL\Enums\ProjectCategory::BACKEND)
                ->addHelpTopic('order', 'usage', __('Usage', 'kocuj-sitemap'),
                    '<p>' . __('You can change the order of any sitemap section by clicking on one of the arrow next to the entry to move it up or down.',
                        'kocuj-sitemap') . '</p>');
            foreach ($addSettingsHelp as $key => $val) {
                Base::getInstance()->getKocujILObj()
                    ->get('settings-help', KocujIL\Enums\ProjectCategory::BACKEND)
                    ->addHelpTopic($val[0], $val[1], $val[2], $val[3]);
            }
        }
    }

    /**
     * Additional callback for controllers for save and restore options
     *
     * @access public
     * @param object $componentObj
     *            Component object
     * @param string $formId
     *            Form identifier
     * @param bool $isDataSet
     *            Container is for data set
     * @param int $dataSetElementId
     *            Data set element identifier if $isDataSet is set to true
     * @param
     *            string &$outputText Output text
     * @return bool Controller has been executed correctly (true) or not (false)
     */
    public function controllerSaveOrRestore($componentObj, $formId, $isDataSet, $dataSetElementId, &$outputText)
    {
        // recreate cache
        try {
            Cache::getInstance()->createCache();
        } catch (\Exception $e) {
        }
        // exit
        return true;
    }

    /**
     * Display plugin form
     *
     * @access public
     * @return void
     */
    public function pluginForm()
    {
        // show form
        Base::getInstance()->getKocujILObj()
            ->get('settings-form', KocujIL\Enums\ProjectCategory::BACKEND)
            ->showForm();
    }

    /**
     * Get field - sitemap element
     *
     * @access public
     * @param object $componentObj
     *            Component object
     * @param string $fieldHtmlId
     *            Field HTML id
     * @param string $fieldHtmlName
     *            Field HTML name
     * @param string $fieldValue
     *            Field value
     * @param string $fieldAttrs
     *            Prepared text with field attributes "id", "name" and "title" and with space at beginning
     * @param string $tipText
     *            Tooltip text
     * @param array $classAndStyle
     *            Class and style for element; class and style data must have the following fields: "all" (string type; HTML classes with "class" tag and HTML styles with "style" tag and space before), "class" (string type; HTML classes), "classwithtag" (string type; HTML classes with "class" tag and space before), "style" (string type; HTML styles), "stylewithtag" (string type; HTML styles with "style" tag and space before)
     * @param int $fieldForWidget
     *            This field is for widget or not; must be one of the following constants from KocujIL\Enums\Project\Components\Backend\SettingsFields\FieldForWidget: NO (when field is not for widget) or YES (when field is for widget)
     * @param int $optionArray
     *            Option is array or standard; must be one of the following constants from KocujIL\Enums\Project\Components\Backend\SettingsFields\OptionArray: NO (when it is standard option) or YES (when it is array option)
     * @param int $orderInWidget
     *            Label and HTML element order in widget; must be one of the following constants from KocujIL\Enums\Project\Components\Backend\SettingsFields\OrderInWidget: FIRST_LABEL (first label, then element), FIRST_ELEMENT (first element, then label)
     * @param array $additional
     *            Additional settings
     * @return string Field
     */
    public function fieldSitemapElement(
        $componentObj,
        $fieldHtmlId,
        $fieldHtmlName,
        $fieldValue,
        $fieldAttrs,
        $tipText,
        array $classAndStyle,
        $fieldForWidget,
        $optionArray,
        $orderInWidget,
        array $additional
    ) {
        // get element name
        $name = '';
        $obj = Sitemap::getInstance()->getElementTypeAdminObject($fieldValue);
        if ($obj !== null) {
            $name = $obj->getAdminOrderName();
        }
        // exit
        return '<input' . $fieldAttrs . ' type="hidden" value="' . esc_attr($fieldValue) . '" style="width:1px;height:1px;margin:0;padding:0;position:absolute;" /><div' . $classAndStyle['all'] . '>' . $name . '</div>';
    }
}
